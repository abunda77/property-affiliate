<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Services\GoWAService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendLeadNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        private GoWAService $goWAService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead;

        // Load relationships if not already loaded
        $lead->loadMissing(['affiliate', 'property']);

        // Send notification to affiliate if affiliate exists
        if ($lead->affiliate && $lead->affiliate->whatsapp) {
            $this->sendToAffiliate($lead);
        }

        // Send optional confirmation to visitor
        if ($lead->whatsapp) {
            $this->sendToVisitor($lead);
        }
    }

    /**
     * Send notification to the affiliate
     */
    private function sendToAffiliate($lead): void
    {
        try {
            $message = sprintf(
                "Halo, ada prospek baru atas nama %s untuk properti %s.\n\nPesan: %s\n\nSegera follow up!",
                $lead->name,
                $lead->property->title,
                $lead->message ?? '-'
            );

            $context = [
                'lead_id' => $lead->id,
                'affiliate_id' => $lead->affiliate_id,
                'property_id' => $lead->property_id,
                'notification_type' => 'affiliate',
            ];

            $success = $this->goWAService->sendMessage(
                $lead->affiliate->whatsapp,
                $message,
                null,
                false,
                3600,
                $context
            );

            if ($success) {
                Log::info('Lead notification sent to affiliate', $context);
            }
        } catch (\Exception $e) {
            // Log error but don't throw - we don't want to block lead creation
            Log::error('Failed to send notification to affiliate', [
                'lead_id' => $lead->id,
                'affiliate_id' => $lead->affiliate_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send confirmation message to the visitor
     */
    private function sendToVisitor($lead): void
    {
        try {
            $message = sprintf(
                "Terima kasih %s! Kami telah menerima permintaan Anda untuk properti %s. Tim kami akan segera menghubungi Anda.",
                $lead->name,
                $lead->property->title
            );

            $context = [
                'lead_id' => $lead->id,
                'property_id' => $lead->property_id,
                'notification_type' => 'visitor_confirmation',
            ];

            $success = $this->goWAService->sendMessage(
                $lead->whatsapp,
                $message,
                null,
                false,
                3600,
                $context
            );

            if ($success) {
                Log::info('Confirmation sent to visitor', $context);
            }
        } catch (\Exception $e) {
            // Log error but don't throw - we don't want to block lead creation
            Log::error('Failed to send confirmation to visitor', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(LeadCreated $event, \Throwable $exception): void
    {
        Log::error('SendLeadNotification listener failed', [
            'lead_id' => $event->lead->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
