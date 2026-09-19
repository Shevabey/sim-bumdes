<?php

namespace App\Livewire\Portal;

use App\Models\Tagihan;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadBukti extends Component
{
    use WithFileUploads;

    public string $idTagihan = '';
    public $bukti;

    protected array $rules = [
        'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ];

    public function mount(string $tagihan): void
    {
        $this->idTagihan = $tagihan;

        // Guard: pastikan tagihan milik pelanggan yang login & statusnya belum_bayar
        $record = Tagihan::where('id_tagihan', $tagihan)
            ->where('id_pelanggan', auth()->user()?->pelanggan?->id_pelanggan)
            ->firstOrFail();

        abort_if($record->status !== 'belum_bayar', 422, 'Tagihan ini tidak dapat diupload.');
    }

    public function simpan(): void
    {
        $this->validate();

        $tagihan = Tagihan::findOrFail($this->idTagihan);

        $path = $this->bukti->store('bukti-transfer', 'public');

        $tagihan->update([
            'metode'             => 'transfer',
            'bukti_transfer_url' => $path,
            'status'             => 'menunggu_verifikasi',
        ]);

        session()->flash('message', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin unit.');

        $this->redirect(route('portal.tagihan'), navigate: true);
    }

    public function render()
    {
        return view('livewire.portal.upload-bukti')
            ->layout('components.layouts.app');
    }
}
