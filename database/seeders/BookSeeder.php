<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                "id" => 2,
                "name" => "book",
                "description" => "buku 1 adalah buku yang keren",
                "author" => "kanye",
                "publisher" => "GOOD BOOK",
                "published_date" => "2009-09-15",
                "pages" => 255,
                "cover_url" => "storage/covers/9BPqjNLp3JMpdAVVK2E7VDrE8cHOBQQwmvjl0uOp.jpg",
                "pdf_url" => "storage/pdfs/evo4x8ononVlOnaYnzQtUHcD9u05ddToap4ausbb.pdf",
                "is_visible" => true,
                "readers" => 0,
                "likes" => 0,
                "dislikes" => 0,
                "favorites" => 0,
                "user_id" => 1,
                "categories" => [6]
            ],
            [
                "id" => 3,
                "name" => "buku 2",
                "description" => "buku buku 22 haha",
                "author" => "dave grohl",
                "publisher" => "BOOK 123",
                "published_date" => "2010-02-08",
                "pages" => 344,
                "cover_url" => "storage/covers/a8IwmQB7nbbPTxMix9cmFxfjI3bW1GYJahv6Fy0v.jpg",
                "pdf_url" => "storage/pdfs/0KpFeCSbTgiALKVoX8yvfwO9BvgwRExZAfpi5DEo.pdf",
                "is_visible" => true,
                "readers" => 0,
                "likes" => 0,
                "dislikes" => 0,
                "favorites" => 0,
                "user_id" => 1,
                "categories" => [1, 3, 5]
            ],
            [
                "id" => 4,
                "name" => "buku 2",
                "description" => "buku buku 22 haha",
                "author" => "dave grohl",
                "publisher" => "BOOK 123",
                "published_date" => "2010-02-08",
                "pages" => 344,
                "cover_url" => "storage/covers/rmgJOER4xfNiUjPtE9hmxCyZbkRlr9R3vTnpqr4B.jpg",
                "pdf_url" => "storage/pdfs/iyXZ3NEiZlnKQ5EHsh4oh9QVQoRGR9FI48nVvMt9.pdf",
                "is_visible" => true,
                "readers" => 0,
                "likes" => 0,
                "dislikes" => 0,
                "favorites" => 0,
                "user_id" => 1,
                "categories" => [1, 3, 5]
            ]
        ];

        foreach ($books as $data) {
            // Insert or update book
            $book = Book::updateOrCreate(
                ["id" => $data["id"]],
                collect($data)->except("categories")->toArray()
            );

            // Attach pivot categories
            if (isset($data["categories"])) {
                $book->categories()->sync($data["categories"]);
            }
        }
    }
}
