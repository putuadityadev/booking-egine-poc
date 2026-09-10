<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\MembershipOAuthService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReleaseScheduledPointsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'points:release-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release pending points for bookings that have reached check-in or check-out date';

    /**
     * Execute the console command.
     */
    public function handle(MembershipOAuthService $oauthService): int
    {
        $today = now()->toDateString();
        $this->info("Scanning bookings scheduled for point release on or before {$today}...");

        $bookings = Booking::with('property.membershipProperty')
            ->where('is_member', true)
            ->where('is_points_materialized', false)
            ->whereNotNull('member_id')
            ->where(function ($query) use ($today) {
                // Checkin mode reached check_in date
                $query->where(function ($q) use ($today) {
                    $q->where('point_release_mode', 'checkin')
                      ->whereDate('check_in', '<=', $today);
                })
                // Checkout mode reached check_out date
                ->orWhere(function ($q) use ($today) {
                    $q->where('point_release_mode', 'checkout')
                      ->whereDate('check_out', '<=', $today);
                });
            })
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No eligible bookings found for point release.');
            return Command::SUCCESS;
        }

        $this->info("Found {$bookings->count()} eligible booking(s). Processing...");

        $successCount = 0;
        $failedCount = 0;

        foreach ($bookings as $booking) {
            $property = $booking->property;
            if (!$property || !$property->has_membership || !$property->membershipProperty) {
                $this->warn("Skipping booking {$booking->reservation_code}: Property membership not active.");
                continue;
            }

            try {
                $txId = $booking->membership_transaction_id ?: $booking->reservation_code;
                $oauthService->materializeTransaction(
                    $property,
                    $txId,
                    $booking->member_id
                );

                $booking->update([
                    'is_points_materialized' => true,
                    'materialized_at' => now(),
                ]);

                $this->line("  ✓ [{$booking->reservation_code}] Released {$booking->points_earned} pts (Mode: {$booking->point_release_mode})");
                $successCount++;
            } catch (Exception $e) {
                $this->error("  ✗ [{$booking->reservation_code}] Error: {$e->getMessage()}");
                Log::error('SCHEDULED_POINT_RELEASE_FAILED', [
                    'reservation_code' => $booking->reservation_code,
                    'error' => $e->getMessage(),
                ]);
                $failedCount++;
            }
        }

        $this->info("Point release complete. Success: {$successCount}, Failed: {$failedCount}.");
        return Command::SUCCESS;
    }
}
