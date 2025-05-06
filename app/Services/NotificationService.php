<?php

namespace App\Services;

use App\Helpers\MessageHelper;
use App\Models\Integration;

class NotificationService
{
    protected $emailIntegration;
    protected $whatsappApiIntegration;

    public function __construct()
    {
        // Obtém a integração ativa para e-mails
        $emailIntegration = Integration::where('slug', 'send-pulse')->where('status', 1)->first();

        if ($emailIntegration) {
            $className = str_replace(' ', '', ucwords(str_replace('-', ' ', $emailIntegration->slug))) . "Integration";
            $this->emailIntegration = app("App\\Integrations\\" . $className);
        } else {
            $this->emailIntegration = null;
        }

        // Obtém a integração ativa para WhatsApp
        $whatsappApiIntegration = Integration::where('slug', 'whatsapp-api')->where('status', 1)->first();

        if ($whatsappApiIntegration) {
            $className = str_replace(' ', '', ucwords(str_replace('-', ' ', $whatsappApiIntegration->slug))) . "Integration";
            $this->whatsappApiIntegration = app("App\\Integrations\\" . $className);
        } else {
            $this->whatsappApiIntegration = null;
        }
    }

    /**
     * Envia notificação por E-mail
     */
    public function sendEmail($recipient, $resource, $target, $type, $data)
    {
        if (!$this->emailIntegration) {
            return false;
        }

        $message = MessageHelper::getMessage($resource, $target, $type, $data);

        if (!$message) {
            return false;
        }

        $content = [
            'user_name' => $data['name'],
            'user_email' => $recipient,
            'subject' => $message['subject'],
            'body' => $message['body'],
        ];

        return $this->emailIntegration::sendEmail($content);
    }


    /**
     * Envia notificação por WhatsApp.
     */
    public function sendWhatsApp($recipient, $resource, $target, $type, $data)
    {
        if (!$this->whatsappApiIntegration) {
            return false;
        }

        $message = MessageHelper::getMessage($resource, $target, $type, $data);

        if (!$message) {
            return false;
        }

        $content = [
            'recipient' => $recipient,
            'message' => $message,
        ];
        
        // Se houver imagem, adiciona os dados necessários
        if (!empty($data['image'])) {
            $content['media'] = $data['image']; 
            $content['media_type'] = 'image'; // Tipo da mídia (pode ser ajustado para outros tipos se necessário)
        }

        if (!empty($data['document_pdf'])) {
            $content['media'] = $data['document_pdf']; 
            $content['media_type'] = 'document'; // Tipo da mídia (pode ser ajustado para outros tipos se necessário)
        }

        return $this->whatsappApiIntegration::sendMessage($content);
    }
}
