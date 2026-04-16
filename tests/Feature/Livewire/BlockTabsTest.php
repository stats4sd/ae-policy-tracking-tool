<?php

use App\Livewire\BlockTabs;
use Livewire\Livewire;

describe('BlockTabs component', function () {

    it('renders successfully', function () {
        Livewire::test(BlockTabs::class, [
            'tabs' => ['Tab One', 'Tab Two', 'Tab Three'],
        ])
            ->assertSuccessful();
    });

    it('defaults to active tab 0', function () {
        Livewire::test(BlockTabs::class, [
            'tabs' => ['Tab One', 'Tab Two'],
        ])
            ->assertSet('activeTab', 0);
    });

    it('setActiveTab updates the active tab', function () {
        Livewire::test(BlockTabs::class, [
            'tabs' => ['Tab One', 'Tab Two', 'Tab Three'],
        ])
            ->call('setActiveTab', 2)
            ->assertSet('activeTab', 2);
    });

    it('dispatches tabChanged event when active tab changes', function () {
        Livewire::test(BlockTabs::class, [
            'tabs' => ['Tab One', 'Tab Two'],
        ])
            ->call('setActiveTab', 1)
            ->assertDispatched('tabChanged', 1);
    });

});
