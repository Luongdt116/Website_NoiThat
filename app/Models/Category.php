<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model danh mục nội thất
class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description'];
    public function products(): HasMany { return $this->hasMany(Product::class); }
}
