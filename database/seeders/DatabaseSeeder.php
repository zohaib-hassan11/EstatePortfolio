<?php

namespace Database\Seeders;

use App\Models\Enquiry;
use App\Models\Property;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@carterproperty.example'],
            ['name' => config('agent.name'), 'password' => Hash::make('password')],
        );

        $this->call([
            PropertySeeder::class,
            TestimonialSeeder::class,
            EnquirySeeder::class,
        ]);
    }
}
