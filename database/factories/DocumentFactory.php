<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Collection;
use App\Models\Course;
use App\Models\Author;
use App\Models\Advisor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->generateSimilarTitle(),
            'subtitle' => $this->faker->sentence,
            'collection_id' => Collection::factory(),
            'course_id' => Course::factory(),
            'author_id' => Author::factory(),
            'advisor_id' => Advisor::factory(),
            'file' => 'test.pdf',
            'publicationYear' => $this->faker->year,
        ];
    }

    /**
     * Gera um título similar
     *
     * @return string
     */
    private function generateSimilarTitle()
    {
        $baseTitle = 'Document Title'; // Título base para a geração de títulos similares
        $suffix = $this->faker->unique()->numberBetween(1, 100); // Sufixo único para cada título

        return "{$baseTitle} {$suffix}";
    }
}
