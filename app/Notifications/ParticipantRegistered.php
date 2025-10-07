<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Participant;
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
        public Participant $participant,
        public Event $event
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
            ->subject('Cadastro realizado - ' . $this->event->title)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Seu cadastro para o sorteio durante o ' . $this->event->title . ' foi realizado com sucesso!')
            ->line('Guarde o código abaixo com cuidado. Ele será necessário para receber seu brinde caso você seja sorteado.')
            ->line('**Seu código: ' . $this->participant->codigo . '**')
            ->line('Obrigado por participar!');
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
