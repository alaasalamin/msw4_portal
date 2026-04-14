import { Link } from '@inertiajs/react';
import { IconArrowRight } from './icons';

const PORTAL_META = {
    customer: { label: 'Kundenportal',     color: 'text-orange-400', badge: 'bg-orange-600/15 border-orange-500/20 text-orange-300' },
    partner:  { label: 'Partnerportal',    color: 'text-orange-400', badge: 'bg-orange-600/15 border-orange-500/20 text-orange-300' },
    employee: { label: 'Mitarbeiterportal',color: 'text-orange-400', badge: 'bg-orange-600/15 border-orange-500/20 text-orange-300' },
};

interface Props {
    name: string;
    portalLink: { href: string; label: string };
    portalType: 'customer' | 'partner' | 'employee';
}

export default function LoggedInCard({ name, portalLink, portalType }: Props) {
    const meta = PORTAL_META[portalType];
    const firstName = name.split(' ')[0];

    return (
        <div className="overflow-hidden rounded-2xl border border-white/10 bg-zinc-800/60 shadow-2xl backdrop-blur-xl">
            <div className="h-0.5 w-full bg-gradient-to-r from-orange-500 via-orange-400 to-transparent" />
            <div className="p-7">
                <div className="mb-6">
                    <p className="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-2">Willkommen zurück</p>
                    <h3 className="font-display text-2xl font-normal text-white">
                        Hey, <span className="text-orange-400">{firstName}</span> 👋
                    </h3>
                    <p className="mt-1 text-sm text-zinc-400">
                        Sie sind angemeldet im{' '}
                        <span className={meta.color}>{meta.label}</span>.
                    </p>
                </div>
                <div className={`mb-6 inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium ${meta.badge}`}>
                    <span className="h-1.5 w-1.5 rounded-full bg-orange-400" />
                    {name}
                </div>
                <Link
                    href={portalLink.href}
                    className="flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-500 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                >
                    {portalLink.label} öffnen
                    <IconArrowRight className="h-4 w-4" />
                </Link>
            </div>
        </div>
    );
}
