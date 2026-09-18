/**
 * KAVISAMRAT — "Home · Alternate Version" motion extras.
 * Loaded only on this template, after kavisamrat-animations.js (which
 * already handles .reveal-up scroll reveals, hero entrance, carousels,
 * and book-card tilt — this file only adds the vintage-specific touches
 * that are unique to this page.
 */
(function () {
  "use strict";

  const REDUCED_MOTION = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const hasGSAP = typeof window.gsap !== "undefined";

  document.addEventListener("DOMContentLoaded", function () {
    if (REDUCED_MOTION) return;

    /* Seal "press" — a quick scale/rotate settle when the photo card enters view */
    const seal = document.querySelector(".ha-seal-stamp");
    if (seal && hasGSAP && window.ScrollTrigger) {
      gsap.set(seal, { scale: 0.6, rotate: 40, autoAlpha: 0 });
      ScrollTrigger.create({
        trigger: ".ha-photo-card",
        start: "top 75%",
        once: true,
        onEnter: () =>
          gsap.to(seal, {
            scale: 1,
            rotate: 10,
            autoAlpha: 1,
            duration: 0.8,
            ease: "back.out(2)",
          }),
      });
    }

    /* Gentle parallax drift on the photo card as the hero scrolls past */
    const heroPhoto = document.querySelector(".ha-hero .ha-photo-card");
    if (heroPhoto && hasGSAP && window.ScrollTrigger && !window.matchMedia("(max-width: 900px)").matches) {
      gsap.to(heroPhoto, {
        y: 40,
        ease: "none",
        scrollTrigger: {
          trigger: ".ha-hero",
          start: "top top",
          end: "bottom top",
          scrub: true,
        },
      });
    }

    /* Manuscript card top-bar sweep is pure CSS (:hover), no JS needed. */
  });
})();
