<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers;

use App\Filament\Admin\Resources\Teams\TeamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

/**
 * @property \App\Models\User $ownerRecord
 */
class OwnedTeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'ownedTeams';

    protected static ?string $relatedResource = TeamResource::class;

    public static function getRelationshipTitle(): string
    {
        return __('Owner Teams');
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
