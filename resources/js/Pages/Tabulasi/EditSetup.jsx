import { useState } from 'react';
import { Head, router } from '@inertiajs/react';

export default function EditSetup({ tabulasi }) {
    const [data, setData] = useState({
        judul: tabulasi.judul,
        agendas: tabulasi.agendas.map(a => ({ id: a.id, nama: a.nama_agenda, deadline: a.deadline || '' })),
        bidangs: tabulasi.bidangs.map(b => ({ id: b.id, nama: b.nama_bidang })),
        koloms: tabulasi.koloms.map(k => ({ id: k.id, nama_kolom: k.nama_kolom, tipe_input: k.tipe_input }))
    });

    const [dialog, setDialog] = useState({ isOpen: false, title: '', message: '', action: null });
    const openConfirm = (title, message, action) => setDialog({ isOpen: true, title, message, action });
    const closeDialog = () => setDialog({ ...dialog, isOpen: false });

    const handleSubmit = (e) => { e.preventDefault(); router.put(`/tabulasi/${tabulasi.link_unik}/setup`, data); };

    const updateArray = (key, index, field, value) => {
        const newData = [...data[key]];
        if (field) newData[index][field] = value; else newData[index] = value;
        setData({ ...data, [key]: newData });
    };

    const addItem = (key, template) => setData({ ...data, [key]: [...data[key], template] });
    
    const removeItem = (key, index, warningMsg) => {
        openConfirm('Hapus Komponen?', warningMsg, () => {
            const newData = [...data[key]];
            newData.splice(index, 1);
            setData({ ...data, [key]: newData });
        });
    };

    const InputClass = "w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all";
    const LabelClass = "block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2";

    return (
        <div className="min-h-screen bg-[#F8FAFC] font-sans text-slate-800 pb-20 relative">
            <Head title="Edit Struktur Tabulasi - SIKAP" />
            
            {dialog.isOpen && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-all">
                    <div className="bg-white rounded-3xl shadow-2xl p-6 md:p-8 max-w-sm w-full border border-slate-100 animate-in zoom-in-95 duration-200">
                        <h3 className="text-xl font-bold mb-2 text-slate-900">{dialog.title}</h3>
                        <p className="text-slate-500 text-sm mb-8 leading-relaxed">{dialog.message}</p>
                        <div className="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button type="button" onClick={closeDialog} className="px-5 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 transition-colors w-full sm:w-auto">Batal</button>
                            <button type="button" onClick={() => { dialog.action(); closeDialog(); }} className="px-5 py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-sm transition-colors w-full sm:w-auto">Ya, Hapus Saja</button>
                        </div>
                    </div>
                </div>
            )}

            <nav className="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/60"><div className="max-w-4xl mx-auto px-4 sm:px-6 flex h-16 items-center"><div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">S</div><span className="text-lg font-bold text-slate-900 tracking-tight">SIKAP</span></div></nav>

            <div className="max-w-4xl mx-auto px-4 sm:px-6 pt-8 md:pt-12">
                <div className="mb-8 md:mb-12">
                    <a href={`/tabulasi/${tabulasi.link_unik}`} className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm group mb-6 w-fit">
                        <svg className="w-4 h-4 text-slate-400 group-hover:text-slate-600 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Pengaturan
                    </a>

                    <h1 className="text-3xl font-bold text-slate-900">Ubah Struktur Tabulasi</h1>
                </div>

                <form onSubmit={handleSubmit} className="space-y-8 md:space-y-10">
                    <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                        <label className={LabelClass}>Judul Tabulasi / Proyek</label>
                        <input type="text" value={data.judul} onChange={e => setData({...data, judul: e.target.value})} required className={InputClass} />
                    </div>

                    <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                        <div className="mb-5"><h2 className="text-lg font-bold text-slate-900">Daftar Agenda</h2></div>
                        <div className="space-y-3 mb-4">
                            {data.agendas.map((agenda, index) => (
                                <div key={index} className="flex flex-col sm:flex-row gap-3">
                                    <input type="text" value={agenda.nama} onChange={e => updateArray('agendas', index, 'nama', e.target.value)} required className={`${InputClass} sm:flex-1`} />
                                    <div className="flex gap-2">
                                        <input type="date" value={agenda.deadline} onChange={e => updateArray('agendas', index, 'deadline', e.target.value)} className={`${InputClass} w-full sm:w-44 text-slate-500`} />
                                        {index > 0 && <button type="button" onClick={() => removeItem('agendas', index, 'Menghapus agenda ini akan MENGHAPUS SEMUA BARANG di dalamnya. Lanjutkan?')} className="px-4 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors font-bold">✕</button>}
                                    </div>
                                </div>
                            ))}
                        </div>
                        <button type="button" onClick={() => addItem('agendas', {id:null, nama:'', deadline:''})} className="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 py-2">+ Tambah Agenda</button>
                    </div>

                    <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                        <div className="mb-5"><h2 className="text-lg font-bold text-slate-900">Daftar Bidang</h2></div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            {data.bidangs.map((bidang, index) => (
                                <div key={index} className="flex gap-2">
                                    <input type="text" value={bidang.nama} onChange={e => updateArray('bidangs', index, 'nama', e.target.value)} required className={InputClass} />
                                    {index > 0 && <button type="button" onClick={() => removeItem('bidangs', index, 'Menghapus bidang ini akan MENGHAPUS SEMUA BARANG milik mereka. Lanjutkan?')} className="px-4 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors font-bold">✕</button>}
                                </div>
                            ))}
                        </div>
                        <button type="button" onClick={() => addItem('bidangs', {id:null, nama:''})} className="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 py-2">+ Tambah Bidang</button>
                    </div>

                    <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                        <div className="mb-5"><h2 className="text-lg font-bold text-slate-900">Kolom Form Isian</h2></div>
                        <div className="space-y-3 mb-4">
                            {data.koloms.map((kolom, index) => (
                                <div key={index} className="flex flex-col sm:flex-row gap-3">
                                    <input type="text" value={kolom.nama_kolom} onChange={e => updateArray('koloms', index, 'nama_kolom', e.target.value)} required className={`${InputClass} sm:flex-1`} />
                                    <div className="flex gap-2">
                                        <select value={kolom.tipe_input} onChange={e => updateArray('koloms', index, 'tipe_input', e.target.value)} className={`${InputClass} w-full sm:w-44`}>
                                            <option value="text">Teks / Paragraf</option><option value="number">Hanya Angka</option><option value="checkbox">Status (Centang)</option>
                                        </select>
                                        {index > 0 && <button type="button" onClick={() => removeItem('koloms', index, 'Menghapus kolom ini akan MENGHILANGKAN DATA di kolom tersebut pada seluruh barang. Lanjutkan?')} className="px-4 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors font-bold">✕</button>}
                                    </div>
                                </div>
                            ))}
                        </div>
                        <button type="button" onClick={() => addItem('koloms', {id:null, nama_kolom:'', tipe_input:'text'})} className="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 py-2">+ Tambah Kolom</button>
                    </div>

                    <div className="pt-4 pb-10 flex sm:justify-end">
                        <button type="submit" className="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white px-10 py-4 rounded-xl shadow-sm font-bold text-base transition-all active:scale-95">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    );
}