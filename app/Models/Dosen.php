<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosens';
    protected $fillable = ['nidn', 'nip', 'nama', 'jabatan', 'prodi', 'email', 'telepon', 'is_active'];

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function rombelsKetua(): HasMany
    {
        return $this->hasMany(Rombel::class, 'dosen_pengampu_id')->where('peran_dosen', 'Ketua');
    }

    public function rombelsAnggota(): HasMany
    {
        return $this->hasMany(Rombel::class, 'dosen_pengampu_id')->where('peran_dosen', 'Anggota');
    }

    public function matakuliahKetua(): HasMany
    {
        return $this->hasMany(MataKuliahDetail::class, 'dosen_ketua_id');
    }

    public function matakuliahAnggota(): HasMany
    {
        return $this->hasMany(MataKuliahDetail::class, 'dosen_anggota_id');
    }

    public function isKetua(): bool
    {
        return in_array($this->jabatan, ['Ketua', 'Ketua & Anggota']);
    }

    public function isAnggota(): bool
    {
        return in_array($this->jabatan, ['Anggota', 'Ketua & Anggota']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}