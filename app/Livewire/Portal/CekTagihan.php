<?php

namespace App\Livewire\Portal;

use App\Models\Tagihan;
use Livewire\Component;

class CekTagihan extends Component
{
    public function render()
    {
        abort_unless(auth()->check() && auth()->user()->pelanggan, 403);

        $tagihan = Tagihan::where(
            'id_pelanggan',
            auth()->user()->pelanggan->id_pelanggan
        )
            ->orderByDesc('jatuh_tempo')
            ->get();

        return view('livewire.portal.cek-tagihan', compact('tagihan'))
            ->layout('components.layouts.app');
    }
}
