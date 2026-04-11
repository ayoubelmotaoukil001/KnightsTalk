<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Local helper: makes the first registered user an admin so /admin/* works.
     * Run: php artisan db:seed
     */
    public function run(): void
    {
        $first = User::query()->orderBy('id')->first();
        if ($first && ! $first->is_admin) {
            $first->update(['is_admin' => true]);
        }
    }
}
