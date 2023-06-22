<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            OwnerSeeder::class,
            TicketStatusSeeder::class,
            AppointmentTypesSeeder::class,
            PaymentStatusSeeder::class,
            TicketPrioritySeeder::class,
            ContractDurationSeeder::class,
            ContractPaymentTermSeeder::class
            /*TicketContractTypes::class,
            TicketProblemTypes::class*/
        ]);
    }
}

