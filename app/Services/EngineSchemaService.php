<?php

namespace App\Services;

use App\Models\ObjectType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EngineSchemaService
{
    /**
     * Real DB table for a given object type's records (e.g. engine_used_phones).
     */
    public function tableFor(ObjectType $type): string
    {
        return 'engine_' . Str::snake(Str::ascii($type->slug));
    }

    public function tableExists(ObjectType $type): bool
    {
        return Schema::hasTable($this->tableFor($type));
    }

    /**
     * Create the table for a new object type.
     */
    public function create(ObjectType $type): void
    {
        $table = $this->tableFor($type);
        if (Schema::hasTable($table)) return;

        Schema::create($table, function (Blueprint $blueprint) use ($type) {
            $blueprint->id();
            $blueprint->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            foreach ($type->attributes ?? [] as $attr) {
                $this->addAttributeColumn($blueprint, $attr);
            }
            $blueprint->timestamps();
            $blueprint->index('customer_id');
        });
    }

    /**
     * Diff old vs new attribute lists and ALTER TABLE accordingly.
     */
    public function syncColumns(ObjectType $type, array $previousAttributes): void
    {
        $table = $this->tableFor($type);
        if (! Schema::hasTable($table)) {
            $this->create($type);
            return;
        }

        $previous = collect($previousAttributes)->keyBy('key');
        $current  = collect($type->attributes ?? [])->keyBy('key');

        $toAdd    = $current->diffKeys($previous);
        $toDrop   = $previous->diffKeys($current);
        // Same key, different type → drop + re-add the column.
        $toRetype = $current->filter(function ($curr, $key) use ($previous) {
            $prev = $previous->get($key);
            return $prev && ($prev['type'] ?? null) !== ($curr['type'] ?? null);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($toDrop, $toRetype) {
            foreach ($toDrop as $attr)   $blueprint->dropColumn($attr['key']);
            foreach ($toRetype as $attr) $blueprint->dropColumn($attr['key']);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($toAdd, $toRetype) {
            foreach ($toAdd as $attr)   $this->addAttributeColumn($blueprint, $attr);
            foreach ($toRetype as $attr) $this->addAttributeColumn($blueprint, $attr);
        });
    }

    public function drop(ObjectType $type): void
    {
        Schema::dropIfExists($this->tableFor($type));
    }

    private function addAttributeColumn(Blueprint $blueprint, array $attr): void
    {
        $key  = $attr['key']  ?? null;
        $kind = $attr['type'] ?? 'text';
        if (! $key) return;

        match ($kind) {
            'number'  => $blueprint->decimal($key, 15, 2)->nullable(),
            'date'    => $blueprint->date($key)->nullable(),
            'boolean' => $blueprint->boolean($key)->nullable(),
            'select'  => $blueprint->string($key)->nullable(),
            'random'  => $blueprint->string($key, 64)->nullable()->index(),
            default   => $blueprint->string($key)->nullable(),
        };
    }
}
