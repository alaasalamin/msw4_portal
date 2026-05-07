import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import DynamicHeader from './Welcome/Sections/DynamicHeader';
import DynamicFooter from './Welcome/Sections/DynamicFooter';

// Page-builder section components (Site Page editor blocks)
import PageHero      from './Sections/PageHero';
import StatsBanner   from './Sections/StatsBanner';
import FeaturesGrid  from './Sections/FeaturesGrid';
import ProcessSteps  from './Sections/ProcessSteps';
import Testimonials  from './Sections/Testimonials';
import CtaBanner     from './Sections/CtaBanner';
import TextBlock     from './Sections/TextBlock';
import FormSection   from './Sections/FormSection';
import Carousel      from './Sections/Carousel';

// Map Filament Builder block type → React component
const SECTION_MAP: Record<string, React.ComponentType<any>> = {
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

interface SitePageData {
    id: number;
    title: string;
    slug: string;
    meta_title: string | null;
    meta_description: string | null;
    status: string;
    sections: Array<{ type: string; data: Record<string, any> }> | null;
}

interface ThemeSection {
    id: string;
    type: string;
    settings: Record<string, unknown>;
}

type Props = PageProps<{
    page: SitePageData;
    homepage?: { sections?: ThemeSection[] } & Record<string, unknown>;
}>;

export default function DynamicPage({ page, homepage }: Props) {
    const themeSections = homepage?.sections ?? [];
    const headerSection = themeSections.find((s) => s.type === 'header');
    const footerSection = themeSections.find((s) => s.type === 'footer');

    const sections = page.sections ?? [];

    return (
        <>
            <Head title={page.meta_title || page.title} />

            {headerSection && <DynamicHeader settings={headerSection.settings as never} />}

            {sections.length === 0 ? (
                <div style={{
                    minHeight: '60vh',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    background: '#fafafa',
                    color: '#9ca3af',
                    fontSize: '0.875rem',
                    fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
                }}>
                    No sections added yet.
                </div>
            ) : (
                sections.map((section, i) => {
                    const Component = SECTION_MAP[section.type];
                    if (!Component) return null;
                    return <Component key={i} {...section.data} />;
                })
            )}

            {footerSection && <DynamicFooter settings={footerSection.settings as never} />}
        </>
    );
}
