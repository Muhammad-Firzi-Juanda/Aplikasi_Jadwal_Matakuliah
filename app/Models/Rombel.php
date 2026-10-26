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
        'mata_kuliah_detail_id', 'kode_rombel', 'nomor_rombel', 'kelas',
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

    /**
     * Scope untuk menyaring dan mengecualikan rombel kelas mengulang.
     */
    public function scopeBukanMengulang($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('kelas')
                ->orWhere(function ($sub) {
                    $sub->where('kelas', 'not like', '%ulang%')
                        ->where('kelas', 'not like', '%mengulang%');
                });
        })
            ->where('kode_rombel', 'not like', '%-ulang%')
            ->where('kode_rombel', 'not like', '%-mengulang%')
            ->whereHas('mataKuliah', fn ($mk) => $mk->bukanMengulang());
    }

    /**
     * Cek apakah rombel ini merupakan kelas mengulang.
     */
    public function getIsMengulangAttribute(): bool
    {
        $kelas = strtolower((string) ($this->kelas ?? ''));
        $kode = strtolower((string) ($this->kode_rombel ?? ''));

        return str_contains($kelas, 'ulang')
            || str_contains($kode, '-ulang')
            || str_contains($kode, '-mengulang')
            || (bool) ($this->mataKuliah?->is_mengulang);
    }

    /**
     * Kode mata kuliah murni tanpa imbuhan kelas.
     */
    public function getCleanKodeMkAttribute(): string
    {
        if (! empty($this->mataKuliah?->clean_kode_mk)) {
            return $this->mataKuliah->clean_kode_mk;
        }

        return preg_replace('/-[A-Za-z0-9]+$/', '', $this->kode_rombel ?? '');
    }

    /**
     * Label rombel/kelas saja (A, B, C, D, ULANG) sesuai data.
     */
    public function getKelasLabelAttribute(): string
    {
        if (! empty($this->kelas)) {
            return strtoupper(trim($this->kelas));
        }

        if (! empty($this->mataKuliah?->kelas_label)) {
            return $this->mataKuliah->kelas_label;
        }

        if (preg_match('/-([A-Za-z0-9]+)$/', $this->kode_rombel ?? '', $matches)) {
            return strtoupper($matches[1]);
        }

        if (! empty($this->nomor_rombel)) {
            return chr(64 + min(26, (int) $this->nomor_rombel));
        }

        return 'A';
    }
}
