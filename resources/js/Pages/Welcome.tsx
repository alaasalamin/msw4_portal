import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import { HomepageContent } from './Welcome/types';
import Navbar           from './Welcome/Navbar';
import HeroSection      from './Welcome/HeroSection';
import StatsSection     from './Welcome/StatsSection';
import ServicesSection  from './Welcome/ServicesSection';
import HowItWorksSection from './Welcome/HowItWorksSection';
import PartnersSection  from './Welcome/PartnersSection';
import TestimonialsSection from './Welcome/TestimonialsSection';
import FooterSection    from './Welcome/FooterSection';

type WelcomeProps = PageProps<{
    canLogin: boolean;
    canRegister: boolean;
    canResetPassword: boolean;
    homepage: HomepageContent;
}>;

export default function Welcome({ auth, canLogin, canRegister, canResetPassword, homepage }: WelcomeProps) {
    const portalLink = auth?.customer
        ? { href: '/customer/dashboard', label: 'Kundenbereich' }
        : auth?.partner
        ? { href: '/partner/dashboard', label: 'Partnerbereich' }
        : auth?.employee
        ? { href: '/employee/dashboard', label: 'Mitarbeiterbereich' }
        : null;

    return (
        <>
            <Head title="Moon.Repair — Handy Reparatur & Datenrettung in Forchheim" />

            <Navbar portalLink={portalLink} canLogin={canLogin} />

            <HeroSection
                hero={homepage.hero}
                canRegister={canRegister}
                canResetPassword={canResetPassword}
                portalLink={portalLink}
                auth={auth}
            />

            <StatsSection stats={homepage.stats} />

            <ServicesSection services={homepage.services} />

            <HowItWorksSection process={homepage.process} />

            <PartnersSection partners={homepage.partners} />

            <TestimonialsSection testimonials={homepage.testimonials} />

            <FooterSection footer={homepage.footer} />
        </>
    );
}
