import '../css/app.css';

const root = document.documentElement;
const toggle = document.querySelector('[data-menu-toggle]');
const menu = document.querySelector('[data-menu]');
const header = document.querySelector('[data-header]');
const progress = document.querySelector('[data-scroll-progress]');
const backToTop = document.querySelector('[data-back-to-top]');
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

toggle?.addEventListener('click', () => {
    const isOpen = menu.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(isOpen));
    document.body.classList.toggle('menu-open', isOpen);
});

menu?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        menu.classList.remove('open');
        toggle?.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('menu-open');
    });
});

const splitHeading = (heading) => {
    if (!heading || heading.dataset.split) return;

    const words = heading.textContent.trim().split(/\s+/);
    heading.dataset.split = 'true';
    heading.setAttribute('aria-label', heading.textContent.trim());
    heading.innerHTML = words.map((word, index) =>
        `<span class="word" aria-hidden="true" style="--word-index:${index}"><span>${word}</span></span>`
    ).join(' ');
};

document.querySelectorAll('.hero h1, .page-hero h1').forEach(splitHeading);

const revealSelectors = [
    '.split-heading > *', '.section-header > *', '.service-card', '.approach-grid > *',
    '.process-list li', '.values-grid > div', '.center-heading', '.team-card',
    '.purpose-grid article', '.services-list article', '.story-grid > *',
    '.empty-events > *', '.contact-grid > *', '.logo-row > div', '.cta-band-inner > *',
    '.executive-statement', '.executive-copy', '.metric-tile', '.premium-heading > *',
    '.capability-card', '.advisory-heading', '.expertise-tabs', '.advisory-panels',
    '.method-card', '.leadership-card',
    '.inner-hero-copy', '.inner-data-card', '.inner-principle-card', '.expertise-chip',
    '.learning-formats > article', '.contact-actions > a',
    '.insight-copy > *', '.insight-benefits > div', '.insight-panel-head',
    '.resource-teaser-card', '.guide-cover-card', '.guide-panel', '.guide-check-item',
    '.guide-brief-grid > article', '.guide-roadmap-grid > article', '.guide-diagnostic-summary',
    '.guide-deliverable-grid > div', '.guide-question-grid > article', '.guide-next-step',
];

document.querySelectorAll(revealSelectors.join(',')).forEach((element, index) => {
    element.classList.add('reveal-item');
    element.style.setProperty('--reveal-delay', `${(index % 4) * 90}ms`);
});

const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
    });
}, { threshold: 0.13, rootMargin: '0px 0px -45px' });

document.querySelectorAll('.reveal-item').forEach((element) => revealObserver.observe(element));

const animateNumber = (element) => {
    const match = element.textContent.trim().match(/([0-9]+)(.*)/);
    if (!match || element.dataset.counted) return;
    element.dataset.counted = 'true';

    const target = Number(match[1]);
    const suffix = match[2];
    const duration = 1350;
    const startedAt = performance.now();

    const tick = (now) => {
        const elapsed = Math.min((now - startedAt) / duration, 1);
        const eased = 1 - Math.pow(1 - elapsed, 4);
        element.textContent = `${Math.round(target * eased)}${suffix}`;
        if (elapsed < 1) requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);
};

const counterObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        animateNumber(entry.target);
        observer.unobserve(entry.target);
    });
}, { threshold: 0.65 });

document.querySelectorAll('.values-grid strong, .hero-proof strong').forEach((counter) => counterObserver.observe(counter));

const interactiveCards = document.querySelectorAll('.service-card, .team-card, .purpose-grid article, .capability-card, .method-card, .metric-tile, .services-list article, .inner-data-card, .inner-principle-card, .expertise-chip, .learning-formats > article, .insight-benefits > div, .resource-teaser-card, .guide-brief-grid > article, .guide-roadmap-grid > article, .guide-question-grid > article, .guide-check-item');

if (!reduceMotion && window.matchMedia('(pointer: fine)').matches) {
    interactiveCards.forEach((card) => {
        card.classList.add('interactive-card');
        card.addEventListener('pointermove', (event) => {
            const bounds = card.getBoundingClientRect();
            const x = event.clientX - bounds.left;
            const y = event.clientY - bounds.top;
            const rotateY = ((x / bounds.width) - .5) * 5;
            const rotateX = ((y / bounds.height) - .5) * -5;
            card.style.setProperty('--pointer-x', `${x}px`);
            card.style.setProperty('--pointer-y', `${y}px`);
            card.style.setProperty('--rotate-x', `${rotateX}deg`);
            card.style.setProperty('--rotate-y', `${rotateY}deg`);
        });
        card.addEventListener('pointerleave', () => {
            card.style.setProperty('--rotate-x', '0deg');
            card.style.setProperty('--rotate-y', '0deg');
        });
    });

    document.querySelectorAll('.button').forEach((button) => {
        button.classList.add('magnetic');
        button.addEventListener('pointermove', (event) => {
            const bounds = button.getBoundingClientRect();
            const x = (event.clientX - bounds.left - bounds.width / 2) * .14;
            const y = (event.clientY - bounds.top - bounds.height / 2) * .18;
            button.style.setProperty('--magnetic-x', `${x}px`);
            button.style.setProperty('--magnetic-y', `${y}px`);
        });
        button.addEventListener('pointerleave', () => {
            button.style.setProperty('--magnetic-x', '0px');
            button.style.setProperty('--magnetic-y', '0px');
        });
    });

    const glow = document.createElement('div');
    glow.className = 'cursor-glow';
    document.body.appendChild(glow);
    window.addEventListener('pointermove', (event) => {
        glow.style.setProperty('--cursor-x', `${event.clientX}px`);
        glow.style.setProperty('--cursor-y', `${event.clientY}px`);
    }, { passive: true });
}

const heroMedia = document.querySelector('.hero-media');
const framedImages = document.querySelectorAll('.image-frame img, .event-visual img');
let ticking = false;

const updateScrollEffects = () => {
    const scrollY = window.scrollY;
    const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
    const ratio = maxScroll > 0 ? scrollY / maxScroll : 0;

    progress?.style.setProperty('transform', `scaleX(${ratio})`);
    header?.classList.toggle('is-scrolled', scrollY > 60);
    backToTop?.classList.toggle('is-visible', scrollY > 650);

    if (!reduceMotion) {
        heroMedia?.style.setProperty('--parallax-y', `${Math.min(scrollY * .2, 130)}px`);
        framedImages.forEach((image) => {
            const bounds = image.parentElement.getBoundingClientRect();
            const offset = (window.innerHeight / 2 - (bounds.top + bounds.height / 2)) * .035;
            image.style.setProperty('--image-parallax', `${Math.max(-18, Math.min(18, offset))}px`);
        });
    }

    ticking = false;
};

window.addEventListener('scroll', () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(updateScrollEffects);
}, { passive: true });

backToTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' }));
updateScrollEffects();

document.querySelectorAll('[data-print-guide]').forEach((button) => {
    button.addEventListener('click', () => window.print());
});

const announcementModal = document.querySelector('[data-announcement-modal]');
if (announcementModal) {
    const storageKey = announcementModal.dataset.announcementKey;
    const showOnce = announcementModal.dataset.showOnce === '1';
    const closeButtons = announcementModal.querySelectorAll('[data-announcement-close]');
    const videos = announcementModal.querySelectorAll('video');

    const wasSeen = () => {
        if (!showOnce || !storageKey) return false;
        try {
            return window.sessionStorage.getItem(storageKey) === 'closed';
        } catch {
            return false;
        }
    };

    const markSeen = () => {
        if (!showOnce || !storageKey) return;
        try {
            window.sessionStorage.setItem(storageKey, 'closed');
        } catch {
            // Browsers may block storage in private contexts; closing should still work.
        }
    };

    const openAnnouncement = () => {
        if (wasSeen()) return;
        announcementModal.hidden = false;
        document.body.classList.add('announcement-modal-open');
    };

    const closeAnnouncement = () => {
        announcementModal.hidden = true;
        document.body.classList.remove('announcement-modal-open');
        markSeen();
        videos.forEach((video) => video.pause());
    };

    closeButtons.forEach((button) => button.addEventListener('click', closeAnnouncement));
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !announcementModal.hidden) {
            closeAnnouncement();
        }
    });
    window.setTimeout(openAnnouncement, 450);
}

document.querySelectorAll('[data-diagnostic]').forEach((diagnostic) => {
    const checks = [...diagnostic.querySelectorAll('[data-diagnostic-check]')];
    const countEl = diagnostic.querySelector('[data-diagnostic-count]');
    const meterEl = diagnostic.querySelector('[data-diagnostic-meter]');
    const titleEl = diagnostic.querySelector('[data-diagnostic-title]');
    const textEl = diagnostic.querySelector('[data-diagnostic-text]');
    const contactLink = diagnostic.querySelector('[data-diagnostic-contact]');
    const baseContactHref = contactLink?.getAttribute('href') || '';

    const getState = (checked, total) => {
        if (checked === 0) return 'empty';
        if (checked <= Math.max(1, Math.floor(total * .25))) return 'low';
        if (checked <= Math.max(2, Math.ceil(total * .5))) return 'medium';
        return 'high';
    };

    const updateDiagnostic = () => {
        const total = checks.length || 1;
        const checked = checks.filter((check) => check.checked).length;
        const state = getState(checked, total);
        const percent = Math.round((checked / total) * 100);

        diagnostic.classList.remove('is-empty', 'is-low', 'is-medium', 'is-high');
        diagnostic.classList.add(`is-${state}`);

        checks.forEach((check) => {
            check.closest('.guide-check-item')?.classList.toggle('is-checked', check.checked);
        });

        if (countEl) countEl.textContent = String(checked);
        if (meterEl) meterEl.style.width = `${percent}%`;
        if (titleEl) titleEl.textContent = diagnostic.dataset[`${state}Title`] || '';
        if (textEl) textEl.textContent = diagnostic.dataset[`${state}Text`] || '';

        if (contactLink && baseContactHref) {
            const contactUrl = new URL(baseContactHref, window.location.origin);
            if (checked > 0) {
                contactUrl.searchParams.set('diagnostico', `${checked}/${total} - ${diagnostic.dataset[`${state}Title`] || state}`);
            } else {
                contactUrl.searchParams.delete('diagnostico');
            }
            contactLink.href = contactUrl.pathname + contactUrl.search + contactUrl.hash;
        }
    };

    checks.forEach((check) => check.addEventListener('change', updateDiagnostic));
    updateDiagnostic();
});

const logoRow = document.querySelector('.logo-row');
if (logoRow && !reduceMotion) {
    const originals = [...logoRow.children];
    originals.forEach((item) => {
        const clone = item.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        logoRow.appendChild(clone);
    });
    logoRow.classList.add('logo-marquee');
}

const expertiseTabs = [...document.querySelectorAll('[data-expertise-tab]')];
const expertisePanels = [...document.querySelectorAll('[data-expertise-panel]')];
const advisoryArea = document.querySelector('.advisory-lab');
let activeExpertise = 0;
let expertiseTimer;

const showExpertise = (index) => {
    activeExpertise = (index + expertiseTabs.length) % expertiseTabs.length;
    const key = expertiseTabs[activeExpertise]?.dataset.expertiseTab;

    expertiseTabs.forEach((tab, tabIndex) => {
        const active = tabIndex === activeExpertise;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', String(active));
    });

    expertisePanels.forEach((panel) => {
        panel.classList.toggle('is-active', panel.dataset.expertisePanel === key);
    });
};

const startExpertiseRotation = () => {
    if (reduceMotion || expertiseTabs.length < 2) return;
    window.clearInterval(expertiseTimer);
    expertiseTimer = window.setInterval(() => showExpertise(activeExpertise + 1), 6500);
};

expertiseTabs.forEach((tab, index) => {
    tab.addEventListener('click', () => {
        showExpertise(index);
        startExpertiseRotation();
    });
});

advisoryArea?.addEventListener('mouseenter', () => window.clearInterval(expertiseTimer));
advisoryArea?.addEventListener('mouseleave', startExpertiseRotation);
startExpertiseRotation();

window.addEventListener('load', () => root.classList.add('page-loaded'));
