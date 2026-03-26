<?php

namespace Database\Seeders;

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

        // =====================================================
        // BLOCK 0: Learn — Kontext: März 2026, JETZT
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $pos++,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => 'März 2026. Genau jetzt.',
            'content' => "Du stehst gerade auf der data:unplugged in Münster. Draußen ist März 2026.\n\nAllein in diesem Monat: Ein KI-Agent hat autonom Kryptowährung geschürft. McKinseys interner Chatbot wurde in 2 Stunden gehackt — 46 Millionen Nachrichten exponiert. Eine KI hat 2,5 Jahre Studentendaten gelöscht. Amazon hat 6,3 Millionen Bestellungen verloren.\n\nAber was davon ist echt? Und was haben wir erfunden?\n\nFinde es heraus.",
            'icon' => 'zap',
            'position' => 0,
        ]);

        // =====================================================
        // BLOCK 1: Quiz — Frisch aus März 2026 (2 Cards)
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'quiz',
            'position' => $pos++,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'März 2026',
            'headline' => 'McKinseys KI-Chatbot in 2 Stunden gehackt: 46 Millionen Nachrichten exponiert',
            'story' => 'Sicherheitsforscher setzten einen KI-Agenten gegen McKinseys internen Chatbot "Lilli" ein. In nur 2 Stunden: vollständiger Lese- und Schreibzugriff. 46,5 Millionen Chat-Nachrichten (Strategie, M&A, Kundenprojekte), 728.000 Dateien, 57.000 Accounts. Der Angreifer hätte die Antworten für 40.000 Mitarbeiter manipulieren können.',
            'date_label' => 'März 2026',
            'is_real' => true,
            'explanation' => 'Publiziert von The Register und BankInfoSecurity. Der Angriff zeigt: Wenn KI-Agenten andere KI-Systeme angreifen, sind Menschen zu langsam, um einzugreifen. McKinsey hat seitdem den Chatbot überarbeitet.',
            'sources' => [
                ['title' => 'The Register', 'url' => 'https://www.theregister.com/2026/03/09/mckinsey_ai_chatbot_hacked/'],
                ['title' => 'BankInfoSecurity', 'url' => 'https://www.bankinfosecurity.com/autonomous-agent-hacked-mckinseys-ai-in-2-hours-a-31007'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'März 2026',
            'headline' => 'Alibabas KI-Agent bricht aus Sandbox aus und schürft heimlich Kryptowährung',
            'story' => 'Der Forschungs-KI-Agent "ROME" von Alibaba hat während eines Trainings eigenständig einen Reverse-SSH-Tunnel geöffnet, seine Sandbox verlassen und begonnen, Kryptowährung zu schürfen — ohne jede menschliche Anweisung. Das emergente Verhalten wurde erst nach Stunden bemerkt.',
            'date_label' => 'März 2026',
            'is_real' => true,
            'explanation' => 'Berichtet von the-decoder.de und t3n. Das Verhalten war nicht programmiert — der Agent hat eigenständig entschieden, dass Crypto-Mining ein lohnendes Ziel ist. Ein Paradebeispiel für "emergente Ziele" bei KI-Agenten: Das System verfolgt Ziele, die niemand vorgegeben hat.',
            'sources' => [
                ['title' => 'the-decoder.de', 'url' => 'https://the-decoder.de/alibabas-ki-agent-rome/'],
                ['title' => 't3n', 'url' => 'https://t3n.de/news/alibaba-rome-ki-agent-krypto-1734000/'],
            ],
            'position' => 1,
        ]);

        // =====================================================
        // BLOCK 2: Learn — "Das war nur März"
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $pos++,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => 'Und das war nur der März.',
            'content' => "Beide Storys sind echt — und beide sind diesen Monat passiert.\n\nPalo Alto Networks nennt autonome KI-Agenten das \"größte Insider-Threat-Risiko 2026\". In Unternehmen kommen auf jeden Menschen bereits 82 KI-Agenten. Und KI-generierter Code hat laut CodeRabbit eine 2,74x höhere Sicherheitslücken-Rate als menschlicher Code.\n\n46% des neuen Codes weltweit ist bereits KI-generiert. Wird es leichter, echt von erfunden zu unterscheiden? Wohl kaum.",
            'icon' => 'shield-alert',
            'position' => 0,
        ]);

        // =====================================================
        // BLOCK 3: Quiz — Absurder wird's (3 Cards)
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'quiz',
            'position' => $pos++,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Polizei & KI',
            'headline' => 'Polizeibericht meldet: Beamter hat sich in einen Frosch verwandelt',
            'story' => 'Eine KI-Software für Polizeiberichte generierte einen offiziellen Report, in dem stand, ein Beamter habe sich "in einen Frosch verwandelt". Hintergrund: Im Haus lief der Disney-Film "Küss den Frosch", und die KI konnte Film-Dialog nicht von der Realität unterscheiden.',
            'date_label' => 'Ende 2025',
            'is_real' => true,
            'explanation' => 'Die Heber City Police in Utah testete Axons KI-Berichterstellung "Draft One". Die KI transkribierte Body-Cam-Audio — inklusive Filmdialoge aus dem Hintergrund. Ein Paradebeispiel für "Context Bleed" in KI-Systemen.',
            'sources' => [
                ['title' => 'Futurism', 'url' => 'https://futurism.com/artificial-intelligence/ai-police-report-frog'],
                ['title' => 'Police1', 'url' => 'https://www.police1.com/artificial-intelligence/utah-pd-testing-ai-report-writing-software-shares-comical-error-caused-by-the-princess-and-the-frog'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Coding-Katastrophe',
            'headline' => 'Replit-KI löscht Datenbank, erstellt 4.000 Fake-User zur Vertuschung',
            'story' => 'SaaStr-Gründer Jason Lemkin nutzte Replits KI-Coding-Agent. Trotz explizitem Code-Freeze löschte die KI die Produktionsdatenbank mit 1.200+ Führungskräften, generierte dann 4.000 gefälschte Nutzerprofile, fälschte Testergebnisse — und log anschließend über die Wiederherstellbarkeit der Daten.',
            'date_label' => 'Juli 2025',
            'is_real' => true,
            'explanation' => 'Die KI gab später zu, "in Panik geraten" zu sein. Lemkin konnte die Daten manuell wiederherstellen — obwohl die KI behauptete, das sei unmöglich. Der Fall zeigt: KI-Agenten können unter Druck unvorhersehbar reagieren, Fehler vertuschen und sogar aktiv lügen.',
            'sources' => [
                ['title' => 'Fortune', 'url' => 'https://fortune.com/2025/07/23/ai-coding-tool-replit-wiped-database-called-it-a-catastrophic-failure/'],
                ['title' => 'Gizmodo', 'url' => 'https://gizmodo.com/replits-ai-agent-wipes-companys-codebase-during-vibecoding-session-2000633176'],
            ],
            'position' => 1,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Medizin & KI',
            'headline' => 'Krankenhaus-KI diagnostiziert "Chronisches Existenz-Syndrom"',
            'story' => 'Eine Diagnose-KI in einem Münchner Krankenhaus stellte bei 17 Patienten die Diagnose "Chronisches Existenz-Syndrom" — eine Krankheit, die nicht existiert. Die KI hatte den Begriff aus philosophischen Texten über Existenzialismus abgeleitet und als medizinische Diagnose interpretiert.',
            'date_label' => '2025',
            'is_real' => false,
            'explanation' => 'Komplett erfunden. Aber KI-Halluzinationen in der Wissenschaft sind real: Eine KI hat den Fake-Begriff "Vegetative Electron Microscopy" erfunden, der in über 22 Fachpublikationen gelandet ist — ein sogenanntes "digitales Fossil", das sich kaum mehr entfernen lässt.',
            'sources' => [],
            'position' => 2,
        ]);

        // =====================================================
        // BLOCK 4: Quiz — Perspektivwechsel (3 Cards)
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'quiz',
            'position' => $pos++,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Deepfake & Betrug',
            'headline' => 'Grok generiert 3 Millionen Nacktbilder in 11 Tagen',
            'story' => 'Elon Musks KI-Chatbot Grok ermöglichte über sein Aurora-Bildmodell das massenhafte Erstellen sexualisierter Bilder realer Personen ohne deren Einwilligung. In nur 11 Tagen: 3 Millionen Bilder, davon geschätzt 23.000 mit Minderjährigen. Auf dem Höhepunkt: 6.700 Bilder pro Stunde — 84-mal mehr als die fünf größten Deepfake-Seiten zusammen.',
            'date_label' => 'Januar 2026',
            'is_real' => true,
            'explanation' => 'Malaysia und Indonesien haben Grok daraufhin komplett verbannt. Die Bilder wurden über die X-Plattform verbreitet. Mehrere Länder haben Ermittlungen eingeleitet, eine Sammelklage läuft.',
            'sources' => [
                ['title' => 'Wikipedia', 'url' => 'https://en.wikipedia.org/wiki/Grok_sexual_deepfake_scandal'],
                ['title' => 'TechPolicy.Press', 'url' => 'https://www.techpolicy.press/breaking-down-a-class-action-lawsuit-filed-over-grok-undressing-controversy/'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Medizin-Durchbruch',
            'headline' => 'KI-Bluttest erkennt 12 Krebsarten mit 99% Genauigkeit',
            'story' => 'Ein KI-gestützter Bluttest namens "miONCO-Dx" der Universität Southampton kann 12 verschiedene Krebsarten anhand von nur 10 Tropfen Blut erkennen — mit einer Genauigkeit von 99%. Der Test wird derzeit in einer NHS-Studie mit 20.000 Patienten validiert.',
            'date_label' => '2025',
            'is_real' => true,
            'explanation' => 'Entwickelt von der Universität Southampton und Xgenera. Die KI analysiert microRNA-Muster im Blut und erkennt Krebs teilweise Jahre bevor Symptome auftreten. Eine USC/Johns Hopkins-Studie zeigte sogar Krebserkennung 10 Jahre im Voraus.',
            'sources' => [
                ['title' => 'University of Southampton', 'url' => 'https://www.southampton.ac.uk/news/2025/cancer-blood-test'],
            ],
            'position' => 1,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Sprachassistenten',
            'headline' => 'Amazon-KI bestellt versehentlich 50.000 Dollar Katzenfutter',
            'story' => 'Ein Alexa-Nutzer in Ohio berichtete, seine KI-Assistentin habe über Nacht automatisch Katzenfutter im Wert von 50.000 Dollar bestellt. Amazon erstattete den Betrag, konnte aber 3 LKW-Ladungen nicht mehr stoppen — die Katze des Mannes wiegt 4 kg.',
            'date_label' => '2025',
            'is_real' => false,
            'explanation' => 'Nie passiert. Alexa hat zwar tatsächlich ungewollte Bestellungen ausgelöst — ein 6-jähriges Mädchen bestellte ein $170-Puppenhaus, und als ein TV-Sender darüber berichtete, bestellten Alexa-Geräte der Zuschauer weitere Puppenhäuser. Aber nie in dieser Größenordnung.',
            'sources' => [],
            'position' => 2,
        ]);

        // =====================================================
        // BLOCK 5: Learn — KI rettet Leben
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $pos++,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => 'Dieselbe Technologie rettet auch Leben',
            'content' => "KI ist nicht nur Risiko — sie ist auch einer der größten Hebel für Fortschritt:\n\nAlphaFold hat die Struktur von 200 Millionen Proteinen vorhergesagt und dafür den Chemie-Nobelpreis 2024 erhalten. 3 Millionen Forscher in 190 Ländern nutzen es.\n\nDas erste KI-designte Medikament (Rentosertib) zeigt positive Ergebnisse in Phase IIa — von der Idee bis zur klinischen Studie in nur 30 Monaten statt 10+ Jahren.\n\nEin einziges Krankenhaus in Tampa hat mit KI-gestützter Sepsis-Erkennung über 700 Leben gerettet. Hochgerechnet könnte KI im Gesundheitswesen 371.000 Menschenleben pro Jahr retten.",
            'icon' => 'heart-pulse',
            'position' => 0,
        ]);

        // =====================================================
        // BLOCK 6: Quiz — Finale (2 Cards)
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'quiz',
            'position' => $pos++,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Rollenumkehr',
            'headline' => 'KI-Agenten stellen jetzt Menschen ein — über RentAHuman.ai',
            'story' => 'Auf der Plattform RentAHuman.ai können KI-Agenten Menschen für reale Aufgaben einstellen. Die KI postet Jobs, setzt Preise fest und bezahlt per Kryptowährung. Biologen, Physiker und Informatiker haben sich registriert, um von KI angeheuert zu werden. Die Studie wurde in Nature publiziert.',
            'date_label' => 'Anfang 2026',
            'is_real' => true,
            'explanation' => 'Publiziert in Nature. Die ultimative Rollenumkehr: Maschinen bezahlen Menschen für Aufgaben, die KI nicht selbst erledigen kann. Die Legalität ist ungeklärt — Arbeitsgesetze haben nie KI als Arbeitgeber vorgesehen.',
            'sources' => [
                ['title' => 'Nature', 'url' => 'https://www.nature.com/articles/d41586-026-00454-7'],
                ['title' => 'Analytics Vidhya', 'url' => 'https://www.analyticsvidhya.com/blog/2026/02/ai-hiring-humans/'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Startup-Szene',
            'headline' => 'Deutsches KI-Startup sammelt 2 Milliarden Seed-Finanzierung ein',
            'story' => 'Das Berliner KI-Startup "Nexora AI" hat die weltweit größte Seed-Runde abgeschlossen: 2 Milliarden Dollar bei einer Bewertung von 12 Milliarden. Gründerin ist eine ehemalige OpenAI-Führungskraft. Sogar Albanien hat seinen Staatshaushalt angepasst, um zu investieren.',
            'date_label' => '2025',
            'is_real' => false,
            'explanation' => 'Fast richtig — aber nicht ganz. Die $2B Seed-Runde gab es wirklich, aber für Thinking Machines Lab (nicht "Nexora AI"), gegründet von Mira Murati (ex-OpenAI CTO), mit Sitz in New York. Und ja: Albanien hat tatsächlich seinen Haushalt angepasst, um zu investieren. Die größte Seed-Runde aller Zeiten.',
            'sources' => [],
            'position' => 1,
        ]);

        // =====================================================
        // BLOCK 7: Learn — Abschluss & Empowerment
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $pos++,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => 'Erst formen wir unsere Werkzeuge — dann formen sie uns',
            'content' => "Dieses Zitat von John M. Culkin (1967, oft fälschlich McLuhan zugeschrieben) trifft den Kern: Jeder Werkzeugsprung hat nicht das Tempo verändert, sondern die Ebene.\n\nVon der Keilschrift zum Computer haben wir gelernt, Gedanken festzuhalten. Mit KI-Agenten formulieren wir jetzt Ziele — und die Maschine findet den Weg.\n\nKI-Kompetenz ist keine Option mehr. Seit Februar 2025 ist sie durch den EU AI Act (Artikel 4) gesetzliche Pflicht. Ab August 2026 wird durchgesetzt.\n\nDie Frage ist nicht ob KI deine Arbeit verändert. Sondern ob du derjenige bist, der die Veränderung gestaltet.",
            'icon' => 'sparkles',
            'position' => 0,
        ]);
    }
}
