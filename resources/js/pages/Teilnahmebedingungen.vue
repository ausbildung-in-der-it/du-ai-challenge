<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
</script>

<template>
    <Head title="Teilnahmebedingungen" />
    <div class="min-h-dvh bg-black px-7 py-12 font-sans text-[#f5f5f7] antialiased sm:px-8 sm:py-16">
        <div class="mx-auto max-w-2xl">
            <a href="/" class="mb-8 inline-flex items-center gap-1 text-[14px] text-white/35 transition-colors hover:text-white/55">
                <ChevronLeft class="h-4 w-4" />
                Zurück
            </a>

            <h1 class="mb-8 text-[28px] leading-[1.15] font-bold tracking-[-0.8px] sm:text-[36px]">
                Teilnahmebedingungen
            </h1>
            <p class="mb-10 text-[15px] text-white/35">
                Gewinnspiel "AI Ready Training" auf der Data Unplugged 2026
            </p>

            <div class="space-y-10 text-[15px] leading-[1.7] text-white/60 sm:text-[16px]">

                <!-- 1. Veranstalter -->
                <section>
                    <h2 class="mb-3 text-[18px] font-semibold text-white/85">1. Veranstalter</h2>
                    <p>
                        Noel Lang<br />
                        c/o RA Matutis<br />
                        Berliner Straße 57<br />
                        14467 Potsdam<br />
                        E-Mail: kontakt@ausbildung-in-der-it.de<br />
                        USt-ID: DE815913098
                    </p>
                </section>

                <!-- 2. Teilnahmeberechtigung -->
                <section>
                    <h2 class="mb-3 text-[18px] font-semibold text-white/85">2. Teilnahmeberechtigung</h2>
                    <p>
                        Teilnahmeberechtigt sind natürliche Personen ab 18 Jahren mit Wohnsitz in Deutschland, Österreich oder der Schweiz.
                        Ausgeschlossen von der Teilnahme sind der Veranstalter und dessen Angehörige.
                        Eine Teilnahme über automatisierte Verfahren (Bots, Skripte) ist nicht gestattet.
                    </p>
                    <p class="mt-3">
                        Die Teilnahme ist kostenlos und an keinen Kauf gebunden.
                    </p>
                </section>

                <!-- 3. Teilnahmezeitraum -->
                <section>
                    <h2 class="mb-3 text-[18px] font-semibold text-white/85">3. Teilnahmezeitraum und Ablauf</h2>
                    <p>
                        Das Gewinnspiel beginnt am <span class="font-semibold text-white/80">27. März 2026</span>
                        und endet am <span class="font-semibold text-white/80">30. April 2026 um 23:59 Uhr (MESZ)</span>.
                    </p>
                    <p class="mt-3">
                        Die Teilnahme erfolgt durch das vollständige Durchspielen der "Real or Fake?" Challenge auf
                        dieser Website und die anschließende Eingabe einer gültigen E-Mail-Adresse.
                        Alternativ kann die Vormerkung über das Newsletter-Formular auf der Startseite erfolgen.
                    </p>
                    <p class="mt-3">
                        Pro Person ist nur eine Teilnahme möglich. Mehrfacheinsendungen werden nicht berücksichtigt.
                    </p>
                </section>

                <!-- 4. Gewinn -->
                <section>
                    <h2 class="mb-3 text-[18px] font-semibold text-white/85">4. Gewinn</h2>
                    <p>Verlost wird:</p>
                    <ul class="mt-3 list-inside list-disc space-y-1.5 text-white/55">
                        <li>1x AI Ready Early Access Platz (Wert: 1.990 EUR netto)</li>
                        <li>Zugang zu allen 7 Modulen (AI Literacy + AI Security) ab 1. Juni 2026</li>
                        <li>Unbegrenzte User-Lizenzen für 12 Monate</li>
                        <li>Halbtägiger Remote-Workshop (3 Stunden)</li>
                        <li>EU AI Act Compliance-Zertifikate</li>
                    </ul>
                    <p class="mt-3">
                        Eine Barauszahlung oder Übertragung des Gewinns auf Dritte ist nicht möglich.
                    </p>
                </section>

                <!-- 5. Gewinnerermittlung -->
                <section>
                    <h2 class="mb-3 text-[18px] font-semibold text-white/85">5. Gewinnerermittlung und Benachrichtigung</h2>
                    <p>
                        Der Gewinner wird nach Ablauf des Teilnahmezeitraums per Zufallsziehung
                        unter allen gültigen Teilnehmern ermittelt.
                    </p>
                    <p class="mt-3">
                        Die Benachrichtigung erfolgt per E-Mail an die bei der Teilnahme angegebene Adresse.
                        Der Gewinner muss sich innerhalb von 14 Tagen nach Benachrichtigung zurückmelden.
                        Erfolgt keine Rückmeldung, wird der Gewinn erneut unter den verbleibenden Teilnehmern verlost.
                    </p>
                </section>

                <!-- 6. Datenschutz -->
                <section>
                    <h2 class="mb-3 text-[18px] font-semibold text-white/85">6. Datenschutz</h2>
                    <p>
                        Im Rahmen des Gewinnspiels wird ausschließlich die E-Mail-Adresse erhoben.
                        Rechtsgrundlage ist Art. 6 Abs. 1 lit. b DSGVO (Durchführung des Gewinnspiels).
                    </p>
                    <p class="mt-3">
                        Die Daten werden ausschließlich zur Durchführung des Gewinnspiels und
                        zur Benachrichtigung des Gewinners verwendet. Eine Weitergabe an Dritte
                        erfolgt nicht. Die Daten werden spätestens 30 Tage nach Abschluss des
                        Gewinnspiels gelöscht, sofern keine gesetzliche Aufbewahrungspflicht besteht.
                    </p>
                    <p class="mt-3">
                        Durch die Teilnahme wird keine Einwilligung zu Werbezwecken erteilt.
                        Die vollständige Datenschutzerklärung findest du unter:
                        <a href="https://ausbildung-in-der-it.de/datenschutz" target="_blank" rel="noopener" class="text-[#007aff] underline decoration-[#007aff]/30 underline-offset-2 hover:text-[#007aff]/80">
                            ausbildung-in-der-it.de/datenschutz
                        </a>
                    </p>
                </section>

                <!-- 7. Haftung -->
                <section>
                    <h2 class="mb-3 text-[18px] font-semibold text-white/85">7. Haftung</h2>
                    <p>
                        Der Veranstalter haftet nicht für technische Störungen, die außerhalb
                        seines Einflussbereichs liegen. Der Veranstalter behält sich vor,
                        das Gewinnspiel jederzeit ohne Vorankündigung und ohne Angabe von Gründen
                        zu beenden oder zu ändern, sofern ein wichtiger Grund vorliegt
                        (z.B. technische Probleme, Manipulation, höhere Gewalt).
                    </p>
                    <p class="mt-3">
                        Bei Verdacht auf Manipulation oder Verstoß gegen diese Teilnahmebedingungen
                        behält sich der Veranstalter vor, Teilnehmer auszuschließen.
                    </p>
                </section>

                <!-- 8. Sonstiges -->
                <section>
                    <h2 class="mb-3 text-[18px] font-semibold text-white/85">8. Sonstiges</h2>
                    <p>
                        Der Rechtsweg ist ausgeschlossen. Es gilt deutsches Recht.
                    </p>
                    <p class="mt-3">
                        Das Gewinnspiel steht in keiner Verbindung zur Data Unplugged Veranstaltung
                        oder deren Organisatoren und wird weder von diesen gesponsert noch unterstützt.
                    </p>
                </section>

                <!-- Veranstalter -->
                <section class="border-t border-white/[0.08] pt-8">
                    <p class="text-[13px] text-white/30">
                        Noel Lang &middot; Potsdam &middot; Stand: März 2026
                    </p>
                </section>
            </div>
        </div>
    </div>
</template>
