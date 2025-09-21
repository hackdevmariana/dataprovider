<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de ejemplo para Payment
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [];
            
            // Generar datos según los campos disponibles
            $data['user_id'] = rand(1, 10);
            $data['payable_type'] = 'App\Models\Subscription';
            $data['payable_id'] = rand(1, 5);
            $data['payment_intent_id'] = 'pi_' . uniqid();
            $data['status'] = 'completed';
            $data['type'] = 'subscription';
            $data['amount'] = rand(10, 1000);
            $data['fee'] = rand(1, 50);
            $data['net_amount'] = $data['amount'] - $data['fee'];
            $data['currency'] = 'EUR';
            $data['payment_method'] = 'card';
            $data['processor'] = 'stripe';
            $data['description'] = 'Pago de suscripción ' . $i;
            $data['processed_at'] = now();

            // Usar el primer campo como identificador único
            $uniqueField = 'user_id';
            Payment::updateOrCreate(
                [$uniqueField => $data[$uniqueField]],
                $data
            );
        }

        $this->command->info('Payment creados exitosamente.');
    }
}