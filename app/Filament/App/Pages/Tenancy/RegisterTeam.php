<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('Register team');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name'),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        return Team::create([
            'name' => $data['name'],
            'user_id' => auth('web')->user()->id,
            'personal_team' => false,
        ]);
    }
}
