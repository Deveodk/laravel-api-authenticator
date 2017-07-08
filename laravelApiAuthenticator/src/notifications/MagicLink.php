<?php

namespace DeveoDK\LaravelApiAuthenticator\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MagicLink extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var string */
    private $magicLink;

    /**
     * MagicLink constructor.
     * @param string $magicLink
     */
    public function __construct($magicLink)
    {
        $this->magicLink = $magicLink;
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
            ->subject(trans('apiAuth.magic_link_mail.subject'))
            ->line(trans('apiAuth.magic_link_mail.heading'))
            ->action(trans('apiAuth.magic_link_mail.actionButton'), $this->magicLink)
            ->line(trans('apiAuth.magic_link_mail.body'));
    }
}
