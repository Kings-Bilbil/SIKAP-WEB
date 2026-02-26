import { Head } from '@inertiajs/react';

export default function Welcome({ error }) {
    return (
        <div className="min-h-screen bg-[#F8FAFC] flex flex-col justify-center items-center p-6 font-sans text-slate-800 relative overflow-hidden">
            <Head title="Login - SIKAP" />
            
            {/* Efek Glow Latar Belakang */}
            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

            <div className="max-w-sm w-full bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden relative">
                <div className="p-10 text-center">
                    <div className="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-500/30">
                        <span className="text-3xl font-bold text-white tracking-tighter">S</span>
                    </div>
                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight mb-1">SIKAP</h1>
                    <p className="text-sm text-slate-500 font-medium mb-10">Sistem Informasi Rekapitulasi</p>
                    
                    <a href="/auth/google" className="flex items-center justify-center w-full bg-white border border-slate-200 text-slate-700 px-4 py-3.5 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all font-semibold shadow-sm group">
                        <svg className="h-5 w-5 mr-3 group-hover:scale-110 transition-transform" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                        </svg>
                        Lanjutkan dengan Google
                    </a>
                    
                    {error && (
                        <div className="mt-6 p-3 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100/50 font-medium">
                            {error}
                        </div>
                    )}
                </div>
            </div>
            <p className="mt-8 text-xs text-slate-400 font-medium tracking-wide">© 2026 SIKAP. Dirancang untuk Kepanitiaan.</p>
        </div>
    );
}