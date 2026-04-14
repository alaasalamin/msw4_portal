interface Step  { num: string; title: string; desc: string }
interface Props { label?: string; title: string; steps: Step[] }

export default function ProcessSteps({ label, title, steps }: Props) {
    return (
        <section className="bg-white py-20 sm:py-28">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    {label && <p className="text-sm font-semibold uppercase tracking-widest text-orange-600">{label}</p>}
                    <h2 className="font-display mt-3 text-3xl font-normal text-zinc-900 sm:text-4xl">{title}</h2>
                </div>

                <div className={`mt-16 grid gap-8 ${steps.length === 2 ? 'sm:grid-cols-2' : steps.length === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-4'}`}>
                    {steps.map(({ num, title: t, desc }, i) => (
                        <div key={num} className="relative">
                            {i < steps.length - 1 && (
                                <div className="absolute left-[calc(50%+3rem)] top-6 hidden h-px w-[calc(100%-6rem)] border-t border-dashed border-zinc-200 sm:block" />
                            )}
                            <div className="flex flex-col items-center text-center">
                                <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-900 font-mono text-sm font-medium text-white shadow">
                                    {num}
                                </div>
                                <h3 className="mt-4 font-semibold text-zinc-900">{t}</h3>
                                <p className="mt-2 text-sm leading-relaxed text-zinc-500">{desc}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
