<?php

namespace App\Models\User;

use App\Http\Traits\ModelTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes, ModelTime;

    /**
     * @var array
     */
    protected $fillable = ['slug', 'parent_id', 'uri', 'remark'];

    public function getNameAttribute()
    {
        return __("permissions.{$this->slug}");
    }

    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission');
    }

    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id')->withoutTrashed();
    }

    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id', 'id')->withoutTrashed();
    }

    public static function getPermissionTree($parentId = 0): array
    {
        $permissions = self::with('children')->where('parent_id', $parentId)->withoutTrashed()->get();

        return self::buildTree($permissions);
    }

    public static function getPermissionFlattenTree($parentId = 0): array
    {
        $permissions = self::with('children')->where('parent_id', $parentId)->withoutTrashed()->get();

        return self::flattenTree($permissions);
    }

    private static function buildTree($permissions): array
    {
        $tree = [];

        foreach ($permissions as $permission) {
            $node = [
                'id' => $permission->id,
                'name' => __("permissions.{$permission->slug}"),
                'slug' => $permission->slug,
                'uri' => $permission->uri,
                'children' => self::buildTree($permission->children),
            ];

            $tree[] = $node;
        }

        return $tree;
    }

    private static function flattenTree($permissions, array &$flatList = []): array
    {
        foreach ($permissions as $permission) {
            $node = [
                'id' => $permission->id,
                'name' => __("permissions.{$permission->slug}"),
                'slug' => $permission->slug,
                'uri' => $permission->uri,
            ];

            $flatList[] = $node;

            if ($permission->children) {
                static::flattenTree($permission->children, $flatList);
            }
        }

        return $flatList;
    }

    /**
     * @param $with
     * @param $keyBy
     * @return Collection
     */
    public static function getAllPermissions($with = null, $keyBy = null): Collection
    {
        switch ($with) {
            case 'parent':
                return $keyBy ? Permission::with('parent')->get()->keyBy($keyBy) : Permission::with('parent')->get();
            case 'children':
                return $keyBy ? Permission::with('children')->get()->keyBy($keyBy) : Permission::with('children')->get();
            default:
                return $keyBy ? Permission::all()->keyBy($keyBy) : Permission::all();
        }
    }


    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
//            $model->roles()->detach();
        });
    }
}
