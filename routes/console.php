<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('sync:git-commits')->everyFiveMinutes();
