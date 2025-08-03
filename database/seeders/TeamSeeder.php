<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            'Marketing',
            'Développement',
            'Design',
            'Support',
            'Direction'
        ];

        foreach ($teams as $team) {
            Team::create([
                'name' => $team,
                'slug' => Str::slug($team),
            ]);
        }
    }
}
