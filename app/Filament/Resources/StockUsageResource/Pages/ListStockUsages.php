<?php

namespace App\Filament\Resources\StockUsageResource\Pages;

use App\Filament\Resources\StockUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockUsages extends ListRecords
{
    protected static string $resource = StockUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
