import { HomepageContent } from './types';

interface Props {
    process: HomepageContent['process'];
}

export default function HowItWorksSection({ process }: Props) {
    return (
        <section id="how-it-works" className="bg-white py-20 sm:py-28">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    <p className="text-sm font-semibold uppercase tracking-widest text-orange-600">{process.label}</p>
                    <h2 className="font-display mt-3 text-3xl font-normal text-zinc-900 sm:text-4xl">{process.title}</h2>
                </div>

                <div className="mt-16 grid gap-8 sm:grid-cols-3">
                    {process.steps.map(({ num, title, desc }, i) => (
                        <div key={num} className="relative">
                            {i < process.steps.length - 1 && (
                                <div className="absolute left-[calc(50%+3rem)] top-6 hidden h-px w-[calc(100%-6rem)] border-t border-dashed border-zinc-200 sm:block" />
                            )}
                            <div className="flex flex-col items-center text-center">
                                <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-900 font-mono text-sm font-medium text-white shadow">
                                    {num}
                                </div>
                                <h3 className="mt-4 font-semibold text-zinc-900">{title}</h3>
                                <p className="mt-2 text-sm leading-relaxed text-zinc-500">{desc}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
