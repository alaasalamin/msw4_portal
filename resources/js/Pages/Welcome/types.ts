export type IconProps = { className?: string };
export type ServiceColor = 'sky' | 'violet' | 'emerald' | 'amber' | 'rose';

export interface StatItem      { value: string; label: string }
export interface ServiceItem   { title: string; desc: string }
export interface StepItem      { num: string; title: string; desc: string }
export interface FeatureItem   { title: string; desc: string }
export interface TestimonialItem { quote: string; name: string; role: string }

export interface HomepageContent {
    hero: {
        badge: string;
        title: string;
        subtitle: string;
        bullets: string[];
        rating: string;
        repairsCount: string;
    };
    stats: StatItem[];
    services: {
        label: string;
        title: string;
        subtitle: string;
        items: ServiceItem[];
    };
    process: {
        label: string;
        title: string;
        steps: StepItem[];
    };
    partners: {
        label: string;
        title: string;
        subtitle: string;
        benefits: string[];
        features: FeatureItem[];
        ctaEmail: string;
    };
    testimonials: TestimonialItem[];
    footer: {
        tagline: string;
        emailHello: string;
        emailPartners: string;
    };
}
