<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        Article::factory()->create(['published_at' => now()]);

        $this->get('/')->assertStatus(200);
    }

    public function test_article_list_and_read_pages_load(): void
    {
        $article = Article::factory()->create(['published_at' => now()]);

        $this->get('/articles')->assertStatus(200);
        $this->get(route('articles.show', $article->slug))->assertStatus(200);
    }

    public function test_like_endpoint_increments(): void
    {
        $article = Article::factory()->create(['published_at' => now(), 'likes_count' => 0]);

        $this->postJson(route('articles.like', $article))->assertOk()->assertJson(['likes' => 1]);

        $this->assertSame(1, $article->fresh()->likes_count);
    }
}
