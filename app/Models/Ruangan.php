<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';
    protected $fillable = ['kode', 'nama', 'tipe', 'kapasitas', 'gedung', 'lantai', 'is_active'];

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function rombels(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }

    public function scopeLab($query)
    {
        return $query->where('tipe', 'Lab');
    }

    public function scopeRuangKelas($query)
    {
        return $query->where('tipe', 'Ruang Kelas');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}