{{-- =========================================================
     PENCARIAN GLOBAL DENGAN SUGGESTION (seperti Google)
     Pakai: @include('partials.search-autocomplete')
     Input harus punya atribut `data-soul-search`
     ========================================================= --}}
<style>
    .soul-search-wrap { position: relative; }
    .soul-search-panel {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 8px);
        z-index: 60;
        max-height: min(70vh, 420px);
        overflow-y: auto;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 20px 45px -18px rgba(15, 23, 42, .35);
        overflow: hidden;
    }
    html.dark-mode .soul-search-panel {
        background: #171717;
        border-color: #3a3a3a;
        box-shadow: 0 20px 45px -18px rgba(0, 0, 0, .8);
    }
    .soul-search-item {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        text-align: left;
        padding: 10px 14px;
        font-size: 12px;
        color: #334155;
        text-decoration: none;
        transition: background-color .12s ease;
    }
    html.dark-mode .soul-search-item { color: #d4d4d4; }
    .soul-search-item:hover,
    .soul-search-item.is-active {
        background: #f1f5f9;
    }
    html.dark-mode .soul-search-item:hover,
    html.dark-mode .soul-search-item.is-active {
        background: #262626;
    }
    .soul-search-item__icon {
        width: 26px;
        height: 26px;
        flex: 0 0 auto;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #64748b;
    }
    html.dark-mode .soul-search-item__icon {
        background: #262626;
        color: #a3a3a3;
    }
    .soul-search-item__label {
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .soul-search-item__meta {
        margin-left: auto;
        flex-shrink: 0;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #94a3b8;
        padding-left: 10px;
    }
    .soul-search-empty {
        padding: 16px 14px;
        font-size: 12px;
        color: #94a3b8;
        text-align: center;
    }
    .soul-search-mark {
        background: rgba(250, 204, 21, .35);
        border-radius: 3px;
        color: inherit;
        font-weight: 800;
    }
</style>

<script>
    (function () {
        if (window.__soulSearchInit) return;
        window.__soulSearchInit = true;

        const ENDPOINT = @json(route('search.suggestions'));
        let activeIndex = -1;
        let currentItems = [];
        let timer = null;

        function escapeHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function highlight(text, query) {
            const safe = escapeHtml(text);
            if (!query) return safe;
            const idx = safe.toLowerCase().indexOf(query.toLowerCase());
            if (idx === -1) return safe;
            return safe.slice(0, idx)
                + '<mark class="soul-search-mark">' + safe.slice(idx, idx + query.length) + '</mark>'
                + safe.slice(idx + query.length);
        }

        function iconSvg() {
            return '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 21l-4.35-4.35"/><circle cx="11" cy="11" r="7"/></svg>';
        }

        function buildPanel(wrap, query) {
            const panel = wrap.querySelector('.soul-search-panel');
            if (!panel) return;

            if (!currentItems.length) {
                panel.innerHTML = '<div class="soul-search-empty">Tidak ada hasil untuk &ldquo;' + escapeHtml(query) + '&rdquo;</div>';
                panel.classList.remove('hidden');
                return;
            }

            panel.innerHTML = currentItems.map(function (item, index) {
                return '<a href="' + escapeHtml(item.url) + '" class="soul-search-item" data-soul-search-index="' + index + '">' +
                    '<span class="soul-search-item__icon">' + iconSvg() + '</span>' +
                    '<span class="soul-search-item__label">' + highlight(item.label, query) + '</span>' +
                    '<span class="soul-search-item__meta">' + escapeHtml(item.meta || item.type || '') + '</span>' +
                '</a>';
            }).join('');
            panel.classList.remove('hidden');
        }

        function hide(panel) {
            panel.classList.add('hidden');
            activeIndex = -1;
        }

        function setActive(panel, index) {
            const links = panel.querySelectorAll('.soul-search-item');
            links.forEach(function (link) { link.classList.remove('is-active'); });
            if (index < 0 || index >= links.length) {
                activeIndex = -1;
                return;
            }
            activeIndex = index;
            links[index].classList.add('is-active');
            links[index].scrollIntoView({ block: 'nearest' });
        }

        function setup(input) {
            const wrap = input.closest('.soul-search-wrap') || input.parentElement;
            if (!wrap) return;

            let panel = wrap.querySelector('.soul-search-panel');
            if (!panel) {
                panel = document.createElement('div');
                panel.className = 'soul-search-panel hidden';
                wrap.appendChild(panel);
            }

            input.setAttribute('autocomplete', 'off');
            input.setAttribute('role', 'combobox');
            input.setAttribute('aria-expanded', 'false');

            input.addEventListener('input', function () {
                clearTimeout(timer);
                const query = this.value.trim();
                if (query.length < 2) {
                    currentItems = [];
                    hide(panel);
                    return;
                }
                timer = setTimeout(function () {
                    fetch(ENDPOINT + '?q=' + encodeURIComponent(query), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            currentItems = (data && data.items) || [];
                            buildPanel(wrap, query);
                            input.setAttribute('aria-expanded', 'true');
                        })
                        .catch(function () {
                            currentItems = [];
                            hide(panel);
                        });
                }, 180);
            });

            input.addEventListener('keydown', function (event) {
                const links = panel.querySelectorAll('.soul-search-item');
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    setActive(panel, Math.min(activeIndex + 1, links.length - 1));
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    setActive(panel, Math.max(activeIndex - 1, 0));
                } else if (event.key === 'Enter') {
                    if (activeIndex >= 0 && links[activeIndex]) {
                        event.preventDefault();
                        links[activeIndex].click();
                    }
                } else if (event.key === 'Escape') {
                    hide(panel);
                }
            });

            document.addEventListener('click', function (event) {
                if (!wrap.contains(event.target)) hide(panel);
            });
        }

        function boot() {
            document.querySelectorAll('input[data-soul-search]').forEach(setup);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', boot);
        } else {
            boot();
        }
    })();
</script>
