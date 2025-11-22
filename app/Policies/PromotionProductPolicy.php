<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PromotionProduct;
use Illuminate\Auth\Access\HandlesAuthorization;

class PromotionProductPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PromotionProduct');
    }

    public function view(AuthUser $authUser, PromotionProduct $promotionProduct): bool
    {
        return $authUser->can('View:PromotionProduct');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PromotionProduct');
    }

    public function update(AuthUser $authUser, PromotionProduct $promotionProduct): bool
    {
        return $authUser->can('Update:PromotionProduct');
    }

    public function delete(AuthUser $authUser, PromotionProduct $promotionProduct): bool
    {
        return $authUser->can('Delete:PromotionProduct');
    }

    public function restore(AuthUser $authUser, PromotionProduct $promotionProduct): bool
    {
        return $authUser->can('Restore:PromotionProduct');
    }

    public function forceDelete(AuthUser $authUser, PromotionProduct $promotionProduct): bool
    {
        return $authUser->can('ForceDelete:PromotionProduct');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PromotionProduct');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PromotionProduct');
    }

    public function replicate(AuthUser $authUser, PromotionProduct $promotionProduct): bool
    {
        return $authUser->can('Replicate:PromotionProduct');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PromotionProduct');
    }

}