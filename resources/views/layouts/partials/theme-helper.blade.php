<script>
    // Aplicar tema INMEDIATAMENTE para evitar fogonazos blancos
    // Este script debe ejecutarse antes de que el body se renderice
    (function() {
        const storedTheme = localStorage.getItem('theme');
        let effectiveTheme;
        if (storedTheme && storedTheme !== 'auto') {
            effectiveTheme = storedTheme;
        } else {
            effectiveTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-bs-theme', effectiveTheme);

        // Función global para obtener el tema efectivo (disponible para otros scripts)
        window.getEffectiveTheme = function() {
            const stored = localStorage.getItem('theme');
            if (stored && stored !== 'auto') {
                return stored;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };
    })();
</script>
