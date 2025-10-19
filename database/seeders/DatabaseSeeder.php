<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $docs = User::factory()->create([
            'name' => 'Docs',
            'email' => 'docs@example.net',
            'password' => Hash::make('docs123'),
            'role' => 'docs'
        ]);

        $token = $docs->createToken('admin')->plainTextToken;
        $this->setEnvValue('SCRIBE_AUTH_KEY', "Bearer {$token}");
        $this->setEnvValue('SCRIBE_AUTH_PLACEHOLDER', $token);
    }

    private function setEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return;
        }

        $escaped = preg_quote("{$key}=", '/');

        if (preg_match("/^{$escaped}.*/m", file_get_contents($path))) {
            file_put_contents(
                $path,
                preg_replace(
                    "/^{$escaped}.*/m",
                    "{$key}=\"{$value}\"",
                    file_get_contents($path)
                )
            );
        } else {
            file_put_contents($path, PHP_EOL . "{$key}=\"{$value}\"", FILE_APPEND);
        }
    }
}
