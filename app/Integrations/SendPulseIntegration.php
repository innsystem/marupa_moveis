<?php

namespace App\Integrations;

use App\Models\Integration;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

// class SendPulseIntegration implements ShouldQueue
class SendPulseIntegration
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct() {}

    public function handle()
    {
        return self::sendEmail($this->content ?? null);
    }

    /**
     * Envia um e-mail utilizando a API do SendPulse.
     */
    public static function sendEmail($content)
    {
        $integration = Integration::where('slug', 'send-pulse')->first();

        if (!$integration) {
            throw new \Exception("Integração SendPulse não encontrada.");
        }

        // Configurações do sistema
        $site_name = $integration->settings['site_name'] ?? null;
        $site_email = $integration->settings['site_email'] ?? null;

        // Credenciais da API
        $sendpulse_client_id = $integration->settings['client_id'] ?? null;
        $sendpulse_client_secret = $integration->settings['client_secret'] ?? null;
        $sendpulse_id_template = $integration->settings['id_template'] ?? null;

        if (!$sendpulse_client_id || !$sendpulse_client_secret) {
            throw new \Exception("Credenciais do SendPulse não configuradas.");
        }

        // Gera o token de acesso
        $access_token = self::generateAccessToken();

        if (!$access_token) {
            throw new \Exception("Falha ao obter o token de acesso do SendPulse.");
        }

        // Disparo do e-mail
        $response = Http::withToken($access_token)->post('https://api.sendpulse.com/smtp/emails', [
            "email" =>  [
                "subject"  => $content['subject'],
                "template" => [
                    "id" => $sendpulse_id_template, // ID do template no SendPulse
                    "variables" => [
                        "customer_name" => $content['user_name'],
                        "sp_subject" => $content['subject'],
                        "sp_content" => $content['body'],
                        "current_year" => Carbon::now()->format('Y'),
                        "sender_company" => $site_name,
                        "sender_email_address" => $site_email,
                    ],
                ],
                "from" => [
                    "name"  => $site_name,
                    "email" => $site_email
                ],
                "to" => [
                    [
                        "customer"  => $content['user_name'],
                        "email"     => $content['user_email']
                    ]
                ],
            ],
        ]);

        \Log::info('SendPulse SendMail :: ' . $content['user_email']);

        return json_decode($response->getBody(), true);
    }

    /**
     * Obtém o token de acesso do SendPulse.
     */
    public static function generateAccessToken()
    {
        $integration = Integration::where('slug', 'send-pulse')->first();

        if (!$integration) {
            return null;
        }

        $sendpulse_client_id = $integration->settings['client_id'] ?? null;
        $sendpulse_client_secret = $integration->settings['client_secret'] ?? null;

        $response = Http::post('https://api.sendpulse.com/oauth/access_token', [
            'grant_type'        => 'client_credentials',
            'client_id'         =>  $sendpulse_client_id,
            'client_secret'     =>  $sendpulse_client_secret
        ]);

        return json_decode($response->getBody(), true)['access_token'] ?? null;
    }

    /**
     * Obtém a lista de templates disponíveis no SendPulse.
     */
    public static function getTemplates()
    {
        $access_token = self::generateAccessToken();

        if (!$access_token) {
            return [];
        }

        $response = Http::withToken($access_token)->get('https://api.sendpulse.com/templates/?owner=me');

        return json_decode($response->getBody(), true);
    }
}
