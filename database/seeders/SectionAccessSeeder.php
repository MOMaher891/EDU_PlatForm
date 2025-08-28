<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseSection;

class SectionAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all course sections
        $sections = CourseSection::all();

        foreach ($sections as $section) {
            // Randomly make some sections purchasable
            if (rand(1, 3) === 1) { // 33% chance
                $price = rand(10, 50); // Random price between $10-$50
                $discountPrice = rand(1, 3) === 1 ? $price * 0.8 : null; // 33% chance of discount

                $section->update([
                    'price' => $price,
                    'discount_price' => $discountPrice,
                    'is_purchasable_separately' => true
                ]);
            }
        }

        $this->command->info('Section pricing data seeded successfully!');
    }
}
