<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Umkm\UmkmAkunAslis\UmkmAkunAsliResource;
use App\Filament\Resources\Umkm\UmkmAkunKeuangans\UmkmAkunKeuanganResource;
use App\Filament\Resources\Umkm\UmkmBukuBesars\UmkmBukuBesarResource;
use App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\UmkmJurnalUmumAsliResource;
use App\Filament\Resources\Umkm\UmkmJurnalUmums\UmkmJurnalUmumResource;
use App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\UmkmPengajuanDataKeuanganResource;
use App\Filament\Resources\Umkm\UmkmPoins\UmkmPoinResource;
use App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\UmkmSaldoAwalAsliResource;
use App\Filament\Resources\Umkm\UmkmSaldoAwals\UmkmSaldoAwalResource;
use App\Filament\Resources\Umkm\UmkmSifatAkunAslis\UmkmSifatAkunAsliResource;
use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\UmkmSifatAkunKeuanganResource;
use App\Filament\Widgets\Umkm\Chart\UmkmMutasiChart;
use App\Filament\Widgets\Umkm\UmkmStatsOverview;
use App\Models\LearnProgress;
use Blade;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UmkmPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->id('umkm')
            ->path('umkm')
            ->login()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Edit Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url('#edit-profile'),
            ])
            ->renderHook(
                'panels::body.end',
                fn (): string => Blade::render('@livewire(\App\Livewire\EditProfileModal::class)')
            )
            ->globalSearch(false)
            ->brandName('UMKM Panel')
            ->discoverResources(in: app_path('Filament/Resources/Umkm'), for: 'App\Filament\Resources\Umkm')
            ->discoverPages(in: app_path('Filament/Pages/Umkm'), for: 'App\Filament\Pages\Umkm')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets/Umkm'), for: 'App\Filament\Widgets\Umkm')
            ->renderHook(
                'panels::auth.login.form.after',
                fn () => view('filament.hooks.halaman-utama-button'),
            )
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                    $totalPoints = 0;
                    if (Auth::check()) {
                        $totalPoints = LearnProgress::where('idUsers', Auth::id())->sum('point');
                    }

                    $isStructured = $totalPoints > 500;
                    $isReady = $totalPoints > 1500;

                    // Siapkan array groups
                    $navGroups = [];

                    if ($isStructured) {
                        $navGroups[] = NavigationGroup::make('Uji Coba')
                            ->items([
                                ...UmkmAkunKeuanganResource::getNavigationItems(),
                                ...UmkmSifatAkunKeuanganResource::getNavigationItems(),
                                ...UmkmSaldoAwalResource::getNavigationItems(),
                                ...UmkmJurnalUmumResource::getNavigationItems(),
                            ]);
                    
                    }
                    // Selalu tambahkan grup Akun Riil
                    if ($isReady) {
                    $navGroups[] = NavigationGroup::make('Akun Riil')
                        ->items([
                            ...UmkmAkunAsliResource::getNavigationItems(),
                            ...UmkmSifatAkunAsliResource::getNavigationItems(),
                            ...UmkmSaldoAwalAsliResource::getNavigationItems(),
                            ...UmkmJurnalUmumAsliResource::getNavigationItems(),
                        ]);
                    }
                    return $builder
                        ->items([
                            ...Dashboard::getNavigationItems(),
                            ...UmkmPoinResource::getNavigationItems(),
                            ...UmkmPengajuanDataKeuanganResource::getNavigationItems(),
                            NavigationItem::make('Pembelajaran')
                                ->url(fn () => route('learning.index'))
                                ->icon('heroicon-o-book-open')
                                ->sort(1),
                        ])
                        ->groups($navGroups); // Masukkan variabel array groups di sini
                })
            ->widgets([
                UmkmStatsOverview::class,
                UmkmMutasiChart::class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
