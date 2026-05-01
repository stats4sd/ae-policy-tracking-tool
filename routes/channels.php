<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('assessment.{assessmentId}', function ($user, $assessmentId) {
    return $user->assessments()->where('assessments.id', $assessmentId)->exists();
});
