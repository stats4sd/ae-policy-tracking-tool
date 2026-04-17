<?php

use App\Models\Assessment;
use App\Models\User;
use Filament\Panel;

describe('User model', function () {

    it('isAdmin returns true for a user with the admin role', function () {
        $user = User::factory()->create();
        $user->assignRole('admin');

        expect($user->isAdmin())->toBeTrue();
    });

    it('isAdmin returns false for a user without the admin role', function () {
        $user = User::factory()->create();

        expect($user->isAdmin())->toBeFalse();
    });

    it('canAccessTenant returns true for assigned assessment', function () {
        $user = User::factory()->create();
        $assessment = Assessment::factory()->create();
        $assessment->members()->attach($user->id);

        expect($user->fresh()->canAccessTenant($assessment))->toBeTrue();
    });

    it('canAccessTenant returns false for unassigned assessment', function () {
        $user = User::factory()->create();
        $assessment = Assessment::factory()->create();

        expect($user->fresh()->canAccessTenant($assessment))->toBeFalse();
    });

    it('canAccessTenant returns true for admin regardless of assignment', function () {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $assessment = Assessment::factory()->create();

        expect($user->fresh()->canAccessTenant($assessment))->toBeTrue();
    });

    it('canAccessPanel returns true for admin on admin panel', function () {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $panel = app(Panel::class)->id('admin');
        expect($user->canAccessPanel($panel))->toBeTrue();
    });

    it('canAccessPanel returns false for non-admin on admin panel', function () {
        $user = User::factory()->create();

        $panel = app(Panel::class)->id('admin');
        expect($user->canAccessPanel($panel))->toBeFalse();
    });

    it('canAccessPanel returns true for any user on app panel', function () {
        $user = User::factory()->create();

        $panel = app(Panel::class)->id('app');
        expect($user->canAccessPanel($panel))->toBeTrue();
    });

    it('getTenants returns all assessments for admin user', function () {
        $user = User::factory()->create();
        $user->assignRole('admin');
        Assessment::factory()->count(3)->create();

        $panel = app(Panel::class)->id('app');
        $tenants = $user->getTenants($panel);

        expect($tenants->count())->toBe(Assessment::count());
    });

    it('getTenants returns only assigned assessments for regular user', function () {
        $user = User::factory()->create();
        $assigned = Assessment::factory()->create();
        Assessment::factory()->create(); // unassigned
        $assigned->members()->attach($user->id);

        $panel = app(Panel::class)->id('app');
        $tenants = $user->getTenants($panel);

        expect($tenants->count())->toBe(1)
            ->and($tenants->first()->id)->toBe($assigned->id);
    });

});
