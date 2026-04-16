<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        // Ensure the 'admin' role exists for every test that needs it.
        // Spatie Permission requires roles to exist before assigning them.
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    })
    ->in('Feature', 'Unit');

/*
 * Provide the livewire() helper without requiring pestphp/pest-plugin-livewire.
 * Delegates to Livewire::test() so existing test syntax is unchanged.
 */
if (! function_exists('livewire')) {
    function livewire(string $component, array $params = []): Testable
    {
        return Livewire::test($component, $params);
    }
}
