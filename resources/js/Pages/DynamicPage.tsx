import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';

// Theme Builder dynamic components (same set used by the homepage)
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
import DynamicSteps     from './Welcome/Sections/DynamicSteps';
import DynamicGallery   from './Welcome/Sections/DynamicGallery';
import DynamicTable     from './Welcome/Sections/DynamicTable';
import DynamicFooter    from './Welcome/Sections/DynamicFooter';

// Legacy block-editor components (kept for older Site Pages that still use
// the /admin/site-pages block editor).
import PageHero      from './Sections/PageHero';
import StatsBanner   from './Sections/StatsBanner';
import FeaturesGrid  from './Sections/FeaturesGrid';
import ProcessSteps  from './Sections/ProcessSteps';
import Testimonials  from './Sections/Testimonials';
import CtaBanner     from './Sections/CtaBanner';
import TextBlock     from './Sections/TextBlock';
import FormSection   from './Sections/FormSection';
import Carousel      from './Sections/Carousel';

const LEGACY_MAP: Record<string, React.ComponentType<any>> = {
    page_hero:      PageHero,
    stats_bar:      StatsBanner,
    features_grid:  FeaturesGrid,
    process_steps:  ProcessSteps,
    testimonials:   Testimonials,
    cta_banner:     CtaBanner,
    text_block:     TextBlock,
    form_block:     FormSection,
    carousel:       Carousel,
};

interface ThemeSection {
    id: string;
    type: string;
    settings: Record<string, unknown>;
}

interface SitePageData {
    id: number;
    title: string;
    slug: string;
    meta_title: string | null;
    meta_description: string | null;
    status: string;
    sections: Array<{ type: string; data: Record<string, any> }> | null;
    theme_sections: ThemeSection[] | null;
}

type Props = PageProps<{
    page: SitePageData;
    homepage?: { sections?: ThemeSection[] } & Record<string, unknown>;
}>;

function renderTheme(section: ThemeSection) {
    switch (section.type) {
        case 'header':     return <DynamicHeader    key={section.id} settings={section.settings as never} />;
        case 'hero':       return <DynamicHero      key={section.id} settings={section.settings as never} />;
        case 'text':       return <DynamicTextBlock key={section.id} settings={section.settings as never} />;
        case 'team':       return <DynamicTeam      key={section.id} settings={section.settings as never} />;
        case 'blog_posts': return <DynamicBlogPosts key={section.id} settings={section.settings as never} />;
        case 'map':        return <DynamicMap       key={section.id} settings={section.settings as never} />;
        case 'reviews':    return <DynamicReviews   key={section.id} settings={section.settings as never} />;
        case 'form':       return <DynamicForm      key={section.id} settings={section.settings as never} />;
        case 'pricing':    return <DynamicPricing   key={section.id} settings={section.settings as never} />;
        case 'faq':        return <DynamicFaq       key={section.id} settings={section.settings as never} />;
        case 'cta':        return <DynamicCta       key={section.id} settings={section.settings as never} />;
        case 'stats':      return <DynamicStats     key={section.id} settings={section.settings as never} />;
        case 'steps':      return <DynamicSteps     key={section.id} settings={section.settings as never} />;
        case 'gallery':    return <DynamicGallery   key={section.id} settings={section.settings as never} />;
        case 'table':      return <DynamicTable     key={section.id} settings={section.settings as never} />;
        case 'footer':     return <DynamicFooter    key={section.id} settings={section.settings as never} />;
        default:           return null;
    }
}

export default function DynamicPage({ page, homepage }: Props) {
    const themeSections   = homepage?.sections ?? [];
    const headerSection   = themeSections.find((s) => s.type === 'header');
    const footerSection   = themeSections.find((s) => s.type === 'footer');

    // Pages share the homepage's header + footer — strip any inadvertently
    // added at the page level so they never render duplicated in the middle.
    const pageThemeSections = (page.theme_sections ?? []).filter(
        (s) => s.type !== 'header' && s.type !== 'footer',
    );
    const legacySections    = page.sections ?? [];

    return (
        <>
            <Head title={page.meta_title || page.title} />

            {headerSection && <DynamicHeader settings={headerSection.settings as never} />}

            {pageThemeSections.length > 0 ? (
                /* Page was built with the Theme Builder. */
                pageThemeSections.map(renderTheme)
            ) : legacySections.length > 0 ? (
                /* Legacy block-editor page from /admin/site-pages. */
                legacySections.map((section, i) => {
                    const Component = LEGACY_MAP[section.type];
                    if (!Component) return null;
                    return <Component key={i} {...section.data} />;
                })
            ) : (
                <main style={{
                    minHeight: '100vh',
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: 'center',
                    justifyContent: 'center',
                    gap: 8,
                    padding: '0 16px',
                    background: '#ffffff',
                    color: '#000000',
                    fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
                    textAlign: 'center',
                }}>
                    <h1 style={{
                        fontSize: 'clamp(2.5rem, 8vw, 5rem)',
                        fontWeight: 600,
                        letterSpacing: '-0.03em',
                        margin: 0,
                        lineHeight: 1.1,
                    }}>
                        Hello World!
                    </h1>
                    <p style={{
                        margin: 0,
                        fontSize: 'clamp(0.9375rem, 1.4vw, 1.0625rem)',
                        color: '#71717a',
                        fontWeight: 500,
                        letterSpacing: '0.01em',
                    }}>
                        bizo
                    </p>
                </main>
            )}

            {footerSection && <DynamicFooter settings={footerSection.settings as never} />}
        </>
    );
}
