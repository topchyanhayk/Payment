<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendWebhookFailedNotification extends Notification
{
    use Queueable;

    private string $uuid;
    private string $kind;

    public function __construct(string $uuid, string $kind)
    {
        $this->uuid = $uuid;
        $this->kind = $kind;
    }

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Send webhook failed')
            ->line('The webhook failed handle by your side')
            ->line('object id is ' . $this->uuid)
            ->line('object kind is ' . $this->kind);
    }
}
