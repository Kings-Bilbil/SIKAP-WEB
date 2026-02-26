<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - SIKAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800">

    <nav class="bg-blue-700 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 flex justify-between h-16 items-center">
            <span class="text-white text-2xl font-bold tracking-wider">SIKAP</span>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 py-10">
        <a href="/tabulasi/{{ $tabulasi->id }}" class="text-gray-500 hover:text-gray-800 mb-6 inline-flex items-center font-medium">&larr; Batal & Kembali</a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 border-t-8 border-t-blue-500">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">Edit Data Barang</h2>
            
            <form action="/tabulasi/{{ $tabulasi->id }}/item/{{ $item->id }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Agenda Tujuan:</label>
                        <select name="agenda_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                            @foreach($tabulasi->agendas as $agenda)
                                <option value="{{ $agenda->id }}" {{ $item->agenda_id == $agenda->id ? 'selected' : '' }}>{{ $agenda->nama_agenda }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($isAdmin)
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bidang:</label>
                        <select name="bidang_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                            @foreach($tabulasi->bidangs as $bidang)
                                <option value="{{ $bidang->id }}" {{ $item->bidang_id == $bidang->id ? 'selected' : '' }}>Bidang {{ $bidang->nama_bidang }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-blue-900 mb-5 border-b border-blue-200 pb-2">Ubah Isian Barang</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($tabulasi->koloms as $kolom)
                            @php $nilaiLama = $item->data_isi[$kolom->nama_kolom] ?? ''; @endphp

                            @if($kolom->tipe_input === 'checkbox')
                                @elseif($kolom->tipe_input === 'number')
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                                    <input type="number" name="isian[{{ $kolom->nama_kolom }}]" value="{{ $nilaiLama }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-blue-500 shadow-sm">
                                </div>
                            @else
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                                    <input type="text" name="isian[{{ $kolom->nama_kolom }}]" value="{{ $nilaiLama }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:border-blue-500 shadow-sm">
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <div class="mt-8 pt-5 text-right">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-md font-bold text-lg transition">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>