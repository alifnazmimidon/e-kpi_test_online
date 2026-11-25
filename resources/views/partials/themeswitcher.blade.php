<!-- FLOATING THEME SWITCHER (PLUG & PLAY) -->
<div class="ekpi-theme-dock" id="ekpiThemeDock">
    <div class="ekpi-theme-popover" role="menu" aria-label="Appearance settings">
        <div class="ekpi-theme-popover-title">Interface Theme</div>

        <button type="button" class="ekpi-theme-option" data-mode="light" role="menuitemradio" aria-checked="true">
            <span class="ekpi-theme-opt-left">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                </svg>
                Light Mode
            </span>
            <span class="ekpi-theme-check" aria-hidden="true">✓</span>
        </button>

        <button type="button" class="ekpi-theme-option" data-mode="dark" role="menuitemradio" aria-checked="false">
            <span class="ekpi-theme-opt-left">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                </svg>
                Dark Mode
            </span>
            <span class="ekpi-theme-check" aria-hidden="true">✓</span>
        </button>

        <button type="button" class="ekpi-theme-option" data-mode="system" role="menuitemradio" aria-checked="false">
            <span class="ekpi-theme-opt-left">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <path d="M8 21h8M12 17v4"/>
                </svg>
                Follow System
            </span>
            <span class="ekpi-theme-check" aria-hidden="true">✓</span>
        </button>
    </div>

    <button type="button" class="ekpi-theme-trigger" id="ekpiThemeTrigger"
            aria-haspopup="true" aria-expanded="false" title="Switch Light / Dark Mode">
        <svg id="ekpiIconSun" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="12" r="4"/>
            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
        </svg>
        <svg id="ekpiIconMoon" viewBox="0 0 24 24" style="display:none;" aria-hidden="true">
            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
        </svg>
        <span id="ekpiThemeLabel">Light</span>
    </button>
</div>