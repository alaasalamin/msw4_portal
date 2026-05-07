import { Head } from '@inertiajs/react';
import DynamicHeader    from './Welcome/Sections/DynamicHeader';
import DynamicHero      from './Welcome/Sections/DynamicHero';
import DynamicTextBlock from './Welcome/Sections/DynamicTextBlock';
import DynamicTeam      from './Welcome/Sections/DynamicTeam';
import DynamicBlogPosts from './Welcome/Sections/DynamicBlogPosts';
import DynamicMap       from './Welcome/Sections/DynamicMap';
import DynamicReviews   from './Welcome/Sections/DynamicReviews';
import DynamicForm      from './Welcome/Sections/DynamicForm';
import DynamicPricing   from './Welcome/Sections/DynamicPricing';
import DynamicFaq       from './Welcome/Sections/DynamicFaq';
import DynamicCta       from './Welcome/Sections/DynamicCta';
import DynamicStats     from './Welcome/Sections/DynamicStats';
import DynamicFooter    from './Welcome/Sections/DynamicFooter';

interface Section {
    id: string;
    type: 'header' | 'hero' | 'text' | 'team' | 'blog_posts' | 'map' | 'reviews' | 'form' | 'pricing' | 'faq' | 'cta' | 'stats' | 'footer';
    settings: Record<string, unknown>;
}

interface WelcomeProps {
    homepage?: { sections?: Section[] } & Record<string, unknown>;
}

function renderSection(section: Section) {
    switch (section.type) {
        case 'header': return <DynamicHeader    key={section.id} settings={section.settings as never} />;
        case 'hero':   return <DynamicHero      key={section.id} settings={section.settings as never} />;
        case 'text':   return <DynamicTextBlock key={section.id} settings={section.settings as never} />;
        case 'team':       return <DynamicTeam      key={section.id} settings={section.settings as never} />;
        case 'blog_posts': return <DynamicBlogPosts key={section.id} settings={section.settings as never} />;
        case 'map':        return <DynamicMap       key={section.id} settings={section.settings as never} />;
        case 'reviews':    return <DynamicReviews   key={section.id} settings={section.settings as never} />;
        case 'form':       return <DynamicForm      key={section.id} settings={section.settings as never} />;
        case 'pricing':    return <DynamicPricing   key={section.id} settings={section.settings as never} />;
        case 'faq':        return <DynamicFaq       key={section.id} settings={section.settings as never} />;
        case 'cta':        return <DynamicCta       key={section.id} settings={section.settings as never} />;
        case 'stats':      return <DynamicStats     key={section.id} settings={section.settings as never} />;
        case 'footer':     return <DynamicFooter    key={section.id} settings={section.settings as never} />;
        default:       return null;
    }
}

export default function Welcome({ homepage }: WelcomeProps) {
    const sections = homepage?.sections ?? [];

    if (sections.length === 0) {
        return (
            <>
                <Head title="Hello World" />
                <main
                    style={{
                        minHeight: '100vh',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        background: '#ffffff',
                        color: '#000000',
                        fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
                    }}
                >
                    <h1 style={{ fontSize: 'clamp(2.5rem, 8vw, 5rem)', fontWeight: 600, letterSpacing: '-0.03em', margin: 0 }}>
                        Hello World!
                    </h1>
                </main>
            </>
        );
    }

    return (
        <>
            <Head title="Home" />
            {sections.map(renderSection)}
        </>
    );
}
