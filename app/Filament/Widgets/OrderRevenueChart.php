<?php

namespace App\Filament\Widgets;

use App\Models\Kategori;
use Filament\Widgets\ChartWidget;

class OrderRevenueChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Pendapatan berdasarkan kategori';

    protected function getData(): array
    {
        $categoryRevenue = Kategori::with(['menu.orderItems.order' => function ($query) {
            $query->where('status', 'paid')
                ->whereBetween('created_at', [
                    now()->startOfYear(),
                    now()->endOfYear()
                ]);
              
        }])
            ->get()
            
            ->map(function ($kategori) {
                $total = 0;
                foreach ($kategori->menu as $menu) {
                    foreach ($menu->orderItems as $orderItem) {
                        // Pastikan order status & tanggal sudah difilter di eager loading
                        if ($orderItem->order && $orderItem->order->status === 'paid') {
                            $total += $orderItem->quantity * $orderItem->price;
                        }
                    }
                }
                return [
                    'category_name' => $kategori->name,
                    'total_revenue' => $total,
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();
  
        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan per Kategori',
                    'data' => $categoryRevenue->pluck('total_revenue'),
                    'backgroundColor' => [
                        'rgba(255, 99, 133, 0.4)',
                        'rgba(54, 162, 235, 0.4)',
                        'rgba(255, 205, 86, 0.4)',
                        'rgba(75, 192, 192, 0.4)',
                        'rgba(153, 102, 255, 0.4)',
                        'rgba(255, 159, 64, 0.4)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $categoryRevenue->pluck('category_name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
