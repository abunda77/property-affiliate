<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AffiliateApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user
    ) {}

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
            ->subject('Welcome to PAMS - Your Affiliate Account is Approved!')
            ->greeting('Hello ' . $this->user->name . '!')
            ->line('Congratulations! Your affiliate account has been approved.')
            ->line('Your unique affiliate code is: **' . $this->user->affiliate_code . '**')
            ->line('You can now start promoting properties and earning commissions.')
            ->action('Login to Dashboard', url('/admin'))
            ->line('Thank you for joining our affiliate program!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'affiliate_code' => $this->user->affiliate_code,
        ];
    }
}
