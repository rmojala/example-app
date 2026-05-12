<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $admin->grantAdminRole();

        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => 'password',
        ]);

        $user1->notes()->createMany([
            [
                'title' => 'User 1\'s first note',
                'details' => 'These are the details of the note.',
            ],
            [
                'title' => 'User 1\'s second note',
            ],
            [
                'title' => 'User 1\'s third note',
            ],
        ]);

        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => 'password',
        ]);

        $user2->notes()->createMany([
            [
                'title' => 'User 2\'s first note',
            ],
            [
                'title' => 'User 2\'s second note',
            ],
        ]);
    }
}
