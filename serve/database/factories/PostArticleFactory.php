<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostArticle>
 */
class PostArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cat_id' => 1,
            'user_id' => 1,
            'mode_id' => 1,
            'title' => $this->faker->company,
            'keywords' => $this->faker->name,
            'description' => $this->faker->catchPhrase(),
            'content' => $this->faker->text(200),
        ];
    }
}
