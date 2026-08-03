{{--
    Umschalter zwischen dunklem und hellem Modus.

    Die runde Flaeche kommt von der einbindenden Seite – auf der
    Rezeptseite ueber die zusaetzliche Klasse .hero-btn. Sichtbar ist
    immer das Icon des Modus, in den geschaltet wird (Steuerung in
    base.css unter "Theme-Switch").
--}}
<button type="button" id="theme-toggle" class="theme-toggle {{ $chipClass ?? 'hero-btn' }}"
    aria-label="Design wechseln" title="Design wechseln">

    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="4.2" />
        <path d="M12 2.5v2M12 19.5v2M2.5 12h2M19.5 12h2M5.2 5.2l1.4 1.4M17.4 17.4l1.4 1.4M18.8 5.2l-1.4 1.4M6.6 17.4l-1.4 1.4" />
    </svg>

    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20 14.2A8.2 8.2 0 0 1 9.8 4a8.4 8.4 0 1 0 10.2 10.2Z" />
    </svg>
</button>

<script>
    (function () {
        var button = document.getElementById('theme-toggle');
        var root = document.documentElement;

        function updateLabel() {
            button.setAttribute(
                'aria-label',
                root.classList.contains('theme-light')
                    ? 'Zu dunklem Design wechseln'
                    : 'Zu hellem Design wechseln'
            );
        }

        updateLabel();

        button.addEventListener('click', function () {
            var isLight = root.classList.toggle('theme-light');

            try {
                localStorage.setItem('theme', isLight ? 'light' : 'dark');
            } catch (e) {
                /* nicht speicherbar – die Auswahl gilt dann nur fuer diese Seite */
            }

            updateLabel();
        });
    })();
</script>
