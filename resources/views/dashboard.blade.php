<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIKAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800">

    <nav class="bg-blue-700 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-white text-2xl font-bold tracking-wider">SIKAP</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-blue-100 text-sm hidden md:block">Masuk sebagai: <b>{{ auth()->user()->name }}</b></span>
                    <img class="h-8 w-8 rounded-full border-2 border-white shadow-sm" src="{{ auth()->user()->avatar }}" alt="Profile">
                    
                    <form action="/logout" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
            <a href="/tabulasi/create" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-md font-medium flex items-center transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Rekapan
            </a>
        </div>

        @if(session('success')) 
            <div class="mb-6 text-green-700 bg-green-100 p-3 rounded-lg text-sm font-bold shadow-sm border border-green-200">
                {{ session('success') }}
            </div> 
        @endif
        
        <h2 class="text-xl font-bold text-gray-700 mb-4">Rekapan Saya (Pembuat)</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            @forelse($tabulasiSaya as $tab)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition border border-gray-200 border-t-4 border-t-blue-600 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-gray-800">{{ $tab->judul }}</h3>
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-medium">Tabulasi</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Tenggat: {{ $tab->deadline ? \Carbon\Carbon::parse($tab->deadline)->format('d M Y') : 'Tidak diatur' }}</p>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm border-t pt-3 mt-2">
                        <form action="/tabulasi/{{ $tab->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus seluruh rekapan ini secara permanen?');" class="m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                        </form>

                        <a href="/tabulasi/{{ $tab->id }}" class="text-blue-600 font-medium hover:underline">Buka &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white p-6 rounded-xl border border-dashed border-gray-300 text-center text-gray-500">
                    Kamu belum membuat rekapan apa pun. Klik "Buat Rekapan" untuk memulai.
                </div>
            @endforelse
        </div>

        <h2 class="text-xl font-bold text-gray-700 mb-4">Dibagikan ke Saya (Pengisi)</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($dibagikanKeSaya as $akses)
                <a href="/tabulasi/{{ $akses->tabulasi->id }}" class="bg-white rounded-xl shadow-sm hover:shadow-md transition cursor-pointer border border-gray-200 border-t-4 border-t-green-500 p-6 block">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-gray-800">{{ $akses->tabulasi->judul }}</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-medium">Tabulasi</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Pembuat: {{ $akses->tabulasi->user->name }}</p>
                    <div class="flex justify-between items-center text-sm border-t pt-3 mt-2">
                        <span class="text-gray-500">Bidang: <span class="font-bold text-green-600">{{ $akses->bidang->nama_bidang }}</span></span>
                        <span class="text-blue-600 font-medium">Buka &rarr;</span>
                    </div>
                </a>
            @empty
                <div class="col-span-3 bg-white p-6 rounded-xl border border-dashed border-gray-300 text-center text-gray-500">
                    Belum ada rekapan yang dibagikan ke email kamu.
                </div>
            @endforelse
        </div>

    </div>
</body>
</html>