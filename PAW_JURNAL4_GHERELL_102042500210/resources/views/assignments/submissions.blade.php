@extends('layouts.app')

@section('title', 'Daftar Pengumpulan — ' . $assignment->judul)

@section('content')
<div style="margin-bottom: 1.25rem;">
    <a href="{{ route('courses.show', $assignment->course_id) }}" class="btn btn-secondary btn-sm">
        Kembali ke Mata Kuliah
    </a>
</div>

@php
    $gradedSubmissions = $submissions->whereNotNull('nilai');
    $averageScore = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('nilai'), 1) : null;
@endphp

<!-- Header Penugasan -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <span class="code-pill">{{ $assignment->course->kode_mk }}</span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">{{ $assignment->course->nama_mk }}</span>
            </div>
            <h1 class="card-title">{{ $assignment->judul }}</h1>
            <p class="card-subtitle">
                Batas Pengumpulan: <strong>{{ $assignment->deadline ? $assignment->deadline->translatedFormat('l, d F Y — H:i') . ' WIB' : '-' }}</strong>
                @if($assignment->isOverdue())
                    <span style="color: #dc2626; font-size: 0.8125rem; font-weight: 600; margin-left: 0.5rem;">Batas Waktu Terlewat</span>
                @else
                    <span style="color: #059669; font-size: 0.8125rem; font-weight: 600; margin-left: 0.5rem;">Sedang Berlangsung</span>
                @endif
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            
            {{-- TODO 7b: Tambahkan tombol Export CSV yang mengarah ke route('assignments.export-csv', $assignment) --}}
<!-- Tombol Export CSV di bagian Header Tugas -->
<div class="flex gap-2">
    <a href="{{ route('assignments.export-csv', $assignment->id) }}" class="bg-red-700 text-white px-4 py-2 rounded text-sm font-semibold">
        Export CSV
    </a>
</div>

<!-- Ringkasan Statistik & Rata-rata Nilai Otomatis -->
<div class="grid grid-cols-3 gap-4 my-4">
    <div>
        <p class="text-sm text-gray-600">Total Mahasiswa</p>
        <p class="text-lg font-bold">{{ $assignment->mataKuliah?->mahasiswas?->count() ?? $assignment->course?->students?->count() ?? $submissions->count() }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-600">Sudah Mengumpulkan</p>
        <p class="text-lg font-bold text-green-600">{{ $submissions->count() }}</p>
    </div>
    <div>
        <p class="text-sm text-gray-600">Rata-rata Nilai</p>
        <p class="text-lg font-bold text-blue-600">
            {{ $submissions->whereNotNull('nilai')->count() > 0 ? round($submissions->whereNotNull('nilai')->avg('nilai'), 1) : '-' }}
    </div>
</div>

<!-- Baris Tabel Rekapitulasi Pengumpulan & Penilaian Mahasiswa -->
@foreach($submissions as $index => $submission)
<tr class="border-b">
    <td class="py-3 px-4">{{ $index + 1 }}</td>
    <td class="py-3 px-4">
        <div class="font-semibold">{{ $submission->user->name }}</div>
        <div class="text-xs text-gray-500">{{ $submission->user->email }}</div>
    </td>
    <td class="py-3 px-4 font-semibold">{{ $submission->user->nim_nip }}</td>
    <td class="py-3 px-4">
        <span class="text-green-600 font-semibold text-sm">Sudah Mengumpulkan</span>
        <div class="text-xs text-gray-500">{{ $submission->created_at->format('d M Y, H:i') }} WIB</div>
        @if(!is_null($submission->nilai))
            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded bg-green-100 text-green-800 font-semibold">
                {{ $submission->nilai }}/100 ({{ $submission->status_kelulusan }})
            </span>
        @endif
    </td>
    <td class="py-3 px-4">
        <details class="text-sm text-red-700 cursor-pointer">
            <summary class="font-medium">Lihat Jawaban ({{ strlen($submission->jawaban_teks) }} Karakter)</summary>
            <p class="mt-2 text-gray-700 bg-gray-50 p-2 rounded">{{ $submission->jawaban_teks }}</p>
        </details>
    </td>
    <td class="py-3 px-4">
        <!-- Form Pengisian Nilai & Catatan Dosen -->
        <form action="{{ route('submissions.grade', $submission->id) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <input 
                type="number" 
                name="nilai" 
                min="0" 
                max="100" 
                value="{{ old('nilai', $submission->nilai) }}" 
                placeholder="0-100"
                required
                class="border rounded px-2 py-1 w-20 text-sm"
            >
            <input 
                type="text" 
                name="catatan_dosen" 
                value="{{ old('catatan_dosen', $submission->catatan_dosen) }}" 
                placeholder="Catatan untuk mahasiswa..."
                class="border rounded px-2 py-1 text-sm flex-1"
            >
            <button type="submit" class="bg-red-700 text-white px-3 py-1 rounded text-sm font-semibold">
                Simpan Nilai
            </button>
        </form>
    </td>
</tr>
@endforeach
            <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn btn-secondary btn-sm">
                Edit Tugas
            </a>
        </div>
    </div>

    <div class="card-body" style="background-color: #f8fafc; border-top: 1px solid var(--border-color); padding: 1rem 1.25rem;">
        <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.25rem;">PETUNJUK PENUGASAN:</div>
        <div style="font-size: 0.925rem; color: var(--text-main); white-space: pre-wrap; line-height: 1.6;">{{ $assignment->deskripsi }}</div>
    </div>
</div>

<!-- Statistik Pengumpulan & Nilai -->
{{-- TODO 7a: Tampilkan kartu ringkasan Total Mahasiswa, Jumlah Mengumpulkan, dan Rata-rata Nilai --}}

<!-- Daftar Pengumpulan Mahasiswa -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="card-title" style="font-size: 1.1rem;">Rekapitulasi Pengumpulan &amp; Penilaian Mahasiswa</h2>
            <p class="card-subtitle">Semua mahasiswa yang terdaftar resmi di kelas mata kuliah ini</p>
        </div>
    </div>

    <div class="card-body" style="padding: 0;">
        @if($students->isEmpty())
            <div class="empty-state" style="padding: 3rem 1.5rem;">
                <p style="font-weight: 600; margin-bottom: 0.25rem;">Belum ada mahasiswa yang disetujui.</p>
                <p style="font-size: 0.875rem; color: var(--slate-500);">Mahasiswa perlu mengajukan pendaftaran dan disetujui untuk mengikuti kelas mata kuliah ini terlebih dahulu.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th>Mahasiswa</th>
                            <th style="width: 130px;">NIM</th>
                            <th style="width: 160px;">Status &amp; Waktu</th>
                            <th>Jawaban Teks</th>
                            <th style="min-width: 280px;">Penilaian Dosen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            @php
                                $sub = $submissions->get($student->id);
                            @endphp
                            <tr>
                                <td style="color: var(--text-muted); font-size: 0.875rem;">{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main);">{{ $student->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $student->email }}</div>
                                </td>
                                <td>
                                    <span class="code-pill">{{ $student->nomor_induk ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($sub)
                                        <div style="color: #059669; font-weight: 600; font-size: 0.8125rem;">Sudah Mengumpulkan</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.125rem;">
                                            {{ $sub->submitted_at ? $sub->submitted_at->translatedFormat('d M Y, H:i') : $sub->updated_at->translatedFormat('d M Y, H:i') }} WIB
                                        </div>
                                        <div style="margin-top: 0.35rem;">
                                            @if($sub->nilai !== null)
                                                <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background-color: {{ $sub->nilai >= 70 ? '#d1fae5' : '#fef3c7' }}; color: {{ $sub->nilai >= 70 ? '#065f46' : '#92400e' }};">
                                                    {{ $sub->nilai }}/100 ({{ $sub->status_nilai }})
                                                </span>
                                            @else
                                                <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background-color: #f1f5f9; color: #475569;">
                                                    {{ $sub->status_nilai }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: #d97706; font-weight: 600; font-size: 0.8125rem;">Belum Mengumpulkan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sub)
                                        <details style="cursor: pointer;">
                                            <summary style="font-weight: 600; color: var(--tel-red); font-size: 0.875rem; outline: none;">
                                                Lihat Jawaban ({{ strlen($sub->jawaban) }} Karakter)
                                            </summary>
                                            <div style="margin-top: 0.75rem; padding: 0.875rem; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap; max-height: 300px; overflow-y: auto;">{{ $sub->jawaban }}</div>
                                        </details>
                                    @else
                                        <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">Tidak ada kiriman</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sub)
                                        {{-- TODO 7c: Tambahkan form PATCH untuk input nilai (0-100) dan catatan_dosen pada setiap mahasiswa yang sudah mengumpulkan --}}
                                        <span style="font-size: 0.8rem; color: var(--text-muted);">Form penilaian belum diimplementasikan</span>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--text-muted);">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
