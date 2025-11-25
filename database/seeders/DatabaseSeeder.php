<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // $user = User::createOrFirst([
        //     'username' => 'kawhi',
        //     'email' => 'kawhi@example.com',
        //     'password' => 'pass123',
        //     'banner_url' => 'storage/covers/rmgJOER4xfNiUjPtE9hmxCyZbkRlr9R3vTnpqr4B.jpg',
        //     'profile_pic_url' => 'storage/covers/a8IwmQB7nbbPTxMix9cmFxfjI3bW1GYJahv6Fy0v.jpg'
        // ]);
        $this->call(CategorySeeder::class);
        $this->call(BookSeeder::class);
        // $user->favoriteCategories()->sync([1,3,5]);


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
