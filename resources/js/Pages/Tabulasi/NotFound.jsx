import { Head } from '@inertiajs/react';

export default function NotFound() {
    return (
        <div className="min-h-screen bg-gray-50 flex flex-col justify-center items-center p-4">
            <Head title="Tabulasi Tidak Ditemukan - SIKAP" />
            <div className="bg-white rounded-3xl shadow-xl border border-gray-200 p-10 max-w-lg w-full text-center border-t-8 border-t-red-500">
                <div className="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg className="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h2 className="text-3xl font-extrabold text-gray-900 mb-2">Ups! Link Salah atau Kedaluwarsa</h2>
                <p className="text-gray-500 mb-8 leading-relaxed">Tabulasi yang Anda cari tidak ditemukan. Pastikan Anda menyalin link dengan benar atau hubungi pembuat tabulasi.</p>
                <a href="/dashboard" className="w-full inline-block bg-gray-900 hover:bg-blue-600 text-white py-3.5 rounded-xl font-bold shadow-md transition">Kembali ke Dashboard</a>
            </div>
        </div>
    );
}