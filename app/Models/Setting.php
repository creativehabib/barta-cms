<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    /** Return the value cast to its declared type. */
    public function typedValue(): mixed
    {
        return match ($this->type) {
            'bool', 'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'int', 'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'json', 'array' => json_decode((string) $this->value, true) ?? [],
            default => $this->value,
        };
    }
}
