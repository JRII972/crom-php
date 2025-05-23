<?php

namespace App\Api;

class Jeu
{
    public function handle(?string $id): array
    {
        // Example logic
        if ($id === null) {
            return ['error' => 'Missing ID'];
        }

        // Dummy response
        return ['id' => $id, 'name' => 'Fake Game', 'status' => 'success'];
    }
}
