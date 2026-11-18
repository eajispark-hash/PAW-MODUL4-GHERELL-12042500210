<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    // Menampilkan daftar pengumpulan tugas & rata-rata nilai otomatis
    public function index(Assignment $assignment)
    {
        $submissions = $assignment->submissions()->with('user')->get();
        $rataRataNilai = $submissions->whereNotNull('nilai')->avg('nilai');

        return view('assignments.submissions', compact('assignment', 'submissions', 'rataRataNilai'));
    }

    // Menangani pemberian nilai tugas oleh dosen
    public function grade(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'catatan_dosen' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'nilai' => $validated['nilai'],
            'catatan_dosen' => $validated['catatan_dosen'],
        ]);

        return back()->with('success', 'Nilai mahasiswa berhasil disimpan.');
    }

    // Menangani ekspor laporan nilai ke format CSV
    public function exportCsv(Assignment $assignment): StreamedResponse
    {
        $fileName = 'rekap_nilai_' . str()->slug($assignment->judul) . '.csv';
        $submissions = $assignment->submissions()->with('user')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($submissions) {
            $handle = fopen('php://output', 'w');

            // Header Kolom CSV
            fputcsv($handle, ['No', 'Nama Mahasiswa', 'NIM', 'Email', 'Waktu Pengumpulan', 'Nilai', 'Status', 'Catatan Dosen']);

            foreach ($submissions as $index => $sub) {
                fputcsv($handle, [
                    $index + 1,
                    $sub->user->name,
                    $sub->user->nim_nip ?? '-',
                    $sub->user->email,
                    $sub->created_at ? $sub->created_at->format('d M Y, H:i') : '-',
                    $sub->nilai ?? 'Belum Dinilai',
                    $sub->status_kelulusan,
                    $sub->catatan_dosen ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}