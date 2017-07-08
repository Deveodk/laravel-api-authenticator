<?php

namespace DeveoDK\LaravelApiAuthenticator\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var string */
    private $resetLink;

    /**
     * PasswordResetNotification constructor.
     * @param $resetLink
     */
    public function __construct($resetLink)
    {
        $this->resetLink = $resetLink;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(trans('apiAuth.password_reset_mail.subject'))
            ->line(trans('apiAuth.password_reset_mail.heading'))
            ->line(trans('apiAuth.password_reset_mail.body'))
            ->action(trans('apiAuth.password_reset_mail.actionButton'), $this->resetLink);
    }
}
