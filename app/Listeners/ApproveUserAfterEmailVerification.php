<?php

namespace App\Listeners;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;

class ApproveUserAfterEmailVerification
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // Only auto-approve if user is still pending
        if ($user instanceof User && $user->status === UserStatus::PENDING) {
            // Approve user and generate affiliate code
            $user->approve();

            // Assign affiliate role
            if (!$user->hasRole('affiliate')) {
                $user->assignRole('affiliate');
            }

            Log::info('User auto-approved after email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'affiliate_code' => $user->affiliate_code,
            ]);
        }
    }
}
