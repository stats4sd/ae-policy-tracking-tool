<?php

namespace Database\Factories;

use App\Models\PolicyDocument;
use App\Models\PolicyDocumentPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PolicyDocumentPageFactory extends Factory
{
    protected $model = PolicyDocumentPage::class;

    public function definition(): array
    {
        return [
            'policy_document_id' => PolicyDocument::factory(),
            'page_number' => fake()->numberBetween(1, 100),
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
