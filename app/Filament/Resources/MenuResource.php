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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
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
                FileUpload::make('image') // Nama kolom di database
                    ->label('Gambar Menu')
                    ->image() // Validasi bahwa ini adalah file gambar
                    ->directory('menu-images') // Folder di dalam storage/app/public
                    ->visibility('public') // Gambar bisa diakses publik (penting untuk frontend)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']) // Tipe file yang diterima
                    ->maxSize(2048) // Ukuran maksimal 2MB
                    ->columnSpanFull(), // Agar mengambil lebar penuh
                   
            ]);
             
    }

    //Membuat table builder menggunakan framework filament
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom 'name' dengan fitur pencarian dan pengurutan
                TextColumn::make('name')
                    ->label('Nama Menu') // Label yang lebih jelas
                    ->searchable()
                    ->sortable(),

                // Kolom 'image' untuk menampilkan gambar menu
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular() // Opsional: membuat gambar thumbnail jadi lingkaran
                    ->size(50) // Opsional: ukuran thumbnail
                    ->defaultImageUrl(url('/images/placeholder-menu.jpg')), // Gambar placeholder jika tidak ada gambar
                    // Pastikan Anda sudah mengupload gambar placeholder ini ke public/images/placeholder-menu.jpg
                    // atau sesuaikan dengan path placeholder dari Tailwind yang Anda gunakan di frontend.

                // Kolom 'price' dengan format angka dan dapat diurutkan
                TextColumn::make('price')
                    ->label('Harga') // Label yang lebih jelas
                    ->numeric()
                    ->sortable()
                    ->money('IDR'), // Menampilkan sebagai format mata uang Rupiah

                // Kolom 'category.name' diambil dari relasi (One to Many relationship)
                TextColumn::make('category.name')
                    ->label('Nama Kategori') // Mengubah label kolom (heading kolom)
                    ->searchable() // Tambahkan searchable untuk kategori
                    ->sortable(), // Tambahkan sortable untuk kategori

                // Kolom 'created_at' (tanggal pembuatan)
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat') // Label yang lebih jelas
                    ->dateTime()
                    ->sortable()
                    // Kolom created_at tidak ditampilkan di awal (hidden by default)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Kolom 'updated_at' (tanggal terakhir diperbarui)
                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui') // Label yang lebih jelas
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter untuk Trashed (soft deletes)
                Tables\Filters\TrashedFilter::make(),
                // Filter berdasarkan kategori (jika ada banyak kategori)
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name')
                    ->preload() // Memuat semua opsi kategori di awal
                    ->searchable(), // Memungkinkan pencarian kategori dalam filter
            ])
            ->actions([
                // Aksi Edit untuk setiap baris
                Tables\Actions\EditAction::make(),
                // Aksi Hapus untuk setiap baris
                Tables\Actions\DeleteAction::make(),
                // Tambahkan aksi RestoreAction jika Anda menggunakan soft deletes
                Tables\Actions\RestoreAction::make(),
                // Tambahkan aksi ForceDeleteAction jika Anda ingin menghapus permanen
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                // Grup Aksi Bulk (untuk beberapa baris sekaligus)
                Tables\Actions\BulkActionGroup::make([
                    // Aksi Hapus Bulk
                    Tables\Actions\DeleteBulkAction::make(),
                    // Aksi Force Delete Bulk
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    // Aksi Restore Bulk
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
