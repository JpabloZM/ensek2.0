<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'active',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];
    
    /**
     * Get the inventory items for the category.
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }
    
    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(InventoryCategory::class, 'parent_id');
    }
    
    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(InventoryCategory::class, 'parent_id');
    }
    
    /**
     * Check if the category has children.
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }
    
    /**
     * Get all available parent categories (except self and children).
     */
    public static function getAvailableParents($exceptId = null)
    {
        $query = self::where('active', true);
        
        if ($exceptId) {
            // Exclude self
            $query->where('id', '!=', $exceptId);
            
            // Exclude all children and sub-children recursively
            $childrenIds = self::getAllChildrenIds($exceptId);
            if (count($childrenIds) > 0) {
                $query->whereNotIn('id', $childrenIds);
            }
        }
        
        return $query->orderBy('name')->get();
    }
    
    /**
     * Get all children and sub-children IDs recursively.
     */
    private static function getAllChildrenIds($categoryId)
    {
        $ids = [];
        $children = self::where('parent_id', $categoryId)->get();
        
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, self::getAllChildrenIds($child->id));
        }
        
        return $ids;
    }
    
    /**
     * Get the full hierarchical path of the category.
     */
    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $category = $this;
        
        while ($category->parent) {
            $category = $category->parent;
            array_unshift($path, $category->name);
        }
        
        return implode(' > ', $path);
    }
}
