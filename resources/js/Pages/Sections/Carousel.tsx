import { useEffect, useRef, useState } from 'react';

interface Slide {
    image: string;
    caption?: string;
    link_url?: string;
    link_label?: string;
}

interface Props {
    heading?: string;
    slides?: Slide[];
    interval?: string | number;
    theme?: 'dark' | 'light';
}

const IcoChevronLeft = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
    </svg>
);

const IcoChevronRight = () => (
    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor">
        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
    </svg>
);

export default function Carousel({ heading, slides = [], interval = 5000, theme = 'dark' }: Props) {
    const [current, setCurrent] = useState(0);
    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);
    const ms = typeof interval === 'string' ? parseInt(interval, 10) : interval;

    const isDark = theme === 'dark';

    const go = (idx: number) => {
        setCurrent((idx + slides.length) % slides.length);
    };

    const next = () => go(current + 1);
    const prev = () => go(current - 1);

    const resetTimer = () => {
        if (timerRef.current) clearInterval(timerRef.current);
        if (slides.length > 1) {
            timerRef.current = setInterval(() => {
                setCurrent(c => (c + 1) % slides.length);
            }, ms);
        }
    };

    useEffect(() => {
        resetTimer();
        return () => { if (timerRef.current) clearInterval(timerRef.current); };
    }, [slides.length, ms]);

    if (slides.length === 0) return null;

    return (
        <section className={`py-16 ${isDark ? 'bg-zinc-900' : 'bg-white'}`}>
            <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

                {heading && (
                    <h2 className={`mb-10 text-center text-3xl font-bold tracking-tight ${isDark ? 'text-white' : 'text-zinc-900'}`}>
                        {heading}
                    </h2>
                )}

                <div className="relative overflow-hidden rounded-2xl shadow-2xl">
                    {/* Slides */}
                    <div
                        className="flex transition-transform duration-500 ease-in-out"
                        style={{ transform: `translateX(-${current * 100}%)` }}
                    >
                        {slides.map((slide, i) => (
                            <div key={i} className="relative min-w-full">
                                <img
                                    src={`/storage/${slide.image}`}
                                    alt={slide.caption ?? `Slide ${i + 1}`}
                                    className="h-[420px] w-full object-cover sm:h-[520px]"
                                    loading={i === 0 ? 'eager' : 'lazy'}
                                />

                                {/* Gradient overlay */}
                                <div className="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent" />

                                {/* Caption + CTA */}
                                {(slide.caption || slide.link_url) && (
                                    <div className="absolute bottom-0 left-0 right-0 p-6 sm:p-10">
                                        {slide.caption && (
                                            <p className="mb-3 text-lg font-semibold text-white drop-shadow sm:text-2xl">
                                                {slide.caption}
                                            </p>
                                        )}
                                        {slide.link_url && (
                                            <a
                                                href={slide.link_url}
                                                className="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white shadow transition-colors duration-200 hover:bg-orange-600"
                                            >
                                                {slide.link_label || 'Learn more'}
                                            </a>
                                        )}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>

                    {/* Prev / Next arrows */}
                    {slides.length > 1 && (
                        <>
                            <button
                                onClick={() => { prev(); resetTimer(); }}
                                aria-label="Previous slide"
                                className="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-black/40 p-2.5 text-white backdrop-blur-sm transition hover:bg-black/60"
                            >
                                <IcoChevronLeft />
                            </button>
                            <button
                                onClick={() => { next(); resetTimer(); }}
                                aria-label="Next slide"
                                className="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-black/40 p-2.5 text-white backdrop-blur-sm transition hover:bg-black/60"
                            >
                                <IcoChevronRight />
                            </button>
                        </>
                    )}

                    {/* Dot indicators */}
                    {slides.length > 1 && (
                        <div className="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
                            {slides.map((_, i) => (
                                <button
                                    key={i}
                                    onClick={() => { go(i); resetTimer(); }}
                                    aria-label={`Go to slide ${i + 1}`}
                                    className={`h-2 rounded-full transition-all duration-300 ${
                                        i === current
                                            ? 'w-6 bg-orange-500'
                                            : 'w-2 bg-white/60 hover:bg-white/90'
                                    }`}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
}
