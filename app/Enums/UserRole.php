<?php

namespace App\Enums;

class UserRole
{
    public const SUPER_ADMIN = 'Super Admin';

    public const ADMIN = 'Admin';

    public const FAKULTAS = 'Fakultas';

    public const JURUSAN = 'Jurusan';

    public const PRODI = 'Prodi';

    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::FAKULTAS,
            self::JURUSAN,
            self::PRODI,
        ];
    }

    public static function nonAdmin(): array
    {
        return [
            self::ADMIN,
            self::FAKULTAS,
            self::JURUSAN,
            self::PRODI,
        ];
    }

    public static function canManageUsers(string $role): bool
    {
        return in_array($role, [self::SUPER_ADMIN, self::ADMIN]);
    }
}
