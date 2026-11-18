<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'mahasiswa_id', // Jaga-jaga jika starter code memakai mahasiswa_id
        'jawaban_teks',
        'jawaban',      // Jaga-jaga jika starter code memakai jawaban
        'submitted_at',
        'nilai',
        'catatan_dosen',
    ];

    // Mengubah submitted_at dari string menjadi objek Carbon/datetime agar bisa memakai translatedFormat()
    protected $casts = [
        'submitted_at' => 'datetime',
        'nilai' => 'integer',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    // Accessor untuk $sub->status_nilai (sesuai baris 181 di submissions.blade.php)
    public function getStatusNilaiAttribute(): string
    {
        if (is_null($this->nilai)) {
            return 'Belum Dinilai';
        }

        return $this->nilai >= 60 ? 'Lulus' : 'Remedial';
    }

    // Accessor cadangan jika ada file lain yang memanggil $sub->status_kelulusan
    public function getStatusKelulusanAttribute(): string
    {
        return $this->status_nilai;
    }
}