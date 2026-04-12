<?php

namespace JSzD\World\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $iso2
 * @property string $phone_code
 */
class Country extends Model {
    public    $timestamps = false;
    protected $guarded    = [];

    protected function casts(): array {
        return [
            'id'         => 'integer',
        ];
    }

    public function getTable() {
        return config('laravel-world.migrations.countries.table_name', parent::getTable());
    }
    
    public function states(): HasMany {
        return $this->hasMany(State::class, 'country_id');
    }
    
    public function cities(): HasMany {
        return $this->hasMany(City::class, 'country_id');
    }
}
