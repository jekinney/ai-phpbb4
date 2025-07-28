<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'key',
        'title',
        'content',
        'metadata',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public static function getByKey($section, $key, $default = null)
    {
        $content = static::where('section', $section)
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return $content ? $content->content : $default;
    }

    public static function getTitleByKey($section, $key, $default = null)
    {
        $content = static::where('section', $section)
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return $content ? $content->title : $default;
    }
}
