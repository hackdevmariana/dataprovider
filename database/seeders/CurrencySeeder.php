<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            // Monedas tradicionales principales
            [
                'iso_code' => 'EUR',
                'symbol' => '€',
                'name' => 'Euro',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'USD',
                'symbol' => '$',
                'name' => 'Dólar Estadounidense',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'GBP',
                'symbol' => '£',
                'name' => 'Libra Esterlina',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'JPY',
                'symbol' => '¥',
                'name' => 'Yen Japonés',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'CHF',
                'symbol' => 'CHF',
                'name' => 'Franco Suizo',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'CAD',
                'symbol' => 'C$',
                'name' => 'Dólar Canadiense',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'AUD',
                'symbol' => 'A$',
                'name' => 'Dólar Australiano',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            
            // Criptomonedas principales
            [
                'iso_code' => 'BTC',
                'symbol' => '₿',
                'name' => 'Bitcoin',
                'is_crypto' => true,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'ETH',
                'symbol' => 'Ξ',
                'name' => 'Ethereum',
                'is_crypto' => true,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'BNB',
                'symbol' => 'BNB',
                'name' => 'Binance Coin',
                'is_crypto' => true,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'ADA',
                'symbol' => 'ADA',
                'name' => 'Cardano',
                'is_crypto' => true,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'SOL',
                'symbol' => 'SOL',
                'name' => 'Solana',
                'is_crypto' => true,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'DOT',
                'symbol' => 'DOT',
                'name' => 'Polkadot',
                'is_crypto' => true,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            
            // Otras monedas importantes
            [
                'iso_code' => 'CNY',
                'symbol' => '¥',
                'name' => 'Yuan Chino',
                'is_crypto' => false,
                'is_supported_by_app' => false,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'RUB',
                'symbol' => '₽',
                'name' => 'Rublo Ruso',
                'is_crypto' => false,
                'is_supported_by_app' => false,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'INR',
                'symbol' => '₹',
                'name' => 'Rupia India',
                'is_crypto' => false,
                'is_supported_by_app' => false,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'BRL',
                'symbol' => 'R$',
                'name' => 'Real Brasileño',
                'is_crypto' => false,
                'is_supported_by_app' => false,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'MXN',
                'symbol' => '$',
                'name' => 'Peso Mexicano',
                'is_crypto' => false,
                'is_supported_by_app' => false,
                'exchangeable_in_calculator' => true,
            ],
            
            // Metales preciosos (como referencia)
            [
                'iso_code' => 'XAU',
                'symbol' => 'Au',
                'name' => 'Oro (onza troy)',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
            [
                'iso_code' => 'XAG',
                'symbol' => 'Ag',
                'name' => 'Plata (onza troy)',
                'is_crypto' => false,
                'is_supported_by_app' => true,
                'exchangeable_in_calculator' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['iso_code' => $currency['iso_code']],
                $currency
            );
        }

        $this->command->info('Currency seeder completed: ' . count($currencies) . ' currencies created/updated.');
    }
}