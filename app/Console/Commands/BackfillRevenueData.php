<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\EventRegistration;
use App\Models\Event;
use App\Models\ConsultationType;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillRevenueData extends Command
{
    protected $signature = 'revenue:backfill';
    protected $description = 'Backfill revenue data for historical appointments and event registrations';

    public function handle()
    {
        $this->info('Starting revenue backfill...');

        $settings = \App\Models\GlobalSetting::first();
        $monthlyPrice = $settings->premium_monthly_amount ?? 5000;
        $yearlyPrice = $settings->premium_yearly_amount ?? 50000;

        // Backfill Subscriptions
        $this->info('Backfilling Subscriptions...');
        $subscriptions = \App\Models\Subscription::where('amount', '<=', 0)->orWhereNull('amount')->get();
        foreach ($subscriptions as $sub) {
            if ($sub->plan_id === 'free_trial') {
                $sub->update(['amount' => 0]);
                $this->line("Set Free Trial Subscription {$sub->id} amount to 0");
                continue;
            }
            $amount = str_contains($sub->plan_id, 'yearly') ? $yearlyPrice : $monthlyPrice;
            $sub->update(['amount' => $amount]);
            $this->line("Updated Subscription {$sub->id} ({$sub->plan_id}) with amount {$amount}");
        }

        // Fix existing free trials that might have been incorrectly backfilled
        \App\Models\Subscription::where('plan_id', 'free_trial')->update(['amount' => 0]);

        // Backfill Appointments
        $this->info('Backfilling Appointments...');
        $appointmentTable = Schema::hasTable('appointments') ? 'appointments' : 'bookings';
        $appointments = DB::table($appointmentTable)->where('amount', '<=', 0)->orWhereNull('amount')->get();
        foreach ($appointments as $appointment) {
            $consultationType = DB::table('consultation_types')->where('id', $appointment->consultation_type_id)->first();
            if ($consultationType && $consultationType->price > 0) {
                DB::table($appointmentTable)->where('id', $appointment->id)->update(['amount' => $consultationType->price]);
                $this->line("Updated Appointment {$appointment->id} with amount {$consultationType->price}");
            }
        }

        // Backfill Event Registrations
        $this->info('Backfilling Event Registrations...');
        $registrations = EventRegistration::where('amount', '<=', 0)->orWhereNull('amount')->get();
        foreach ($registrations as $registration) {
            if ($registration->event && $registration->event->price > 0) {
                $registration->update(['amount' => $registration->event->price]);
                $this->line("Updated Registration {$registration->booking_code} for Event {$registration->event->title} with amount {$registration->event->price}");
            }
        }

        $this->info('Revenue backfill completed!');
    }
}
