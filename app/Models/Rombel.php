<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombel extends Model
{
    use HasFactory;

    protected $table = 'rombels';

    protected $fillable = [
        'mata_kuliah_detail_id', 'kode_rombel', 'nomor_rombel',
        'kapasitas', 'ruangan_id', 'hari', 'jam_mulai', 'jam_selesai',
        'dosen_pengampu_id', 'peran_dosen', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliahDetail::class, 'mata_kuliah_detail_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function dosenPengampu(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pengampu_id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function scopeHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDurasiMenitAttribute(): int
    {
        $mulai = Carbon::parse($this->jam_mulai);
        $selesai = Carbon::parse($this->jam_selesai);

        return $mulai->diffInMinutes($selesai);
    }
}
