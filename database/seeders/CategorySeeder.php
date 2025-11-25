<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "id" => 1,
                "name" => "Fiksi",
                "description" => "Kumpulan buku fiksi",
                "pic_url" => "storage/categories/ZXJJEug9aCxCR5YaFtusP98Puf1XqvmbeJsicB83.jpg"
            ],
            [
                "id" => 3,
                "name" => "Kids",
                "description" => "Kids books stuff",
                "pic_url" => "storage/categories/u9rLzmaDsFogfySm3OD03y0qwLH7xitztA9DaIZ8.jpg"
            ],
            [
                "id" => 5,
                "name" => "Kerajaan",
                "description" => "Buku tentang kerajaan",
                "pic_url" => "storage/categories/m3VFSAK4FHv0kmhX8zDFSNi5WFOzwMhrD4RifgSv.jpg"
            ],
            [
                "id" => 6,
                "name" => "Fantasi",
                "description" => "Buku tentang fantasi",
                "pic_url" => "storage/categories/j81PEPfjd5Bblgwe3xBHJHja7qqIx87c9ORq9uGc.jpg"
            ],
            [
                "id" => 7,
                "name" => "Kuliner",
                "description" => "Buku tentang kuliner",
                "pic_url" => "storage/categories/QDKHvACFajAA7yeR5cFdzUBNsv0QosyxJ2sT3xZX.jpg"
            ],
            [
                "id" => 8,
                "name" => "Sejarah",
                "description" => "Buku tentang sejarah",
                "pic_url" => "storage/categories/Ph9yG89sYV3HnRz8CG0g7S7K3GNY0A4LuaM9Z2h2.jpg"
            ],
        ];
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],  
                $category
            );
        }
    }
}
