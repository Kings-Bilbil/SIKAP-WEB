<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tabulasi->judul }} - SIKAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800">

    <nav class="bg-blue-700 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">
            <span class="text-white text-2xl font-bold tracking-wider">SIKAP</span>
            <div class="flex items-center space-x-4">
                <span class="text-blue-100 text-sm hidden md:block">Masuk sebagai: <b>{{ auth()->user()->name }}</b></span>
                <img class="h-8 w-8 rounded-full border-2 border-white shadow-sm" src="{{ auth()->user()->avatar }}" alt="Profile">
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <a href="/dashboard" class="text-gray-500 hover:text-gray-800 mb-4 inline-flex items-center font-medium">&larr; Kembali ke Dashboard</a>

        @if(session('success')) <div class="mb-4 text-green-700 bg-green-100 p-3 rounded-lg text-sm font-bold shadow-sm border border-green-200">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="mb-4 text-red-700 bg-red-100 p-3 rounded-lg text-sm font-bold shadow-sm border border-red-200">{{ session('error') }}</div> @endif

        @if(isset($isGuest) && $isGuest)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-10 max-w-lg mx-auto text-center mt-10 border-t-8 border-t-yellow-400">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Akses Terkunci</h2>
                <p class="text-gray-500 mb-8">Anda belum memiliki akses untuk mengisi atau melihat tabulasi <b>"{{ $tabulasi->judul }}"</b>. Silakan minta akses kepada pembuat.</p>
                
                <form action="/tabulasi/{{ $tabulasi->id }}/minta-akses" method="POST" class="text-left">
                    @csrf
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Bidang/Divisi Anda:</label>
                    <select name="bidang_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 mb-6 bg-gray-50">
                        @foreach($tabulasi->bidangs as $bidang)
                            <option value="{{ $bidang->id }}">Bidang {{ $bidang->nama_bidang }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-bold shadow-md transition">Kirim Permintaan Akses</button>
                </form>
            </div>

        @elseif(isset($isPending) && $isPending)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-10 max-w-lg mx-auto text-center mt-10 border-t-8 border-t-blue-400">
                <svg class="w-16 h-16 text-blue-500 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Menunggu Persetujuan</h2>
                <p class="text-gray-500">Permintaan aksesmu sudah dikirim ke Admin. Silakan tunggu hingga Admin menekan tombol terima, lalu <i>refresh</i> halaman ini.</p>
            </div>

        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="bg-blue-700 px-6 py-5 text-white flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $tabulasi->judul }}</h2>
                        <p class="text-blue-200 text-sm mt-1">Peran Anda: 
                            @if($isAdmin) <span class="bg-blue-800 px-2 py-1 rounded text-white font-medium">Admin / Pembuat</span>
                            @else <span class="bg-blue-800 px-2 py-1 rounded text-white font-medium">Bidang {{ $akses->bidang->nama_bidang }}</span>
                            @endif
                        </p>
                    </div>
                    <button onclick="alert('Bagikan link ini ke grup: ' + window.location.href)" class="bg-white text-blue-700 px-4 py-2 rounded-lg text-sm font-bold shadow hover:bg-gray-50">Share Link Minta Akses</button>
                </div>
                
                <div class="flex border-b border-gray-200 bg-gray-50 px-2 overflow-x-auto">
                    <button onclick="switchInnerTab('tab-form')" id="btn-tab-form" class="inner-tab px-6 py-3 font-bold text-blue-700 border-b-2 border-blue-700 whitespace-nowrap">Form Pengisi</button>
                    <button onclick="switchInnerTab('tab-rekap')" id="btn-tab-rekap" class="inner-tab px-6 py-3 font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">Tampilan Rekap</button>
                    @if($isAdmin)
                    <button onclick="switchInnerTab('tab-pengaturan')" id="btn-tab-pengaturan" class="inner-tab px-6 py-3 font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">Pengaturan Akses</button>
                    @endif
                </div>
            </div>

            <div id="tab-form" class="inner-content block">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                    <form action="/tabulasi/{{ $tabulasi->id }}/item" method="POST">
                        @csrf
                        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Agenda Tujuan:</label>
                                <select name="agenda_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="" disabled selected>-- Klik untuk memilih Agenda --</option>
                                    @foreach($tabulasi->agendas as $agenda) <option value="{{ $agenda->id }}">{{ $agenda->nama_agenda }}</option> @endforeach
                                </select>
                            </div>
                            @if($isAdmin)
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Input Atas Nama Bidang:</label>
                                <select name="bidang_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="" disabled selected>-- Admin bebas memilih Bidang --</option>
                                    @foreach($tabulasi->bidangs as $bidang) <option value="{{ $bidang->id }}">Bidang {{ $bidang->nama_bidang }}</option> @endforeach
                                </select>
                            </div>
                            @endif
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-blue-900 mb-5 border-b border-blue-200 pb-2">Form Input Barang</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                @foreach($tabulasi->koloms as $kolom)
                                    @if($kolom->tipe_input === 'checkbox')
                                    @elseif($kolom->tipe_input === 'number')
                                        <div><label class="block text-sm font-bold text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label><input type="number" name="isian[{{ $kolom->nama_kolom }}]" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-blue-500"></div>
                                    @else
                                        <div class="md:col-span-2"><label class="block text-sm font-bold text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label><input type="text" name="isian[{{ $kolom->nama_kolom }}]" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-blue-500"></div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-8 border-t border-blue-200 pt-5 text-right"><button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg shadow-md font-bold transition">Submit Barang</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="tab-rekap" class="inner-content hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-xl font-bold text-gray-800">Tabel Rekapitulasi</h3>
                        <div class="flex items-center space-x-3">
                            <span class="bg-blue-100 text-blue-800 text-sm font-bold px-3 py-2 rounded-lg">Total: {{ $tabulasi->items->count() }} Barang</span>
                            @if($isAdmin && $tabulasi->items->count() > 0)
                                <a href="/tabulasi/{{ $tabulasi->id }}/export" class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-4 py-2 rounded-lg shadow transition">Export ke CSV</a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                        <table class="min-w-full bg-white text-sm">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="py-4 px-4 font-bold text-gray-700 whitespace-nowrap">No</th>
                                    <th class="py-4 px-4 font-bold text-gray-700 whitespace-nowrap">Waktu Input</th>
                                    <th class="py-4 px-4 font-bold text-gray-700 whitespace-nowrap">Agenda</th>
                                    <th class="py-4 px-4 font-bold text-gray-700 whitespace-nowrap">Bidang</th>
                                    <th class="py-4 px-4 font-bold text-gray-700 whitespace-nowrap">Nama Pengisi</th>
                                    @foreach($tabulasi->koloms as $kolom) <th class="py-4 px-4 font-bold text-gray-700 whitespace-nowrap border-l border-gray-200">{{ $kolom->nama_kolom }}</th> @endforeach
                                    <th class="py-4 px-4 font-bold text-gray-700 whitespace-nowrap border-l border-gray-200 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tabulasi->items as $index => $item)
                                    <tr class="border-b hover:bg-gray-50 transition">
                                        <td class="py-3 px-4">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4 text-gray-500 whitespace-nowrap">{{ $item->created_at->format('d M Y, H:i') }}</td>
                                        <td class="py-3 px-4 font-medium">{{ $item->agenda->nama_agenda ?? '-' }}</td>
                                        <td class="py-3 px-4 font-bold text-blue-600">{{ $item->bidang->nama_bidang ?? '-' }}</td>
                                        <td class="py-3 px-4">{{ $item->user->name ?? 'Pengisi' }}</td>
                                        
                                        @foreach($tabulasi->koloms as $kolom)
                                            <td class="py-3 px-4 border-l border-gray-100">
                                                @php $nilai = $item->data_isi[$kolom->nama_kolom] ?? false; @endphp
                                                @if($kolom->tipe_input === 'checkbox')
                                                    @if($isAdmin)
                                                        <form action="/tabulasi/{{ $tabulasi->id }}/item/{{ $item->id }}/toggle" method="POST" class="inline">
                                                            @csrf <input type="hidden" name="nama_kolom" value="{{ $kolom->nama_kolom }}">
                                                            <input type="checkbox" onchange="this.form.submit()" {{ $nilai ? 'checked' : '' }} class="h-5 w-5 text-green-600 rounded border-gray-300 cursor-pointer">
                                                        </form>
                                                    @else
                                                        @if($nilai) <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-bold">✓ Ya</span>
                                                        @else <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs font-bold">- Belum</span> @endif
                                                    @endif
                                                @else
                                                    {{ $nilai ?? '-' }}
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="py-3 px-4 text-center border-l border-gray-100 whitespace-nowrap">
                                            @if($isAdmin || $item->user_id === auth()->id())
                                                <a href="/tabulasi/{{ $tabulasi->id }}/item/{{ $item->id }}/edit" class="text-blue-500 hover:text-blue-700 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded transition mr-1">Edit</a>
                                                <form action="/tabulasi/{{ $tabulasi->id }}/item/{{ $item->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded transition">Hapus</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ 6 + $tabulasi->koloms->count() }}" class="py-10 text-center text-gray-500 font-medium">Belum ada barang.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($isAdmin)
            <div id="tab-pengaturan" class="inner-content hidden">
                
                @php $pendingRequests = $tabulasi->akses_tabulasis->where('status', 'pending'); @endphp
                @if($pendingRequests->count() > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
                    <h3 class="text-lg font-bold text-yellow-800 mb-4">Permintaan Akses Masuk ({{ $pendingRequests->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($pendingRequests as $req)
                        <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm border border-yellow-100">
                            <div>
                                <p class="font-bold text-gray-800">{{ $req->email_pengisi }}</p>
                                <p class="text-sm text-gray-500">Minta akses ke Bidang <span class="font-bold text-blue-600">{{ $req->bidang->nama_bidang }}</span></p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="/tabulasi/{{ $tabulasi->id }}/akses/{{ $req->id }}/terima" method="POST">
                                    @csrf <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm">Terima</button>
                                </form>
                                <form action="/tabulasi/{{ $tabulasi->id }}/akses/{{ $req->id }}" method="POST">
                                    @csrf @method('DELETE') <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm">Tolak</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Tambah Akses Manual</h3>
                    <form action="/tabulasi/{{ $tabulasi->id }}/akses" method="POST" class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3 mb-8 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        @csrf
                        <input type="email" name="email_pengisi" required placeholder="email.teman@gmail.com" class="border border-gray-300 rounded-lg px-4 py-2 flex-1 outline-none focus:ring-2 focus:ring-blue-500">
                        <select name="bidang_id" required class="border border-gray-300 rounded-lg px-4 py-2 bg-white outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($tabulasi->bidangs as $bidang) <option value="{{ $bidang->id }}">Bidang {{ $bidang->nama_bidang }}</option> @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-sm">Beri Akses Langsung</button>
                    </form>

                    <h4 class="font-bold text-gray-700 mb-3">Daftar Pengisi yang Disetujui</h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="text-left py-3 px-4 text-sm font-bold text-gray-700">Email</th>
                                    <th class="text-left py-3 px-4 text-sm font-bold text-gray-700">Bidang</th>
                                    <th class="text-right py-3 px-4 text-sm font-bold text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tabulasi->akses_tabulasis->where('status', 'approved') as $akses_tab)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm">{{ $akses_tab->email_pengisi }}</td>
                                    <td class="py-3 px-4 text-sm font-bold text-blue-600">{{ $akses_tab->bidang->nama_bidang }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <form action="/tabulasi/{{ $tabulasi->id }}/akses/{{ $akses_tab->id }}" method="POST" onsubmit="return confirm('Yakin ingin mencabut akses ini?');" class="inline">
                                            @csrf @method('DELETE') <button type="submit" class="text-red-500 font-bold text-xs">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        @endif
    </div>

    <script>
        function switchInnerTab(tabId) {
            document.querySelectorAll('.inner-content').forEach(el => { el.classList.add('hidden'); el.classList.remove('block'); });
            document.querySelectorAll('.inner-tab').forEach(el => { el.classList.remove('font-bold', 'text-blue-700', 'border-b-2', 'border-blue-700'); el.classList.add('font-medium', 'text-gray-500'); });
            document.getElementById(tabId).classList.remove('hidden'); document.getElementById(tabId).classList.add('block');
            const btn = document.getElementById('btn-' + tabId);
            btn.classList.remove('font-medium', 'text-gray-500'); btn.classList.add('font-bold', 'text-blue-700', 'border-b-2', 'border-blue-700');
        }
    </script>
</body>
</html>