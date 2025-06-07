<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class TopSellingMenusChart extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Menu Terlaris';
    protected static ?int $sort = 3; // Atur urutan

    protected function getType(): string
    {
        return 'bar'; // Atau 'horizontalBar' jika chart.js Anda mendukung
    }

    protected function getData(): array
    {
        $data = OrderItem::query()
            ->select(
                'menus.name as menu_name',
                DB::raw('SUM(order_items.quantity) as total_quantity_sold')
            )
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            // Filter order yang sudah paid (opsional, sesuaikan kebutuhan)
            // ->join('orders', 'order_items.order_id', '=', 'orders.id')
            // ->where('orders.status', 'paid')
            ->groupBy('menus.name')
            ->orderByDesc('total_quantity_sold')
            ->limit(5) // Ambil hanya 5 menu teratas
            ->get();



            // Reverse the data to show top 1 at the top of a horizontal bar chart
       $labels = $data->pluck('menu_name')->toArray();
$quantities = $data->pluck('total_quantity_sold')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Kuantitas Terjual',
                    'data' => $quantities,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    // Jika Anda ingin bar horizontal, Anda mungkin perlu meng override options
    // protected function getOptions(): array
    // {
    //     return [
    //         'indexAxis' => 'y', // Untuk membuat bar horizontal
    //     ];
    // }
}