<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParticipantRegistered extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $codigo
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Cadastro realizado - PHPeste 2025')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Seu cadastro para o PHPeste 2025 foi realizado com sucesso!')
            ->line('Guarde o código abaixo com cuidado. Ele será necessário para receber seu brinde no evento caso você seja sorteado.')
            ->line('**Seu código: ' . $this->codigo . '**')
            ->line('Obrigado por participar do PHPeste 2025!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
