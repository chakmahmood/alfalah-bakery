<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StockReturn;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockReturnPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockReturn');
    }

    public function view(AuthUser $authUser, StockReturn $stockReturn): bool
    {
        return $authUser->can('View:StockReturn');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockReturn');
    }

    public function update(AuthUser $authUser, StockReturn $stockReturn): bool
    {
        return $authUser->can('Update:StockReturn');
    }

    public function delete(AuthUser $authUser, StockReturn $stockReturn): bool
    {
        return $authUser->can('Delete:StockReturn');
    }

    public function restore(AuthUser $authUser, StockReturn $stockReturn): bool
    {
        return $authUser->can('Restore:StockReturn');
    }

    public function forceDelete(AuthUser $authUser, StockReturn $stockReturn): bool
    {
        return $authUser->can('ForceDelete:StockReturn');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockReturn');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockReturn');
    }

    public function replicate(AuthUser $authUser, StockReturn $stockReturn): bool
    {
        return $authUser->can('Replicate:StockReturn');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockReturn');
    }

}