<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        return !! $role->intersect($this->roles)->count();
    }

    public function hasPermission($permission): bool
    {
        return $this->hasRoleThroughPermission($permission);
    }

    protected function hasRoleThroughPermission($permission): bool
    {
        $permissionModel = Permission::where('slug', $permission)->first();
        
        if (!$permissionModel) {
            return false;
        }

        foreach ($permissionModel->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }

        return false;
    }

    public function giveRole(...$roles)
    {
        $roles = $this->getAllRoles($roles);
        if ($roles === null) {
            return $this;
        }
        $this->roles()->saveMany($roles);
        return $this;
    }

    public function withdrawRole(...$roles)
    {
        $roles = $this->getAllRoles($roles);
        $this->roles()->detach($roles);
        return $this;
    }

    protected function getAllRoles(array $roles)
    {
        return Role::whereIn('slug', $roles)->get();
    }
}
