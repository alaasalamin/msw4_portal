<?php

namespace App\Providers;

use App\Models\Device;
use App\Models\Post;
use App\Models\Setting;
use App\Models\SitePage;
use App\Models\UserTypePermission;
use App\Observers\DeviceObserver;
use App\Observers\PostObserver;
use App\Observers\SitePageObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
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
        // Force HTTPS when running behind an SSL-terminating reverse proxy (Nginx)
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);

        Device::observe(DeviceObserver::class);
        Post::observe(PostObserver::class);
        SitePage::observe(SitePageObserver::class);

        $this->applyMailSettingsFromDatabase();

        Gate::define('viewPulse', function ($user) {
            return $user->id === 1 || $user->email === 'alaasalamin1996@gmail.com';
        });

        $this->registerTypePermissionGates();
    }

    private function registerTypePermissionGates(): void
    {
        try {
            foreach (UserTypePermission::definitions() as $type => $permissions) {
                foreach (array_keys($permissions) as $permission) {
                    Gate::define($permission, function ($user) use ($type, $permission) {
                        if (($user->type ?? null) !== $type) {
                            return true; // Gate only applies to the matching type
                        }
                        return UserTypePermission::check($type, $permission);
                    });
                }
            }
        } catch (\Throwable) {
            // Skip if table doesn't exist yet (fresh install)
        }
    }

    private function applyMailSettingsFromDatabase(): void
    {
        try {
            if (Setting::get('mail_host')) {
                config([
                    'mail.default'                 => Setting::get('mail_mailer', 'smtp'),
                    'mail.mailers.smtp.host'       => Setting::get('mail_host'),
                    'mail.mailers.smtp.port'       => Setting::get('mail_port', 587),
                    'mail.mailers.smtp.encryption' => Setting::get('mail_encryption', 'tls'),
                    'mail.mailers.smtp.username'   => Setting::get('mail_username'),
                    'mail.mailers.smtp.password'   => Setting::get('mail_password'),
                    'mail.from.address'            => Setting::get('mail_from_address', config('mail.from.address')),
                    'mail.from.name'               => Setting::get('mail_from_name', config('mail.from.name')),
                ]);
            }
        } catch (\Throwable) {
            // Silently skip if the settings table doesn't exist yet (e.g. during fresh migration)
        }
    }
}
