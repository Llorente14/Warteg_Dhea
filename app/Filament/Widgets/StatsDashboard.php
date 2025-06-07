<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Stock; // Tambahkan ini
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsDashboard extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // --- Statistik Jumlah Orderan Minggu Ini ---
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $currentWeekOrders = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $lastWeekOrders = Order::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY),
            Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY)
        ])->count();

        $orderPercentageChange = 0;
        $orderDescription = 'Tidak ada perubahan dari minggu sebelumnya';
        $orderDescriptionIcon = 'heroicon-m-minus';
        $orderColor = 'gray';

        if ($lastWeekOrders > 0) {
            $orderPercentageChange = (($currentWeekOrders - $lastWeekOrders) / $lastWeekOrders) * 100;
        } elseif ($currentWeekOrders > 0) {
            $orderPercentageChange = 100; // Minggu lalu 0, minggu ini ada
        }

        if ($orderPercentageChange > 0) {
            $orderDescription = round($orderPercentageChange) . '% naik dari minggu sebelumnya';
            $orderDescriptionIcon = 'heroicon-m-arrow-trending-up';
            $orderColor = 'success';
        } elseif ($orderPercentageChange < 0) {
            $orderDescription = round(abs($orderPercentageChange)) . '% turun dari minggu sebelumnya';
            $orderDescriptionIcon = 'heroicon-m-arrow-trending-down';
            $orderColor = 'danger';
        }

        // --- Statistik Total Pendapatan Minggu Ini (Asumsi Order memiliki kolom 'total_price' dan 'status' seperti 'completed') ---
        // Jika status orderan Anda berbeda, sesuaikan `where('status', 'completed')`
        // Jika nama kolom harga Anda berbeda, sesuaikan 'total_price'
        $currentWeekRevenue = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                                    ->where('status', 'paid', 'completed') // Contoh: hanya order yang selesai
                                    ->sum('total_price'); // Ganti 'total_price' dengan nama kolom harga Anda

        $lastWeekRevenue = Order::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY),
            Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY)
        ])
        ->where('status', 'completed')
        ->sum('total_price');

        $revenuePercentageChange = 0;
        $revenueDescription = 'Tidak ada perubahan dari minggu sebelumnya';
        $revenueDescriptionIcon = 'heroicon-m-minus';
        $revenueColor = 'gray';

        if ($lastWeekRevenue > 0) {
            $revenuePercentageChange = (($currentWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100;
        } elseif ($currentWeekRevenue > 0) {
            $revenuePercentageChange = 100;
        }

        if ($revenuePercentageChange > 0) {
            $revenueDescription = round($revenuePercentageChange) . '% naik dari minggu sebelumnya';
            $revenueDescriptionIcon = 'heroicon-m-arrow-trending-up';
            $revenueColor = 'success';
        } elseif ($revenuePercentageChange < 0) {
            $revenueDescription = round(abs($revenuePercentageChange)) . '% turun dari minggu sebelumnya';
            $revenueDescriptionIcon = 'heroicon-m-arrow-trending-down';
            $revenueColor = 'danger';
        }

        // --- Statistik Stok Barang Akan Habis ---
        // Asumsi stok dianggap "akan habis" jika quantity <= 10
        $lowStockItems = Stock::where('quantity', '<=', 10)
                               ->count();
        
        $lowStockColor = $lowStockItems > 0 ? 'warning' : 'success'; // Jika ada item sedikit, beri warning
        $lowStockDescription = $lowStockItems > 0 ? 'Perlu segera ditambah' : 'Semua stok aman';
        $lowStockIcon = $lowStockItems > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle';


        return [
            Stat::make('Jumlah Orderan Minggu Ini', $currentWeekOrders . ' orderan')
                ->description($orderDescription)
                ->descriptionIcon($orderDescriptionIcon)
                ->color($orderColor),

            Stat::make('Total Pendapatan Minggu Ini', 'Rp ' . number_format($currentWeekRevenue, 0, ',', '.'))
                ->description($revenueDescription)
                ->descriptionIcon($revenueDescriptionIcon)
                ->color($revenueColor),
            
            Stat::make('Stok Barang Akan Habis', $lowStockItems . ' item')
                ->description($lowStockDescription)
                ->descriptionIcon($lowStockIcon)
                ->color($lowStockColor),
        ];
    }
}