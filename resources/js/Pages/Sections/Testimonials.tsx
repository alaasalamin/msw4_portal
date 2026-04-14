interface Item  { quote: string; name: string; role: string }
interface Props { items: Item[] }

const Star = () => (
    <svg className="h-4 w-4 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
        <path fillRule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clipRule="evenodd" />
    </svg>
);

export default function Testimonials({ items }: Props) {
    const colClass = items.length <= 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-3';

    return (
        <section className="bg-zinc-50 py-16 sm:py-24">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className={`grid gap-6 ${colClass}`}>
                    {items.map(({ quote, name, role }) => (
                        <blockquote key={name} className="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                            <div className="mb-4 flex gap-0.5">
                                {[...Array(5)].map((_, i) => <Star key={i} />)}
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
