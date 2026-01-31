/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2023 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

(() => {
    'use strict'

    const getStoredTheme = () => localStorage.getItem('theme')
    const setStoredTheme = theme => localStorage.setItem('theme', theme)

    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme()
        if (storedTheme) {
            return storedTheme
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }

    // Obtener el tema efectivo (resolviendo 'auto' a 'light' o 'dark')
    const getEffectiveTheme = (theme) => {
        if (theme === 'auto') {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        }
        return theme
    }

    // Aplicar el tema INMEDIATAMENTE para evitar fogonazos blancos
    // Esto se ejecuta antes de que el DOM esté completamente cargado
    const preferredTheme = getPreferredTheme()
    document.documentElement.setAttribute('data-bs-theme', getEffectiveTheme(preferredTheme))

    function setPrismjsTheme(theme) {
        const effectiveTheme = getEffectiveTheme(theme)
        let id = effectiveTheme === 'light' ? 'prism-coy' : 'prism-tomorrow';
        let theme_url = `https://${location.hostname}/build/prismjs/${id}.min.css`;

        const prismjs = document.querySelector('#prismjs-theme');
        if (prismjs !== null) {
            prismjs.setAttribute('href', theme_url)
        }
    }

    const setTheme = (theme, updateDependencies = true) => {
        document.documentElement.setAttribute('data-bs-theme', getEffectiveTheme(theme))

        // Solo actualizar dependencias si el DOM está listo
        if (updateDependencies) {
            tinymce_reload()
            setPrismjsTheme(theme)
        }
    }

    // Actualizar dependencias cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setPrismjsTheme(preferredTheme)
        }, { once: true })
    } else {
        setPrismjsTheme(preferredTheme)
    }

    const showActiveTheme = (theme, focus = false) => {
        const themeSwitcher = document.querySelector('#bd-theme')

        if (!themeSwitcher) {
            return
        }

        const themeSwitcherText = document.querySelector('#bd-theme-text')
        const activeThemeIcon = document.querySelector('.theme-icon-active')
        const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
        const svgOfActiveBtn = btnToActive.querySelector('i').getAttribute('data-icon')

        document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
            element.classList.remove('active')
            element.setAttribute('aria-pressed', 'false')
        })

        activeThemeIcon.className = "bi me-1 theme-icon-active";
        activeThemeIcon.classList.add(svgOfActiveBtn)

        btnToActive.classList.add('active')
        btnToActive.setAttribute('aria-pressed', 'true')
        activeThemeIcon.setAttribute('href', svgOfActiveBtn)
        const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
        themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

        if (focus) {
            themeSwitcher.focus()
        }
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const storedTheme = getStoredTheme()
        if (storedTheme !== 'light' && storedTheme !== 'dark') {
            setTheme(getPreferredTheme())
        }
    })

    window.addEventListener('DOMContentLoaded', () => {
        showActiveTheme(getPreferredTheme())

        document.querySelectorAll('[data-bs-theme-value]')
            .forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const theme = toggle.getAttribute('data-bs-theme-value')
                    setStoredTheme(theme)
                    setTheme(theme)
                    showActiveTheme(theme, true)
                })
            })
    })

    function tinymce_reload() {
        if (typeof tinymce === 'undefined' || typeof tinymce_config === 'undefined') {
            return;
        }

        if (!tinymce.activeEditor) {
            return;
        }

        let content = tinymce.activeEditor.getContent();
        tinymce.activeEditor.destroy();

        tinymce_config.skin = document.documentElement.getAttribute('data-bs-theme') === 'dark'
            ? "oxide-dark"
            : "oxide";
        tinymce_config.content_css = document.documentElement.getAttribute('data-bs-theme') === 'dark'
            ? "dark"
            : "default";

        tinymce.init(tinymce_config);
        tinymce.activeEditor.setContent(content);
    }
})()
