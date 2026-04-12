<?php

namespace Database\Seeders;

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use JSzD\World\Models\City;
use JSzD\World\Models\Country;
use JSzD\World\Models\State;

class WorldSeeder extends Seeder {
    private Builder $schema;
    private array   $countries = [
        'data' => [],
    ];
    private array   $states    = [
        'data' => [],
    ];

    private array $cities = [
        'data' => [],
    ];

    public function __construct() {
        $this->checkMemoryLimit();
        $this->schema = app('db')->connection()->getSchemaBuilder();
        $this->init();

    }

    public function run() {
        $this->command->getOutput()->block('Seeding start');

        // 1. Countries
        $this->command->getOutput()->text('Seeding countries...');
        $countryData = [];
        foreach ($this->countries['data'] as $countryArray) {
            $countryArray = array_map(fn($field) => is_string($field) ? trim($field) : $field, $countryArray);
            $countryData[] = [
                'name'       => $countryArray['name'],
                'iso2'       => $countryArray['iso2'],
                'phone_code' => $countryArray['phone_code'],
            ];
        }
        Country::insert($countryData);
        $countryMap = Country::all()->pluck('id', 'iso2')->toArray();

        // 2. States
        $this->command->getOutput()->text('Seeding states...');
        $stateData = [];
        $jsonStateMap = []; // map json_id to state_code for city mapping later
        foreach ($this->states['data'] as $stateArray) {
            if (!isset($countryMap[$stateArray['country_code']])) {
                continue;
            }
            $stateArray = array_map(fn($field) => is_string($field) ? trim($field) : $field, $stateArray);
            $countryId = $countryMap[$stateArray['country_code']];
            $stateData[] = [
                'name'         => $stateArray['name'],
                'country_code' => $stateArray['country_code'],
                'state_code'   => $stateArray['state_code'],
                'country_id'   => $countryId,
            ];
            $jsonStateMap[$stateArray['id']] = [
                'country_id' => $countryId,
                'state_code' => $stateArray['state_code']
            ];
        }
        foreach (array_chunk($stateData, 500) as $chunk) {
            State::insert($chunk);
        }
        
        $dbStateMap = State::all()->mapWithKeys(fn($s) => ["{$s->country_id}-{$s->state_code}" => $s->id])->toArray();
        $finalStateMap = [];
        foreach ($jsonStateMap as $jsonId => $mapping) {
            $key = "{$mapping['country_id']}-{$mapping['state_code']}";
            if (isset($dbStateMap[$key])) {
                $finalStateMap[$jsonId] = $dbStateMap[$key];
            }
        }

        // 3. Cities
        $this->command->getOutput()->text('Seeding cities...');
        $cityData = [];
        $totalCities = count($this->cities['data']);
        $this->command->getOutput()->progressStart($totalCities);
        
        foreach ($this->cities['data'] as $cityArray) {
            if (!isset($countryMap[$cityArray['country_code']]) || !isset($finalStateMap[$cityArray['state_id']])) {
                $this->command->getOutput()->progressAdvance();
                continue;
            }
            $cityArray = array_map(fn($field) => is_string($field) ? trim($field) : $field, $cityArray);
            $cityData[] = [
                'name'         => $cityArray['name'],
                'country_code' => $cityArray['country_code'],
                'state_id'     => $finalStateMap[$cityArray['state_id']],
                'country_id'   => $countryMap[$cityArray['country_code']],
            ];
            
            if (count($cityData) >= 1000) {
                City::insert($cityData);
                $this->command->getOutput()->progressAdvance(count($cityData));
                $cityData = [];
            }
        }
        if (!empty($cityData)) {
            City::insert($cityData);
            $this->command->getOutput()->progressAdvance(count($cityData));
        }
        
        $this->command->getOutput()->progressFinish();
        $this->command->getOutput()->block('Seeding end');
    }

    private function seedStates(Country $country, int $countryJsonId): void {
        // Obsolete, but kept to avoid errors if called from elsewhere or for backward compatibility (though private)
    }

    private function seedCities(Country $country, State $state, int $stateJsonId): void {
        // Obsolete
    }

    private function init() {
        $this->schema->disableForeignKeyConstraints();
        State::truncate();
        Country::truncate();
        City::truncate();
        $this->schema->enableForeignKeyConstraints();

        $this->countries['data'] = json_decode(File::get(JSZD_LW . '/resources/json/countries.json'), true);
        $this->states['data'] = json_decode(File::get(JSZD_LW . '/resources/json/states.json'), true);
        $this->cities['data'] = json_decode(File::get(JSZD_LW . '/resources/json/cities.json'), true);

        if (!empty(config('laravel-world.countries.only'))) {
            $this->countries['data'] = array_filter($this->countries['data'], function ($country) {
                return in_array($country['iso2'], config('laravel-world.countries.only'));
            });
        } else if (!empty(config('laravel-world.countries.except'))) {
            $this->countries['data'] = array_filter($this->countries['data'], function ($country) {
                return !in_array($country['iso2'], config('laravel-world.countries.except'));
            });
        }
    }

    /**
     * Check if the current memory limit is sufficient for seeding large datasets
     */
    private function checkMemoryLimit(): void {
        $currentLimit = ini_get('memory_limit');
        $recommendedLimit = '512M';
        $currentBytes = $this->convertToBytes($currentLimit);
        $recommendedBytes = $this->convertToBytes($recommendedLimit);

        if ($currentBytes < $recommendedBytes && $currentLimit !== '-1') {
            $message = "Insufficient memory limit detected! Current: {$currentLimit}, Recommended: {$recommendedLimit}\n";

            $message .= "To fix this, run the command with increased memory:\n" .
                "php -d memory_limit={$recommendedLimit} artisan db:seed --class=WorldSeeder";

            if ($this->command && method_exists($this->command, 'getOutput')) {
                $this->command->getOutput()->error($message);
            } else {
                fwrite(STDERR, $message . "\n");
            }
            exit(1);
        }
    }

    private function convertToBytes(string $memoryLimit): int {
        if ($memoryLimit === '-1') {
            return PHP_INT_MAX;
        }

        $unit = strtolower(substr($memoryLimit, -1));
        $value = (int)substr($memoryLimit, 0, -1);

        switch ($unit) {
            case 'g':
                return $value * 1024 * 1024 * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'k':
                return $value * 1024;
            default:
                return (int)$memoryLimit;
        }
    }
}
