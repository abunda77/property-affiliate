<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GoWAApiFailureNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private int $failureCount,
        private array $context = []
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('GoWA API Repeated Failures Alert')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The GoWA WhatsApp API has experienced repeated failures.')
            ->line('**Failure Count:** ' . $this->failureCount . ' failures in the last hour')
            ->line('**Impact:** Lead notifications may not be delivered to affiliates.')
            ->line('**Action Required:** Please check the GoWA API configuration and service status.')
            ->action('View System Settings', url('/admin/settings'))
            ->line('Recent failure context:')
            ->line($this->formatContext())
            ->line('Please investigate and resolve this issue as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'gowa_api_failure',
            'failure_count' => $this->failureCount,
            'context' => $this->context,
            'message' => 'GoWA API has experienced ' . $this->failureCount . ' failures in the last hour.',
        ];
    }

    /**
     * Format context for display
     */
    private function formatContext(): string
    {
        if (empty($this->context)) {
            return 'No additional context available.';
        }

        $formatted = [];
        foreach ($this->context as $key => $value) {
            $formatted[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . (is_array($value) ? json_encode($value) : $value);
        }

        return implode("\n", $formatted);
    }
}
