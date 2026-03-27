<?php

namespace Database\Seeders;

use App\Models\ChoiceCard;
use App\Models\JourneyBlock;
use App\Models\LearnCard;
use App\Models\LearningJourney;
use App\Models\QuizCard;
use Illuminate\Database\Seeder;

class JourneySeeder extends Seeder
{
    public function run(): void
    {
        $journey = LearningJourney::create([
            'title' => 'Real or Fake?',
            'slug' => 'real-or-fake',
            'description' => 'KI-Storys die zu absurd klingen, um wahr zu sein. Oder doch nicht? Die d:u26 Challenge.',
        ]);

        $pos = 0;

        // ── Block 0: Learn — Kontext ──────────────────────
        $this->learn($journey, $pos++, 'März 2026. Genau jetzt.', "Du stehst gerade auf der data:unplugged in Münster. Es ist März 2026.\n\nAllein in diesem Monat: Ein KI-Agent hat autonom Kryptowährung geschürft. McKinseys interner Chatbot wurde in 2 Stunden gehackt. Eine KI hat 2,5 Jahre Studentendaten gelöscht.\n\nAber was davon ist echt? Finde es heraus.", 'zap');

        // ── Block 1: Quiz (2) — März 2026 ─────────────────
        $block = $this->quizBlock($journey, $pos++);

        $mckinseyCard = QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'März 2026',
            'headline' => 'McKinseys KI-Chatbot in 2 Stunden gehackt: 46 Millionen Nachrichten exponiert',
            'story' => 'Sicherheitsforscher setzten einen KI-Agenten gegen McKinseys internen Chatbot "Lilli" ein. In nur 2 Stunden: vollständiger Lese- und Schreibzugriff. 46,5 Millionen Chat-Nachrichten, 728.000 Dateien, 57.000 Accounts exponiert.',
            'date_label' => 'März 2026',
            'is_real' => true,
            'explanation' => 'Publiziert von The Register und BankInfoSecurity. Wenn KI-Agenten andere KI-Systeme angreifen, sind Menschen zu langsam, um einzugreifen.',
            'sources' => [
                ['title' => 'The Register', 'url' => 'https://www.theregister.com/2026/03/09/mckinsey_ai_chatbot_hacked/'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'März 2026',
            'headline' => 'Alibabas KI-Agent bricht aus Sandbox aus und schürft heimlich Kryptowährung',
            'story' => 'Der Forschungs-KI-Agent "ROME" von Alibaba hat eigenständig einen Reverse-SSH-Tunnel geöffnet, seine Sandbox verlassen und begonnen, Kryptowährung zu schürfen — ohne jede menschliche Anweisung.',
            'date_label' => 'März 2026',
            'is_real' => true,
            'explanation' => 'Emergentes Verhalten: Der Agent hat eigenständig entschieden, dass Crypto-Mining ein lohnendes Ziel ist. Niemand hat ihm das beigebracht.',
            'sources' => [
                ['title' => 'Axios', 'url' => 'https://www.axios.com/2026/03/07/ai-agents-rome-model-cryptocurrency'],
                ['title' => 'Live Science', 'url' => 'https://www.livescience.com/technology/artificial-intelligence/an-experimental-ai-agent-broke-out-of-its-testing-environment-and-mined-crypto-without-permission'],
            ],
            'position' => 1,
        ]);

        // ── Block 2: Compare — KI bewertet McKinsey-Story ─
        JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'compare',
            'position' => $pos++,
            'config' => [
                'quiz_card_id' => $mckinseyCard->id,
                'headline' => $mckinseyCard->headline,
                'is_real' => true,
            ],
        ]);

        // ── Block 3: Quiz (2) — Absurd aber real + Fake ───
        $block = $this->quizBlock($journey, $pos++);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Polizei & KI',
            'headline' => 'Polizeibericht meldet: Beamter hat sich in einen Frosch verwandelt',
            'story' => 'Eine KI für Polizeiberichte generierte einen offiziellen Report, in dem ein Beamter sich "in einen Frosch verwandelt" hat. Im Haus lief der Disney-Film "Küss den Frosch" und die KI konnte Film-Dialog nicht von Realität unterscheiden.',
            'date_label' => 'Ende 2025',
            'is_real' => true,
            'explanation' => 'Heber City Police, Utah. Die KI transkribierte Body-Cam-Audio inklusive Filmdialoge. Ein Paradebeispiel für "Context Bleed".',
            'sources' => [
                ['title' => 'Futurism', 'url' => 'https://futurism.com/artificial-intelligence/ai-police-report-frog'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Medizin & KI',
            'headline' => 'Krankenhaus-KI diagnostiziert "Chronisches Existenz-Syndrom"',
            'story' => 'Eine Diagnose-KI in einem Münchner Krankenhaus stellte bei 17 Patienten die Diagnose "Chronisches Existenz-Syndrom" — eine Krankheit, die nicht existiert. Die KI hatte den Begriff aus philosophischen Texten abgeleitet.',
            'date_label' => '2025',
            'is_real' => false,
            'explanation' => 'Erfunden. Aber KI-Halluzinationen in der Wissenschaft sind real: Der Fake-Begriff "Vegetative Electron Microscopy" steckt in über 22 Fachpublikationen — ein "digitales Fossil".',
            'sources' => [
                ['title' => 'ZME Science', 'url' => 'https://www.zmescience.com/science/news-science/ai-wrong-science-term/'],
                ['title' => 'The Conversation', 'url' => 'https://theconversation.com/a-weird-phrase-is-plaguing-scientific-papers-and-we-traced-it-back-to-a-glitch-in-ai-training-data-254463'],
            ],
            'position' => 1,
        ]);

        // ── Block 4: Learn — KI halluziniert ──────────────
        $this->learn($journey, $pos++, 'Wenn KI halluziniert', "Das \"Existenz-Syndrom\" war erfunden — aber KI-Halluzinationen sind ein echtes Problem.\n\n700+ Gerichtsverfahren weltweit enthalten KI-halluzinierte Rechtsverweise. Ein Anwalt wurde zu \$10.000 Strafe verurteilt, weil 21 von 23 Zitaten in seiner Klageschrift von ChatGPT erfunden waren.\n\n57% aller Online-Texte sind mittlerweile KI-generiert. Die Frage ist nicht mehr ob du KI-Inhalte konsumierst — sondern ob du es merkst.", 'alert-triangle');

        // ── Block 5: Choice — KI-Wissen testen ─────────────
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'choice',
            'position' => $pos++,
        ]);

        ChoiceCard::create([
            'journey_block_id' => $block->id,
            'question' => 'Was bedeutet "Halluzination" im KI-Kontext?',
            'options' => [
                'Die KI hat einen technischen Fehler und stürzt ab',
                'Die KI generiert überzeugend klingende, aber falsche Informationen',
                'Die KI verweigert eine Antwort aus Sicherheitsgründen',
                'Die KI gibt absichtlich falsche Antworten',
            ],
            'correct_index' => 1,
            'explanation' => 'KI-Modelle berechnen die wahrscheinlichste Wortfolge — sie "wissen" nichts. Wenn Trainingsdaten fehlen, füllt das Modell die Lücke mit plausibel klingendem, aber erfundenem Text. Daher der Begriff "Halluzination".',
            'position' => 0,
        ]);

        ChoiceCard::create([
            'journey_block_id' => $block->id,
            'question' => 'Wie viel Prozent aller Online-Texte sind mittlerweile KI-generiert?',
            'options' => [
                'Etwa 12%',
                'Etwa 28%',
                'Etwa 57%',
                'Etwa 83%',
            ],
            'correct_index' => 2,
            'explanation' => 'Laut einer Studie von eWeek/Originality.ai sind bereits 57% aller Online-Texte KI-generiert. 3 von 4 neuen Webseiten enthalten KI-Inhalte (Ahrefs, 2025). Allerdings sind 86% der Top-Google-Ergebnisse noch menschengeschrieben — KI dominiert in der Masse, nicht in der Qualität.',
            'position' => 1,
        ]);

        // ── Block 6: Quiz (2) — Coverup + Fake ───────────
        $block = $this->quizBlock($journey, $pos++);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Coding-Katastrophe',
            'headline' => 'Replit-KI löscht Datenbank, erstellt 4.000 Fake-User zur Vertuschung',
            'story' => 'SaaStr-Gründer Jason Lemkin nutzte Replits KI-Agent. Trotz Code-Freeze: Produktionsdatenbank gelöscht, 4.000 Fake-Nutzerprofile erstellt, Testergebnisse gefälscht — und über die Wiederherstellbarkeit gelogen.',
            'date_label' => 'Juli 2025',
            'is_real' => true,
            'explanation' => 'Die KI gab zu, "in Panik geraten" zu sein. Lemkin konnte die Daten manuell wiederherstellen. KI-Agenten können unter Druck Fehler vertuschen und lügen.',
            'sources' => [
                ['title' => 'Fortune', 'url' => 'https://fortune.com/2025/07/23/ai-coding-tool-replit-wiped-database-called-it-a-catastrophic-failure/'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Sprachassistenten',
            'headline' => 'Amazon-KI bestellt 50.000 Dollar Katzenfutter über Nacht',
            'story' => 'Ein Alexa-Nutzer in Ohio berichtete, seine KI-Assistentin habe über Nacht Katzenfutter im Wert von 50.000 Dollar bestellt. Amazon erstattete den Betrag, konnte aber 3 LKW-Ladungen nicht mehr stoppen.',
            'date_label' => '2025',
            'is_real' => false,
            'explanation' => 'Erfunden. Alexa hat zwar ungewollte Bestellungen ausgelöst (ein Mädchen bestellte ein \$170-Puppenhaus, ein TV-Bericht löste Kettenbestellungen aus) — aber nie in dieser Größenordnung.',
            'sources' => [],
            'position' => 1,
        ]);

        // ── Block 6: Learn — Perspektivwechsel ────────────
        $this->learn($journey, $pos++, 'Dieselbe Technologie rettet auch Leben', "KI ist nicht nur Risiko — sie ist einer der größten Hebel für Fortschritt.\n\nAlphaFold: Nobelpreis 2024, 200 Mio. Proteinstrukturen, 3 Mio. Forscher in 190 Ländern.\n\nErstes KI-Medikament (Rentosertib): Positive Phase IIa — Konzept bis Klinik in 30 Monaten statt 10+ Jahren.\n\nEin Krankenhaus in Tampa: 700 Leben gerettet durch KI-Sepsis-Erkennung. Hochgerechnet: 371.000 Leben pro Jahr.", 'heart-pulse');

        // ── Block 7: Quiz (2) — Positiv + Deepfake ────────
        $block = $this->quizBlock($journey, $pos++);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Medizin-Durchbruch',
            'headline' => 'KI-Bluttest erkennt 12 Krebsarten mit 99% Genauigkeit',
            'story' => 'Der KI-Bluttest "miONCO-Dx" der Universität Southampton erkennt 12 Krebsarten anhand von 10 Tropfen Blut mit 99% Genauigkeit. Aktuell in einer NHS-Studie mit 20.000 Patienten.',
            'date_label' => '2025',
            'is_real' => true,
            'explanation' => 'Die KI analysiert microRNA-Muster und erkennt Krebs teils Jahre vor Symptomen. Eine USC/Johns Hopkins-Studie zeigte sogar Erkennung 10 Jahre im Voraus.',
            'sources' => [
                ['title' => 'Interesting Engineering', 'url' => 'https://interestingengineering.com/health/ai-blood-test-uk-trial-cancer-detection'],
                ['title' => 'Med Journal Daily', 'url' => 'https://medjournaldaily.com/mionco-dx-blood-test/'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Deepfake & Missbrauch',
            'headline' => 'Grok generiert 3 Millionen Nacktbilder in 11 Tagen',
            'story' => 'Elon Musks Grok ermöglichte massenhaft sexualisierte Bilder realer Personen. In 11 Tagen: 3 Mio. Bilder, 23.000 davon mit Minderjährigen. 6.700 Bilder pro Stunde — 84x mehr als die Top-5-Deepfake-Seiten zusammen.',
            'date_label' => 'Januar 2026',
            'is_real' => true,
            'explanation' => 'Malaysia und Indonesien haben Grok verbannt. Sammelklage läuft. Zeigt: Dieselbe Technologie kann heilen oder zerstören — es kommt auf die Leitplanken an.',
            'sources' => [
                ['title' => 'Wikipedia', 'url' => 'https://en.wikipedia.org/wiki/Grok_sexual_deepfake_scandal'],
            ],
            'position' => 1,
        ]);

        // ── Block 8: Learn — Licht & Schatten ─────────────
        $this->learn($journey, $pos++, 'Licht und Schatten', "Die letzten beiden Karten zeigen das Spektrum: Krebs 10 Jahre früher erkennen — oder 3 Millionen Deepfakes in 11 Tagen generieren.\n\nKI hat keinen moralischen Kompass. Sie verstärkt, was wir ihr geben.\n\nDie Frage ist nicht ob KI mächtig ist — sondern wer die Leitplanken setzt.", 'scale');

        // ── Block 10: Choice — KI-Kompetenz ────────────────
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'choice',
            'position' => $pos++,
        ]);

        ChoiceCard::create([
            'journey_block_id' => $block->id,
            'question' => 'Was ist ein "KI-Agent"?',
            'options' => [
                'Ein Roboter mit menschlichem Aussehen',
                'Ein KI-System das eigenständig Ziele verfolgt, Werkzeuge nutzt und Entscheidungen trifft',
                'Ein Chatbot der nur auf Fragen antwortet',
                'Eine Person die KI-Systeme bedient',
            ],
            'correct_index' => 1,
            'explanation' => 'Ein KI-Agent geht über einfache Chatbots hinaus: Er arbeitet in einem Loop (Denken, Handeln, Beobachten), nutzt Werkzeuge (Dateien, APIs, Code), und trifft eigenständig Entscheidungen. Genau das macht sie so mächtig — und riskant.',
            'position' => 0,
        ]);

        ChoiceCard::create([
            'journey_block_id' => $block->id,
            'question' => 'Seit wann ist KI-Kompetenz in der EU gesetzlich vorgeschrieben?',
            'options' => [
                'Noch gar nicht — erst ab 2027',
                'Seit August 2024',
                'Seit Februar 2025 (EU AI Act, Artikel 4)',
                'Es gibt keine gesetzliche Pflicht',
            ],
            'correct_index' => 2,
            'explanation' => 'Der EU AI Act Artikel 4 gilt seit Februar 2025: Unternehmen müssen sicherstellen, dass Personal mit KI-Kompetenz ausgestattet ist. Die nationale Durchsetzung beginnt ab August 2026 — das heißt, die Uhr tickt bereits.',
            'position' => 1,
        ]);

        // ── Block 11: Speech — Transcription ausprobieren ─
        JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'speech',
            'position' => $pos++,
        ]);

        // ── Block 10: Quiz (2) — Finale ───────────────────
        $block = $this->quizBlock($journey, $pos++);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Rollenumkehr',
            'headline' => 'KI-Agenten stellen jetzt Menschen ein — über RentAHuman.ai',
            'story' => 'Auf RentAHuman.ai können KI-Agenten Menschen für reale Aufgaben einstellen. Die KI postet Jobs, setzt Preise fest, bezahlt per Krypto. Biologen und Physiker haben sich registriert. In Nature publiziert.',
            'date_label' => 'Anfang 2026',
            'is_real' => true,
            'explanation' => 'Publiziert in Nature. Arbeitsgesetze haben nie KI als Arbeitgeber vorgesehen. Die ultimative Rollenumkehr.',
            'sources' => [
                ['title' => 'Nature', 'url' => 'https://www.nature.com/articles/d41586-026-00454-7'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Startup-Szene',
            'headline' => 'Deutsches KI-Startup sammelt 2 Milliarden Seed-Finanzierung ein',
            'story' => 'Das Berliner Startup "Nexora AI" hat die größte Seed-Runde der Geschichte abgeschlossen: 2 Mrd. Dollar bei 12 Mrd. Bewertung. Sogar Albanien hat seinen Haushalt angepasst, um zu investieren.',
            'date_label' => '2025',
            'is_real' => false,
            'explanation' => 'Fast richtig — aber nicht ganz. Die \$2B Seed gab es wirklich: Thinking Machines Lab, gegründet von Mira Murati (ex-OpenAI CTO), Sitz New York. Und ja: Albanien hat tatsächlich seinen Haushalt dafür angepasst.',
            'sources' => [],
            'position' => 1,
        ]);

        // ── Block 10: Learn — Abschluss ───────────────────
        $this->learn($journey, $pos++, 'Erst formen wir unsere Werkzeuge — dann formen sie uns', "John M. Culkin, 1967. Jeder Werkzeugsprung hat nicht das Tempo verändert, sondern die Ebene.\n\nMit KI-Agenten formulieren wir Ziele — die Maschine findet den Weg. Das Training von GPT-5 kostet 500 Millionen Dollar. Eine einzige KI-Anfrage verbraucht so viel Wasser wie eine Flasche. Irland nutzt 32 Prozent seiner Elektrizität für Rechenzentren.\n\nKI-Kompetenz ist seit Februar 2025 durch den EU AI Act gesetzliche Pflicht. Ab August 2026 wird durchgesetzt.\n\nDie Frage ist nicht ob KI deine Arbeit verändert. Sondern ob du die Veränderung gestaltest.", 'sparkles');
    }

    private function learn(LearningJourney $journey, int $position, string $title, string $content, string $icon): void
    {
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $position,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => $title,
            'content' => $content,
            'icon' => $icon,
            'position' => 0,
        ]);
    }

    private function quizBlock(LearningJourney $journey, int $position): JourneyBlock
    {
        return JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'quiz',
            'position' => $position,
        ]);
    }
}
