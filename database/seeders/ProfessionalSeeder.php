<?php

namespace Database\Seeders;

use App\Models\Professional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/professionals.json');

        if (! File::exists($jsonPath)) {
            $this->command->error("Professionals JSON file not found at: {$jsonPath}");

            return;
        }

        $professionals = json_decode(File::get($jsonPath), true);

        if (! is_array($professionals)) {
            $this->command->error("Invalid JSON format in: {$jsonPath}");

            return;
        }

        $this->command->info('Seeding '.count($professionals).' professionals...');

        $created = 0;
        $updated = 0;

        foreach ($professionals as $pro) {
            $model = Professional::updateOrCreate(
                ['email' => $pro['email']],
                [
                    'name' => $pro['name'],
                    'profession' => $pro['profession'],
                    'specialization' => $pro['specialization'],
                    'location' => $pro['location'],
                    'address' => $pro['address'],
                    'phone' => $pro['phone'],
                    'description' => $pro['description'],
                    'status' => $pro['status'] ?? 'active',
                ]
            );

            if ($model->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Professionals seeded successfully! ({$created} created, {$updated} updated, Total: ".Professional::count().')');
    }
}
