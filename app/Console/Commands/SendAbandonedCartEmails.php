<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use App\Models\User;
use App\Notifications\AbandonedCartNotification;
use Illuminate\Console\Command;

class SendAbandonedCartEmails extends Command
{
    protected $signature   = 'cart:send-recovery-emails';
    protected $description = 'Send recovery emails to users with carts abandoned for more than 24 hours.';

    public function handle(): int
    {
        // Find users with cart items (type=cart) not updated in the last 24h
        // who haven't received a recovery email in the last 7 days
        $userIds = CartItem::where('type', 'cart')
            ->where('updated_at', '<', now()->subHours(24))
            ->distinct()
            ->pluck('user_id');

        if ($userIds->isEmpty()) {
            $this->info('No abandoned carts found.');
            return self::SUCCESS;
        }

        $users = User::whereIn('id', $userIds)
            ->where('role', 'approved')
            ->where(function ($q) {
                $q->whereNull('cart_recovery_sent_at')
                  ->orWhere('cart_recovery_sent_at', '<', now()->subDays(7));
            })
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            // Check if user placed an order recently (within 24h) — skip if so
            $recentOrder = $user->orders()
                ->where('created_at', '>', now()->subHours(24))
                ->exists();

            if ($recentOrder) {
                continue;
            }

            $cartItems = CartItem::where('user_id', $user->id)
                ->where('type', 'cart')
                ->with(['product', 'printJob.template'])
                ->get();

            if ($cartItems->isEmpty()) {
                continue;
            }

            $user->notify(new AbandonedCartNotification($cartItems));
            $user->update(['cart_recovery_sent_at' => now()]);
            $sent++;
        }

        $this->info("Sent {$sent} abandoned cart email(s).");

        return self::SUCCESS;
    }
}
