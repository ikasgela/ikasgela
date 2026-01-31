<script>
    // Función global para obtener el tema efectivo (disponible inmediatamente)
    window.getEffectiveTheme = function() {
        const storedTheme = localStorage.getItem('theme')
        if (storedTheme && storedTheme !== 'auto') {
            return storedTheme
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    };
</script>
