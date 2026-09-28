<script>
    (() => {
        try {
            const preference = localStorage.getItem('soul-theme-mode');
            const isDark = preference === 'dark' || (preference === null && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark-mode', isDark);
            document.documentElement.classList.toggle('dark', isDark);
        } catch (error) {
            const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark-mode', isDark);
            document.documentElement.classList.toggle('dark', isDark);
        }
    })();
</script>
<link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}" data-soul-theme>
<script src="{{ asset('js/dark-mode.js') }}" defer data-soul-theme></script>
