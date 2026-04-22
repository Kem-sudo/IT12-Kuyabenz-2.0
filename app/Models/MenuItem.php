<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'image',
    ];

    protected $appends = [
        'image_url',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return '/images/Errorimage.jpg';
        }

        if (Storage::disk('public')->exists($this->image)) {
            return '/storage/' . $this->image;
        }

        return '/images/Errorimage.jpg';
    }
}