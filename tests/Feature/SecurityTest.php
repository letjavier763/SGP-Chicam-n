<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Support\Facades\File;

class SecurityTest extends TestCase
{
    /**
     * Test that SQL injection attempts are blocked.
     */
    public function test_login_blocks_sqli_attempts(): void
    {
        $this->withoutExceptionHandling();
        // Define some classic SQLi payloads
        $payloads = [
            "admin' OR '1'='1",
            "admin' UNION SELECT * FROM usuarios --",
            "admin' AND sleep(5) --",
            "admin' #",
        ];

        foreach ($payloads as $payload) {
            $response = $this->post('/login', [
                'username' => $payload,
                'password' => 'some_password',
            ]);

            // The middleware blocks this and returns a redirect back with error
            $response->assertSessionHasErrors('username');
            $this->assertGuest();
        }
    }

    /**
     * Test that a security log is generated in storage/logs/security.log.
     */
    public function test_login_blocks_sqli_and_logs(): void
    {
        $this->withoutExceptionHandling();
        $logPath = storage_path('logs/laravel.log');

        $response = $this->post('/login', [
            'username' => "admin' OR 1=1",
            'password' => 'some_password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();

        $this->assertTrue(File::exists($logPath), 'laravel.log file should exist');
        $this->assertStringContainsString('SQL Injection attempt detected', File::get($logPath));
    }
}
