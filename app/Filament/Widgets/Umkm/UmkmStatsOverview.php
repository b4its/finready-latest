<?php

namespace App\Filament\Widgets\Umkm;

use App\Models\LearnProgress;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UmkmStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total_poin_progress =  LearnProgress::where('point')->count();

        return [
            //
            Stat::make('Total Point', $total_poin_progress)
                ->description('Jumlah Point Yang Telah Diperoleh')
                ->descriptionIcon('heroicon-m-star')
                ->icon('heroicon-m-star')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
