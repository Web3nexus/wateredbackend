<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserStat;
use Illuminate\Database\Seeder;

/**
 * BackfillUserStatsSeeder
 *
 * Creates a UserStat row for every existing user who doesn't already have one.
 * Run once:  php artisan db:seed --class=BackfillUserStatsSeeder
 */
class BackfillUserStatsSeeder extends Seeder
{
    public function run(): void
    {
        $count = 0;

        User::chunkById(100, function ($users) use (&$count) {
            foreach ($users as $user) {
                $created = UserStat::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'daily_streak'             => 0,
                        'time_spent_minutes'       => 0,
                        'nima_sedani_time_minutes' => 0,
                        'amount_spent_kobo'        => 0,
                    ]
                );

                if ($created->wasRecentlyCreated) {
                    $count++;
                }
            }
        });

        $this->command->info("BackfillUserStatsSeeder: created {$count} new UserStat record(s).");
    }
}
