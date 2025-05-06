<?php

namespace App\Integrations;

use App\Models\Integration;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

// class WhatsappApiIntegration implements ShouldQueue
class WhatsappApiIntegration
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct() {}

    public function handle()
    {
        return self::sendMessage($this->content ?? null);
    }

    public static function sendMessage($content)
    {

        $integration = Integration::where('slug', 'whatsapp-api')->first();

        if (!$integration) {
            throw new \Exception("Integração WhatsApp não encontrada.");
        }

        $whatsapp_host = $integration->settings['host'] ?? null;
        $whatsapp_token = $integration->settings['token'] ?? null;

        if (!$whatsapp_host || !$whatsapp_token) {
            throw new \Exception("Credenciais do WhatsApp não configuradas.");
        }

        $formatted_number = self::formatNumber($content['recipient']);
        // $formatted_number = $content['recipient'];

        // Verifica se há mídia na mensagem
        if (!empty($content['media'])) {
            // Envio de imagem
            $media_type = $content['media_type'] ?? 'image'; // Tipo de mídia (image, video, etc.)
            $image_url = $content['media']; // Caminho da imagem
            $caption = $content['message'] ?? ''; // Legenda opcional
            $url_send_media = str_replace('sendText', 'sendMedia', $whatsapp_host);

            \Log::info($media_type . ' - ' . $image_url);

            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "apikey" => $whatsapp_token,
            ])->post($url_send_media, [
                "number" => $formatted_number,
                "mediatype" => $media_type,
                "media" => $image_url,
                "caption" => '',
                "fileName" => basename($image_url), // Extrai o nome do arquivo da URL
                "delay" => 1200,
                "linkPreview" => true,
            ]);
        } else {
            // \Log::info($content['message']);

            // Envio de texto
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "apikey" => $whatsapp_token,
            ])->post($whatsapp_host, [
                "number" => $formatted_number,
                'text' => $content['message'],
                "delay" => 1200,
                "linkPreview" => true,
            ]);
        }

        $result = json_decode($response->getBody(), true);
        $resultEncode   = json_encode($result);

        if (isset($result['status']) && $result['status'] != 'PENDING') {
            \Log::alert('WPPService :: ' . $formatted_number .  ' :: ' . $resultEncode);
        } else {
            \Log::info('WPPService :: ' . $formatted_number .  ' :: ' . $result['status'] . ' -> SUCCESS');
        }

        return $result;
    }

    protected static function formatNumber($number)
    {
        $replace = ['(', ')', '.', ' ', '-'];
        return str_replace($replace, '', $number);
    }


    // ****************
    /* API WHATSAPP */
    /* API WHATSAPP */
    // ****************
    // List Instances
    public static function listInstances()
    {
        $whatsapp_api_host = config('project.whatsapp.api_host');
        $whatsapp_api_key = config('project.whatsapp.api_key');
        $url_target = 'instance/fetchInstances';

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => $whatsapp_api_key
        ])->get($whatsapp_api_host . $url_target);

        if ($response->successful()) {
            $result = $response->json();
            return $result;
        } else {
            return false;
        }
    }

    // Create Instance
    public static function createInstance($request)
    {
        $whatsapp_api_host = config('project.whatsapp.api_host');
        $whatsapp_api_key = config('project.whatsapp.api_key');
        $url_target = 'instance/create';

        $array_request = [
            "instanceName"      => $request['instance_name'],
            "token"             => 'wpp-' . date('ymdhis') . Str::random(16),
            "qrcode"            => false,
        ];

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => $whatsapp_api_key
        ])->post($whatsapp_api_host . $url_target, $array_request);

        if ($response->successful()) {
            $result = $response->json();
            return $result;
        } else {
            return false;
        }
    }

    // Connect Instance
    public static function connectInstance($instance_name)
    {
        $whatsapp_api_host = config('project.whatsapp.api_host');
        $whatsapp_api_key = config('project.whatsapp.api_key');
        $url_target = 'instance/connect/' . $instance_name;

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => $whatsapp_api_key
        ])->get($whatsapp_api_host . $url_target);

        if ($response->successful()) {
            $result = $response->json();

            if (isset($result['code'])) {
                return '<h2 class="h3">Use seu WhatsApp <br/> para conectar o aparelho!</h2><img src="' . $result['base64'] . '" class="img-fluid" width="220" height="220">';
            } else {
                if ($result['instance']['state'] == 'open') {
                    $status = 'A Instância "' . strtoupper(auth()->user()->instancia_whatsapp) . '" já está conectada';
                } else {
                    $status = 'Desconectado';
                }
                return $status;
            }
        } else {
            return false;
        }
    }

    // Check Status Instance
    public static function statusInstance($instance_name)
    {
        $whatsapp_api_host = config('project.whatsapp.api_host');
        $whatsapp_api_key = config('project.whatsapp.api_key');
        $url_target = 'instance/connectionState/' . $instance_name;

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => $whatsapp_api_key
        ])->get($whatsapp_api_host . $url_target);

        if ($response->successful()) {
            $result = $response->json();

            return $result['instance']['state'];
        } else {
            return false;
        }
    }

    // Logout Instance
    public static function logoutInstance($instance_name)
    {
        $whatsapp_api_host = config('project.whatsapp.api_host');
        $whatsapp_api_key = config('project.whatsapp.api_key');
        $url_target = 'instance/logout/' . $instance_name;

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => $whatsapp_api_key
        ])->delete($whatsapp_api_host . $url_target);

        if ($response->successful()) {
            $result = $response->json();

            if ($response->successful()) {
                return 'A Instância ' . strtoupper(auth()->user()->instancia_whatsapp) . ' foi descontectada!';
            } else {
                return 'Ops, houve algum problema ao descontectar. Tente novamente!';
            }
        } else {
            return false;
        }
    }
}
