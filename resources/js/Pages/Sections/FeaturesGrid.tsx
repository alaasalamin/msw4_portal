// Icons cycle for feature cards
const ICONS = [
    // Phone
    (cls: string) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 15.75h3" /></svg>,
    // Laptop
    (cls: string) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3" /></svg>,
    // Shield
    (cls: string) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>,
    // Clock
    (cls: string) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>,
    // Database
    (cls: string) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>,
    // Check
    (cls: string) => <svg className={cls} fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>,
];

interface Item  { title: string; desc: string }
interface Props {
    label?: string;
    title: string;
    subtitle?: string;
    items: Item[];
    theme?: 'light' | 'dark';
}

export default function FeaturesGrid({ label, title, subtitle, items, theme = 'light' }: Props) {
    const isDark = theme === 'dark';

    return (
        <section className={`py-20 sm:py-28 ${isDark ? 'bg-zinc-900' : 'bg-zinc-50'}`}>
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    {label && <p className="text-sm font-semibold uppercase tracking-widest text-orange-500">{label}</p>}
                    <h2 className={`font-display mt-3 text-3xl font-normal sm:text-4xl ${isDark ? 'text-white' : 'text-zinc-900'}`}>{title}</h2>
                    {subtitle && <p className={`mt-4 text-lg ${isDark ? 'text-zinc-400' : 'text-zinc-500'}`}>{subtitle}</p>}
                </div>

                <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {items.map(({ title: t, desc }, i) => {
                        const Icon = ICONS[i % ICONS.length];
                        return (
                            <div
                                key={i}
                                className={`group rounded-2xl border p-6 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md ${
                                    isDark
                                        ? 'border-white/5 bg-white/5'
                                        : 'border-orange-100 bg-orange-50'
                                }`}
                            >
                                <div className="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm">
                                    {Icon('h-5 w-5 text-orange-600')}
                                </div>
                                <h3 className={`mb-2 font-semibold ${isDark ? 'text-white' : 'text-zinc-900'}`}>{t}</h3>
                                <p className={`text-sm leading-relaxed ${isDark ? 'text-zinc-400' : 'text-zinc-500'}`}>{desc}</p>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}
