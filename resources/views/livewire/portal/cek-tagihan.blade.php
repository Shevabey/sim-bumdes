<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tagihan Saya</h1>

    @if(session('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if($tagihan->isEmpty())
        <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-500">
            Belum ada tagihan untuk akun ini.
        </div>
    @else
        <div class="space-y-4">
            @foreach($tagihan as $item)
                <div class="bg-white border rounded-lg p-5 shadow-sm flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs text-gray-400 font-mono">{{ $item->id_tagihan }}</p>
                        <p class="text-lg font-semibold text-gray-800 mt-1">
                            Rp{{ number_format($item->jumlah ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            Jatuh tempo: {{ $item->jatuh_tempo?->format('d M Y') ?? '-' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span @class([
                            'px-3 py-1 rounded-full text-xs font-semibold',
                            'bg-yellow-100 text-yellow-700' => $item->status === 'belum_bayar',
                            'bg-blue-100  text-blue-700'   => $item->status === 'menunggu_verifikasi',
                            'bg-green-100 text-green-700'  => $item->status === 'lunas',
                            'bg-red-100   text-red-700'    => $item->status === 'ditolak',
                        ])>
                            {{ str_replace('_', ' ', ucfirst($item->status)) }}
                        </span>

                        @if($item->status === 'belum_bayar')
                            <a href="{{ route('portal.upload-bukti', $item->id_tagihan) }}"
                               id="btn-upload-{{ $item->id_tagihan }}"
                               class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg
                                      hover:bg-blue-700 transition">
                                Upload Bukti
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
