import { HomepageContent, ServiceColor } from './types';
import { IconPhone, IconLaptop, IconTablet, IconShield, IconDatabase, IconClock } from './icons';

// Icon slot per position (order matches the 6 service cards)
const SLOT_ICONS = [IconPhone, IconLaptop, IconTablet, IconShield, IconDatabase, IconClock];

// Color cycling per position
const SLOT_COLORS: ServiceColor[] = ['sky', 'violet', 'emerald', 'amber', 'rose', 'sky'];

const colorMap: Record<ServiceColor, { bg: string; icon: string; border: string }> = {
    sky:     { bg: 'bg-orange-50',  icon: 'text-orange-600',  border: 'border-orange-100' },
    violet:  { bg: 'bg-orange-50',  icon: 'text-orange-600',  border: 'border-orange-100' },
    emerald: { bg: 'bg-emerald-50', icon: 'text-emerald-600', border: 'border-emerald-100' },
    amber:   { bg: 'bg-orange-50',  icon: 'text-orange-600',  border: 'border-orange-100' },
    rose:    { bg: 'bg-rose-50',    icon: 'text-rose-600',    border: 'border-rose-100' },
};

interface Props {
    services: HomepageContent['services'];
}

export default function ServicesSection({ services }: Props) {
    return (
        <section id="services" className="bg-zinc-50 py-20 sm:py-28">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    <p className="text-sm font-semibold uppercase tracking-widest text-orange-600">{services.label}</p>
                    <h2 className="font-display mt-3 text-3xl font-normal text-zinc-900 sm:text-4xl">{services.title}</h2>
                    <p className="mt-4 text-lg text-zinc-500">{services.subtitle}</p>
                </div>

                <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {services.items.map(({ title, desc }, i) => {
                        const Icon = SLOT_ICONS[i % SLOT_ICONS.length];
                        const c = colorMap[SLOT_COLORS[i % SLOT_COLORS.length]];
                        return (
                            <div
                                key={i}
                                className={`group rounded-2xl border ${c.border} ${c.bg} p-6 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md`}
                            >
                                <div className="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm">
                                    <Icon className={`h-5 w-5 ${c.icon}`} />
                                </div>
                                <h3 className="mb-2 font-semibold text-zinc-900">{title}</h3>
                                <p className="text-sm leading-relaxed text-zinc-500">{desc}</p>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}
