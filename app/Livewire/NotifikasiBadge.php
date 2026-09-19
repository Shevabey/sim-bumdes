<?php

namespace App\Livewire;

use Livewire\Component;

class NotifikasiBadge extends Component
{
    /**
     * Polling setiap 10 detik via wire:poll di view.
     * Menampilkan jumlah notifikasi yang belum dibaca.
     */
    public function render()
    {
        $jumlahBelumDibaca = auth()->check()
            ? auth()->user()->unreadNotifications->count()
            : 0;

        return view('livewire.notifikasi-badge', compact('jumlahBelumDibaca'));
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function tandaiSudahDibaca(): void
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications->markAsRead();
        }
    }
}
