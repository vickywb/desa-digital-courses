<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    /**
    * Boot function dari Laravel Trait.
    * Otomatis dipanggil saat Model menggunakan Trait ini.
    * Saat membuat Model baru, generate UUID jika Primary Key kosong.
    */
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->getKey())) {
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());
            }
        });
    }

    /**
    * Memberitahu Laravel bahwa ID tidak auto-increment (karena pakai string UUID).
    */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
    * Memberitahu Laravel bahwa tipe Primary Key adalah string.
    */
    public function getKeyType(): string
    {
        return 'string';
    }
}