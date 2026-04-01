<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reservas:liberar')->everyMinute();
