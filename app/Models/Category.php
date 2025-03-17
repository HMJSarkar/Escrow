<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'status', // active, inactive
        'icon',
        'order',
    ];

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the assets in this category.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Check if category is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get all active categories.
     */
    public static function getActive()
    {
        return self::where('status', 'active')->orderBy('order')->get();
    }

    /**
     * Get category tree structure.
     */
    public static function getTree()
    {
        $categories = self::where('parent_id', null)
            ->with('children')
            ->orderBy('order')
            ->get();

        return $categories;
    }
}