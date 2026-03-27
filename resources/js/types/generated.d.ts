declare namespace App {
    namespace Data {
        export type ChoiceCardData = {
            id: number;
            question: string;
            options: string[];
            correct_index: number;
            explanation: string;
            position: number;
        };
        export type JourneyBlockData = {
            id: number;
            type: string;
            position: number;
            config: Record<string, any> | null;
            quiz_cards: App.Data.QuizCardData[];
            learn_cards: App.Data.LearnCardData[];
            choice_cards: App.Data.ChoiceCardData[];
        };
        export type LearnCardData = {
            id: number;
            title: string;
            content: string;
            icon: string | null;
            position: number;
        };
        export type LearningJourneyData = {
            id: number;
            title: string;
            slug: string;
            description: string | null;
            blocks: App.Data.JourneyBlockData[];
        };
        export type QuizCardData = {
            id: number;
            category: string;
            headline: string;
            story: string;
            date_label: string;
            is_real: boolean;
            explanation: string;
            sources: App.Data.SourceData[];
            position: number;
        };
        export type SourceData = {
            title: string;
            url: string;
        };
    }
}
