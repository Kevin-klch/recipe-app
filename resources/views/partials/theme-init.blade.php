{{--
    Gehoert in den <head>, moeglichst weit oben.

    Setzt den hellen Modus noch vor dem ersten Paint, damit die Seite beim
    Laden nicht kurz dunkel aufblitzt. Dunkel ist der Standard, deshalb
    passiert ohne gespeicherte Auswahl gar nichts.
--}}
<script>
    (function () {
        try {
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('theme-light');
            }
        } catch (e) {
            /* localStorage gesperrt (privater Modus) – dann bleibt es dunkel */
        }
    })();
</script>
