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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // DEBUG VERSION - Add logging
    public function getImageUrlAttribute()
    {
        \Log::info("Image URL accessor called for item: {$this->id}", [
            'item_name' => $this->name,
            'image_field' => $this->image,
            'exists_in_storage' => $this->image ? Storage::disk('public')->exists($this->image) : false
        ]);

        // If no image is set, return default
        if (!$this->image) {
            return asset('images/default-food.png');
        }

        // Check if file exists in storage
        if (Storage::disk('public')->exists($this->image)) {
            $url = Storage::url($this->image);
            \Log::info("Using storage URL: {$url}");
            return $url;
        }

        // If file doesn't exist, log warning and return default
        \Log::warning("Image file not found in storage: {$this->image}");
        return asset('images/default-food.png');
    }
}