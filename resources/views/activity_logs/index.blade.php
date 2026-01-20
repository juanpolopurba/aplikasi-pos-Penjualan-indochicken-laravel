<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            <i class="fas fa-history mr-2 text-indigo-600"></i> Audit Log Aktivitas Sistem
        </h2>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 md:p-6 text-gray-900">
                    <div class="mb-6 text-sm text-gray-500 italic">
                        Menampilkan jejak digital aksi penting oleh Admin, Manager, dan Kasir.
                    </div>

                    {{-- TAMPILAN MOBILE: CARD TIMELINE (Muncul hanya di HP) --}}
                    <div class="block md:hidden space-y-4">
                        @foreach($logs as $log)
                            <div class="relative pl-6 border-l-2 {{ $log->aksi == 'HAPUS' || $log->aksi == 'VOID' ? 'border-red-500' : 'border-indigo-500' }}">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full {{ $log->aksi == 'HAPUS' || $log->aksi == 'VOID' ? 'bg-red-500' : 'bg-indigo-500' }} border-2 border-white shadow-sm"></div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="text-[10px] font-mono text-gray-400 block">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                            <span class="font-bold text-sm text-gray-800">{{ $log->user->name ?? 'User Tidak Ditemukan' }}</span>
                                            <span class="text-[10px] text-gray-400 uppercase">({{ $log->user->role ?? 'Role Tidak Ditemukan'}})</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $log->aksi == 'HAPUS' || $log->aksi == 'VOID' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $log->aksi }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-700 leading-relaxed mb-2">
                                        {{ $log->keterangan }}
                                    </p>
                                    <div class="text-[9px] font-mono text-gray-400 flex items-center">
                                        <i class="fas fa-network-wired mr-1"></i> IP: {{ $log->ip_address }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- TAMPILAN DESKTOP: TABEL (Muncul di layar md ke atas) --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-4 border-b font-bold text-gray-600">Waktu</th>
                                    <th class="p-4 border-b font-bold text-gray-600">User</th>
                                    <th class="p-4 border-b font-bold text-gray-600 text-center">Aksi</th>
                                    <th class="p-4 border-b font-bold text-gray-600">Keterangan</th>
                                    <th class="p-4 border-b font-bold text-gray-600">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 border-b text-gray-500 font-mono text-xs">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="p-4 border-b">
                                        <div class="font-bold text-gray-800">{{ $log->user->name ?? 'User Tidak Ditemukan'}}</div>
                                        <div class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $log->user->role ?? 'Role Tidak Ditemukan' }}</div>
                                    </td>
                                    <td class="p-4 border-b text-center">
                                        <span class="px-2 py-1 rounded text-[10px] font-black uppercase {{ $log->aksi == 'HAPUS' || $log->aksi == 'VOID' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $log->aksi }}
                                        </span>
                                    </td>
                                    <td class="p-4 border-b text-gray-700 leading-relaxed">
                                        {{ $log->keterangan }}
                                    </td>
                                    <td class="p-4 border-b font-mono text-[10px] text-gray-400">
                                        {{ $log->ip_address }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>