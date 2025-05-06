<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserGroupsSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(StatusesSeeder::class);
        $this->call(IntegrationsSeeder::class);

        $this->call(PagesSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(PortfoliosSeeder::class);
        $this->call(TestimonialsSeeder::class);

        // $this->call(InvoicesSeeder::class);        
        // $this->call(TransactionsSeeder::class);        
    }
}
