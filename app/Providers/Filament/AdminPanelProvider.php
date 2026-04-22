<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Admin\AdminAkunKeuangans\AdminAkunKeuanganResource;
use App\Filament\Resources\Admin\AdminInvestors\AdminInvestorResource;
use App\Filament\Resources\Admin\AdminJurnalUmums\AdminJurnalUmumResource;
use App\Filament\Resources\Admin\AdminModuleContents\AdminModuleContentResource;
use App\Filament\Resources\Admin\AdminModules\AdminModuleResource;
use App\Filament\Resources\Admin\AdminPoins\AdminPoinResource;
use App\Filament\Resources\Admin\AdminQuestions\AdminQuestionResource;
use App\Filament\Resources\Admin\AdminRooms\AdminRoomResource;
use App\Filament\Resources\Admin\AdminSaldoAwals\AdminSaldoAwalResource;
use App\Filament\Resources\Admin\AdminSifatSaldoAkuns\AdminSifatSaldoAkunResource;
use App\Filament\Resources\Admin\AdminUmkms\AdminUmkmResource;
use App\Filament\Widgets\Admin\AdminStatsOverview;
use Blade;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
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
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
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
            ->brandName('Admin Panel')
            ->discoverResources(in: app_path('Filament/Resources/Admin'), for: 'App\Filament\Resources\Admin')
            ->discoverPages(in: app_path('Filament/Pages/Admin'), for: 'App\Filament\Pages\Admin')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets/Admin'), for: 'App\Filament\Widgets\Admin')
            ->renderHook(
                'panels::auth.login.form.after',
                fn () => view('filament.hooks.halaman-utama-button'),
            )
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        // 1. Dashboard selalu di atas
                        ...Dashboard::getNavigationItems(),
                        ...AdminPoinResource::getNavigationItems(),
                    ])
                    ->groups([
                        // 2. Grup Akun di urutan kedua
                        NavigationGroup::make('Akun')
                            ->items([
                                ...AdminInvestorResource::getNavigationItems(),
                                ...AdminUmkmResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make('Pembelajaran')
                            ->items([
                                ...AdminModuleResource::getNavigationItems(),
                                ...AdminModuleContentResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make('Kuis')
                            ->items([
                                ...AdminRoomResource::getNavigationItems(),
                                ...AdminQuestionResource::getNavigationItems(),
                            ]),
                        
                        // 3. Grup Kosong (tanpa label) di urutan terbawah
                        NavigationGroup::make('Keuangan') 
                            ->items([
                                ...AdminAkunKeuanganResource::getNavigationItems(),
                                ...AdminSifatSaldoAkunResource::getNavigationItems(),
                                ...AdminSaldoAwalResource::getNavigationItems(),
                                ...AdminJurnalUmumResource::getNavigationItems(),
                            ]),
                    ]);
            })
            ->widgets([
                AccountWidget::class,
                AdminStatsOverview::class,
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
