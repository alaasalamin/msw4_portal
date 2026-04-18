<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrmService
{
    private string $baseUrl;
    private string $secret;

    public function __construct()
    {
        $this->baseUrl = rtrim(Setting::get('crm_url') ?? '', '/');
        $this->secret  = Setting::get('crm_secret') ?? '';
    }

    public function isConfigured(): bool
    {
        return filled($this->baseUrl) && filled($this->secret);
    }

    /** Register (or re-register) a form in the CRM — idempotent */
    public function registerForm(string $crmKey, string $formName, string $tableName, array $fields): bool
    {
        if (! $this->isConfigured()) return false;

        try {
            $response = $this->post('/api/forms/register', [
                'key'        => $crmKey,
                'name'       => $formName,
                'table_name' => $tableName,
                'fields'     => $fields,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('CRM registerForm failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /** Push a form submission to the CRM */
    public function submitEntry(string $crmKey, array $data): bool
    {
        if (! $this->isConfigured()) return false;

        try {
            $response = $this->post("/api/forms/{$crmKey}/submit", ['data' => $data]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('CRM submitEntry failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function post(string $path, array $payload): Response
    {
        return Http::timeout(10)
            ->withHeader('X-Secret-Key', $this->secret)
            ->post($this->baseUrl . $path, $payload);
    }
}
