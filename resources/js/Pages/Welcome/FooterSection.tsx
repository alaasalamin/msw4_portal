import { IconArrowRight, IconWrench } from './icons';
import { HomepageContent } from './types';

interface Props {
    footer: HomepageContent['footer'];
}

export default function FooterSection({ footer }: Props) {
    return (
        <footer className="border-t border-zinc-800 bg-zinc-900">
            <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div className="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <div className="flex items-center gap-2.5">
                            <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-600">
                                <IconWrench className="h-4 w-4 text-white" />
                            </span>
                            <span className="font-display text-xl font-normal text-white">Moon<span className="text-orange-400">.Repair</span></span>
                        </div>
                        <p className="mt-4 text-sm leading-relaxed text-zinc-400">{footer.tagline}</p>
                    </div>

                    <div>
                        <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Services</h3>
                        <ul className="space-y-2 text-sm text-zinc-500">
                            {['Smartphone Repair', 'Laptop Repair', 'Tablet Repair', 'Data Recovery', 'Warranty Service'].map((s) => (
                                <li key={s}><a href="#services" className="transition hover:text-zinc-300">{s}</a></li>
                            ))}
                        </ul>
                    </div>

                    <div>
                        <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Company</h3>
                        <ul className="space-y-2 text-sm text-zinc-500">
                            {['About Us', 'Partner Programme', 'Careers', 'Privacy Policy', 'Terms of Service'].map((s) => (
                                <li key={s}><a href="#" className="transition hover:text-zinc-300">{s}</a></li>
                            ))}
                        </ul>
                    </div>

                    <div>
                        <h3 className="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400">Contact</h3>
                        <ul className="space-y-2 text-sm text-zinc-500">
                            <li><a href={`mailto:${footer.emailHello}`}    className="transition hover:text-zinc-300">{footer.emailHello}</a></li>
                            <li><a href={`mailto:${footer.emailPartners}`} className="transition hover:text-zinc-300">{footer.emailPartners}</a></li>
                        </ul>
                        <div className="mt-6">
                            <a
                                href="#login"
                                className="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                            >
                                Sign in
                                <IconArrowRight className="h-3.5 w-3.5" />
                            </a>
                        </div>
                    </div>
                </div>

                <div className="mt-10 flex flex-col items-center justify-between gap-4 border-t border-zinc-800 pt-8 sm:flex-row">
                    <p className="text-xs text-zinc-500">© {new Date().getFullYear()} Moon.Repair · moon.repair · All rights reserved.</p>
                    <p className="text-xs text-zinc-600">Built with Laravel · Inertia.js · React</p>
                </div>
            </div>
        </footer>
    );
}
