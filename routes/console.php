<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:make-admin {email}', function (string $email) {
    $user = User::query()->where('email', $email)->first();
    if (! $user) {
        $this->error("No user found with email: {$email}");

        return 1;
    }
    $user->forceFill(['is_admin' => true])->save();
    $this->info("OK — {$email} is now an admin. Log out and log in if the site still acts old.");

    return 0;
})->purpose('Set is_admin = true for a user (for /admin routes)');
