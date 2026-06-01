import { animate, stagger, inView, scroll } from "motion";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

// Helper to safe-init
const safeInit = () => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initShowroomAnimations();
            initStorytelling();
        });
    } else {
        initShowroomAnimations();
        initStorytelling();
    }
};

document.addEventListener("livewire:navigated", safeInit);
document.addEventListener("DOMContentLoaded", safeInit);

// Livewire 3+ Hook
document.addEventListener("livewire:init", () => {
    Livewire.hook('commit.reveal', () => {
        queueMicrotask(() => {
            initShowroomAnimations();
            initStorytelling();
        });
    });
});

let parallaxRaf;

function initStorytelling() {
    const section = document.querySelector(".storytelling-section");
    if (!section) return;

    // Clean up previous animations/events
    ScrollTrigger.getAll().forEach(st => st.kill());
    if (parallaxRaf) cancelAnimationFrame(parallaxRaf);

    const fill = document.querySelector(".storytelling-progress-fill");
    const cue = document.querySelector(".storytelling-cue");

    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: section,
            start: "top top",
            end: "bottom bottom",
            scrub: 1.2,
            onUpdate: (self) => {
                if (fill) fill.style.width = `${self.progress * 100}%`;
                if (cue) cue.style.opacity = self.progress < 0.05 ? "1" : "0";
            }
        }
    });

    // Phase 0 -> 0.12: Intro text
    tl.to(".storytelling-eyebrow", { opacity: 1, y: 0, duration: 0.4 }, 0)
      .to(".storytelling-subtitle", { opacity: 1, y: 0, duration: 0.4 }, 0.1)
      .to(".storytelling-h1", { opacity: 1, scale: 1, duration: 0.6 }, 0.05);

    // Phase 0.15 -> 0.45: Background reveal + zoom
    tl.to(".storytelling-bg", { opacity: 1, duration: 0.4 }, 0.15)
      .to(".storytelling-bg", { scale: 1.05, duration: 0.6 }, 0.20);

    // Phase 0.45 -> 0.6: Title scale down
    tl.to(".storytelling-h1", { scale: 0.92, duration: 0.25 }, 0.45);

    // Phase 0.58 -> 0.72: Fade out title, deeper BG zoom
    tl.to(".storytelling-title", { opacity: 0, y: -20, duration: 0.2 }, 0.58)
      .to(".storytelling-bg", { scale: 1.10, duration: 0.3 }, 0.60)
      .to(".storytelling-bg", { opacity: 0.45, duration: 0.2 }, 0.65);

    // Phase 0.72 -> 1.0: Specs reveal
    tl.to(".storytelling-specs", { opacity: 1, duration: 0.15 }, 0.72)
      .to(".storytelling-card", { 
          opacity: 1, 
          y: 0, 
          duration: 0.25, 
          stagger: 0.06,
          ease: "power3.out"
      }, 0.76);

    // Mouse parallax logic
    let mx = 0, my = 0, currX = 0, currY = 0;
    const onMouseMove = (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        mx = (e.clientX - cx) / cx;
        my = (e.clientY - cy) / cy;
    };
    document.addEventListener("mousemove", onMouseMove);

    const animateParallax = () => {
        const st = tl.scrollTrigger;
        if (st && st.progress > 0.05 && st.progress < 0.8) {
            currX += (mx * 15 - currX) * 0.05;
            currY += (my * 10 - currY) * 0.05;
            gsap.set(".storytelling-bg", {
                xPercent: currX * 0.5,
                yPercent: currY * 0.5,
            });
        }
        parallaxRaf = requestAnimationFrame(animateParallax);
    };
    animateParallax();
}

function initShowroomAnimations() {
    // 1. Hero Reveal Animation (Cinema Preset)
    const heroSection = document.querySelector(".cinema-hero");
    if (heroSection && heroSection.getAttribute('data-animated') !== 'true') {
        const heroTl = gsap.timeline({ defaults: { ease: "power4.out" } });
        
        heroTl.fromTo(".cinema-hero-eyebrow", 
            { opacity: 0, y: 20 }, 
            { opacity: 1, y: 0, duration: 1 }, 0.2
        ).fromTo(".cinema-hero-h1", 
            { opacity: 0, y: 40, scale: 1.05 }, 
            { opacity: 1, y: 0, scale: 1, duration: 1.2 }, 0.4
        ).fromTo(".cinema-hero-content", 
            { opacity: 0, x: -20 }, 
            { opacity: 1, x: 0, duration: 1 }, 0.8
        ).fromTo(".cinema-hero-btn", 
            { opacity: 0, y: 20 }, 
            { opacity: 1, y: 0, duration: 0.8 }, 1
        );

        heroSection.setAttribute('data-animated', 'true');
    }

    // 2. Staggered Entry for Showroom Cards (Scroll-triggered)
    const items = document.querySelectorAll(".showroom-item");
    items.forEach((item, index) => {
        if (item.getAttribute('data-scroll-init') === 'true') return;

        inView(item, ({ target }) => {
            animate(
                target,
                { opacity: [0, 1], y: [40, 0], scale: [0.9, 1] },
                { 
                    duration: 0.8, 
                    easing: [0.23, 1, 0.32, 1],
                    delay: (index % 3) * 0.1 // Simple stagger logic based on row position
                }
            );
            return () => {}; // Optional cleanup
        }, { margin: "0px 0px -100px 0px" });

        item.setAttribute('data-scroll-init', 'true');
    });

    // 3. 3D Tilt & Premium Hover Interactions
    document.querySelectorAll(".showroom-item").forEach(item => {
        if (item.getAttribute('data-hover-premium') === 'true') return;

        const inner = item.querySelector(".showroom-item-inner") || item;
        
        item.addEventListener("mousemove", (e) => {
            const rect = item.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;

            gsap.to(inner, {
                rotateX: rotateX,
                rotateY: rotateY,
                duration: 0.5,
                ease: "power2.out",
                transformPerspective: 1000
            });
        });

        item.addEventListener("mouseleave", () => {
            gsap.to(inner, {
                rotateX: 0,
                rotateY: 0,
                duration: 0.8,
                ease: "elastic.out(1, 0.5)"
            });
        });

        item.setAttribute('data-hover-premium', 'true');
    });

    // 4. Hero Parallax Effect (Refined)
    const heroImage = document.querySelector(".hero-parallax-img");
    if (heroImage) {
        gsap.to(heroImage, {
            yPercent: 20,
            ease: "none",
            scrollTrigger: {
                trigger: ".cinema-hero",
                start: "top top",
                end: "bottom top",
                scrub: true
            }
        });
    }

    // 5. Category Pills Entry
    const pills = document.querySelectorAll(".category-pill");
    if (pills.length > 0) {
        const toAnimatePills = Array.from(pills).filter(p => p.getAttribute('data-animated') !== 'true');
        if (toAnimatePills.length > 0) {
            animate(
                toAnimatePills,
                { opacity: [0, 1], x: [-10, 0] },
                { delay: stagger(0.02), duration: 0.4 }
            ).finished.then(() => {
                toAnimatePills.forEach(p => p.setAttribute('data-animated', 'true'));
            });
        }
    }

    // 6. Comparison Table Row Stagger
    const table = document.querySelector(".motion-table");
    if (table) {
        const rows = table.querySelectorAll(".motion-row");
        const toAnimateRows = Array.from(rows).filter(r => r.getAttribute('data-animated') !== 'true');
        if (toAnimateRows.length > 0) {
            animate(
                toAnimateRows,
                { opacity: [0, 1], x: [-15, 0] },
                { delay: stagger(0.04), duration: 0.5, easing: [0.23, 1, 0.32, 1] }
            ).finished.then(() => {
                toAnimateRows.forEach(r => r.setAttribute('data-animated', 'true'));
            });
        }
    }
}

window.initShowroomAnimations = initShowroomAnimations;
window.initStorytelling = initStorytelling;
