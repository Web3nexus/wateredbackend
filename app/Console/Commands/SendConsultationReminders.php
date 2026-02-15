<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Mail\AppointmentReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendConsultationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watered:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send consultation reminders 1h and 30m before start time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Find appointments in exactly 1 hour (+/- 5 mins to catch run intervals)
        $oneHourFromNow = $now->copy()->addHour();
        $this->remind($oneHourFromNow, '1 hour');

        // Find appointments in exactly 30 minutes
        $thirtyMinsFromNow = $now->copy()->addMinutes(30);
        $this->remind($thirtyMinsFromNow, '30 minutes');
    }

    protected function remind(Carbon $targetTime, string $label)
    {
        $appointments = Appointment::where('appointment_status', 'confirmed')
            ->whereBetween('start_time', [
                $targetTime->copy()->subMinutes(2),
                $targetTime->copy()->addMinutes(2)
            ])
            ->get();

        foreach ($appointments as $appointment) {
            $this->info("Sending {$label} reminder for appointment ID: {$appointment->id}");

            // Handle both auth users and guests
            $recipient = $appointment->user ? $appointment->user->email : $appointment->email;

            if ($recipient) {
                Mail::to($recipient)->send(new AppointmentReminderMail($appointment));
            }
        }
    }
}
