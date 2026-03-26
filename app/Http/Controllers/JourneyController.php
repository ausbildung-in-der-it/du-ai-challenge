<?php

namespace App\Http\Controllers;

use App\Data\JourneyBlockData;
use App\Data\LearnCardData;
use App\Data\LearningJourneyData;
use App\Data\QuizCardData;
use App\Data\SourceData;
use App\Models\LearningJourney;
use Inertia\Inertia;
use Inertia\Response;

class JourneyController extends Controller
{
    public function show(LearningJourney $journey): Response
    {
        $journey->load([
            'blocks.quizCards',
            'blocks.learnCards',
        ]);

        return Inertia::render('Journey/Show', [
            'journey' => new LearningJourneyData(
                id: $journey->id,
                title: $journey->title,
                slug: $journey->slug,
                description: $journey->description,
                blocks: $journey->blocks->map(fn ($block) => new JourneyBlockData(
                    id: $block->id,
                    type: $block->type,
                    position: $block->position,
                    quiz_cards: $block->quizCards->map(fn ($card) => new QuizCardData(
                        id: $card->id,
                        category: $card->category,
                        headline: $card->headline,
                        story: $card->story,
                        date_label: $card->date_label,
                        is_real: $card->is_real,
                        explanation: $card->explanation,
                        sources: collect($card->sources ?? [])->map(fn ($s) => new SourceData(
                            title: $s['title'],
                            url: $s['url'],
                        ))->all(),
                        position: $card->position,
                    ))->all(),
                    learn_cards: $block->learnCards->map(fn ($card) => new LearnCardData(
                        id: $card->id,
                        title: $card->title,
                        content: $card->content,
                        icon: $card->icon,
                        position: $card->position,
                    ))->all(),
                ))->all(),
            ),
        ]);
    }
}
