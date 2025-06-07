<?php

namespace App\Filament\Resources;

use App\Filament\Exports\StockUsageExporter;
use App\Filament\Resources\StockUsageResource\Pages;
use App\Filament\Resources\StockUsageResource\RelationManagers;
use App\Models\StockUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ExportAction;

class StockUsageResource extends Resource
{
    protected static ?string $model = StockUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manajemen Stok';
    protected static ?string $modelLabel = 'Pemakaian Stok';
    protected static ?string $pluralModelLabel = 'Laporan Pemakaian Stok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Biasanya tidak perlu form untuk StockUsage karena diisi otomatis oleh observer
                Forms\Components\Placeholder::make('info')
                    ->content('Data pemakaian stok dibuat secara otomatis oleh sistem.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id') // Atau order.order_number jika ada
                    ->label('ID Order')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stock.name')
                    ->label('Nama Stok')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('used_quantity')
                    ->label('Jumlah Dipakai')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Pemakaian')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stock_id')
                    ->label('Filter Stok')
                    ->relationship('stock', 'name')
                    ->searchable(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Hanya view, karena data ini otomatis
            ])
            ->bulkActions([
                // Tidak disarankan bulk delete untuk histori pemakaian stok
            ])
            ->headerActions([
                ExportAction::make()->exporter(StockUsageExporter::class)
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
            'index' => Pages\ListStockUsages::route('/'),
            // Tidak perlu create/edit page untuk StockUsage
        ];
    }
}