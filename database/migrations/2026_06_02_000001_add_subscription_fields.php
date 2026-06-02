<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Remove duplicate provider_subscription_id rows before adding unique constraint.
        // Keep only the row with the highest id for each duplicate group.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('
                DELETE t1 FROM subscriptions t1
                INNER JOIN subscriptions t2
                WHERE t1.provider_subscription_id = t2.provider_subscription_id
                AND t1.id < t2.id
            ');
        } else {
            $duplicates = DB::table('subscriptions')
                ->select('provider_subscription_id')
                ->groupBy('provider_subscription_id')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('provider_subscription_id');

            foreach ($duplicates as $dupRef) {
                $keepId = DB::table('subscriptions')
                    ->where('provider_subscription_id', $dupRef)
                    ->orderByDesc('id')
                    ->value('id');

                DB::table('subscriptions')
                    ->where('provider_subscription_id', $dupRef)
                    ->where('id', '<>', $keepId)
                    ->delete();
            }
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'original_transaction_id')) {
                $table->string('original_transaction_id')->nullable()->after('provider_subscription_id');
            }
            if (!Schema::hasColumn('subscriptions', 'raw_provider_event')) {
                $table->text('raw_provider_event')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('subscriptions', 'processed_at')) {
                $table->dateTime('processed_at')->nullable()->after('raw_provider_event');
            }
        });

        // Add unique constraint if not already present.
        // hasIndex was added in Laravel 11.x — fall back gracefully.
        $uniqueName = 'subscriptions_provider_subscription_unique';
        $indexExists = method_exists(Schema::class, 'hasIndex')
            ? Schema::hasIndex('subscriptions', $uniqueName)
            : false;

        if (!$indexExists) {
            try {
                Schema::table('subscriptions', function (Blueprint $table) use ($uniqueName) {
                    $table->unique('provider_subscription_id', $uniqueName);
                });
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'Duplicate')) {
                    // Another process already added it; safe to ignore
                } else {
                    throw $e;
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropUnique('subscriptions_provider_subscription_unique');
            $table->dropColumn(['original_transaction_id', 'raw_provider_event', 'processed_at']);
        });
    }
};
