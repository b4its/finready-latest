<?php

namespace App\Filament\Resources\Admin\AdminQuestions;

use App\Filament\Resources\Admin\AdminQuestions\Pages\CreateAdminQuestion;
use App\Filament\Resources\Admin\AdminQuestions\Pages\EditAdminQuestion;
use App\Filament\Resources\Admin\AdminQuestions\Pages\ListAdminQuestions;
use App\Filament\Resources\Admin\AdminQuestions\Schemas\AdminQuestionForm;
use App\Filament\Resources\Admin\AdminQuestions\Tables\AdminQuestionsTable;
use App\Models\Question;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminQuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'question';
    protected static ?string $title = 'Pertanyaan';
    protected static ?string $slug = 'list-pertanyaan';

    public static function form(Schema $schema): Schema
    {
        return AdminQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminQuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Kuis';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pertanyaan';
    }
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-light-bulb';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminQuestions::route('/'),
            // 'create' => CreateAdminQuestion::route('/create'),
            // 'edit' => EditAdminQuestion::route('/{record}/edit'),
        ];
    }
}
