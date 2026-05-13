<?php

namespace App\Services;

use App\Models\EngineRecord;
use App\Models\ObjectType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class EngineContractRenderer
{
    /**
     * Render an ObjectType's contract template with this record's data
     * substituted in, then turn the HTML into a PDF byte stream.
     */
    public function pdf(EngineRecord $record): string
    {
        $type = $record->type;
        if (! $type) {
            throw new \RuntimeException('Record is not bound to an object type.');
        }

        $html = $this->renderHtml($type, $record);

        return Pdf::loadHTML($this->wrap($html, $type, $record))
            ->setPaper('A4')
            ->output();
    }

    /**
     * Replace every `{{ key }}` / `{{ customer.field }}` placeholder in the
     * stored contract template with its real value. Anything we don't know
     * about is left blank.
     */
    public function renderHtml(ObjectType $type, EngineRecord $record): string
    {
        $template = (string) ($type->contract_template ?? '');
        if ($template === '') {
            return '<p><em>No contract template defined for this object type yet.</em></p>';
        }

        $variables = $this->variablesFor($type, $record);

        return preg_replace_callback(
            '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_.]*)\s*\}\}/',
            fn (array $match): string => (string) data_get($variables, $match[1], ''),
            $template,
        );
    }

    /**
     * The data bag available to placeholders. Record fields at the top level,
     * customer fields under `customer.*`, plus a few conveniences.
     */
    public function variablesFor(ObjectType $type, EngineRecord $record): array
    {
        $vars = [
            'id'         => $record->getKey(),
            'type'       => $type->name,
            'date'       => Carbon::now()->format('Y-m-d'),
            'date_long'  => Carbon::now()->translatedFormat('F j, Y'),
            'customer'   => $record->customer
                ? array_filter($record->customer->only([
                    'id', 'name', 'email', 'phone', 'company_name',
                    'address_street', 'address_building_number', 'address_city', 'address_zip_code',
                ]), fn ($v) => $v !== null)
                : [],
        ];

        foreach ($type->attributes ?? [] as $attr) {
            $key = $attr['key'] ?? null;
            if (! $key) continue;
            $value = $record->{$key} ?? null;
            if ($value !== null && ($attr['type'] ?? null) === 'boolean') {
                $value = $value ? 'Yes' : 'No';
            }
            $vars[$key] = (string) ($value ?? '');
        }

        return $vars;
    }

    /**
     * Wrap user-edited HTML in a print-friendly document shell so DomPDF
     * produces something that looks like a formal contract.
     */
    private function wrap(string $bodyHtml, ObjectType $type, EngineRecord $record): string
    {
        $title = e($type->name) . ' — Contract';
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$title}</title>
    <style>
        @page { margin: 24mm 22mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; color: #111; line-height: 1.55; }
        h1, h2, h3 { color: #0f172a; }
        h1 { font-size: 22pt; margin: 0 0 14pt; }
        h2 { font-size: 16pt; margin: 18pt 0 8pt; }
        h3 { font-size: 13pt; margin: 14pt 0 6pt; }
        p  { margin: 0 0 9pt; }
        ul, ol { margin: 0 0 9pt 22pt; }
        hr { border: none; border-top: 1px solid #d1d5db; margin: 18pt 0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; padding: 6pt 8pt; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 600; text-align: left; }
        .meta { font-size: 9.5pt; color: #6b7280; margin-bottom: 18pt; }
    </style>
</head>
<body>
    <div class="meta">Generated {$this->today()} · {$this->footerMeta($type, $record)}</div>
    {$bodyHtml}
</body>
</html>
HTML;
    }

    private function today(): string
    {
        return Carbon::now()->format('Y-m-d H:i');
    }

    private function footerMeta(ObjectType $type, EngineRecord $record): string
    {
        return e($type->name) . ' #' . $record->getKey();
    }
}
