import { IconStar } from './icons';
import { TestimonialItem } from './types';

interface Props {
    testimonials: TestimonialItem[];
}

export default function TestimonialsSection({ testimonials }: Props) {
    return (
        <section className="bg-zinc-50 py-16 sm:py-24">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="grid gap-6 sm:grid-cols-3">
                    {testimonials.map(({ quote, name, role }) => (
                        <blockquote key={name} className="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                            <div className="mb-4 flex">
                                {[...Array(5)].map((_, i) => (
                                    <IconStar key={i} className="h-4 w-4 text-orange-400" />
                                ))}
                            </div>
                            <p className="text-sm leading-relaxed text-zinc-600">"{quote}"</p>
                            <footer className="mt-4">
                                <p className="text-sm font-semibold text-zinc-900">{name}</p>
                                <p className="text-xs text-zinc-400">{role}</p>
                            </footer>
                        </blockquote>
                    ))}
                </div>
            </div>
        </section>
    );
}
