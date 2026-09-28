<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(rand(4, 8)),
            'slug' => null, // generated from the title by Article::booted()
            'excerpt' => fake()->paragraph(),
            'body' => implode("\n\n", fake()->paragraphs(rand(3, 6))),
            'category' => fake()->randomElement(Article::CATEGORIES),
            'cover_image' => 'https://picsum.photos/seed/'.Str::random(8).'/1200/630',
            'author_name' => fake()->name(),
            'likes_count' => fake()->numberBetween(0, 240),
            'published_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'user_id' => null,
        ];
    }

    /**
     * Mark the article as still in draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
