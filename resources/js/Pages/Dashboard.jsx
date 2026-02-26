import { useState } from 'react';
import { Head, router } from '@inertiajs/react';

export default function Dashboard({ auth, tabulasiSaya, dibagikanKeSaya, flash }) {
    const [dialog, setDialog] = useState({ isOpen: false, idToDelete: null });

    const handleLogout = (e) => { e.preventDefault(); router.post('/logout'); };
    const triggerDelete = (link_unik) => setDialog({ isOpen: true, idToDelete: link_unik });
    const confirmDelete = () => router.delete(`/tabulasi/${dialog.idToDelete}`, { onFinish: () => setDialog({ isOpen: false, idToDelete: null }) });

    return (
        <div className="min-h-screen bg-[#F8FAFC] font-sans text-slate-800 pb-20">
            <Head title="Dashboard - SIKAP" />
            
            {dialog.isOpen && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-all">
                    <div className="bg-white rounded-3xl shadow-2xl p-6 md:p-8 max-w-sm w-full border border-slate-100 animate-in zoom-in-95 duration-200">
                        <h3 className="text-xl font-bold mb-2 text-slate-900">Hapus Tabulasi?</h3>
                        <p className="text-slate-500 text-sm mb-8 leading-relaxed">Semua data barang, agenda, dan bidang akan terhapus selamanya. Tindakan ini tidak bisa dibatalkan.</p>
                        <div className="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button onClick={() => setDialog({ isOpen: false, idToDelete: null })} className="px-5 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 transition-colors w-full sm:w-auto">Batal</button>
                            <button onClick={confirmDelete} className="px-5 py-2.5 rounded-xl font-semibold bg-red-500 text-white hover:bg-red-600 shadow-sm transition-colors w-full sm:w-auto">Hapus Permanen</button>
                        </div>
                    </div>
                </div>
            )}

            <nav className="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/60">
                <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16 md:h-20 items-center">
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-sm">S</div>
                            <span className="text-xl font-bold text-slate-900 tracking-tight hidden sm:block">SIKAP</span>
                        </div>
                        <div className="flex items-center space-x-3 md:space-x-4">
                            <div className="text-right hidden sm:block">
                                <p className="text-sm font-semibold text-slate-900 leading-tight">{auth.user.name}</p>
                            </div>
                            <img className="h-9 w-9 rounded-full ring-2 ring-slate-100 object-cover" src={auth.user.avatar} alt="Profile" />
                            <div className="w-px h-6 bg-slate-200 mx-1 hidden sm:block"></div>
                            <button onClick={handleLogout} className="text-sm font-semibold text-slate-500 hover:text-slate-900 transition-colors px-2">Logout</button>
                        </div>
                    </div>
                </div>
            </nav>

            <main className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 md:pt-12">
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-5">
                    <h1 className="text-3xl font-bold text-slate-900 tracking-tight">Dashboard</h1>
                    <a href="/tabulasi/create" className="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 rounded-xl shadow-sm font-semibold transition-all flex justify-center items-center gap-2">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Rekapan
                    </a>
                </div>
                
                {flash.success && <div className="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl font-medium text-sm flex items-center gap-3 animate-in fade-in"><svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path></svg>{flash.success}</div>}

                <section className="mb-12">
                    <h2 className="text-sm font-bold text-slate-400 uppercase tracking-wider mb-5 px-1">Dikelola oleh Anda</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
                        {tabulasiSaya.map(tab => (
                            <div key={tab.id} className="bg-white rounded-2xl border border-slate-200/60 p-6 flex flex-col justify-between hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:border-slate-300 transition-all group">
                                <div>
                                    <h3 className="text-lg font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">{tab.judul}</h3>
                                    <span className="inline-block bg-slate-100 text-slate-600 text-[10px] px-2.5 py-1 rounded-md font-mono tracking-wider mb-6">ID: {tab.link_unik}</span>
                                </div>
                                <div className="flex justify-between items-center pt-5 border-t border-slate-100">
                                    <button onClick={() => triggerDelete(tab.link_unik)} className="text-sm font-medium text-slate-400 hover:text-red-500 transition-colors">Hapus</button>
                                    <a href={`/tabulasi/${tab.link_unik}`} className="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">Buka Tabulasi &rarr;</a>
                                </div>
                            </div>
                        ))}
                        {tabulasiSaya.length === 0 && <div className="col-span-full bg-transparent border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center flex flex-col items-center justify-center"><p className="text-slate-500 text-sm font-medium">Belum ada rekapan yang Anda buat.</p></div>}
                    </div>
                </section>

                <section>
                    <h2 className="text-sm font-bold text-slate-400 uppercase tracking-wider mb-5 px-1">Dibagikan ke Anda</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
                        {dibagikanKeSaya.map(akses => {
                            if (!akses.tabulasi) return null; // Pelindung Anti-Crash React
                            return (
                                <a key={akses.id} href={`/tabulasi/${akses.tabulasi.link_unik}`} className="bg-white rounded-2xl border border-slate-200/60 p-6 block hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:border-blue-200 transition-all group">
                                    <h3 className="text-lg font-bold text-slate-900 mb-1 line-clamp-2">{akses.tabulasi.judul}</h3>
                                    <p className="text-xs text-slate-500 mb-6 font-medium">Pembuat: {akses.tabulasi.user?.name}</p>
                                    <div className="flex justify-between items-center pt-5 border-t border-slate-100">
                                        <span className="text-xs font-semibold bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg">
                                            {akses.peran === 'ketua' ? 'Ketua Panitia' : `Bidang: ${akses.bidang?.nama_bidang || '-'}`}
                                        </span>
                                        <svg className="w-5 h-5 text-slate-300 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </a>
                            );
                        })}
                        {dibagikanKeSaya.length === 0 && <div className="col-span-full bg-transparent border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center flex flex-col items-center justify-center"><p className="text-slate-500 text-sm font-medium">Belum ada undangan pengisian form.</p></div>}
                    </div>
                </section>
            </main>
        </div>
    );
}