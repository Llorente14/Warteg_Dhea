<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use App\Models\Menu;
use Filament\Resources\Components\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    
    //Membuat grouping pada sidebar
    protected static ?string $navigationGroup = 'Master Data';
    
    //Agar slug nya tidak menjadi plural
    protected static ?string $slug = 'menu';
    
    //Untuk mengubah title dari resource
    protected static ?string $navigationLabel = 'Daftar Menu';
    //Untuk mengubah icon dari resource 
    protected static ?string $navigationIcon = 'mdi-food-outline';

    //Untuk form builder
    public static function form(Form $form): Form
    {
        //Membuat component untuk form input berdasarkan migrationnya
        return $form
            ->schema([
                // dibuat menjadi select option kategori name
                Select::make('category_id')
                    ->relationship(name: 'category', titleAttribute: 'name')
                   
                    ->native(false),
                TextInput::make('name')
                    ->required()
                    //maxLength(255) agar maks karakter dalam suatu input "nama" hanya 255 char
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->numeric(),
          
            ]);
             
    }

    //Membuat table builder menggunakan framework filament
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Membuat colum table nama dengan jenis kolom teks
                TextColumn::make('name')
                    //Kolom table nama jadi dapat di search di searchbar
                    ->searchable()
                    //Kolom table nama juga jadi dapat di sorting sesuai alphabet/kecil-besar/besar-kecil
                    ->sortable(),
                TextColumn::make('price')
                    //Kolom table harga wajib didisplat dengan type numeric
                    ->numeric()
                    ->sortable(),
                //Kolom table nama_kategori diambil dari model kategori alias berelasi (One to Many relationship)
                TextColumn::make('category.name')
                    //Mengubah label kolom (heading kolom) menjadi Nama Kategori
                    ->label('Nama Kategori'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    //Mengubah kolom created_at tiddak ditampilkan diawal jadi harus di centang pada checkbox baru muncul
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }

     public function getTitle(): string
    {
        return "Menu Warteg";
    }
    
    //Handling soft deletes
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
