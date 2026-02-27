<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\EventRegistration;
use App\Models\Event;
use App\Models\ConsultationType;

class BackfillRevenueData extends Command
{
    protected $signature = 'revenue:backfill';
    protected $description = 'Backfill revenue data for historical appointments and event registrations';

    public function handle()
    {
        $this->info('Starting revenue backfill...');

        // Backfill Appointments
        $this->info('Backfilling Appointments...');
        $appointments = Appointment::where('amount', '<=', 0)->get();
        foreach ($appointments as $appointment) {
            if ($appointment->consultationType && $appointment->consultationType->price > 0) {
                $appointment->update(['amount' => $appointment->consultationType->price]);
                $this->line("Updated Appointment {$appointment->appointment_code} with amount {$appointment->consultationType->price}");
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
