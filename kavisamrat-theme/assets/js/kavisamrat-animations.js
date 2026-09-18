/**
 * KAVISAMRAT — Motion Layer
 * Inspired by the scroll/hover rhythm of sethring.com, built on GSAP + ScrollTrigger.
 * Degrades gracefully: if GSAP fails to load, IntersectionObserver fallback still reveals content.
 */
(function () {
  "use strict";

  const REDUCED_MOTION = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const hasGSAP = typeof window.gsap !== "undefined";

  if (hasGSAP && window.ScrollTrigger) {
    gsap.registerPlugin(ScrollTrigger);
  }

  /* ---------------------------------------------
     1. Scroll reveals — SiteOrigin rows, book cards, text blocks
  --------------------------------------------- */
  function initScrollReveals() {
    const targets = document.querySelectorAll(
      ".reveal-up, .panel-builder-layout, .book-card, .kv-card"
    );

    targets.forEach((el) => el.classList.add("reveal-up"));

    if (REDUCED_MOTION) {
      targets.forEach((el) => el.classList.add("is-visible"));
      return;
    }

    if (hasGSAP && window.ScrollTrigger) {
      // Stagger by parent grid so archive/grid entrances feel choreographed
      const grids = document.querySelectorAll(".kv-grid, .siteorigin-panels-builder-layout");
      const handled = new Set();

      grids.forEach((grid) => {
        const items = grid.querySelectorAll(".reveal-up");
        if (!items.length) return;
        items.forEach((i) => handled.add(i));

        gsap.set(items, { autoAlpha: 0, y: 32 });
        ScrollTrigger.batch(items, {
          start: "top 88%",
          onEnter: (batch) =>
            gsap.to(batch, {
              autoAlpha: 1,
              y: 0,
              duration: 0.7,
              ease: "power3.out",
              stagger: 0.08,
            }),
          once: true,
        });
      });

      // Anything not inside a grid, reveal individually
      targets.forEach((el) => {
        if (handled.has(el)) return;
        gsap.set(el, { autoAlpha: 0, y: 32 });
        ScrollTrigger.create({
          trigger: el,
          start: "top 90%",
          once: true,
          onEnter: () =>
            gsap.to(el, { autoAlpha: 1, y: 0, duration: 0.7, ease: "power3.out" }),
        });
      });
    } else {
      // Fallback: IntersectionObserver
      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add("is-visible");
              io.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.15 }
      );
      targets.forEach((el) => io.observe(el));
    }
  }

  /* ---------------------------------------------
     2. Hero entrance
  --------------------------------------------- */
  function initHeroEntrance() {
    const hero = document.querySelector(".kv-hero");
    if (!hero || REDUCED_MOTION) return;

    const items = hero.querySelectorAll(
      ".kv-hero__telugu, .kv-hero__eyebrow, .kv-hero__title, .kv-hero__telugu-title, .kv-hero__quote, .kv-hero__actions"
    );

    const artwork = hero.querySelector(".kv-hero__artwork");

    if (hasGSAP) {
      gsap.set(items, { autoAlpha: 0, y: 24 });
      gsap.to(items, {
        autoAlpha: 1,
        y: 0,
        duration: 0.9,
        ease: "power3.out",
        stagger: 0.12,
        delay: 0.15,
      });
      if (artwork) {
        gsap.fromTo(artwork, { autoAlpha: 0, y: 24, scale: .97 }, { autoAlpha: 1, y: 0, scale: 1, duration: 1.2, ease: "power2.out", delay: .45 });
        gsap.to(artwork, { yPercent: -5, ease: "none", scrollTrigger: { trigger: hero, start: "top top", end: "bottom top", scrub: true } });
      }
    } else {
      items.forEach((el, i) => {
        el.style.transition = "opacity .8s ease, transform .8s ease";
        el.style.transitionDelay = i * 0.12 + "s";
        requestAnimationFrame(() => {
          el.style.opacity = "1";
          el.style.transform = "translateY(0)";
        });
      });
    }
  }

  /* ---------------------------------------------
     3. Book Highlights carousel — swipe + fade/slide
  --------------------------------------------- */
  function initCarousels() {
    document.querySelectorAll("[data-kv-carousel]").forEach((root) => {
      const track = root.querySelector(".kv-carousel__track");
      const slides = Array.from(root.querySelectorAll(".kv-carousel__slide"));
      const dotsWrap = root.querySelector(".kv-carousel__nav");
      const slideLabel = root.dataset.kvCarouselLabel || "slide";
      const previousButton = root.dataset.kvCarouselArrows !== undefined
        ? root.querySelector(".kv-carousel__arrow--prev")
        : null;
      const nextButton = root.dataset.kvCarouselArrows !== undefined
        ? root.querySelector(".kv-carousel__arrow--next")
        : null;
      if (!track || !slides.length) return;

      let index = 0;
      let startX = 0;
      let currentX = 0;
      let dragging = false;

      // Build dots
      if (dotsWrap) {
        dotsWrap.innerHTML = "";
        slides.forEach((_, i) => {
          const dot = document.createElement("button");
          dot.className = "kv-carousel__dot" + (i === 0 ? " is-active" : "");
          dot.setAttribute("aria-label", "Go to " + slideLabel + " " + (i + 1));
          dot.addEventListener("click", () => goTo(i));
          dotsWrap.appendChild(dot);
        });
      }

      function goTo(i) {
        index = Math.max(0, Math.min(slides.length - 1, i));
        const offset = -(index * slides[0].getBoundingClientRect().width);
        if (hasGSAP && !REDUCED_MOTION) {
          gsap.to(track, { x: offset, duration: 0.6, ease: "power3.out" });
        } else {
          track.style.transform = `translateX(${offset}px)`;
        }
        if (dotsWrap) {
          Array.from(dotsWrap.children).forEach((d, di) =>
            d.classList.toggle("is-active", di === index)
          );
        }
        if (previousButton) previousButton.disabled = index === 0;
        if (nextButton) nextButton.disabled = index === slides.length - 1;
      }

      if (previousButton) previousButton.addEventListener("click", () => goTo(index - 1));
      if (nextButton) nextButton.addEventListener("click", () => goTo(index + 1));
      goTo(0);

      // Pointer / touch swipe
      track.addEventListener("pointerdown", (e) => {
        dragging = true;
        startX = e.clientX;
        track.setPointerCapture(e.pointerId);
      });
      track.addEventListener("pointermove", (e) => {
        if (!dragging) return;
        currentX = e.clientX - startX;
      });
      track.addEventListener("pointerup", () => {
        if (!dragging) return;
        dragging = false;
        if (currentX < -50) goTo(index + 1);
        else if (currentX > 50) goTo(index - 1);
        else goTo(index);
        currentX = 0;
      });

      window.addEventListener("resize", () => goTo(index));
    });
  }

  /* ---------------------------------------------
     4. Book card 3D tilt on hover (desktop only, respects reduced motion)
  --------------------------------------------- */
  function initTilt() {
    if (REDUCED_MOTION || window.matchMedia("(hover: none)").matches) return;

    document.querySelectorAll(".book-card__cover-wrap").forEach((card) => {
      card.addEventListener("mousemove", (e) => {
        const rect = card.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;
        const rotateX = (-y * 8).toFixed(2);
        const rotateY = (x * 8).toFixed(2);
        card.style.transform = `translateY(-6px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.03)`;
      });
      card.addEventListener("mouseleave", () => {
        card.style.transform = "";
      });
    });
  }

  /* ---------------------------------------------
     Init
  --------------------------------------------- */
  document.addEventListener("DOMContentLoaded", () => {
    initHeroEntrance();
    initScrollReveals();
    initCarousels();
    initTilt();
  });
})();


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
