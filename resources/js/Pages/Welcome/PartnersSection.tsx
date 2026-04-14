import { IconArrowRight, IconBuilding, IconCheck } from './icons';
import { HomepageContent } from './types';

interface Props {
    partners: HomepageContent['partners'];
}

export default function PartnersSection({ partners }: Props) {
    return (
        <section id="partners" className="relative overflow-hidden bg-zinc-900 py-20 sm:py-28">
            <div className="pointer-events-none absolute -top-32 right-0 h-[500px] w-[500px] rounded-full bg-orange-600/10 blur-3xl" />

            <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="grid items-center gap-12 lg:grid-cols-2">
                    <div>
                        <span className="inline-flex items-center gap-2 rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-xs font-medium text-orange-300">
                            <IconBuilding className="h-3.5 w-3.5" />
                            {partners.label}
                        </span>
                        <h2 className="font-display mt-4 text-3xl font-normal text-white sm:text-4xl">{partners.title}</h2>
                        <p className="mt-4 text-lg text-zinc-400">{partners.subtitle}</p>

                        <ul className="mt-8 grid gap-3 sm:grid-cols-2">
                            {partners.benefits.map((benefit) => (
                                <li key={benefit} className="flex items-start gap-2.5 text-sm text-zinc-300">
                                    <span className="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-orange-600/30">
                                        <IconCheck className="h-2.5 w-2.5 text-orange-400" />
                                    </span>
                                    {benefit}
                                </li>
                            ))}
                        </ul>

                        <div className="mt-10 flex flex-wrap gap-3">
                            <a
                                href="#login"
                                className="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-orange-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-500 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                            >
                                Access Partner Portal
                                <IconArrowRight className="h-4 w-4" />
                            </a>
                            <a
                                href={`mailto:${partners.ctaEmail}`}
                                className="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-white/10 px-6 py-3 text-sm font-medium text-zinc-300 transition hover:border-white/20 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                            >
                                Contact Sales
                            </a>
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        {partners.features.map(({ title, desc }) => (
                            <div key={title} className="rounded-xl border border-white/5 bg-white/5 p-5">
                                <h3 className="mb-1 text-sm font-semibold text-white">{title}</h3>
                                <p className="text-xs leading-relaxed text-zinc-400">{desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
