/* ===================== GENERIC SCROLL REVEAL ===================== */
        /* Fades/slides any `.reveal` element into place the first time it enters the
           viewport (see `.reveal` / `.reveal--left/right/scale` in welcome.css). */
        (function () {
            var items = document.querySelectorAll('.reveal');
            if (!items.length) return;

            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduceMotion || !('IntersectionObserver' in window)) {
                items.forEach(function (el) { el.classList.add('is-in'); });
                return;
            }

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });

            items.forEach(function (el) { observer.observe(el); });
        })();

        /* ===================== HEADER OVERLAY ON SCROLL ===================== */
        (function () {
            var header = document.querySelector('.site-header');
            if (!header) return;
            function updateHeader() {
                if (window.scrollY > 12) {
                    header.classList.add('is-scrolled');
                } else {
                    header.classList.remove('is-scrolled');
                }
            }
            updateHeader();
            window.addEventListener('scroll', updateHeader, { passive: true });
        })();

        /* ===================== TEAM CARD 3D TILT ===================== */
        (function () {
            var cards = document.querySelectorAll('[data-tilt]');
            if (!cards.length) return;
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduceMotion) return;

            cards.forEach(function (card) {
                var inner = card.querySelector('.team-card-inner');
                if (!inner) return;

                card.addEventListener('mousemove', function (e) {
                    var rect = card.getBoundingClientRect();
                    var px = (e.clientX - rect.left) / rect.width;
                    var py = (e.clientY - rect.top) / rect.height;
                    var rotateY = (px - 0.5) * 12;
                    var rotateX = (0.5 - py) * 10;
                    inner.style.transform =
                        'rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
                });

                card.addEventListener('mouseleave', function () {
                    inner.style.transform = 'rotateX(0deg) rotateY(0deg)';
                });
            });
        })();

        /* ===================== MOBILE SHEET ===================== */
        var sheetOverlay = document.getElementById('sheetOverlay');
        var sheetPanel = document.getElementById('sheetPanel');

        function openSheet() {
            sheetOverlay.classList.add('active');
            sheetPanel.classList.add('active');
        }

        function closeSheet() {
            sheetOverlay.classList.remove('active');
            sheetPanel.classList.remove('active');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSheet();
            }
        });

        /* ===================== STACKED SECTIONS OVERLAP SCROLL EFFECT ===================== */
        /* Ekskul (.coverflow) -> Fitur 1 -> Fitur 2 -> Fitur 3 -> Fitur 4 stack on top of
           each other via position:sticky; this fades/scales each section's inner content
           as the next one in the chain slides up to cover it. */

        /* ===================== SCROLL TABLET (CONTAINER SCROLL) ===================== */
        /* Tablet/IPad frame tilts + zooms back to flat while the header drifts up,
           driven by scroll progress through a tall section. */
        (function () {
            var section = document.querySelector('[data-tablet-scroll]');
            if (!section) return;

            var card = section.querySelector('[data-tablet-card]');
            var header = section.querySelector('[data-tablet-header]');
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduceMotion || !card) return;

            var ticking = false;

            function update() {
                ticking = false;
                var vh = window.innerHeight || document.documentElement.clientHeight;
                var total = section.offsetHeight - vh;
                if (total <= 0) return;

                var rect = section.getBoundingClientRect();
                var progress = -rect.top / total;
                progress = Math.max(0, Math.min(1, progress));

                var rotate = 20 * (1 - progress);
                var scale = 1.05 - progress * 0.05;
                var translate = -100 * progress;

                card.style.transform = 'rotateX(' + rotate + 'deg) scale(' + scale + ')';
                header.style.transform = 'translateY(' + translate + 'px)';
            }

            function onScroll() {
                if (!ticking) {
                    ticking = true;
                    window.requestAnimationFrame(update);
                }
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll, { passive: true });
            update();
        })();

        (function () {
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduceMotion) return;

            var stackSections = Array.prototype.slice.call(
                document.querySelectorAll('.coverflow, .feature-detail-section')
            );
            if (stackSections.length < 2) return;

            var pairs = [];
            for (var i = 0; i < stackSections.length - 1; i++) {
                var current = stackSections[i];
                var next = stackSections[i + 1];
                var content = current.querySelector('.coverflow-inner, .feature-detail-container');
                if (content) {
                    pairs.push({ content: content, next: next });
                }
            }
            if (pairs.length === 0) return;

            var ticking = false;

            function update() {
                ticking = false;
                var viewportH = window.innerHeight || document.documentElement.clientHeight;

                pairs.forEach(function (pair) {
                    var rect = pair.next.getBoundingClientRect();
                    var progress = 1 - (rect.top / viewportH);
                    progress = Math.max(0, Math.min(1, progress));

                    var scale = 1 - progress * 0.08;
                    var translateY = progress * -50;
                    var opacity = 1 - progress * 0.85;

                    pair.content.style.transform = 'translateY(' + translateY + 'px) scale(' + scale + ')';
                    pair.content.style.opacity = opacity;
                });
            }

            function onScroll() {
                if (!ticking) {
                    ticking = true;
                    window.requestAnimationFrame(update);
                }
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll, { passive: true });
            update();
        })();

        /* ===================== EKSKUL 3D COVERFLOW CAROUSEL ===================== */
        (function () {
            var carousel = document.querySelector('[data-coverflow]');
            if (!carousel) return;

            var stage = carousel.querySelector('.coverflow-stage');
            if (!stage) return;

            var items = Array.prototype.slice.call(stage.querySelectorAll('.coverflow-card'));
            var total = items.length;
            if (total === 0) return;

            var section  = carousel.closest('.coverflow') || carousel;
            var bgImg    = section.querySelector('[data-coverflow-bg]');
            var prevBtn  = section.querySelector('[data-coverflow-prev]');
            var nextBtn  = section.querySelector('[data-coverflow-next]');
            var dotEls   = Array.prototype.slice.call(section.querySelectorAll('[data-coverflow-dot]'));

            var autoplayDelay = 5000;
            var currentIndex  = 0;
            var timer         = null;
            var touchStartX   = 0;

            function getConfig() {
                var cs = window.getComputedStyle(stage);
                var read = function (name, dflt) {
                    var v = parseFloat(cs.getPropertyValue(name));
                    return isNaN(v) ? dflt : v;
                };
                return {
                    shift1: read('--shift-1', 285),
                    shift2: read('--shift-2', 510)
                };
            }

            function goTo(idx) {
                currentIndex = ((idx % total) + total) % total;
                render();
            }

            function next() { goTo(currentIndex + 1); }
            function prev() { goTo(currentIndex - 1); }

            function render() {
                var cfg = getConfig();

                items.forEach(function (item, idx) {
                    var offset = (idx - currentIndex + total) % total;
                    var tx = 0, scale = 0.4, rot = 0, opacity = 0, zIndex = 0;
                    var filter = 'brightness(0.4) blur(2px)';
                    var isCenter = false;

                    if (offset === 0) {
                        isCenter = true;
                        tx = 0; scale = 1; rot = 0; opacity = 1; zIndex = 30;
                        filter = 'brightness(1)';
                    } else if (offset === 1) {
                        tx = cfg.shift1; scale = 0.84; rot = -24; opacity = 0.65; zIndex = 20;
                        filter = 'brightness(0.75)';
                    } else if (offset === 2) {
                        tx = cfg.shift2; scale = 0.68; rot = -38; opacity = 0.38; zIndex = 10;
                        filter = 'brightness(0.55) blur(1px)';
                    } else if (offset === total - 1) {
                        tx = -cfg.shift1; scale = 0.84; rot = 24; opacity = 0.65; zIndex = 20;
                        filter = 'brightness(0.75)';
                    } else if (offset === total - 2) {
                        tx = -cfg.shift2; scale = 0.68; rot = 38; opacity = 0.38; zIndex = 10;
                        filter = 'brightness(0.55) blur(1px)';
                    }

                    item.style.transform = 'translateX(' + tx + 'px) scale(' + scale + ') rotateY(' + rot + 'deg)';
                    item.style.opacity = opacity;
                    item.style.zIndex = zIndex;
                    item.style.filter = filter;
                    item.style.cursor = isCenter ? 'default' : 'pointer';
                    item.style.boxShadow = isCenter
                        ? '0 25px 60px rgba(0,0,0,0.9), 0 0 35px rgba(197,168,128,0.25)'
                        : '0 15px 35px rgba(0,0,0,0.5)';
                    item.classList.toggle('is-center', isCenter);
                    item.__center = isCenter;

                    var content = item.querySelector('.coverflow-content');
                    if (content) {
                        content.style.opacity = isCenter ? 1 : 0;
                        content.style.transform = isCenter ? 'translateY(0px)' : 'translateY(16px)';
                        content.style.pointerEvents = isCenter ? 'auto' : 'none';
                    }
                });

                if (bgImg) {
                    var img = items[currentIndex].querySelector('.coverflow-card-img');
                    if (img && img.getAttribute('src')) {
                        bgImg.src = img.src;
                        bgImg.classList.add('is-visible');
                    } else {
                        bgImg.removeAttribute('src');
                        bgImg.classList.remove('is-visible');
                    }
                }

                dotEls.forEach(function (dot, idx) {
                    dot.classList.toggle('active', idx === currentIndex);
                });
            }

            items.forEach(function (item, idx) {
                item.addEventListener('click', function () {
                    if (item.__center) return;
                    goTo(idx);
                });
            });

            if (prevBtn) prevBtn.addEventListener('click', prev);
            if (nextBtn) nextBtn.addEventListener('click', next);
            if (prevBtn) prevBtn.style.display = total > 1 ? '' : 'none';
            if (nextBtn) nextBtn.style.display = total > 1 ? '' : 'none';

            dotEls.forEach(function (dot, idx) {
                dot.addEventListener('click', function () { goTo(idx); });
            });

            section.addEventListener('mouseenter', stop);
            section.addEventListener('mouseleave', start);
            section.addEventListener('touchstart', function (e) {
                touchStartX = e.touches[0].clientX;
            }, { passive: true });
            section.addEventListener('touchend', function (e) {
                var diff = e.changedTouches[0].clientX - touchStartX;
                if (Math.abs(diff) > 45) {
                    if (diff < 0) next(); else prev();
                }
            }, { passive: true });

            document.addEventListener('keydown', function (e) {
                var tag = (e.target && e.target.tagName) || '';
                if (tag === 'INPUT' || tag === 'TEXTAREA') return;
                if (e.key === 'ArrowLeft') prev();
                else if (e.key === 'ArrowRight') next();
            });

            var resizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(render, 150);
            });

            function start() {
                stop();
                if (total > 1) timer = setInterval(next, autoplayDelay);
            }

            function stop() {
                if (timer) { clearInterval(timer); timer = null; }
            }

            render();
            start();
        })();

        /* ===================== 3D TABLET SCROLL ANIMATION ===================== */
        (function () {
            var track = document.getElementById('tabletTrack');
            if (!track) return;

            var cards = Array.prototype.slice.call(track.querySelectorAll('.tablet-card'));
            if (cards.length === 0) return;

            var indicator = track.parentElement.querySelector('.tablet-scroll-indicator');

            function isElementInViewport(el, threshold) {
                var rect = el.getBoundingClientRect();
                var viewH = window.innerHeight || document.documentElement.clientHeight;
                var visibleTop = Math.max(0, rect.top);
                var visibleBottom = Math.min(viewH, rect.bottom);
                var visibleHeight = Math.max(0, visibleBottom - visibleTop);
                return visibleHeight / rect.height >= (threshold || 0.3);
            }

            function updateVisibility() {
                cards.forEach(function (card) {
                    var inView = isElementInViewport(card, 0.25);
                    card.classList.toggle('is-visible', inView);
                });

                if (indicator) {
                    var lastCard = cards[cards.length - 1];
                    if (lastCard && isElementInViewport(lastCard, 0.5)) {
                        indicator.style.opacity = '0';
                        indicator.style.pointerEvents = 'none';
                    } else {
                        indicator.style.opacity = '';
                        indicator.style.pointerEvents = '';
                    }
                }
            }

            var scrollTimer;
            function onScroll() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(updateVisibility, 10);
            }

            track.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll, { passive: true });
            window.addEventListener('scroll', onScroll, { passive: true });

            var touchStartX = 0;
            track.addEventListener('touchstart', function (e) {
                touchStartX = e.touches[0].clientX;
            }, { passive: true });

            var lastScrollLeft = track.scrollLeft;
            track.addEventListener('scroll', function () {
                var delta = track.scrollLeft - lastScrollLeft;
                lastScrollLeft = track.scrollLeft;

                cards.forEach(function (card) {
                    var depth = parseFloat(card.getAttribute('data-depth')) || 0;
                    var rect = card.getBoundingClientRect();
                    var trackRect = track.getBoundingClientRect();
                    var cardCenter = rect.left + rect.width / 2;
                    var trackCenter = trackRect.left + trackRect.width / 2;
                    var offset = (cardCenter - trackCenter) / (trackRect.width / 2);

                    var frame = card.querySelector('.tablet-frame');
                    if (frame) {
                        var rotateY = offset * 15 * depth;
                        var translateZ = Math.abs(offset) * 30 * depth;
                        frame.style.transform = 'rotateX(5deg) rotateY(' + (-rotateY) + 'deg) translateZ(' + translateZ + 'px)';
                    }
                });
            }, { passive: true });

            updateVisibility();
        })();
