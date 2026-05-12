<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class EngineRecord extends Model
{
    protected $guarded = ['id'];

    public ?ObjectType $type = null;

    public static function forType(ObjectType $type): self
    {
        $instance = new self();
        $instance->setTable($type->engineTable());
        $instance->type = $type;
        return $instance;
    }

    public function newInstance($attributes = [], $exists = false)
    {
        $instance = parent::newInstance($attributes, $exists);
        $instance->setTable($this->getTable());
        $instance->type = $this->type;
        return $instance;
    }

    /**
     * Livewire's ModelSynth uses this to re-hydrate a model from its id during
     * an update. A fresh `new EngineRecord` doesn't know which engine_<slug>
     * table to look in, so walk every type's table until we find the row.
     */
    public function newQueryForRestoration($ids)
    {
        if ($this->type) {
            return parent::newQueryForRestoration($ids);
        }

        $id = is_array($ids) ? ($ids[0] ?? null) : $ids;

        foreach (ObjectType::orderBy('id')->get() as $candidate) {
            $table = $candidate->engineTable();
            if (! Schema::hasTable($table)) continue;
            $instance = static::forType($candidate);
            if ($instance->newQuery()->whereKey($id)->exists()) {
                $this->setTable($table);
                $this->type = $candidate;
                return parent::newQueryForRestoration($ids);
            }
        }

        return parent::newQueryForRestoration($ids);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
