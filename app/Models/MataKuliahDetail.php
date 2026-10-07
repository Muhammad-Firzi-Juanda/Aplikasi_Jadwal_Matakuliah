<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliahDetail extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah_details';

    protected $fillable = [
        'kode_mk', 'nama_mk', 'sks', 'semester', 'tipe', 'prodi',
        'dosen_ketua_id', 'dosen_anggota_id', 'jumlah_kelas',
        'kapasitas_per_kelas', 'butuh_lab', 'catatan', 'is_active',
    ];

    protected $casts = [
        'butuh_lab' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function dosenKetua(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_ketua_id');
    }

    public function dosenAnggota(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_anggota_id');
    }

    public function rombels(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }

    public function scopeSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeWajib($query)
    {
        return $query->where('tipe', 'Wajib');
    }

    public function scopePilihan($query)
    {
        return $query->where('tipe', 'Pilihan');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProdi($query, $prodi)
    {
        return $query->where('prodi', $prodi);
    }
}
