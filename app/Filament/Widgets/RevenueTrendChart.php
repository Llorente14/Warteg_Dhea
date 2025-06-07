<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Carbon\Carbon;

class RevenueTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan 7 Hari Terakhir';
    protected static ?int $sort = 2; // Atur urutan
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '350px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        $today = Carbon::today();

        for ($i = 6; $i >= 0; $i--) { // 7 hari terakhir termasuk hari ini
            $date = $today->copy()->subDays($i);
            $labels[] = $date->format('d M'); // Format tanggal: 01 Jan

            $revenue = Order::where('status', 'paid')
                            ->whereDate('paid_at', $date)
                            ->sum('total_price');
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data,
                    'fill' => 'start', // Mengisi area di bawah garis
                    'borderColor' => '#36A2EB', // Warna garis
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)', // Warna area
                ],
            ],
        ];
    }
}