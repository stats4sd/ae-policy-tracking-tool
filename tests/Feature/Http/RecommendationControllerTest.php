<?php

use App\Models\PriorityAction;
use App\Models\Recommendation;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('GET /recommendations', function () {

    it('returns all recommendations', function () {
        Recommendation::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->getJson('/recommendations')
            ->assertOk()
            ->assertJsonCount(3);
    });

    it('includes priority actions in the response', function () {
        $recommendation = Recommendation::factory()->create();
        PriorityAction::factory()->create(['recommendation_id' => $recommendation->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/recommendations')
            ->assertOk();

        $item = collect($response->json())->firstWhere('id', $recommendation->id);
        expect($item)->toHaveKey('priority_actions')
            ->and($item['priority_actions'])->toHaveCount(1);
    });

    it('returns empty array when no recommendations exist', function () {
        $this->actingAs($this->user)
            ->getJson('/recommendations')
            ->assertOk()
            ->assertJsonCount(0);
    });

    it('requires authentication', function () {
        $this->getJson('/recommendations')
            ->assertUnauthorized();
    });

});

describe('GET /recommendations/{recommendation}', function () {

    it('returns a single recommendation', function () {
        $recommendation = Recommendation::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/recommendations/{$recommendation->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $recommendation->id]);
    });

    it('returns 404 for a non-existent recommendation', function () {
        $this->actingAs($this->user)
            ->getJson('/recommendations/99999')
            ->assertNotFound();
    });

});

describe('GET /types', function () {

    it('returns all types', function () {
        $this->actingAs($this->user)
            ->getJson('/types')
            ->assertOk()
            ->assertJsonIsArray();
    });

    it('requires authentication', function () {
        $this->getJson('/types')
            ->assertUnauthorized();
    });

});
