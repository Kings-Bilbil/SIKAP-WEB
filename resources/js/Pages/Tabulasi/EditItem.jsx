import { useState } from 'react';
import { Head, router } from '@inertiajs/react';

export default function EditItem({ tabulasi, item, isAdmin }) {
    const [data, setData] = useState({
        agenda_id: item.agenda_id || '',
        bidang_id: item.bidang_id || '',
        isian: item.data_isi || {}
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        router.put(`/tabulasi/${tabulasi.link_unik}/item/${item.id}`, data);
    };

    const handleIsianChange = (nama_kolom, value) => {
        setData({ ...data, isian: { ...data.isian, [nama_kolom]: value } });
    };

    const InputClass = "w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all";
    const LabelClass = "block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2";

    return (
        <div className="min-h-screen bg-[#F8FAFC] font-sans text-slate-800 pb-20">
            <Head title="Edit Barang - SIKAP" />
            <nav className="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/60"><div className="max-w-2xl mx-auto px-4 sm:px-6 flex h-16 items-center"><div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">S</div><span className="text-lg font-bold text-slate-900 tracking-tight">SIKAP</span></div></nav>

            <div className="max-w-2xl mx-auto px-4 sm:px-6 pt-8 md:pt-12">
                <div className="mb-8 md:mb-12">
                    <a href={`/tabulasi/${tabulasi.link_unik}`} className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm group mb-6 w-fit">
                        <svg className="w-4 h-4 text-slate-400 group-hover:text-slate-600 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Batal Edit
                    </a>
                    <h1 className="text-3xl font-bold text-slate-900">Edit Data Barang</h1>
                </div>

                <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-10 shadow-sm">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-2">
                            <div>
                                <label className={LabelClass}>Pindah Agenda?</label>
                                <select value={data.agenda_id} onChange={e => setData({...data, agenda_id: e.target.value})} required className={InputClass}>
                                    {tabulasi.agendas.map(agenda => <option key={agenda.id} value={agenda.id}>{agenda.nama_agenda}</option>)}
                                </select>
                            </div>
                            {isAdmin && (
                                <div>
                                    <label className={LabelClass}>Pindah Bidang?</label>
                                    <select value={data.bidang_id} onChange={e => setData({...data, bidang_id: e.target.value})} required className={InputClass}>
                                        {tabulasi.bidangs.map(bidang => <option key={bidang.id} value={bidang.id}>{bidang.nama_bidang}</option>)}
                                    </select>
                                </div>
                            )}
                        </div>

                        <div className="w-full h-px bg-slate-100 my-6"></div>

                        {tabulasi.koloms.map((kolom, index) => {
                            if (kolom.tipe_input === 'checkbox') return null; 
                            return (
                                <div key={index}>
                                    <label className={LabelClass}>{kolom.nama_kolom}</label>
                                    <input type={kolom.tipe_input === 'number' ? 'number' : 'text'} value={data.isian[kolom.nama_kolom] || ''} onChange={e => handleIsianChange(kolom.nama_kolom, e.target.value)} required className={InputClass} />
                                </div>
                            );
                        })}
                        
                        <div className="pt-6 flex justify-end">
                            <button type="submit" className="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-10 py-3.5 rounded-xl shadow-[0_8px_30px_rgb(37,99,235,0.2)] font-bold transition-all active:scale-[0.98]">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}