<?php

namespace App\Filament\Widgets;

use App\Models\System;
use App\Models\SystemUpdate;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // This tells Filament to put it on the Dashboard
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        // Admin sees Global, Dev sees personal
        $systemCount = ($user->role === 'admin') 
            ? System::count() 
            : System::where('user_id', $user->id)->count();

        $updateCount = ($user->role === 'admin') 
            ? SystemUpdate::count() 
            : SystemUpdate::where('user_id', $user->id)->count();

        return [
            Stat::make('Systems', $systemCount)
                ->description($user->role === 'admin' ? 'Total Global' : 'My Systems')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('primary'),

            Stat::make('Updates', $updateCount)
                ->description($user->role === 'admin' ? 'Total Logs' : 'My Updates')
                ->descriptionIcon('heroicon-m-hashtag')
                ->color('success'),
        ];
    }
}