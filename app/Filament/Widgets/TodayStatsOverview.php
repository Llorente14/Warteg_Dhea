<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Carbon\Carbon;

class TodayStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday(); // Mendapatkan tanggal kemarin

        // 1. Total Pendapatan Hari Ini
        $todayRevenue = Order::where('status', 'paid')
                             ->whereDate('paid_at', $today)
                             ->sum('total_price');

        // 2. Total Pendapatan Hari Sebelumnya
        $yesterdayRevenue = Order::where('status', 'paid')
                                 ->whereDate('paid_at', $yesterday)
                                 ->sum('total_price');

        // Logika perbandingan untuk Pendapatan Hari Ini
        $revenueDescription = '';
        $revenueDescriptionIcon = '';
        $revenueColor = '';

        if ($yesterdayRevenue > 0) {
            $percentageChange = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
            if ($percentageChange > 0) {
                $revenueDescription = round($percentageChange) . '% naik dari hari sebelumnya';
                $revenueDescriptionIcon = 'heroicon-m-arrow-trending-up';
                $revenueColor = 'success';
            } elseif ($percentageChange < 0) {
                $revenueDescription = round(abs($percentageChange)) . '% turun dari hari sebelumnya'; // abs() untuk nilai mutlak
                $revenueDescriptionIcon = 'heroicon-m-arrow-trending-down'; // Ikon panah bawah
                $revenueColor = 'danger'; // Warna merah
            } else {
                $revenueDescription = 'Sama dengan hari sebelumnya';
                $revenueDescriptionIcon = 'heroicon-m-minus'; // Ikon minus/strip
                $revenueColor = 'gray'; // Warna abu-abu
            }
        } elseif ($todayRevenue > 0) {
            // Jika kemarin 0, tapi hari ini ada pendapatan
            $revenueDescription = '100% naik dari hari sebelumnya (kemarin 0)';
            $revenueDescriptionIcon = 'heroicon-m-arrow-trending-up';
            $revenueColor = 'success';
        } else {
            // Jika hari ini dan kemarin 0 pendapatan
            $revenueDescription = 'Tidak ada pendapatan hari ini';
            $revenueDescriptionIcon = 'heroicon-m-minus';
            $revenueColor = 'gray';
        }


        // 3. Jumlah Order Hari Ini (Logika ini tetap sama)
        $todayOrdersCount = Order::whereDate('created_at', $today)
                                 ->count();

        return [
            Stat::make('Pendapatan Hari Ini', 'Rp' . number_format($todayRevenue, 0, ',', '.'))
                ->description($revenueDescription) // Gunakan deskripsi dinamis
                ->descriptionIcon($revenueDescriptionIcon) // Gunakan ikon dinamis
                ->color($revenueColor), // Gunakan warna dinamis
            Stat::make('Jumlah Order Hari Ini', $todayOrdersCount)
                ->description('Total pesanan yang masuk hari ini')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
            // Anda bisa menambahkan stat lain di sini jika diperlukan
        ];
    }
}