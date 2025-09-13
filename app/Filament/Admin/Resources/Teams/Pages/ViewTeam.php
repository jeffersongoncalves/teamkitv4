<?php

namespace App\Filament\Admin\Resources\Teams\Pages;

use App\Filament\Admin\Resources\Teams\TeamResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTeam extends ViewRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
