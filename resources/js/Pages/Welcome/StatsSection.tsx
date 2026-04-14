import { StatItem } from './types';

interface Props {
    stats: StatItem[];
}

export default function StatsSection({ stats }: Props) {
    return (
        <section className="border-y border-zinc-200 bg-white">
            <div className="mx-auto grid max-w-7xl grid-cols-2 gap-px bg-zinc-200 sm:grid-cols-4">
                {stats.map(({ value, label }) => (
                    <div key={label} className="flex flex-col items-center justify-center bg-white px-6 py-8 text-center">
                        <span className="font-display text-3xl font-normal text-zinc-900">{value}</span>
                        <span className="mt-1 text-sm text-zinc-500">{label}</span>
                    </div>
                ))}
            </div>
        </section>
    );
}
