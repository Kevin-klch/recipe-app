<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Zugangsdaten:
     *   admin@admin.de  / admin      (is_admin)
     *   alle uebrigen   / password
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin', 'email' => 'admin@admin.de', 'password' => 'admin', 'is_admin' => true],
            ['name' => 'Anna Berger', 'email' => 'anna@example.de'],
            ['name' => 'Lukas Hoffmann', 'email' => 'lukas@example.de'],
            ['name' => 'Mia Schneider', 'email' => 'mia@example.de'],
            ['name' => 'Jonas Weber', 'email' => 'jonas@example.de'],
            ['name' => 'Lena Fischer', 'email' => 'lena@example.de'],
            ['name' => 'Tim Bauer', 'email' => 'tim@example.de'],
            ['name' => 'Sarah Wolf', 'email' => 'sarah@example.de'],
            ['name' => 'Felix Krüger', 'email' => 'felix@example.de'],
            ['name' => 'Laura Schmitt', 'email' => 'laura@example.de'],
        ];

        foreach ($users as $data) {
            // forceFill, weil is_admin und email_verified_at nicht fillable sind
            // und bei Mass Assignment sonst still verworfen wuerden.
            // Das Klartext-Passwort wird durch den 'hashed'-Cast des Models gehasht.
            User::firstOrNew(['email' => $data['email']])
                ->forceFill([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'] ?? 'password',
                    'is_admin' => $data['is_admin'] ?? false,
                    'email_verified_at' => now(),
                ])
                ->save();
        }
    }
}
