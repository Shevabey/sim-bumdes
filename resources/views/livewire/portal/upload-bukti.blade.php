<div class="max-w-lg mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Upload Bukti Transfer</h1>

    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <p class="text-sm text-gray-500 mb-4">
            ID Tagihan:
            <span class="font-mono font-semibold text-gray-700">{{ $idTagihan }}</span>
        </p>

        <form wire:submit="simpan" enctype="multipart/form-data">
            <div class="mb-5">
                <label for="bukti-upload" class="block text-sm font-medium text-gray-700 mb-1">
                    Bukti Transfer
                    <span class="text-gray-400 font-normal">(JPG, PNG, atau PDF — maks. 2 MB)</span>
                </label>

                <input type="file"
                       id="bukti-upload"
                       wire:model="bukti"
                       accept=".jpg,.jpeg,.png,.pdf"
                       class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100">

                {{-- Loading indicator saat file sedang diupload ke server --}}
                <div wire:loading wire:target="bukti" class="mt-1 text-xs text-blue-500">
                    Sedang memproses file...
                </div>

                @error('bukti')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Preview nama file yang dipilih --}}
            @if($bukti)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                    File dipilih: <span class="font-semibold">{{ $bukti->getClientOriginalName() }}</span>
                </div>
            @endif

            <button type="submit"
                    id="btn-simpan-bukti"
                    wire:loading.attr="disabled"
                    wire:target="simpan"
                    class="w-full py-2.5 px-4 bg-blue-600 text-white font-semibold
                           rounded-lg hover:bg-blue-700 disabled:opacity-60
                           disabled:cursor-not-allowed transition">
                <span wire:loading.remove wire:target="simpan">Kirim Bukti Transfer</span>
                <span wire:loading wire:target="simpan">Mengupload...</span>
            </button>
        </form>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('portal.tagihan') }}"
           class="text-sm text-gray-500 hover:text-gray-700 underline">
            ← Kembali ke daftar tagihan
        </a>
    </div>
</div>
