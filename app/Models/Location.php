<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

 /**
     * The following code was generated for use with Filament Google Maps
     *
     * php artisan fgm:model-code Location --lat=lat --lng=log --location=location --terse
     */


     protected $fillable = [
        'location',
    ];

    protected $appends = [
        'location',
    ];

    public function getLocationAttribute(): array
    {
        return [
            "lat" => (float)$this->lat,
            "lng" => (float)$this->log,
        ];
    }

    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location))
        {
            $this->attributes['lat'] = $location['lat'];
            $this->attributes['log'] = $location['lng'];
            unset($this->attributes['location']);
        }
    }

    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'lat',
            'lng' => 'log',
        ];
    }

    public static function getComputedLocation(): string
    {
        return 'location';
    }
}