import { useState, Fragment, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';

export default function Show({ auth, tabulasi, isAdmin, akses, isGuest, isPending, flash }) {
    
    // 1. STATE HARUS DIDEKLARASIKAN PALING ATAS
    const [activeMainTab, setActiveMainTab] = useState('rekap');
    const [activeAgendaTab, setActiveAgendaTab] = useState(tabulasi?.agendas?.[0]?.id || '');
    
    const [bidangId, setBidangId] = useState('');
    const [dataIsian, setDataIsian] = useState({});
    const [emailAkses, setEmailAkses] = useState('');
    const [peranAkses, setPeranAkses] = useState('anggota');
    const [bidangAksesId, setBidangAksesId] = useState('');
    
    const [copied, setCopied] = useState(false);
    const [dialog, setDialog] = useState({ isOpen: false, title: '', message: '', action: null, btnStyle: '' });
    
    // 2. USEEFFECT SEKARANG AMAN KARENA VARIABEL DI ATAS SUDAH ADA
    useEffect(() => {
        if (!isGuest && !isPending && activeMainTab !== 'form') {
            const interval = setInterval(() => { 
                router.reload({ 
                    only: ['tabulasi', 'isGuest', 'isPending', 'akses'], 
                    preserveScroll: true, 
                    preserveState: true 
                }); 
            }, 5000); 
            return () => clearInterval(interval);
        }
    }, [isGuest, isPending, activeMainTab]);

    const openConfirm = (title, message, btnStyle, action) => setDialog({ isOpen: true, title, message, btnStyle, action });
    const closeDialog = () => setDialog({ ...dialog, isOpen: false });

    const isKetua = akses?.peran === 'ketua';

    const formatTanggalIndo = (dateString) => {
        if (!dateString) return 'Tidak diatur';
        return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    const isDeadlinePassed = (deadlineString) => {
        if (!deadlineString) return false;
        const deadlineDate = new Date(deadlineString);
        deadlineDate.setHours(23, 59, 59, 999); 
        return new Date() > deadlineDate;
    };

    const handleIsianChange = (nama_kolom, value) => setDataIsian(prev => ({ ...prev, [nama_kolom]: value }));

    const submitBarang = (e) => {
        e.preventDefault();
        router.post(`/tabulasi/${tabulasi.link_unik}/item`, { agenda_id: activeAgendaTab, bidang_id: bidangId, isian: dataIsian }, { preserveScroll: true, onSuccess: () => {setDataIsian({}); setActiveMainTab('rekap');} });
    };

    const toggleCheckbox = (itemId, namaKolom) => router.post(`/tabulasi/${tabulasi.link_unik}/item/${itemId}/toggle`, { nama_kolom: namaKolom }, { preserveScroll: true });
    
    const deleteItem = (itemId) => {
        openConfirm('Hapus Barang?', 'Yakin ingin menghapus barang ini secara permanen?', 'bg-red-500 hover:bg-red-600 text-white', () => router.delete(`/tabulasi/${tabulasi.link_unik}/item/${itemId}`, { preserveScroll: true }));
    };

    const submitMintaAkses = (e) => { e.preventDefault(); router.post(`/tabulasi/${tabulasi.link_unik}/minta-akses`, { bidang_id: bidangId }); };
    
    const submitAksesManual = (e) => { 
        e.preventDefault(); 
        router.post(`/tabulasi/${tabulasi.link_unik}/akses`, { email_pengisi: emailAkses, peran: peranAkses, bidang_id: peranAkses === 'anggota' ? bidangAksesId : null }, { preserveScroll: true, onSuccess: () => setEmailAkses('') }); 
    };

    const updateStatusAkses = (aksesId, action) => {
        if (action === 'approve') router.post(`/tabulasi/${tabulasi.link_unik}/akses/${aksesId}/terima`, {}, { preserveScroll: true });
        else if (action === 'delete') openConfirm('Cabut Akses?', 'Yakin ingin mencabut akses untuk pengisi ini?', 'bg-red-500 hover:bg-red-600 text-white', () => router.delete(`/tabulasi/${tabulasi.link_unik}/akses/${aksesId}`, { preserveScroll: true }));
    };

    const handleCopyLink = () => { navigator.clipboard.writeText(window.location.href); setCopied(true); setTimeout(() => setCopied(false), 2500); };

    const InputClass = "w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all";
    const LabelClass = "block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2";

    if (isGuest || isPending) {
        return (
            <div className="min-h-screen bg-[#F8FAFC] flex flex-col justify-center items-center p-4">
                <Head title="Akses Terkunci - SIKAP" />
                <div className="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 md:p-10 max-w-sm w-full text-center relative overflow-hidden">
                    <div className={`absolute top-0 left-0 w-full h-1.5 ${isPending ? 'bg-amber-400' : 'bg-blue-600'}`}></div>
                    
                    {isGuest ? (
                        <>
                            <div className="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6"><svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg></div>
                            <h2 className="text-xl font-bold text-slate-900 mb-2">Akses Terbatas</h2>
                            <p className="text-slate-500 text-sm mb-8 leading-relaxed">Anda butuh undangan untuk mengakses form <b>{tabulasi.judul}</b>.</p>
                            <form onSubmit={submitMintaAkses} className="text-left">
                                <label className={LabelClass}>Minta Akses Sebagai Anggota:</label>
                                <select value={bidangId} onChange={e => setBidangId(e.target.value)} required className={`${InputClass} mb-5`}>
                                    <option value="" disabled>-- Pilih Bidang --</option>
                                    {tabulasi.bidangs.map(b => <option key={b.id} value={b.id}>{b.nama_bidang}</option>)}
                                </select>
                                <button type="submit" className="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl font-semibold shadow-sm transition-all">Minta Akses Sekarang</button>
                            </form>
                        </>
                    ) : (
                        <>
                            <div className="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-6"><svg className="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                            <h2 className="text-xl font-bold text-slate-900 mb-2">Menunggu Konfirmasi</h2>
                            <p className="text-slate-500 text-sm mb-6 leading-relaxed">Permintaan Anda telah dikirim ke pembuat form. Layar ini akan terbuka otomatis saat disetujui.</p>
                            <a href="/dashboard" className="text-blue-600 text-sm font-semibold hover:underline">Kembali ke Dashboard</a>
                        </>
                    )}
                </div>
            </div>
        );
    }

    const pendingRequests = tabulasi.akses_tabulasis.filter(a => a.status === 'pending');
    const approvedRequests = tabulasi.akses_tabulasis.filter(a => a.status === 'approved');
    const activeAgendaData = tabulasi.agendas.find(a => a.id === activeAgendaTab);
    
    const deadlineHabis = isDeadlinePassed(activeAgendaData?.deadline);
    const formTerkunci = deadlineHabis && !isAdmin;

    return (
        <div className="min-h-screen bg-[#F8FAFC] font-sans text-slate-800 pb-20 relative">
            <Head title={`${tabulasi.judul} - SIKAP`} />

            {dialog.isOpen && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-all">
                    <div className="bg-white rounded-3xl shadow-2xl p-6 md:p-8 max-w-sm w-full border border-slate-100 animate-in zoom-in-95 duration-200">
                        <h3 className="text-xl font-bold mb-2 text-slate-900">{dialog.title}</h3>
                        <p className="text-slate-500 text-sm mb-8 leading-relaxed">{dialog.message}</p>
                        <div className="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button onClick={closeDialog} className="px-5 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 transition-colors">Batal</button>
                            <button onClick={() => { dialog.action(); closeDialog(); }} className={`px-5 py-2.5 rounded-xl font-semibold shadow-sm transition-colors ${dialog.btnStyle}`}>Konfirmasi</button>
                        </div>
                    </div>
                </div>
            )}

            <div className="bg-white border-b border-slate-200/60 pt-4 md:pt-6 pb-4 px-4 sm:px-6 sticky top-0 z-40">
                <div className="max-w-6xl mx-auto">
                    
                    <div className="mb-4">
                        <a href="/dashboard" className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all group w-fit">
                            <svg className="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Dashboard
                        </a>
                    </div>

                    <div className="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-5">
                        <div>
                            <h1 className="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                                {tabulasi.judul}
                                <span className="bg-emerald-100 text-emerald-700 text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-md font-bold flex items-center gap-1.5"><span className="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>Live</span>
                            </h1>
                            <p className="text-slate-500 font-medium text-sm mt-1">Peran Anda: <span className="text-slate-800 font-bold">{isAdmin ? 'Pembuat' : (isKetua ? 'Ketua Panitia' : `Bidang ${akses?.bidang?.nama_bidang || ''}`)}</span></p>
                        </div>
                        <div className="flex items-center gap-3 w-full md:w-auto">
                            <button onClick={handleCopyLink} className={`flex-1 md:flex-none justify-center px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm border transition-all flex items-center gap-2 ${copied ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300'}`}>
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                {copied ? 'Tersalin!' : 'Salin Tautan'}
                            </button>
                        </div>
                    </div>

                    <div className="flex gap-6 overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                        <button onClick={() => setActiveMainTab('rekap')} className={`pb-3 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors ${activeMainTab === 'rekap' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'}`}>Tabel Rekapitulasi</button>
                        <button onClick={() => setActiveMainTab('form')} className={`pb-3 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors ${activeMainTab === 'form' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'}`}>Input Barang</button>
                        {isAdmin && <button onClick={() => setActiveMainTab('pengaturan')} className={`pb-3 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors flex items-center gap-2 ${activeMainTab === 'pengaturan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'}`}>Pengaturan {pendingRequests.length > 0 && <span className="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-md leading-none">{pendingRequests.length}</span>}</button>}
                    </div>
                </div>
            </div>

            <main className="max-w-6xl mx-auto px-4 sm:px-6 pt-6 md:pt-8">
                {flash.success && <div className="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl font-medium text-sm animate-in fade-in">{flash.success}</div>}
                {flash.error && <div className="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl font-medium text-sm animate-in fade-in">{flash.error}</div>}
                
                {activeMainTab !== 'pengaturan' && (
                    <div className="flex gap-2 mb-6 overflow-x-auto pb-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                        {tabulasi.agendas.map(agenda => {
                            const isLewat = isDeadlinePassed(agenda.deadline);
                            return (
                                <button key={agenda.id} onClick={() => setActiveAgendaTab(agenda.id)} className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-all border ${activeAgendaTab === agenda.id ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}`}>
                                    {agenda.nama_agenda}
                                    {isLewat && <span className={`text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded-md ${activeAgendaTab === agenda.id ? 'bg-slate-700 text-slate-300' : 'bg-red-50 text-red-600'}`}>Tutup</span>}
                                </button>
                            );
                        })}
                    </div>
                )}

                {activeMainTab === 'rekap' && (() => {
                    const itemsInAgenda = tabulasi.items.filter(item => item.agenda_id === activeAgendaTab);
                    return (
                        <div className="bg-white border border-slate-200/60 rounded-3xl shadow-sm overflow-hidden animate-in fade-in duration-300">
                            <div className="p-5 md:p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
                                <div>
                                    <h3 className="text-lg font-bold text-slate-900">{activeAgendaData?.nama_agenda}</h3>
                                    <p className="text-xs font-medium text-slate-500 mt-1">Tenggat: {formatTanggalIndo(activeAgendaData?.deadline)} • {itemsInAgenda.length} Barang</p>
                                </div>
                                {isAdmin && itemsInAgenda.length > 0 && (
                                    <a href={`/tabulasi/${tabulasi.link_unik}/export`} className="w-full sm:w-auto bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-xl transition-colors text-center flex items-center justify-center gap-2 shadow-sm">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Unduh Spreadsheet (.xlsx)
                                    </a>
                                )}
                            </div>
                            
                            <div className="overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                                <table className="min-w-full text-sm text-left">
                                    <thead className="bg-white border-b border-slate-100">
                                        <tr>
                                            <th className="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">Input</th>
                                            <th className="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">Oleh</th>
                                            {tabulasi.koloms.map((k, i) => <th key={i} className="px-6 py-4 text-[11px] font-bold text-blue-600 uppercase tracking-widest whitespace-nowrap bg-blue-50/30">{k.nama_kolom}</th>)}
                                            <th className="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100/50">
                                        {itemsInAgenda.length === 0 ? (
                                            <tr><td colSpan={3 + tabulasi.koloms.length} className="py-16 text-center text-slate-400 text-sm font-medium">Data kosong. Yuk mulai isi form!</td></tr>
                                        ) : (
                                            tabulasi.bidangs.map(bidang => {
                                                const itemsInBidang = itemsInAgenda.filter(item => item.bidang_id === bidang.id);
                                                if (itemsInBidang.length === 0) return null; 
                                                return (
                                                    <Fragment key={bidang.id}>
                                                        <tr><td colSpan={3 + tabulasi.koloms.length} className="px-6 py-3 bg-slate-50 border-t border-slate-100 text-xs font-bold text-slate-700 uppercase tracking-wider">Bidang {bidang.nama_bidang}</td></tr>
                                                        {itemsInBidang.map(item => (
                                                            <tr key={item.id} className="bg-white hover:bg-slate-50/50 transition-colors group">
                                                                <td className="px-6 py-4 text-xs text-slate-400 whitespace-nowrap">{new Date(item.created_at).toLocaleString('id-ID', {day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit'})}</td>
                                                                <td className="px-6 py-4 font-medium text-slate-700">{item.user?.name ? item.user.name.split(' ')[0] : 'Pengisi'}</td>
                                                                {tabulasi.koloms.map((kolom, i) => {
                                                                    const nilai = item.data_isi[kolom.nama_kolom] || false;
                                                                    return (
                                                                        <td key={i} className="px-6 py-4">
                                                                            {kolom.tipe_input === 'checkbox' ? (
                                                                                isAdmin ? (
                                                                                    <div className="flex items-center">
                                                                                        <input type="checkbox" checked={nilai} onChange={() => toggleCheckbox(item.id, kolom.nama_kolom)} className="w-5 h-5 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer transition-all" />
                                                                                    </div>
                                                                                ) : (
                                                                                    nilai ? <span className="inline-flex items-center bg-emerald-50 text-emerald-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-emerald-100">✓ Done</span> : <span className="text-slate-300">-</span>
                                                                                )
                                                                            ) : <span className="text-slate-600">{nilai || '-'}</span>}
                                                                        </td>
                                                                    );
                                                                })}
                                                                <td className="px-6 py-4">
                                                                    {(isAdmin || item.user_id === auth.user.id) ? (
                                                                        <div className="flex justify-center items-center gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                                                            <a href={`/tabulasi/${tabulasi.link_unik}/item/${item.id}/edit`} className="text-slate-400 hover:text-blue-600 p-1.5 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></a>
                                                                            <button onClick={() => deleteItem(item.id)} className="text-slate-400 hover:text-red-500 p-1.5 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                                                        </div>
                                                                    ) : null}
                                                                </td>
                                                            </tr>
                                                        ))}
                                                    </Fragment>
                                                );
                                            })
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    );
                })()}

                {activeMainTab === 'form' && (
                    <div className="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200/60 p-6 md:p-10 shadow-sm animate-in fade-in duration-300">
                        {formTerkunci ? (
                            <div className="text-center py-10">
                                <div className="w-16 h-16 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg></div>
                                <h3 className="text-lg font-bold text-slate-900 mb-1">Tenggat Waktu Berakhir</h3>
                                <p className="text-sm text-slate-500">Pengisian untuk "{activeAgendaData?.nama_agenda}" telah ditutup.</p>
                            </div>
                        ) : (
                            <form onSubmit={submitBarang} className="space-y-6">
                                {deadlineHabis && isAdmin && (
                                    <div className="p-4 bg-amber-50 rounded-xl text-amber-700 text-xs font-semibold border border-amber-100/50 flex items-center gap-2">
                                        <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Mode Pembuat: Tenggat sudah lewat, tapi Anda tetap bisa mengisi form.
                                    </div>
                                )}
                                
                                {(isAdmin || isKetua) && (
                                    <div>
                                        <label className={LabelClass}>Pilih Bidang Penanggung Jawab</label>
                                        <select value={bidangId} onChange={e => setBidangId(e.target.value)} required className={InputClass}>
                                            <option value="" disabled>-- Pilih Bidang --</option>
                                            {tabulasi.bidangs.map(b => <option key={b.id} value={b.id}>{b.nama_bidang}</option>)}
                                        </select>
                                    </div>
                                )}
                                
                                {tabulasi.koloms.map((kolom, index) => {
                                    if (kolom.tipe_input === 'checkbox') return null; 
                                    return (
                                        <div key={kolom.id || index}>
                                            <label className={LabelClass}>{kolom.nama_kolom}</label>
                                            <input type={kolom.tipe_input === 'number' ? 'number' : 'text'} value={dataIsian[kolom.nama_kolom] || ''} onChange={e => handleIsianChange(kolom.nama_kolom, e.target.value)} required className={InputClass} placeholder={`Ketik ${kolom.nama_kolom.toLowerCase()}...`} />
                                        </div>
                                    );
                                })}
                                <div className="pt-4"><button type="submit" className="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold shadow-[0_8px_30px_rgb(37,99,235,0.2)] transition-all active:scale-[0.98]">Submit Barang</button></div>
                            </form>
                        )}
                    </div>
                )}

                {activeMainTab === 'pengaturan' && isAdmin && (
                    <div className="space-y-6 animate-in fade-in duration-300">
                        <div className="bg-slate-900 rounded-3xl p-8 flex flex-col md:flex-row justify-between items-start md:items-center relative overflow-hidden shadow-lg">
                            <div className="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl"></div>
                            <div className="relative z-10 mb-6 md:mb-0">
                                <h3 className="text-xl font-bold text-white mb-1">Struktur Tabulasi</h3>
                                <p className="text-slate-400 text-sm">Tambah/hapus kolom form, bidang, dan atur tenggat waktu agenda.</p>
                            </div>
                            <a href={`/tabulasi/${tabulasi.link_unik}/edit-setup`} className="relative z-10 w-full md:w-auto text-center bg-white text-slate-900 hover:bg-slate-100 px-6 py-3 rounded-xl font-bold transition-colors text-sm shadow-sm">Edit Struktur Dasar</a>
                        </div>

                        {pendingRequests.length > 0 && (
                            <div className="bg-white rounded-3xl border border-amber-200 p-6 md:p-8 shadow-sm">
                                <h3 className="text-sm font-bold text-amber-600 uppercase tracking-wider mb-6 flex items-center gap-2"><span className="w-2 h-2 bg-amber-500 rounded-full animate-ping"></span> {pendingRequests.length} Permintaan Masuk</h3>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {pendingRequests.map(req => (
                                        <div key={req.id} className="bg-amber-50/50 p-5 rounded-2xl border border-amber-100">
                                            <p className="font-bold text-slate-900 text-sm break-all mb-1">{req.email_pengisi}</p>
                                            <p className="text-xs text-slate-500 mb-5">Meminta akses ke bidang <span className="font-bold text-slate-700">{req.bidang.nama_bidang}</span></p>
                                            <div className="flex gap-2">
                                                <button onClick={() => updateStatusAkses(req.id, 'approve')} className="flex-1 bg-white border border-slate-200 text-slate-900 py-2 rounded-lg text-xs font-bold hover:bg-slate-50 hover:border-slate-300 transition-colors shadow-sm">Terima</button>
                                                <button onClick={() => updateStatusAkses(req.id, 'delete')} className="px-4 bg-transparent text-slate-400 hover:text-red-500 py-2 rounded-lg text-xs font-bold transition-colors">Abaikan</button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        <div className="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-sm">
                            <h3 className="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6">Undang Anggota Atau Ketua</h3>
                            
                            <form onSubmit={submitAksesManual} className="flex flex-col sm:flex-row gap-3 mb-10">
                                <input type="email" value={emailAkses} onChange={e => setEmailAkses(e.target.value)} required placeholder="Ketik email Google panitia..." className={`${InputClass} sm:flex-1`} />
                                
                                <select value={peranAkses} onChange={e => setPeranAkses(e.target.value)} required className={`${InputClass} sm:w-40`}>
                                    <option value="anggota">Anggota Biasa</option>
                                    <option value="ketua">Ketua Panitia</option>
                                </select>

                                {peranAkses === 'anggota' && (
                                    <select value={bidangAksesId} onChange={e => setBidangAksesId(e.target.value)} required className={`${InputClass} sm:w-48`}>
                                        <option value="" disabled>Pilih Bidang</option>
                                        {tabulasi.bidangs.map(b => <option key={b.id} value={b.id}>{b.nama_bidang}</option>)}
                                    </select>
                                )}

                                <button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-sm transition-colors text-sm">Undang</button>
                            </form>
                            
                            <h3 className="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Akses Aktif</h3>
                            <div className="border border-slate-100 rounded-2xl overflow-hidden">
                                {approvedRequests.length === 0 ? (
                                    <div className="p-8 text-center text-slate-400 text-sm font-medium bg-slate-50/50">Belum ada anggota yang diundang.</div>
                                ) : (
                                    <div className="divide-y divide-slate-100">
                                        {approvedRequests.map(akses => (
                                            <div key={akses.id} className="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 sm:px-6 hover:bg-slate-50 transition-colors gap-3">
                                                <div>
                                                    <p className="font-bold text-slate-900 text-sm flex items-center gap-2">
                                                        {akses.email_pengisi} 
                                                        {akses.peran === 'ketua' && <span className="bg-indigo-100 text-indigo-700 text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded font-bold">Ketua</span>}
                                                    </p>
                                                    <p className="text-xs text-slate-500 mt-0.5">{akses.peran === 'ketua' ? 'Memantau Semua Bidang' : `Bidang ${akses.bidang?.nama_bidang || '-'}`}</p>
                                                </div>
                                                <button onClick={() => updateStatusAkses(akses.id, 'delete')} className="text-xs font-semibold text-slate-400 hover:text-red-500 bg-white border border-slate-200 hover:border-red-200 px-3 py-1.5 rounded-lg transition-colors">Cabut Akses</button>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                )}
            </main>
        </div>
    );
}