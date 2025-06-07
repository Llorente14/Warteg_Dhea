<?php

namespace App\Filament\Resources;

use App\Filament\Exports\StockExporter;
use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Columns\IconColumn; // <--- HAPUS INI
use Filament\Tables\Columns\ToggleColumn; // <--- TAMBAHKAN INI
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ExportAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ExportBulkAction;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Manajemen Stok';
    protected static ?string $modelLabel = 'Stok Barang';
    protected static ?string $pluralModelLabel = 'Manajemen Stok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Stok')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Item')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('type')
                            ->label('Kategori')
                            ->options([
                                'alat masak' => 'Alat Masak',
                                'alat makan' => 'Alat Makan',
                                'kemasan takeaway' => 'Kemasan Takeaway',
                            ])
                            ->native(false)
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Jumlah Stok')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('unit')
                            ->required(),

                        Forms\Components\Toggle::make('is_available')
                            ->label('Tersedia')
                            ->helperText('Apakah item ini tersedia untuk digunakan?')
                            ->default(true)
                            ->visibleOn('edit'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Item')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'alat masak' => 'info',
                        'alat makan' => 'success',
                        'kemasan takeaway' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),

               
                Tables\Columns\ToggleColumn::make('is_available') 
                    ->label('Tersedia') // Label untuk kolom
                    ->tooltip(fn (bool $state): string => $state ? 'Klik untuk set TIDAK tersedia' : 'Klik untuk set TERSEDIA'),
                    // ToggleColumn secara otomatis menangani update database dan refresh

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Kategori')
                    ->options([
                        'alat masak' => 'Alat Masak',
                        'alat makan' => 'Alat Makan',
                        'kemasan takeaway' => 'Kemasan Takeaway',
                    ]),
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Ketersediaan')
                    ->boolean()
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia')
                    ->placeholder('Semua'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()->exporter(StockExporter::class)
            ])
            ->headerActions([
                ExportAction::make()->exporter(StockExporter::class)
                    
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}