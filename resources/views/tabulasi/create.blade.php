<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tabulasi - SIKAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800">

    <nav class="bg-blue-700 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">
            <span class="text-white text-2xl font-bold tracking-wider">SIKAP</span>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <a href="/dashboard" class="text-gray-500 hover:text-gray-800 mb-4 inline-flex items-center font-medium">
            &larr; Kembali ke Dashboard
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">Buat Tabulasi Baru</h2>
            
            <form action="/tabulasi" method="POST" class="space-y-6">
                @csrf <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Judul Tabulasi</label>
                        <input type="text" name="judul" required placeholder="Contoh: Perlengkapan INFEST 2026" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tenggat Waktu (Deadline)</label>
                        <input type="date" name="deadline" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Daftar Agenda</label>
                        <p class="text-xs text-gray-500 mb-2">Pisahkan dengan koma (contoh: Coaching, Pitching, In Screen)</p>
                        <textarea name="agendas" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Daftar Bidang</label>
                        <p class="text-xs text-gray-500 mb-2">Pisahkan dengan koma (contoh: Acara, Konsumsi, Sekretaris)</p>
                        <textarea name="bidangs" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-4">Setup Kolom Isian Barang</h4>
                    
                    <div id="kolom-container" class="space-y-3 mb-4">
                        <div class="flex space-x-3 kolom-row">
                            <input type="text" name="nama_kolom[]" value="Nama Barang" required class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                            <select name="tipe_input[]" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white outline-none">
                                <option value="text" selected>Teks</option>
                                <option value="number">Angka</option>
                                <option value="checkbox">Checkbox</option>
                            </select>
                        </div>
                        <div class="flex space-x-3 kolom-row">
                            <input type="text" name="nama_kolom[]" value="Jumlah" required class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                            <select name="tipe_input[]" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white outline-none">
                                <option value="text">Teks</option>
                                <option value="number" selected>Angka</option>
                                <option value="checkbox">Checkbox</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" onclick="tambahKolom()" class="text-sm text-blue-600 font-bold hover:underline">+ Tambah Kolom Lainnya</button>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg shadow-md font-bold transition">Simpan & Buat Tabulasi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function tambahKolom() {
            const container = document.getElementById('kolom-container');
            const newRow = document.createElement('div');
            newRow.className = 'flex space-x-3 kolom-row';
            newRow.innerHTML = `
                <input type="text" name="nama_kolom[]" placeholder="Nama Kolom Baru" required class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                <select name="tipe_input[]" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white outline-none">
                    <option value="text">Teks</option>
                    <option value="number">Angka</option>
                    <option value="checkbox">Checkbox</option>
                </select>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 font-bold px-2 hover:bg-red-50 rounded">X</button>
            `;
            container.appendChild(newRow);
        }
    </script>
</body>
</html>