<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Comment::observe(\App\Observers\CommentObserver::class);

        // Apply Mail Settings from Database
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('global_settings')) {
                $settings = \App\Models\GlobalSetting::first();
                if ($settings) {
                    \Illuminate\Support\Facades\Log::debug('Applying Mail Settings from Database: ' . $settings->mail_mailer);
                    config([
                        'mail.mailers.smtp.host' => $settings->mail_host ?? config('mail.mailers.smtp.host'),
                        'mail.mailers.smtp.port' => $settings->mail_port ?? config('mail.mailers.smtp.port'),
                        'mail.mailers.smtp.username' => $settings->mail_username ?? config('mail.mailers.smtp.username'),
                        'mail.mailers.smtp.password' => $settings->mail_password ?? config('mail.mailers.smtp.password'),
                        'mail.mailers.smtp.encryption' => $settings->mail_encryption ?? config('mail.mailers.smtp.encryption'),
                        'mail.from.address' => $settings->mail_from_address ?? config('mail.from.address'),
                        'mail.from.name' => $settings->mail_from_name ?? config('mail.from.name'),
                        'mail.default' => $settings->mail_mailer ?? config('mail.default'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silence errors during migration or if DB is not ready
        }
    }
}
