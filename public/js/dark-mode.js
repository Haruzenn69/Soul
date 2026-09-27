(function () {
    var storageKey = 'soul-theme-mode';
    var root = document.documentElement;

    function isDark() {
        return root.classList.contains('dark-mode') || root.classList.contains('dark');
    }

    function applyTheme(dark) {
        root.classList.toggle('dark-mode', dark);
        root.classList.toggle('dark', dark);
        try {
            localStorage.setItem(storageKey, dark ? 'dark' : 'light');
        } catch (error) {
            console.warn('Unable to save the theme preference.', error);
        }
        updateButton();
        window.dispatchEvent(new CustomEvent('soul-theme-change', { detail: { dark: dark } }));
    }

    function updateButton() {
        var button = document.getElementById('soul-theme-toggle');
        if (!button) return;
        var dark = isDark();
        button.setAttribute('aria-pressed', dark ? 'true' : 'false');
        button.setAttribute('aria-label', dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
        button.title = dark ? 'Mode terang' : 'Mode gelap';
        button.innerHTML = dark
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13z"/></svg>';
    }

    function mountButton() {
        if (document.getElementById('soul-theme-toggle')) return;
        var button = document.createElement('button');
        button.id = 'soul-theme-toggle';
        button.type = 'button';
        button.addEventListener('click', function () {
            applyTheme(!isDark());
        });
        document.body.appendChild(button);
        updateButton();
    }

    var preference = null;
    try {
        preference = localStorage.getItem(storageKey);
    } catch (error) {
        console.warn('Unable to read the theme preference.', error);
    }
    var initialDark = preference === 'dark' || (preference === null && window.matchMedia('(prefers-color-scheme: dark)').matches);
    root.classList.toggle('dark-mode', initialDark);
    root.classList.toggle('dark', initialDark);

    window.SoulTheme = {
        isDark: isDark,
        applyTheme: applyTheme,
        toggle: function () {
            applyTheme(!isDark());
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountButton, { once: true });
    } else {
        mountButton();
    }
})();
