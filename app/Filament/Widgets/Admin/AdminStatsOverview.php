<?php

namespace App\Filament\Widgets\Admin;

use App\Models\Modul;
use App\Models\ModuleContent;
use App\Models\Room;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total_investor_accounts =User::where('role', 'investor')->count();
        $total_umkm_accounts =User::where('role', 'umkm')->count();
        $total_modul = Modul::count();
        $total_modul_konten = ModuleContent::count();
        $total_room = Room::count();


        return [
            //
            Stat::make('Total Akun', User::count())
                ->description('Jumlah Seluruh Akun')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Total Akun Investor', $total_investor_accounts)
                ->description('Jumlah Seluruh Akun Investor')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Akun UMKM', $total_umkm_accounts)
                ->description('Jumlah Seluruh Akun UMKM')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            
            Stat::make('Total Modul', $total_modul)
                ->description('Jumlah Seluruh Modul')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),
            
            Stat::make('Total Modul Konten', $total_modul_konten)
                ->description('Jumlah Seluruh Modul')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),

            Stat::make('Total Kuis', $total_room)
                ->description('Jumlah Seluruh Kuis')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),
            
            // Stat::make('Total Investor', $total_investor_accounts)
            //     ->description('Jumlah akun dengan role investor')
            //     ->descriptionIcon('heroicon-m-users')
            //     ->icon('heroicon-m-users')
            //     ->color('success')
            //     ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
