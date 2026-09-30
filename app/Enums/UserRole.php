<?php

namespace App\Enums;

class UserRole
{
    public const SUPER_ADMIN = 'Super Admin';
    public const FAKULTAS = 'Fakultas';
    public const JURUSAN = 'Jurusan';
    public const PRODI = 'Prodi';

    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::FAKULTAS,
            self::JURUSAN,
            self::PRODI,
        ];
    }

    public static function nonAdmin(): array
    {
        return [
            self::FAKULTAS,
            self::JURUSAN,
            self::PRODI,
        ];
    }
}
