<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalendarHoliday;
use App\Models\CalendarHolidayLocation;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;

class CalendarHolidayLocationSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para ubicaciones de fiestas del calendario.
     */
    public function run(): void
    {
        $this->command->info('📍 Creando ubicaciones para fiestas del calendario...');

        // Buscar entidades geográficas existentes
        $spain = Country::where('name', 'Spain')->orWhere('name', 'España')->first();
        if (!$spain) {
            $spain = Country::first();
        }

        $madrid = AutonomousCommunity::where('name', 'Madrid')->first();
        $barcelona = AutonomousCommunity::where('name', 'Cataluña')->orWhere('name', 'Catalunya')->first();
        $valencia = AutonomousCommunity::where('name', 'Valencia')->orWhere('name', 'Comunitat Valenciana')->first();
        $andalucia = AutonomousCommunity::where('name', 'Andalucía')->first();
        $galicia = AutonomousCommunity::where('name', 'Galicia')->first();
        $paisVasco = AutonomousCommunity::where('name', 'País Vasco')->orWhere('name', 'Euskadi')->first();

        // Crear ubicaciones para fiestas existentes
        $this->createHolidayLocations($spain, $madrid, $barcelona, $valencia, $andalucia, $galicia, $paisVasco);

        $this->command->info('✅ Ubicaciones de fiestas creadas exitosamente');
        
        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Crear ubicaciones para las fiestas existentes.
     */
    private function createHolidayLocations($spain, $madrid, $barcelona, $valencia, $andalucia, $galicia, $paisVasco): void
    {
        // Fiestas nacionales (toda España)
        $this->createNationalHolidayLocations($spain);

        // Fiestas autonómicas
        $this->createRegionalHolidayLocations($madrid, $barcelona, $valencia, $andalucia, $galicia, $paisVasco);

        // Fiestas locales (municipios específicos)
        $this->createLocalHolidayLocations($madrid, $barcelona, $valencia, $andalucia, $galicia, $paisVasco);
    }

    /**
     * Crear ubicaciones para fiestas nacionales.
     */
    private function createNationalHolidayLocations($spain): void
    {
        $nationalHolidays = [
            'Año Nuevo',
            'Día de los Reyes Magos',
            'Día del Trabajador',
            'Día de la Hispanidad',
            'Día de Todos los Santos',
            'Día de la Constitución',
            'Día de la Inmaculada Concepción',
            'Navidad',
        ];

        foreach ($nationalHolidays as $holidayName) {
            $holiday = CalendarHoliday::where('name', 'LIKE', "%{$holidayName}%")->first();
            if ($holiday) {
                CalendarHolidayLocation::create([
                    'calendar_holiday_id' => $holiday->id,
                    'country_id' => $spain->id,
                    'municipality_id' => null,
                    'province_id' => null,
                    'autonomous_community_id' => null,
                ]);
            }
        }
    }

    /**
     * Crear ubicaciones para fiestas autonómicas.
     */
    private function createRegionalHolidayLocations($madrid, $barcelona, $valencia, $andalucia, $galicia, $paisVasco): void
    {
        // Madrid
        if ($madrid) {
            $this->createRegionalHoliday('San Isidro', $madrid, $spain ?? null);
            $this->createRegionalHoliday('Día de la Comunidad de Madrid', $madrid, $spain ?? null);
        }

        // Cataluña
        if ($barcelona) {
            $this->createRegionalHoliday('Sant Jordi', $barcelona, $spain ?? null);
            $this->createRegionalHoliday('Diada de Catalunya', $barcelona, $spain ?? null);
        }

        // Valencia
        if ($valencia) {
            $this->createRegionalHoliday('San Vicente Ferrer', $valencia, $spain ?? null);
            $this->createRegionalHoliday('Día de la Comunidad Valenciana', $valencia, $spain ?? null);
        }

        // Andalucía
        if ($andalucia) {
            $this->createRegionalHoliday('Día de Andalucía', $andalucia, $spain ?? null);
        }

        // Galicia
        if ($galicia) {
            $this->createRegionalHoliday('Día de Galicia', $galicia, $spain ?? null);
        }

        // País Vasco
        if ($paisVasco) {
            $this->createRegionalHoliday('Día de Euskadi', $paisVasco, $spain ?? null);
        }
    }

    /**
     * Crear ubicación para una fiesta autonómica.
     */
    private function createRegionalHoliday($holidayName, $autonomousCommunity, $country): void
    {
        $holiday = CalendarHoliday::where('name', 'LIKE', "%{$holidayName}%")->first();
        if ($holiday) {
            CalendarHolidayLocation::create([
                'calendar_holiday_id' => $holiday->id,
                'autonomous_community_id' => $autonomousCommunity->id,
                'country_id' => $country ? $country->id : null,
                'municipality_id' => null,
                'province_id' => null,
            ]);
        }
    }

    /**
     * Crear ubicaciones para fiestas locales.
     */
    private function createLocalHolidayLocations($madrid, $barcelona, $valencia, $andalucia, $galicia, $paisVasco): void
    {
        // Fiestas de Madrid
        if ($madrid) {
            $madridProvince = Province::where('autonomous_community_id', $madrid->id)->first();
            if ($madridProvince) {
                $this->createLocalHoliday('San Isidro', $madrid, $madridProvince, $spain ?? null);
                $this->createLocalHoliday('Virgen de la Almudena', $madrid, $madridProvince, $spain ?? null);
            }
        }

        // Fiestas de Barcelona
        if ($barcelona) {
            $barcelonaProvince = Province::where('autonomous_community_id', $barcelona->id)->first();
            if ($barcelonaProvince) {
                $this->createLocalHoliday('La Mercè', $barcelona, $barcelonaProvince, $spain ?? null);
                $this->createLocalHoliday('Sant Joan', $barcelona, $barcelonaProvince, $spain ?? null);
            }
        }

        // Fiestas de Valencia
        if ($valencia) {
            $valenciaProvince = Province::where('autonomous_community_id', $valencia->id)->first();
            if ($valenciaProvince) {
                $this->createLocalHoliday('Las Fallas', $valencia, $valenciaProvince, $spain ?? null);
                $this->createLocalHoliday('San Vicente Ferrer', $valencia, $valenciaProvince, $spain ?? null);
            }
        }

        // Fiestas de Sevilla
        if ($andalucia) {
            $sevillaProvince = Province::where('autonomous_community_id', $andalucia->id)->first();
            if ($sevillaProvince) {
                $this->createLocalHoliday('Semana Santa', $andalucia, $sevillaProvince, $spain ?? null);
                $this->createLocalHoliday('Feria de Abril', $andalucia, $sevillaProvince, $spain ?? null);
            }
        }

        // Fiestas de Santiago de Compostela
        if ($galicia) {
            $corunaProvince = Province::where('autonomous_community_id', $galicia->id)->first();
            if ($corunaProvince) {
                $this->createLocalHoliday('Santiago Apóstol', $galicia, $corunaProvince, $spain ?? null);
            }
        }

        // Fiestas de Bilbao
        if ($paisVasco) {
            $vizcayaProvince = Province::where('autonomous_community_id', $paisVasco->id)->first();
            if ($vizcayaProvince) {
                $this->createLocalHoliday('Aste Nagusia', $paisVasco, $vizcayaProvince, $spain ?? null);
            }
        }

        // Fiestas de municipios específicos
        $this->createMunicipalitySpecificHolidays();
    }

    /**
     * Crear ubicación para una fiesta local.
     */
    private function createLocalHoliday($holidayName, $autonomousCommunity, $province, $country): void
    {
        $holiday = CalendarHoliday::where('name', 'LIKE', "%{$holidayName}%")->first();
        if ($holiday) {
            CalendarHolidayLocation::create([
                'calendar_holiday_id' => $holiday->id,
                'autonomous_community_id' => $autonomousCommunity->id,
                'province_id' => $province->id,
                'country_id' => $country ? $country->id : null,
                'municipality_id' => null,
            ]);
        }
    }

    /**
     * Crear ubicaciones para fiestas de municipios específicos.
     */
    private function createMunicipalitySpecificHolidays(): void
    {
        // Buscar municipios importantes
        $madridCity = Municipality::where('name', 'Madrid')->first();
        $barcelonaCity = Municipality::where('name', 'Barcelona')->first();
        $valenciaCity = Municipality::where('name', 'Valencia')->first();
        $sevillaCity = Municipality::where('name', 'Sevilla')->first();
        $bilbaoCity = Municipality::where('name', 'Bilbao')->first();
        $santiagoCity = Municipality::where('name', 'Santiago de Compostela')->first();

        // Fiestas de Madrid
        if ($madridCity) {
            $this->createMunicipalityHoliday('San Isidro', $madridCity);
            $this->createMunicipalityHoliday('Virgen de la Almudena', $madridCity);
        }

        // Fiestas de Barcelona
        if ($barcelonaCity) {
            $this->createMunicipalityHoliday('La Mercè', $barcelonaCity);
            $this->createMunicipalityHoliday('Sant Joan', $barcelonaCity);
        }

        // Fiestas de Valencia
        if ($valenciaCity) {
            $this->createMunicipalityHoliday('Las Fallas', $valenciaCity);
            $this->createMunicipalityHoliday('San Vicente Ferrer', $valenciaCity);
        }

        // Fiestas de Sevilla
        if ($sevillaCity) {
            $this->createMunicipalityHoliday('Semana Santa', $sevillaCity);
            $this->createMunicipalityHoliday('Feria de Abril', $sevillaCity);
        }

        // Fiestas de Bilbao
        if ($bilbaoCity) {
            $this->createMunicipalityHoliday('Aste Nagusia', $bilbaoCity);
        }

        // Fiestas de Santiago de Compostela
        if ($santiagoCity) {
            $this->createMunicipalityHoliday('Santiago Apóstol', $santiagoCity);
        }
    }

    /**
     * Crear ubicación para una fiesta de municipio específico.
     */
    private function createMunicipalityHoliday($holidayName, $municipality): void
    {
        $holiday = CalendarHoliday::where('name', 'LIKE', "%{$holidayName}%")->first();
        if ($holiday) {
            // Buscar la provincia y comunidad autónoma del municipio
            $province = $municipality->province;
            $autonomousCommunity = $province ? $province->autonomousCommunity : null;
            $country = $autonomousCommunity ? $autonomousCommunity->country : null;

            CalendarHolidayLocation::create([
                'calendar_holiday_id' => $holiday->id,
                'municipality_id' => $municipality->id,
                'province_id' => $province ? $province->id : null,
                'autonomous_community_id' => $autonomousCommunity ? $autonomousCommunity->id : null,
                'country_id' => $country ? $country->id : null,
            ]);
        }
    }

    /**
     * Mostrar estadísticas del seeder.
     */
    private function showStatistics(): void
    {
        $totalLocations = CalendarHolidayLocation::count();
        $totalHolidays = CalendarHoliday::count();
        $locationsWithCountry = CalendarHolidayLocation::whereNotNull('country_id')->count();
        $locationsWithAutonomousCommunity = CalendarHolidayLocation::whereNotNull('autonomous_community_id')->count();
        $locationsWithProvince = CalendarHolidayLocation::whereNotNull('province_id')->count();
        $locationsWithMunicipality = CalendarHolidayLocation::whereNotNull('municipality_id')->count();

        $this->command->info('📊 Estadísticas del seeder de ubicaciones de fiestas:');
        $this->command->info("   • Total de ubicaciones creadas: {$totalLocations}");
        $this->command->info("   • Total de fiestas en el sistema: {$totalHolidays}");
        $this->command->info("   • Ubicaciones a nivel nacional: {$locationsWithCountry}");
        $this->command->info("   • Ubicaciones a nivel autonómico: {$locationsWithAutonomousCommunity}");
        $this->command->info("   • Ubicaciones a nivel provincial: {$locationsWithProvince}");
        $this->command->info("   • Ubicaciones a nivel municipal: {$locationsWithMunicipality}");

        // Mostrar algunas ubicaciones creadas
        $recentLocations = CalendarHolidayLocation::with(['holiday', 'municipality', 'province', 'autonomousCommunity', 'country'])
            ->latest()
            ->take(10)
            ->get();

        $this->command->info('📍 Últimas ubicaciones creadas:');
        foreach ($recentLocations as $location) {
            $locationInfo = [];
            if ($location->municipality) $locationInfo[] = $location->municipality->name;
            if ($location->province) $locationInfo[] = $location->province->name;
            if ($location->autonomousCommunity) $locationInfo[] = $location->autonomousCommunity->name;
            if ($location->country) $locationInfo[] = $location->country->name;
            
            $locationString = implode(' → ', $locationInfo);
            $this->command->info("   • {$location->holiday->name}: {$locationString}");
        }
    }
}
