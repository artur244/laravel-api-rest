<?php

namespace Tests\Feature\API;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BooksController extends TestCase
{
    use RefreshDatabase;

    public function test_get_books_endpoint()
    {
        $books = Book::factory(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $response->assertJson(function(AssertableJson $json) use ($books){
            $book = $books->first();


            // $json->whereType('0.id', 'integer');
            // $json->whereType('0.title', 'string');
            // $json->whereType('0.isbn', 'string');
            $json->whereAllType([
                '0.id' => 'integer',
                '0.title' => 'string',
                '0.isbn' => 'string'
            ]);

            $json->hasAll([
                '0.id',
                '0.title',
                '0.isbn',
            ]);

            $json->whereAll([
                '0.id' => $book->id,
                '0.title' => $book->title,
                '0.isbn' => $book->isbn,
            ]);
        });
    }

    public function test_get_single_book_endpoint()
    {
        $book = Book::factory(1)->createOne();
        $response = $this->getJson('/api/books/'. $book->id);

        $response->assertStatus(200);

        $response->assertJson(function(AssertableJson $json) use ($book){
            //Verifica se as colunas existem
            //Utilizar "etc" para ignorar colunas como "created_at" e "updated_at"
            //Caso não utilizar o "etc" ele vai retronar erro no teste por falta dessas colunas na função "hasAll"
            $json->hasAll([
                'id',
                'title',
                'isbn',
            ])->etc();

            //Verifica os tipos de cada coluna
            $json->whereAllType([
                'id' => 'integer',
                'title' => 'string',
                'isbn' => 'string'
            ]);

            //Verifica os valroes de cada coluna
            $json->whereAll([
                'id' => $book->id,
                'title' => $book->title,
                'isbn' => $book->isbn,
            ]);
        });
    }

    public function test_post_book_endpoint()
    {
        $book = Book::factory(1)->makeOne()->toArray();

        $response = $this->postJson('/api/books', $book);

        $response->assertStatus(201);

        $response->assertJson(function(AssertableJson $json) use ($book){
            //Verifica os valroes de cada coluna
            //Utilizar "etc" para ignorar colunas como "created_at" e "updated_at"
            //Caso não utilizar o "etc" ele vai retronar erro no teste por falta dessas colunas na função "whereAll"
            $json->whereAll([
                'title' => $book['title'],
                'isbn' => $book['isbn'],
            ])->etc();
        });
    }

    public function test_put_book_endpoint()
    {
        Book::factory(1)->createOne();

        $book = [
            'title' => 'Teste Atualizando',
            'isbn' => 1234567890,
        ];

        $response = $this->putJson('/api/books/1', $book);

        $response->assertStatus(200);

        $response->assertJson(function(AssertableJson $json) use ($book){
            //Verifica os valroes de cada coluna
            //Utilizar "etc" para ignorar colunas como "created_at" e "updated_at"
            //Caso não utilizar o "etc" ele vai retronar erro no teste por falta dessas colunas na função "whereAll"
            $json->whereAll([
                'title' => $book['title'],
                'isbn' => $book['isbn'],
            ])->etc();
        });
    }

    public function test_patch_book_endpoint()
    {
        Book::factory(1)->createOne();

        $book = [
            'title' => 'Teste Atualizando patch'
        ];

        $response = $this->patchJson('/api/books/1', $book);

        $response->assertStatus(200);

        $response->assertJson(function(AssertableJson $json) use ($book){
            //Verifica os valroes de cada coluna
            //Utilizar "etc" para ignorar colunas como "created_at" e "updated_at"
            //Caso não utilizar o "etc" ele vai retronar erro no teste por falta dessas colunas na função "where"
            $json->where('title', $book['title'])->etc();
        });
    }

    public function test_delete_book_endpoint()
    {
        Book::factory(1)->createOne();

        $response = $this->deleteJson('/api/books/1');

        $response->assertStatus(204);
    }

    public function test_post_book_should_validate_when_try_create_a_invalid_nullable_book()
    {
        $response = $this->postJson('/api/books', []);

        $response->assertStatus(422);

        $response->assertJson(function(AssertableJson $json){
            $json->hasAll(['message', 'errors']);
        });
    }
}
