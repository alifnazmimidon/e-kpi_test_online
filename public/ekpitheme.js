(function () {
    const STORAGE_KEY = 'ekpi_theme_mode'; // 'light' | 'dark' | 'system'
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    let normalizeTimer = null;

    function getSavedMode() {
        return localStorage.getItem(STORAGE_KEY) || 'light';
    }

    function resolveTheme(mode) {
        if (mode === 'system') {
            return mediaQuery.matches ? 'dark' : 'light';
        }
        return mode === 'dark' ? 'dark' : 'light';
    }

    function parseRgb(str) {
        const m = str && str.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
        if (!m) return null;
        return {
            r: Number(m[1]),
            g: Number(m[2]),
            b: Number(m[3]),
            a: m[4] === undefined ? 1 : Number(m[4])
        };
    }

    function clearAdaptiveMarkers() {
        document.querySelectorAll('[data-ekpi-dark-surface]').forEach(el => {
            el.removeAttribute('data-ekpi-dark-surface');
        });
        document.querySelectorAll('[data-ekpi-dark-text]').forEach(el => {
            el.removeAttribute('data-ekpi-dark-text');
        });
    }

    function normalizeDarkSurfaces() {
        if (document.documentElement.getAttribute('data-theme') !== 'dark' || !document.body) return;

        const skipTags = new Set(['SCRIPT', 'STYLE', 'SVG', 'PATH', 'CIRCLE', 'RECT', 'LINE', 'USE', 'SYMBOL', 'IMG', 'CANVAS', 'VIDEO']);
        const nodes = document.body.querySelectorAll('*');

        for (let i = 0; i < nodes.length; i++) {
            const el = nodes[i];
            if (skipTags.has(el.tagName)) continue;
            if (el.closest('.ekpi-theme-dock') || el.closest('.switch') || el.closest('.switch-track')) continue;

            const cs = window.getComputedStyle(el);
            if (cs.display === 'none' || cs.visibility === 'hidden') continue;

            // 1. Kesan latar cerah / pastel yang belum ditukar ke gelap
            const bg = parseRgb(cs.backgroundColor);
            if (bg && bg.a > 0.85) {
                const brightness = (bg.r * 299 + bg.g * 587 + bg.b * 114) / 1000;
                if (brightness >= 232) {
                    if (bg.r > bg.g + 12) {
                        el.setAttribute('data-ekpi-dark-surface', 'tint-red');
                    } else if (bg.g > bg.r + 8) {
                        el.setAttribute('data-ekpi-dark-surface', 'tint-green');
                    } else if (bg.b > bg.r + 8) {
                        el.setAttribute('data-ekpi-dark-surface', 'tint-blue');
                    } else if (brightness >= 252) {
                        el.setAttribute('data-ekpi-dark-surface', 'card');
                    } else {
                        el.setAttribute('data-ekpi-dark-surface', 'sub');
                    }
                }
            }

            // 2. Kesan teks gelap neutral yang sukar dibaca di atas latar gelap
            const fg = parseRgb(cs.color);
            if (fg && fg.a > 0.5) {
                const fgBright = (fg.r * 299 + fg.g * 587 + fg.b * 114) / 1000;
                const saturation = Math.max(fg.r, fg.g, fg.b) - Math.min(fg.r, fg.g, fg.b);
                if (saturation < 45 && fgBright < 115) {
                    el.setAttribute('data-ekpi-dark-text', fgBright < 65 ? 'primary' : 'secondary');
                }
            }
        }
    }

    function scheduleNormalize() {
        clearTimeout(normalizeTimer);
        normalizeTimer = setTimeout(normalizeDarkSurfaces, 40);
    }

    function applyTheme(mode) {
        const resolved = resolveTheme(mode);
        document.documentElement.setAttribute('data-theme', resolved);
        document.documentElement.setAttribute('data-theme-mode', mode);
        updateWidgetUI(mode, resolved);

        if (resolved === 'dark') {
            scheduleNormalize();
        } else {
            clearAdaptiveMarkers();
        }
    }

    function updateWidgetUI(mode, resolved) {
        const label = document.getElementById('ekpiThemeLabel');
        const sunIcon = document.getElementById('ekpiIconSun');
        const moonIcon = document.getElementById('ekpiIconMoon');

        if (label) {
            label.textContent = mode === 'system' ? 'Auto' : (resolved === 'dark' ? 'Dark' : 'Light');
        }
        if (sunIcon && moonIcon) {
            sunIcon.style.display = resolved === 'dark' ? 'none' : 'block';
            moonIcon.style.display = resolved === 'dark' ? 'block' : 'none';
        }

        document.querySelectorAll('.ekpi-theme-option').forEach(btn => {
            const isActive = btn.dataset.mode === mode;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-checked', String(isActive));
        });
    }

    window.EKPI_Theme = {
        set(mode) {
            localStorage.setItem(STORAGE_KEY, mode);
            applyTheme(mode);
        },
        get() {
            return getSavedMode();
        }
    };

    // Aktifkan tema segera sebelum halaman dilukis (anti-flash)
    applyTheme(getSavedMode());

    mediaQuery.addEventListener('change', () => {
        if (getSavedMode() === 'system') applyTheme('system');
    });

    window.addEventListener('storage', (e) => {
        if (e.key === STORAGE_KEY) applyTheme(getSavedMode());
    });

    document.addEventListener('DOMContentLoaded', () => {
        applyTheme(getSavedMode());

        // Pantau elemen dinamik (jadual AJAX, modal, notifikasi)
        const observer = new MutationObserver(() => {
            if (document.documentElement.getAttribute('data-theme') === 'dark') {
                scheduleNormalize();
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });

        const dock = document.getElementById('ekpiThemeDock');
        const trigger = document.getElementById('ekpiThemeTrigger');
        if (!dock || !trigger) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dock.classList.toggle('open');
            trigger.setAttribute('aria-expanded', String(isOpen));
        });

        dock.querySelectorAll('.ekpi-theme-option').forEach(btn => {
            btn.addEventListener('click', () => {
                window.EKPI_Theme.set(btn.dataset.mode);
                dock.classList.remove('open');
                trigger.setAttribute('aria-expanded', 'false');
            });
        });

        document.addEventListener('click', (e) => {
            if (!dock.contains(e.target)) {
                dock.classList.remove('open');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && dock.classList.contains('open')) {
                dock.classList.remove('open');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    });
})();