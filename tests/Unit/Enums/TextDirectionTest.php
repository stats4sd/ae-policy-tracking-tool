<?php

use App\Enums\TextDirection;

describe('TextDirection enum', function () {

    it('has the LEFT_TO_RIGHT case with value left_to_right', function () {
        expect(TextDirection::LEFT_TO_RIGHT->value)->toBe('left_to_right');
    });

    it('has the RIGHT_TO_LEFT case with value right_to_left', function () {
        expect(TextDirection::RIGHT_TO_LEFT->value)->toBe('right_to_left');
    });

    it('provides a human readable label for LEFT_TO_RIGHT', function () {
        expect(TextDirection::LEFT_TO_RIGHT->getLabel())->toBe('Left to right');
    });

    it('provides a human readable label for RIGHT_TO_LEFT', function () {
        expect(TextDirection::RIGHT_TO_LEFT->getLabel())->toBe('Right to left');
    });

    it('provides a description for LEFT_TO_RIGHT', function () {
        expect(TextDirection::LEFT_TO_RIGHT->getDescription())
            ->toContain('left side')
            ->toContain('right');
    });

    it('provides a description for RIGHT_TO_LEFT', function () {
        expect(TextDirection::RIGHT_TO_LEFT->getDescription())
            ->toContain('right side')
            ->toContain('left');
    });

    it('can be cast from string value', function () {
        expect(TextDirection::from('left_to_right'))->toBe(TextDirection::LEFT_TO_RIGHT)
            ->and(TextDirection::from('right_to_left'))->toBe(TextDirection::RIGHT_TO_LEFT);
    });

    it('has exactly two cases', function () {
        expect(TextDirection::cases())->toHaveCount(2);
    });

});
