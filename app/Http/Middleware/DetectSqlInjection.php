<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DetectSqlInjection
{
    /**
     * SQL Injection detection patterns.
     */
    protected array $patterns = [
        '/(union\s+all\s+select|union\s+select)/i',
        '/or\s+\d+\s*=\s*\d+/i',
        '/or\s+["\']?\w+["\']?\s*=\s*["\']?\w+["\']/i',
        '/and\s+\d+\s*=\s*\d+/i',
        '/exec\s*\(\s*\@/i',
        '/\b(select|insert|update|delete|drop|alter|create|truncate)\b.*\b(from|into|table|set)\b/is',
        '/["\']\s*or\s*["\']/i',
        '/["\']\s*and\s*["\']/i',
        '/(?:--|\#|\/\*)\s*$/',
        '/\/\*.*?\*\//s',
        '/\bwaitfor\s+delay\b/i',
        '/\bpg_sleep\b/i',
        '/\bsleep\(\d+\)/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $inputs = $request->all();

        if ($this->detectSqlInjection($inputs)) {
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            $method = $request->method();
            $url = $request->fullUrl();

            // 1. Log to standard Laravel log
            $logMessage = sprintf(
                "SQL Injection attempt detected! IP: %s, URL: %s, Method: %s, User-Agent: %s, Inputs: %s",
                $ip,
                $url,
                $method,
                $userAgent,
                json_encode($request->except(['password'])) // exclude password for security
            );
            
            Log::warning($logMessage);

            // 2. Log in Database Bitacora if user exists
            try {
                $firstUser = Usuario::first();
                if ($firstUser) {
                    Bitacora::registrar(
                        $firstUser->id_usuario,
                        'intento_sqli',
                        'usuarios',
                        null,
                        "Intento de inyección SQL detectado desde la IP: {$ip}. Inputs: " . substr(json_encode($request->except(['password'])), 0, 500),
                        $ip
                    );
                }
            } catch (\Throwable $e) {
                // Fail silently if DB is not ready to prevent side-channel crashing
                Log::error("Failed to write SQLi attempt to DB Bitacora: " . $e->getMessage());
            }

            // 3. Block request
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Petición rechazada debido a actividad sospechosa detectada.',
                ], 400);
            }

            return back()->withErrors([
                'username' => 'Actividad sospechosa detectada. Intento registrado.',
            ]);
        }

        return $next($request);
    }

    /**
     * Recursively scan inputs for SQL Injection patterns.
     */
    protected function detectSqlInjection($value): bool
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->detectSqlInjection($item)) {
                    return true;
                }
            }
        } elseif (is_string($value)) {
            foreach ($this->patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    return true;
                }
            }
        }

        return false;
    }
}
