<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a demo user if doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'demo@mnvfinancial.com'],
            [
                'name' => 'Usuário Demo',
                'password' => bcrypt('password'),
            ]
        );

        // Create sample accounts
        $accounts = [
            [
                'name' => 'Conta Corrente Principal',
                'type' => 'checking',
                'balance' => 5000.00,
                'description' => 'Conta principal para movimentações do dia a dia',
            ],
            [
                'name' => 'Poupança',
                'type' => 'savings',
                'balance' => 15000.00,
                'description' => 'Reserva de emergência',
            ],
            [
                'name' => 'Cartão de Crédito',
                'type' => 'credit_card',
                'balance' => -2500.00,
                'description' => 'Cartão de crédito com limite de R$ 10.000',
            ],
            [
                'name' => 'Investimentos',
                'type' => 'investment',
                'balance' => 25000.00,
                'description' => 'Aplicações em renda fixa e variável',
            ],
        ];

        foreach ($accounts as $accountData) {
            Account::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $accountData['name'],
                ],
                array_merge($accountData, ['user_id' => $user->id])
            );
        }
    }
}
