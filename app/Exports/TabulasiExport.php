<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class TabulasiExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $tabulasi;
    protected $styledRows = []; // Menyimpan baris mana saja yang perlu diwarnai

    public function __construct($tabulasi)
    {
        $this->tabulasi = $tabulasi;
    }

    public function headings(): array
    {
        $headers = ['No', 'Waktu Input', 'Pengisi'];
        foreach ($this->tabulasi->koloms as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }
        return $headers;
    }

    public function array(): array
    {
        $data = [];
        $totalKolomDinamis = $this->tabulasi->koloms->count();
        $totalKolom = 3 + $totalKolomDinamis;
        $currentRow = 2; // Mulai mencatat dari baris ke-2 (Baris 1 adalah Header)

        // LOOPING PER AGENDA
        foreach ($this->tabulasi->agendas as $agenda) {
            // 1. Baris Sub-Header Agenda (Hijau Gelap)
            $rowAgenda = array_fill(0, $totalKolom, '');
            $rowAgenda[0] = "AGENDA: " . strtoupper($agenda->nama_agenda);
            $data[] = $rowAgenda;
            $this->styledRows['agenda'][] = $currentRow++;

            $itemsInAgenda = $this->tabulasi->items->where('agenda_id', $agenda->id);

            // LOOPING PER BIDANG DI DALAM AGENDA
            foreach ($this->tabulasi->bidangs as $bidang) {
                $itemsInBidang = $itemsInAgenda->where('bidang_id', $bidang->id);

                if ($itemsInBidang->count() > 0) {
                    // 2. Baris Sub-Header Bidang (Biru Muda)
                    $rowBidang = array_fill(0, $totalKolom, '');
                    $rowBidang[0] = "Bidang: " . $bidang->nama_bidang;
                    $data[] = $rowBidang;
                    $this->styledRows['bidang'][] = $currentRow++;

                    // 3. Masukkan Barang
                    $no = 1;
                    foreach ($itemsInBidang as $item) {
                        // Format Tanggal Indonesia
                        $tanggalFormat = Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMMM YYYY HH:mm');

                        $rowItem = [
                            $no++,
                            $tanggalFormat,
                            $item->user->name ?? 'Pengisi'
                        ];

                        foreach ($this->tabulasi->koloms as $kolom) {
                            $nilai = $item->data_isi[$kolom->nama_kolom] ?? '';
                            if ($kolom->tipe_input === 'checkbox') $rowItem[] = $nilai ? 'Ya' : 'Tidak';
                            else $rowItem[] = $nilai;
                        }
                        $data[] = $rowItem;
                        $currentRow++;
                    }
                }
            }
            // Baris Kosong Pemisah antar Agenda
            $data[] = array_fill(0, $totalKolom, '');
            $currentRow++;
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FF2563EB']]],
        ];

        foreach ($this->styledRows['agenda'] ?? [] as $row) {
            $styles[$row] = ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FF047857']]]; // Hijau
        }
        foreach ($this->styledRows['bidang'] ?? [] as $row) {
            $styles[$row] = ['font' => ['bold' => true, 'color' => ['argb' => 'FF1E3A8A']], 'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FFDBEAFE']]]; // Biru Muda
        }

        return $styles;
    }
}