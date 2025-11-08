<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                TextColumn::make('name')->searchable()->label('Name'),
                TextColumn::make('email')->searchable()->label('Email'),
                TextColumn::make('branch.name')->label('Cabang')->sortable(),
                BadgeColumn::make('roles.name')
                    ->label('Role')
                    ->colors(['primary'])
                    ->separator(', '),
                IconColumn::make('is_active')->boolean()->label('Aktif'),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->label('Verified At'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Created At'),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label('User Aktif')
                    ->query(fn ($query) => $query->where('is_active', true)),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
