<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tabulasi;
use App\Models\Agenda;
use App\Models\Bidang;
use App\Models\Kolom;
use App\Models\AksesTabulasi;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TabulasiExport;

class TabulasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tabulasiSaya = Tabulasi::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        // FIX FATAL ERROR: Hanya ambil hak akses yang tabulasinya MASIH ADA (Belum Dihapus)
        $dibagikanKeSaya = AksesTabulasi::with(['tabulasi.user', 'bidang'])
            ->where('email_pengisi', $user->email)
            ->where('status', 'approved')
            ->whereHas('tabulasi') 
            ->get();

        return inertia('Dashboard', [
            'auth' => ['user' => $user],
            'tabulasiSaya' => $tabulasiSaya,
            'dibagikanKeSaya' => $dibagikanKeSaya,
            'flash' => ['success' => session('success')]
        ]);
    }

    public function create() { return inertia('Tabulasi/Create', ['auth' => ['user' => Auth::user()]]); }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255', 'agendas' => 'required|array', 
            'bidangs' => 'required|array', 'nama_kolom' => 'required|array', 'tipe_input' => 'required|array',
        ]);

        $tabulasi = Tabulasi::create(['user_id' => Auth::id(), 'judul' => $request->judul, 'link_unik' => Str::random(10)]);

        foreach ($request->agendas as $agenda) { if (!empty($agenda['nama'])) Agenda::create(['tabulasi_id' => $tabulasi->id, 'nama_agenda' => $agenda['nama'], 'deadline' => $agenda['deadline'] ?: null]); }
        foreach ($request->bidangs as $bidang) { if (!empty($bidang['nama'])) Bidang::create(['tabulasi_id' => $tabulasi->id, 'nama_bidang' => $bidang['nama']]); }
        foreach ($request->nama_kolom as $index => $nama) { if (!empty($nama)) Kolom::create(['tabulasi_id' => $tabulasi->id, 'nama_kolom' => $nama, 'tipe_input' => $request->tipe_input[$index] ?? 'text']); }
        return redirect('/dashboard', 303)->with('success', 'Rekapan berhasil dibuat!');
    }

    public function show($link)
    {
        $user = Auth::user();
        $tabulasi = Tabulasi::with(['agendas', 'bidangs', 'koloms', 'akses_tabulasis.bidang', 'items.agenda', 'items.bidang', 'items.user'])
                    ->where('link_unik', $link)->first();

        if (!$tabulasi) return inertia('Tabulasi/NotFound');

        $isAdmin = $tabulasi->user_id === $user->id;
        $akses = null; $isGuest = false; $isPending = false;

        if (!$isAdmin) {
            $akses = $tabulasi->akses_tabulasis->where('email_pengisi', $user->email)->first();
            if (!$akses) $isGuest = true;
            elseif ($akses->status === 'pending') $isPending = true;
        }

        return inertia('Tabulasi/Show', [
            'auth' => ['user' => $user], 'tabulasi' => $tabulasi, 'isAdmin' => $isAdmin,
            'akses' => $akses, 'isGuest' => $isGuest, 'isPending' => $isPending,
            'flash' => ['success' => session('success'), 'error' => session('error')]
        ]);
    }

    public function editSetup($link)
    {
        $tabulasi = Tabulasi::with(['agendas', 'bidangs', 'koloms'])->where('link_unik', $link)->firstOrFail();
        if ($tabulasi->user_id !== Auth::id()) abort(403);
        return inertia('Tabulasi/EditSetup', ['auth' => ['user' => Auth::user()], 'tabulasi' => $tabulasi]);
    }

    public function updateSetup(Request $request, $link)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        if ($tabulasi->user_id !== Auth::id()) abort(403);

        $request->validate(['judul' => 'required|string|max:255', 'agendas' => 'required|array', 'bidangs' => 'required|array', 'koloms' => 'required|array']);
        $tabulasi->update(['judul' => $request->judul]);

        $agendaIds = collect($request->agendas)->pluck('id')->filter()->toArray();
        Agenda::where('tabulasi_id', $tabulasi->id)->whereNotIn('id', $agendaIds)->delete();
        foreach ($request->agendas as $agendaData) { if (!empty($agendaData['nama'])) Agenda::updateOrCreate(['id' => $agendaData['id'] ?? null, 'tabulasi_id' => $tabulasi->id], ['nama_agenda' => $agendaData['nama'], 'deadline' => $agendaData['deadline'] ?: null]); }

        $bidangIds = collect($request->bidangs)->pluck('id')->filter()->toArray();
        Bidang::where('tabulasi_id', $tabulasi->id)->whereNotIn('id', $bidangIds)->delete();
        foreach ($request->bidangs as $bidangData) { if (!empty($bidangData['nama'])) Bidang::updateOrCreate(['id' => $bidangData['id'] ?? null, 'tabulasi_id' => $tabulasi->id], ['nama_bidang' => $bidangData['nama']]); }

        $kolomIds = collect($request->koloms)->pluck('id')->filter()->toArray();
        Kolom::where('tabulasi_id', $tabulasi->id)->whereNotIn('id', $kolomIds)->delete();
        foreach ($request->koloms as $kolomData) { if (!empty($kolomData['nama_kolom'])) Kolom::updateOrCreate(['id' => $kolomData['id'] ?? null, 'tabulasi_id' => $tabulasi->id], ['nama_kolom' => $kolomData['nama_kolom'], 'tipe_input' => $kolomData['tipe_input']]); }

        return redirect("/tabulasi/{$link}", 303)->with('success', 'Struktur tabulasi berhasil diperbarui!');
    }

    public function requestAccess(Request $request, $link)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        $request->validate(['bidang_id' => 'required|exists:bidangs,id']);
        AksesTabulasi::create(['tabulasi_id' => $tabulasi->id, 'bidang_id' => $request->bidang_id, 'email_pengisi' => Auth::user()->email, 'status' => 'pending', 'peran' => 'anggota']);
        return back(303)->with('success', 'Permintaan akses dikirim. Silakan tunggu Pembuat menyetujuinya.');
    }

    public function approveAccess($link, $akses_id)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        if ($tabulasi->user_id !== Auth::id()) abort(403);
        AksesTabulasi::findOrFail($akses_id)->update(['status' => 'approved']);
        return back(303)->with('success', 'Permintaan disetujui!');
    }

    public function storeAkses(Request $request, $link)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        $request->validate(['email_pengisi' => 'required|email', 'peran' => 'required|in:anggota,ketua', 'bidang_id' => 'required_if:peran,anggota']);
        if (AksesTabulasi::where('tabulasi_id', $tabulasi->id)->where('email_pengisi', $request->email_pengisi)->first()) return back(303)->with('error', 'Email tersebut sudah terdaftar!');
        
        AksesTabulasi::create(['tabulasi_id' => $tabulasi->id, 'bidang_id' => $request->peran === 'ketua' ? null : $request->bidang_id, 'email_pengisi' => $request->email_pengisi, 'status' => 'approved', 'peran' => $request->peran]);
        return back(303)->with('success', 'Akses berhasil ditambahkan!');
    }

    public function destroyAkses($link, $akses_id)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        if ($tabulasi->user_id !== Auth::id()) abort(403);
        AksesTabulasi::findOrFail($akses_id)->delete();
        return back(303)->with('success', 'Akses pengisi berhasil dicabut!');
    }

    public function storeItem(Request $request, $link)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        $user = Auth::user();
        $isAdmin = $tabulasi->user_id === $user->id;

        $isKetua = false;
        $akses = null;
        if (!$isAdmin) {
            $akses = AksesTabulasi::where('tabulasi_id', $tabulasi->id)->where('email_pengisi', $user->email)->first();
            if (!$akses || $akses->status !== 'approved') return back(303)->with('error', 'Sesi habis: Hak akses Anda telah dicabut oleh Pembuat.');
            $isKetua = $akses->peran === 'ketua';
        }

        $request->validate(['agenda_id' => 'required|exists:agendas,id', 'isian' => 'required|array', 'bidang_id' => ($isAdmin || $isKetua) ? 'required|exists:bidangs,id' : '']);

        $bidang_id = ($isAdmin || $isKetua) ? $request->bidang_id : $akses->bidang_id;
        $dataIsi = $request->isian;
        foreach ($tabulasi->koloms as $kolom) { if ($kolom->tipe_input === 'checkbox') $dataIsi[$kolom->nama_kolom] = false; }

        Item::create(['tabulasi_id' => $tabulasi->id, 'agenda_id' => $request->agenda_id, 'bidang_id' => $bidang_id, 'user_id' => $user->id, 'data_isi' => $dataIsi]);
        return back(303)->with('success', 'Barang berhasil ditambahkan!');
    }

    public function editItem($link, $item_id)
    {
        $tabulasi = Tabulasi::with(['koloms', 'agendas', 'bidangs'])->where('link_unik', $link)->firstOrFail();
        $item = Item::findOrFail($item_id);
        if ($tabulasi->user_id !== Auth::id() && $item->user_id !== Auth::id()) abort(403);
        return inertia('Tabulasi/EditItem', ['auth' => ['user' => Auth::user()], 'tabulasi' => $tabulasi, 'item' => $item, 'isAdmin' => $tabulasi->user_id === Auth::id()]);
    }

    public function updateItem(Request $request, $link, $item_id)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        $item = Item::findOrFail($item_id);
        $isAdmin = $tabulasi->user_id === Auth::id();

        if (!$isAdmin && $item->user_id !== Auth::id()) abort(403);
        $request->validate(['agenda_id' => 'required|exists:agendas,id', 'isian' => 'required|array']);
        
        $dataIsi = $request->isian;
        $existingData = $item->data_isi;
        foreach ($tabulasi->koloms as $kolom) { if ($kolom->tipe_input === 'checkbox') $dataIsi[$kolom->nama_kolom] = $existingData[$kolom->nama_kolom] ?? false; }

        $item->update(['agenda_id' => $request->agenda_id, 'bidang_id' => $isAdmin ? ($request->bidang_id ?? $item->bidang_id) : $item->bidang_id, 'data_isi' => $dataIsi]);
        return redirect("/tabulasi/{$link}", 303)->with('success', 'Data barang berhasil diperbarui!');
    }

    public function toggleItem(Request $request, $link, $item_id)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        if ($tabulasi->user_id !== Auth::id()) abort(403);
        $item = Item::findOrFail($item_id);
        $dataIsi = $item->data_isi;
        $dataIsi[$request->nama_kolom] = !($dataIsi[$request->nama_kolom] ?? false);
        $item->update(['data_isi' => $dataIsi]);
        return back(303)->with('success', "Status diperbarui!");
    }

    public function destroyItem($link, $item_id)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        $item = Item::findOrFail($item_id);
        if ($tabulasi->user_id !== Auth::id() && $item->user_id !== Auth::id()) abort(403);
        $item->delete();
        return back(303)->with('success', 'Barang dihapus!');
    }

    public function destroy($link)
    {
        $tabulasi = Tabulasi::where('link_unik', $link)->firstOrFail();
        if ($tabulasi->user_id !== Auth::id()) abort(403);
        
        // FIX FATAL ERROR: Hapus semua data anak (Cascade Delete) agar bersih
        $tabulasi->items()->delete();
        $tabulasi->akses_tabulasis()->delete();
        $tabulasi->koloms()->delete();
        $tabulasi->bidangs()->delete();
        $tabulasi->agendas()->delete();

        $tabulasi->delete();
        return redirect('/dashboard', 303)->with('success', 'Rekapan dihapus permanen!');
    }

    public function exportCsv($link)
    {
        $tabulasi = Tabulasi::with(['koloms', 'items.agenda', 'items.bidang', 'items.user', 'bidangs', 'agendas'])->where('link_unik', $link)->firstOrFail();
        if ($tabulasi->user_id !== Auth::id()) abort(403);
        $fileName = 'Rekap_SIKAP_' . str_replace(' ', '_', $tabulasi->judul) . '.xlsx';
        return Excel::download(new TabulasiExport($tabulasi), $fileName);
    }
}