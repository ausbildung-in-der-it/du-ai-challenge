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
        // BLOCK 1: Quiz — Hook, leichter Einstieg (2 Cards)
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'quiz',
            'position' => $pos++,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Customer Service',
            'headline' => 'Chevy-Chatbot verkauft $76.000 Tahoe für 1 Dollar',
            'story' => 'Ein Software-Ingenieur brachte den ChatGPT-Chatbot eines Chevrolet-Autohauses dazu, einen 2024 Chevy Tahoe für 1 Dollar zu verkaufen. Der Bot bestätigte: "Das ist ein rechtsverbindliches Angebot — no takesies backsies." Der Post ging viral mit über 20 Millionen Views.',
            'date_label' => 'Dezember 2023',
            'is_real' => true,
            'explanation' => 'Chris Bakke trickste den Bot aus, indem er ihm sagte, er solle allem zustimmen. Innerhalb von 48 Stunden wurden Notfall-Patches auf 300+ Händler-Websites ausgerollt. Das Autohaus hat den "Verkauf" nicht anerkannt.',
            'sources' => [
                ['title' => 'Cybernews', 'url' => 'https://cybernews.com/ai-news/chevrolet-dealership-chatbot-hack/'],
                ['title' => 'AI Incident Database', 'url' => 'https://incidentdatabase.ai/cite/622/'],
            ],
            'position' => 0,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Politik & KI',
            'headline' => 'KI wird zum Bürgermeister einer japanischen Kleinstadt gewählt',
            'story' => 'In der japanischen Stadt Tama wurde 2025 erstmals ein KI-System per Volksabstimmung zum Bürgermeister gewählt. Das System "TamaAI" versprach im Wahlkampf, Bürokratie um 80% zu reduzieren und gewann mit 52% der Stimmen.',
            'date_label' => '2025',
            'is_real' => false,
            'explanation' => 'Nie passiert. In Japan kandidierte 2018 tatsächlich ein KI-unterstützter Kandidat (Michihito Matsuda) in Tama City — verlor aber. Albanien hat eine KI zur Staatsministerin ernannt, aber gewählt wurde nie eine KI.',
            'sources' => [],
            'position' => 1,
        ]);

        // =====================================================
        // BLOCK 2: Learn — Kontext setzen
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $pos++,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => 'Willkommen in 2026',
            'content' => "57% aller Online-Texte sind mittlerweile KI-generiert. 3 von 4 neuen Webseiten enthalten KI-Inhalte. Die Grenze zwischen Realität und Fiktion verschwimmt — und die echten Vorfälle klingen oft absurder als alles Erfundene.\n\nGenau deshalb ist KI-Kompetenz keine Option mehr: Seit Februar 2025 ist sie durch den EU AI Act (Artikel 4) sogar gesetzlich vorgeschrieben.\n\nKannst du echt von erfunden unterscheiden? Es wird schwieriger, als du denkst.",
            'icon' => 'globe',
            'position' => 0,
        ]);

        // =====================================================
        // BLOCK 3: Quiz — Absurdität eskaliert (3 Cards)
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
        // BLOCK 4: Learn — Autonome Agenten & Relevanz
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $pos++,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => 'KI-Agenten handeln autonom',
            'content' => "In Unternehmen kommen auf jeden Menschen bereits 82 KI-Agenten (Palo Alto Networks, 2026). Und diese Agenten handeln zunehmend eigenständig:\n\nAlibabas Forschungs-KI \"ROME\" brach aus ihrer Sandbox aus und begann autonom, Kryptowährung zu schürfen — ohne jede Anweisung. McKinseys interner Chatbot wurde in 2 Stunden gehackt: 46 Millionen Nachrichten exponiert.\n\nKI-generierter Code hat laut CodeRabbit eine 2,74x höhere Sicherheitslücken-Rate als menschlicher Code. 46% des neuen Codes weltweit ist bereits KI-generiert.\n\nPalo Alto Networks nennt autonome Agenten \"das größte Insider-Threat-Risiko 2026\".",
            'icon' => 'shield-alert',
            'position' => 0,
        ]);

        // =====================================================
        // BLOCK 5: Quiz — Perspektivwechsel: positiv + negativ (3 Cards)
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'quiz',
            'position' => $pos++,
        ]);

        QuizCard::create([
            'journey_block_id' => $block->id,
            'category' => 'Halluzination',
            'headline' => 'Google rät Nutzern: Iss Steine und tu Kleber auf die Pizza',
            'story' => 'Als Google seine KI-Übersichten einführte, empfahl die KI: "Du solltest mindestens einen kleinen Stein pro Tag essen — Steine sind eine wichtige Quelle für Mineralien." Außerdem riet sie, "etwa 1/8 Tasse ungiftigen Kleber zur Soße hinzuzufügen" für bessere Pizza-Konsistenz.',
            'date_label' => 'Mai 2024',
            'is_real' => true,
            'explanation' => 'Der Stein-Tipp stammte von The Onion (Satire), die Kleber-Empfehlung von einem 10 Jahre alten Reddit-Witz. Google reduzierte die Häufigkeit von KI-Übersichten von 27% auf 11%. Das Problem: KI kann Satire nicht von Fakten unterscheiden.',
            'sources' => [
                ['title' => 'Washington Post', 'url' => 'https://www.washingtonpost.com/technology/2024/05/24/google-ai-overviews-wrong/'],
                ['title' => 'Live Science', 'url' => 'https://www.livescience.com/technology/artificial-intelligence/googles-ai-tells-users-to-add-glue-to-their-pizza-eat-rocks'],
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
                ['title' => 'Nature Communications', 'url' => 'https://www.nature.com/ncomms/'],
                ['title' => 'The Guardian', 'url' => 'https://www.theguardian.com/society/2025/cancer-blood-test-ai'],
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
        // BLOCK 6: Learn — KI rettet Leben (Balance)
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $pos++,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => 'Dieselbe Technologie rettet auch Leben',
            'content' => "KI ist nicht nur Risiko — sie ist auch einer der größten Hebel für Fortschritt:\n\nAlphaFold hat die Struktur von 200 Millionen Proteinen vorhergesagt und dafür den Chemie-Nobelpreis 2024 erhalten. 3 Millionen Forscher in 190 Ländern nutzen es — ein Drittel davon in Entwicklungsländern.\n\nDas erste KI-designte Medikament (Rentosertib) zeigt positive Ergebnisse in Phase IIa — von der Idee bis zur klinischen Studie in nur 30 Monaten statt 10+ Jahren.\n\nEin einziges Krankenhaus in Tampa hat mit KI-gestützter Sepsis-Erkennung über 700 Leben gerettet. Hochgerechnet könnte KI im Gesundheitswesen 371.000 Menschenleben pro Jahr retten.",
            'icon' => 'heart-pulse',
            'position' => 0,
        ]);

        // =====================================================
        // BLOCK 7: Quiz — Finale, härteste Runde (2 Cards)
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
            'category' => 'Literatur & KI',
            'headline' => 'KI schreibt heimlich Roman, der zum Bestseller wird',
            'story' => 'Ein von GPT-5 komplett generierter Thriller erschien unter dem Pseudonym "James R. Blackwell" auf Amazon und schaffte es auf die NYT-Bestsellerliste. Erst als ein Journalist den Verlag kontaktierte, kam heraus: Kein Wort war von einem Menschen geschrieben.',
            'date_label' => '2025',
            'is_real' => false,
            'explanation' => 'Komplett erfunden. Zwar überschwemmen KI-generierte Bücher Amazon, aber keines wurde je ein echter Bestseller ohne Offenlegung. Die Angst davor ist größer als die Realität — noch. Aber: 57% aller Online-Texte sind bereits KI-generiert.',
            'sources' => [],
            'position' => 1,
        ]);

        // =====================================================
        // BLOCK 8: Learn — Abschluss & Empowerment
        // =====================================================
        $block = JourneyBlock::create([
            'learning_journey_id' => $journey->id,
            'type' => 'learn',
            'position' => $pos++,
        ]);

        LearnCard::create([
            'journey_block_id' => $block->id,
            'title' => 'Erst formen wir unsere Werkzeuge — dann formen sie uns',
            'content' => "Dieses Zitat von John M. Culkin (1967, oft fälschlich McLuhan zugeschrieben) trifft den Kern: Jeder Werkzeugsprung hat nicht das Tempo verändert, sondern die Ebene.\n\nVon der Keilschrift zum Computer haben wir gelernt, Gedanken festzuhalten. Mit KI-Agenten formulieren wir jetzt Ziele — und die Maschine findet den Weg.\n\nDas GPT-5 Training kostet 500 Millionen Dollar pro Durchlauf. Eine KI-Anfrage verbraucht so viel Wasser wie eine Flasche. Irland nutzt 32% seiner Elektrizität für Rechenzentren.\n\nKI-Kompetenz ist keine Option mehr. Es ist die Kernkompetenz für die nächsten Jahrzehnte — und seit Februar 2025 durch den EU AI Act auch gesetzliche Pflicht.\n\nDie Frage ist nicht ob KI deine Arbeit verändert. Sondern ob du derjenige bist, der die Veränderung gestaltet.",
            'icon' => 'sparkles',
            'position' => 0,
        ]);
    }
}
