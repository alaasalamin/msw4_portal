interface Stat { value: string; label: string }
interface Props { items: Stat[] }

export default function StatsBanner({ items }: Props) {
    const count = items.length;
    const colClass = count <= 2 ? 'sm:grid-cols-2' : count === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-4';

    return (
        <section className="border-y border-zinc-200 bg-white">
            <div className={`mx-auto grid max-w-7xl grid-cols-2 gap-px bg-zinc-200 ${colClass}`}>
                {items.map(({ value, label }) => (
                    <div key={label} className="flex flex-col items-center justify-center bg-white px-6 py-8 text-center">
                        <span className="font-display text-3xl font-normal text-zinc-900">{value}</span>
                        <span className="mt-1 text-sm text-zinc-500">{label}</span>
                    </div>
                ))}
            </div>
        </section>
    );
}
