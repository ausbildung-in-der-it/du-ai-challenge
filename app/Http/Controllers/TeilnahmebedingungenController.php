<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class TeilnahmebedingungenController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Teilnahmebedingungen');
    }
}
