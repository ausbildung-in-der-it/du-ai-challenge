declare namespace App {
    namespace Data {
        export type JourneyBlockData = {
            id: number;
            type: string;
            position: number;
            quiz_cards: App.Data.QuizCardData[];
            learn_cards: App.Data.LearnCardData[];
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
