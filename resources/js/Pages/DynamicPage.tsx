import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import { HomepageContent } from './Welcome/types';
import Navbar        from './Welcome/Navbar';
import FooterSection from './Welcome/FooterSection';

// Section components
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

type Props = PageProps<{
    page: SitePageData;
    homepage: HomepageContent;
}>;

export default function DynamicPage({ auth, page, homepage }: Props) {
    const portalLink = auth?.customer
        ? { href: '/customer/dashboard', label: 'Kundenbereich' }
        : auth?.partner
        ? { href: '/partner/dashboard', label: 'Partnerbereich' }
        : auth?.employee
        ? { href: '/employee/dashboard', label: 'Mitarbeiterbereich' }
        : null;

    const sections = page.sections ?? [];

    return (
        <>
            <Head title={page.meta_title || page.title} />

            <Navbar portalLink={portalLink} canLogin={true} />

            {/* Push content below fixed navbar */}
            <div className="pt-16">
                {sections.length === 0 ? (
                    <div className="flex min-h-[60vh] items-center justify-center bg-zinc-50">
                        <p className="text-sm text-zinc-400">No sections added yet.</p>
                    </div>
                ) : (
                    sections.map((section, i) => {
                        const Component = SECTION_MAP[section.type];
                        if (!Component) return null;
                        return <Component key={i} {...section.data} />;
                    })
                )}
            </div>

            <FooterSection footer={homepage.footer} />
        </>
    );
}
