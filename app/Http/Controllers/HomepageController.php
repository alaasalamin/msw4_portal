<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class HomepageController extends Controller
{
    /**
     * Returns all homepage content, merging saved settings with hardcoded defaults.
     * Called from the route and can be called from tests or seeders.
     */
    public static function content(): array
    {
        $j = fn(string $key, array $default) =>
            json_decode(Setting::get($key) ?? '', true) ?? $default;

        return [
            'hero' => [
                'badge'        => Setting::get('home_hero_badge',         'Forchheim, Bayern · Zertifizierter Reparaturbetrieb'),
                'title'        => Setting::get('home_hero_title',         'Handy Reparatur &amp; <span class="text-orange-400">Datenrettung</span>'),
                'subtitle'     => Setting::get('home_hero_subtitle',      'Moon.Repair ist dein professionelles Reparatur- und Datenrettungszentrum in Forchheim. Ob Displayschaden, Akkutausch oder verlorene Daten — wir reparieren schnell, zuverlässig und mit Garantie.'),
                'bullets'      => $j('home_hero_bullets', [
                    'Diagnose meist noch am selben Tag',
                    '90 Tage Garantie auf alle Reparaturen',
                    'Professionelle Datenrettung bei Wasserschäden & Defekten',
                    'Transparente Preise — keine versteckten Kosten',
                ]),
                'rating'       => Setting::get('home_hero_rating',        '4.7/5'),
                'repairsCount' => Setting::get('home_hero_repairs_count', 'über 13.810 Reparaturen'),
            ],

            'stats' => $j('home_stats', [
                ['value' => '13,810', 'label' => 'Devices Repaired'],
                ['value' => '320+',   'label' => 'B2B Partners'],
                ['value' => '98.6%',  'label' => 'Satisfaction Rate'],
                ['value' => '< 24h',  'label' => 'Avg. Turnaround'],
            ]),

            'services' => [
                'label'    => Setting::get('home_services_label',    'What we repair'),
                'title'    => Setting::get('home_services_title',    'Every device, every problem'),
                'subtitle' => Setting::get('home_services_subtitle', 'Our certified technicians handle everything from cracked screens to complex motherboard issues.'),
                'items'    => $j('home_services_items', [
                    ['title' => 'Smartphone Repair',  'desc' => 'Screen replacements, battery swaps, charging port fixes, and water damage recovery for all major brands.'],
                    ['title' => 'Laptop & PC Repair', 'desc' => 'Keyboard, display, motherboard diagnostics, SSD upgrades, and OS recovery for Windows and macOS.'],
                    ['title' => 'Tablet Repair',      'desc' => 'iPad and Android tablet glass, digitizer, and battery replacement with original components.'],
                    ['title' => 'Warranty Service',   'desc' => 'Authorised warranty repairs and post-warranty service with certified parts and documented diagnostics.'],
                    ['title' => 'Data Recovery',      'desc' => 'Professional data extraction from damaged drives, broken phones, and failed storage media.'],
                    ['title' => 'Express Turnaround', 'desc' => 'Same-day diagnosis and priority repair lanes for business customers and urgent individual requests.'],
                ]),
            ],

            'process' => [
                'label' => Setting::get('home_process_label', 'The process'),
                'title' => Setting::get('home_process_title', 'Simple from start to finish'),
                'steps' => $j('home_steps', [
                    ['num' => '01', 'title' => 'Submit Your Device', 'desc' => 'Drop off in-store, or your B2B partner ships the device through our portal.'],
                    ['num' => '02', 'title' => 'Diagnosis & Quote',  'desc' => 'Our certified technicians diagnose the issue and send you a transparent repair quote.'],
                    ['num' => '03', 'title' => 'Repair & Return',    'desc' => 'Approved repairs are completed with genuine parts and shipped back or ready for collection.'],
                ]),
            ],

            'partners' => [
                'label'    => Setting::get('home_partners_label',    'B2B Partner Programme'),
                'title'    => Setting::get('home_partners_title',    'Scale your repair operations with a trusted partner'),
                'subtitle' => Setting::get('home_partners_subtitle', 'Retail chains, insurers, and telecom operators rely on Moon.Repair to handle high-volume device repairs with predictable SLAs and complete visibility.'),
                'benefits' => $j('home_partners_benefits', [
                    'Bulk repair submissions via dedicated portal',
                    'Priority SLA with guaranteed turnaround times',
                    'Real-time device tracking per shipment',
                    'Consolidated invoicing and reporting',
                    'Dedicated account manager',
                    'API integration available',
                ]),
                'features' => $j('home_partners_features', [
                    ['title' => 'Bulk Submissions', 'desc' => 'Upload device lists via CSV or API integration.'],
                    ['title' => 'Live Tracking',    'desc' => 'Real-time status updates per device and shipment.'],
                    ['title' => 'SLA Dashboard',    'desc' => 'Monitor repair SLAs and escalate instantly.'],
                    ['title' => 'Auto Invoicing',   'desc' => 'Consolidated monthly invoices with full line items.'],
                ]),
                'ctaEmail' => Setting::get('home_partners_cta_email', 'partners@moon.repair'),
            ],

            'testimonials' => $j('home_testimonials', [
                ['quote' => 'Moon.Repair turned our device repair backlog from weeks into days. The partner portal is genuinely excellent.', 'name' => 'Sarah M.',  'role' => 'Operations Lead, TelecomPlus'],
                ['quote' => 'Dropped my phone in the morning, had a diagnosis by noon and it was ready by closing time. Incredible.',        'name' => 'Ahmed K.', 'role' => 'Individual Customer'],
                ['quote' => 'The real-time tracking and consolidated invoicing save us hours every month. Highly recommended.',              'name' => 'Lisa T.',  'role' => 'Procurement Manager, RetailChain AG'],
            ]),

            'footer' => [
                'tagline'       => Setting::get('home_footer_tagline',        'Professional device repair at moon.repair — trusted by individuals and businesses. Certified technicians, genuine parts.'),
                'emailHello'    => Setting::get('home_footer_email_hello',    'hello@moon.repair'),
                'emailPartners' => Setting::get('home_footer_email_partners', 'partners@moon.repair'),
            ],
        ];
    }
}
