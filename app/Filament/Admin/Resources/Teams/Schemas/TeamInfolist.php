<?php

namespace App\Filament\Admin\Resources\Teams\Schemas;

use App\Filament\Schemas\Components\AdditionalInformation;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('owner.name')
                            ->label(__('Owner')),
                        TextEntry::make('name'),
                        IconEntry::make('personal_team')
                            ->boolean(),
                    ]),
                AdditionalInformation::make([
                    'created_at',
                    'updated_at',
                ]),
            ]);
    }
}
