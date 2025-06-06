<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Customer;


// Import semua komponen Forms dari Filament
use Filament\Forms\Components\{
    Group,
    Repeater,
    Select,
    TextInput,
    DateTimePicker,
    Section,
    Textarea
};
use Filament\Forms\Form;
use Filament\Forms\Get; // Penting untuk mendapatkan nilai field
use Filament\Forms\Set; // Penting untuk mengatur nilai field

// Import semua komponen Tables dari Filament
use Filament\Tables\{
    Columns\TextColumn,
    Table
};
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions;
use Filament\Tables;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Sales & Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Detail Pesanan')
                            ->schema([
                                Select::make('customer_id')
                                    ->label('Pelanggan')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->email()
                                            ->unique(Customer::class, 'email')
                                            ->nullable(),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->nullable(),
                                        Textarea::make('address')
                                            ->rows(3)
                                            ->maxLength(65535)
                                            ->nullable(),
                                    ])
                                    ->editOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->email()
                                            ->unique(Customer::class, 'email', ignoreRecord: true)
                                            ->nullable(),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->nullable(),
                                    ]),
                                Select::make('payment_method')
                                    ->options([
                                        'cash' => 'Cash',
                                        'qris' => 'Qris',
                                        'transfer bank' => 'Transfer Bank',
                                        'other' => 'Other',
                                    ])
                                    ->native(false)
                                    ->default('cash')
                                    ->selectablePlaceholder(false)
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->native(false)
                                    ->default('pending')
                                    ->selectablePlaceholder(false)
                                    ->required(),
                                DateTimePicker::make('paid_at')
                                    ->label('Dibayar Pada')
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->weekStartsOnMonday()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->displayFormat('Y-m-d H:i:s')
                                    ->nullable(),
                                Textarea::make('notes')
                                    ->rows(3)
                                    ->maxLength(65535)
                                    ->nullable(),
                            ])->columns(2),

                        Section::make('Daftar Menu yang Dipesan')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship('items')
                                    ->schema([
                                        Select::make('menu_id')
                                            ->label('Menu')
                                            ->options(Menu::pluck('name', 'id'))
                                            ->required()
                                            ->searchable()
                                            ->reactive() // Penting agar perubahan ini memicu afterStateUpdated
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                $selectedMenuId = $get('menu_id'); // Get 'menu_id' dari item repeater saat ini
                                                if ($selectedMenuId) {
                                                    $menu = Menu::find($selectedMenuId);
                                                    if ($menu) {
                                                        $set('price', $menu->price); // Set 'price' untuk item repeater saat ini
                                                    }
                                                }
                                                // Panggil kembali fungsi perhitungan total untuk form utama
                                                // Get('items') akan mendapatkan seluruh array item dari form utama
                                                // Set('total_price') akan mengatur total_price di form utama
                                                $set('total_price', self::calculateTotalPrice($get('items')));
                                            })
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                        TextInput::make('quantity')
                                            ->label('Qty')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->minValue(1)
                                            ->reactive() // Penting agar perubahan ini memicu afterStateUpdated
                                            ->hint(fn (Get $get) => 'Subtotal: Rp' . number_format($get('price') * $get('quantity'), 0, ',', '.'))
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                $set('quantity', (int) $get('quantity')); // Set 'quantity' untuk item repeater saat ini
                                                // Panggil kembali fungsi perhitungan total untuk form utama
                                                $set('total_price', self::calculateTotalPrice($get('items')));
                                            }),

                                        TextInput::make('price')
                                            ->label('Harga Satuan')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->disabled() // Harga diambil dari database Menu
                                            ->dehydrated(true), // Simpan nilai ini ke tabel order_items
                                    ])
                                    ->defaultItems(1)
                                    ->columns(3)
                                    ->live() // Membuat repeater reaktif. Penting untuk menambahkan/menghapus item
                                    ->cloneable() // Memungkinkan duplikasi item
                                    ->minItems(1) // Minimal 1 item dalam pesanan
                                    ->columnSpan('full')
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        // Panggil kembali fungsi perhitungan total untuk form utama
                                        // Ini akan terpicu saat item repeater ditambah atau dihapus
                                        $set('total_price', self::calculateTotalPrice($get('items')));
                                    }),


                                TextInput::make('total_price')
                                    ->label('Total Harga Keseluruhan')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true) // Simpan nilai ini ke tabel orders
                                    ->hint('Total harga akan otomatis terhitung dari menu yang dipilih.')
                                    ->default(0)
                                    ->extraAttributes([
                                        'class' => 'text-right font-bold text-xl',
                                    ])
                                    ->afterStateHydrated(function (Get $get, Set $set) {
                                        // Ini akan berjalan saat form dimuat (edit/create)
                                        // Get('items') akan mendapatkan seluruh array item dari form utama
                                        $set('total_price', self::calculateTotalPrice($get('items')));
                                    })
                                    ->live(debounce: 500), // Penting agar TextInput ini reaktif secara visual
                            ]),
                    ])->columnSpan('full'),
            ]);
    }

    /**
     * Helper method to calculate the total price from a list of order items.
     * @param array|null $items Array of order item data from the form state.
     * @return float The calculated total price.
     */
    protected static function calculateTotalPrice(?array $items): float
    {
        $total = 0;
        // Pastikan $items adalah array dan ada isinya
        if (is_array($items)) {
            foreach ($items as $item) {
                // Pastikan menu_id dan quantity ada sebelum perhitungan
                if (isset($item['menu_id']) && isset($item['quantity'])) {
                    // Ambil harga dari model Menu (lebih akurat dan jika harga menu berubah di masa depan)
                    $menu = Menu::find($item['menu_id']);
                    if ($menu) {
                        $total += ($menu->price * $item['quantity']);
                    }
                }
            }
        }
        return (float) $total; // Pastikan mengembalikan float
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    //Agar hurud depan kapital 
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Dibayar Pada')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])
                ->default('pending'), // Opsional: default filter
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\Action::make('markAsCompleted')
                    ->label('Tandai Selesai')
                    ->visible(fn (Order $record): bool => $record->status !== 'completed') // Hanya tampil jika belum selesai
                    ->action(function (Order $record) {
                        $record->status = 'completed';
                        $record->save();
                        Notification::make()
                            ->title('Pesanan Selesai')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),

            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}