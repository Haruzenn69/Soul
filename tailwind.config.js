import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,ts,tsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Halaman utama memuat "Plus Jakarta Sans" (Google Fonts),
                // halaman auth memuat "Figtree" (fonts.bunny.net).
                // Rantai fallback ini memilih otomatis font yang termuat.
                sans: ['"Plus Jakarta Sans"', 'Figtree', 'Inter', ...defaultTheme.fontFamily.sans],
                serif: ['"DM Serif Display"', ...defaultTheme.fontFamily.serif],
            },
            borderRadius: {
                lg: 'var(--radius)',
                md: 'calc(var(--radius) - 2px)',
                sm: 'calc(var(--radius) - 4px)',
            },
            colors: {
                // Palet semantik yang sebelumnya didefinisikan ulang sebagai
                // `tailwind.config` inline di dalam setiap blade.
                theme: {
                    blue: '#2563EB',
                    darkBlue: '#1D4ED8',
                    yellow: '#FACC15',
                    dark: '#0F172A',
                    light: '#F8FAFC',
                    lightBg: '#F8FAFC',
                },
            },
        },
    },

    // Catatan: @tailwindcss/forms sengaja tidak diaktifkan.
    // 90+ halaman memakai class utilitas apa adanya (tanpa komponen form),
    // dan cdn.tailwindcss.com yang sebelumnya dipakai TIDAK memuat plugin ini.
    // Mengaktifkannya akan mengubah tampilan seluruh input/select/textarea.
    plugins: [],
};
