import { useState } from 'react';
import { Head, router } from '@inertiajs/react';

export default function Create({ auth }) {
    const [data, setData] = useState({
        judul: '', agendas: [{ nama: '', deadline: '' }], bidangs: [{ nama: '' }],
        nama_kolom: ['Nama Barang', 'Jumlah'], tipe_input: ['text', 'number']
    });

    const handleSubmit = (e) => { e.preventDefault(); router.post('/tabulasi', data); };

    const updateArray = (key, index, field, value) => {
        const newData = [...data[key]];
        if (field) newData[index][field] = value; else newData[index] = value;
        setData({ ...data, [key]: newData });
    };
    
    const addItem = (key, template) => setData({ ...data, [key]: [...data[key], template] });
    const removeItem = (key, index) => { const newData = [...data[key]]; newData.splice(index, 1); setData({ ...data, [key]: newData }); };

    const InputClass = "w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all";
    const LabelClass = "block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2";

    return (
        <div className="min-h-screen bg-[#F8FAFC] font-sans text-slate-800 pb-20">
            <Head title="Buat Tabulasi - SIKAP" />
            <nav className="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/60"><div className="max-w-4xl mx-auto px-4 sm:px-6 flex h-16 items-center"><div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">S</div><span className="text-lg font-bold text-slate-900 tracking-tight">SIKAP</span></div></nav>

            <div className="max-w-4xl mx-auto px-4 sm:px-6 pt-8 md:pt-12">
                <div className="mb-8 md:mb-12">
                    <a href="/dashboard" className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm group mb-6 w-fit">
                        <svg className="w-4 h-4 text-slate-400 group-hover:text-slate-600 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>
                    <h1 className="text-3xl font-bold text-slate-900">Setup Rekapan Baru</h1>
                </div>

                <form onSubmit={handleSubmit} className="space-y-8 md:space-y-10">
                    <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                        <label className={LabelClass}>Judul Tabulasi / Proyek</label>
                        <input type="text" value={data.judul} onChange={e => setData({...data, judul: e.target.value})} required placeholder="Contoh: Perlengkapan Acara INFEST 2026" className={InputClass} />
                    </div>

                    <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                        <div className="mb-5"><h2 className="text-lg font-bold text-slate-900">Daftar Agenda</h2><p className="text-sm text-slate-500 mt-1">Kegiatan yang membutuhkan barang. Beri tenggat waktu jika perlu.</p></div>
                        <div className="space-y-3 mb-4">
                            {data.agendas.map((agenda, index) => (
                                <div key={index} className="flex flex-col sm:flex-row gap-3">
                                    <input type="text" value={agenda.nama} onChange={e => updateArray('agendas', index, 'nama', e.target.value)} required placeholder={`Agenda ${index + 1}`} className={`${InputClass} sm:flex-1`} />
                                    <div className="flex gap-2">
                                        <input type="date" value={agenda.deadline} onChange={e => updateArray('agendas', index, 'deadline', e.target.value)} className={`${InputClass} w-full sm:w-44 text-slate-500`} />
                                        {index > 0 && <button type="button" onClick={() => removeItem('agendas', index)} className="px-4 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors font-bold">✕</button>}
                                    </div>
                                </div>
                            ))}
                        </div>
                        <button type="button" onClick={() => addItem('agendas', {nama:'', deadline:''})} className="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 py-2">+ Tambah Agenda</button>
                    </div>

                    <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                        <div className="mb-5"><h2 className="text-lg font-bold text-slate-900">Daftar Bidang</h2><p className="text-sm text-slate-500 mt-1">Siapa saja panitia yang akan mengisi barang ini?</p></div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            {data.bidangs.map((bidang, index) => (
                                <div key={index} className="flex gap-2">
                                    <input type="text" value={bidang.nama} onChange={e => updateArray('bidangs', index, 'nama', e.target.value)} required placeholder={`Nama Bidang ${index + 1}`} className={InputClass} />
                                    {index > 0 && <button type="button" onClick={() => removeItem('bidangs', index)} className="px-4 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors font-bold">✕</button>}
                                </div>
                            ))}
                        </div>
                        <button type="button" onClick={() => addItem('bidangs', {nama:''})} className="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 py-2">+ Tambah Bidang</button>
                    </div>

                    <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                        <div className="mb-5"><h2 className="text-lg font-bold text-slate-900">Kolom Form Isian</h2><p className="text-sm text-slate-500 mt-1">Struktur tabel barang. Kolom status (Checkbox) hanya bisa diklik oleh Pembuat.</p></div>
                        <div className="space-y-3 mb-4">
                            {data.nama_kolom.map((nama, index) => (
                                <div key={index} className="flex flex-col sm:flex-row gap-3">
                                    <input type="text" value={nama} onChange={e => {const n=[...data.nama_kolom]; n[index]=e.target.value; setData({...data, nama_kolom:n})}} required placeholder="Nama Kolom" className={`${InputClass} sm:flex-1`} />
                                    <div className="flex gap-2">
                                        <select value={data.tipe_input[index]} onChange={e => {const t=[...data.tipe_input]; t[index]=e.target.value; setData({...data, tipe_input:t})}} className={`${InputClass} w-full sm:w-44`}>
                                            <option value="text">Teks / Paragraf</option><option value="number">Hanya Angka</option><option value="checkbox">Status (Centang)</option>
                                        </select>
                                        {index > 0 && <button type="button" onClick={() => {removeItem('nama_kolom', index); removeItem('tipe_input', index)}} className="px-4 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors font-bold">✕</button>}
                                    </div>
                                </div>
                            ))}
                        </div>
                        <button type="button" onClick={() => {addItem('nama_kolom', ''); addItem('tipe_input', 'text')}} className="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 py-2">+ Tambah Kolom</button>
                    </div>

                    <div className="pt-4 pb-10 flex sm:justify-end">
                        <button type="submit" className="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-xl shadow-[0_8px_30px_rgb(37,99,235,0.2)] font-bold text-base transition-all active:scale-95">Simpan & Buat Tabulasi</button>
                    </div>
                </form>
            </div>
        </div>
    );
}