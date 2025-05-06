<?php

namespace App\Jobs;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $recipient;
    protected $resource;
    protected $target;
    protected $messageType;
    protected $data;

    /**
     * Cria uma nova instância do job.
     */
    public function __construct($type, $recipient, $resource, $target, $messageType, $data)
    {
        $this->type = $type; // 'email' ou 'whatsapp'
        $this->recipient = $recipient;
        $this->resource = $resource;
        $this->target = $target;
        $this->messageType = $messageType;
        $this->data = $data;
    }

    /**
     * Executa o job.
     */
    public function handle()
    {
        $notificationService = new NotificationService();

        if ($this->type === 'email') {
            $notificationService->sendEmail($this->recipient, $this->resource, $this->target, $this->messageType, $this->data);
        } elseif ($this->type === 'whatsapp') {
            $notificationService->sendWhatsApp($this->recipient, $this->resource, $this->target, $this->messageType, $this->data);
        }
    }
}
