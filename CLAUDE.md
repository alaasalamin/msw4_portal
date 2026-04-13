# MSW4 Portal

## Stack
- Laravel 13 + Inertia.js + Vue + Tailwind
- PHP 8.4, Node, MySQL, Redis
- Laravel Reverb (WebSockets), Laravel Pulse, Livewire v3

## Server
- VPS: `217.160.255.189`
- SSH: `root@217.160.255.189`
- Web root: `/var/www/msw`
- Domain: `msw.bizo.technology`

## Database
- MySQL, database: `msw`, user: `alaeddin`
- Connect: `mysql -u alaeddin -pgenKudu911 msw`

## Deploy
- Push to `main` triggers GitHub Actions (`.github/workflows/deploy.yml`)
- Deploys via `appleboy/ssh-action` over SSH
- PHP binary: `php8.4`

## Services
- Queue worker: `systemctl [start|stop|restart] laravel-queue`
- Reverb WebSocket: `systemctl [start|stop|restart] laravel-reverb`
- Redis: `systemctl [start|stop|restart] redis-server`
