<?php

namespace App\Notifications;

use App\Models\Feedback;
use Illuminate\Notifications\Notification;

class FeedbackDiterima extends Notification
{
    public function __construct(protected Feedback $feedback) {}

    /**
     * Channel yang digunakan: database (tanpa broadcast real-time,
     * ditampilkan via polling Livewire sesuai SRS NFR).
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data yang disimpan ke tabel notifications.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'id_feedback'  => $this->feedback->id_feedback,
            'isi_catatan'  => $this->feedback->isi_catatan,
            'dari'         => $this->feedback->pengirim?->nama ?? '—',
            'ke_bumdes'    => $this->feedback->ke_id_bumdes,
            'ke_unit'      => $this->feedback->ke_id_unit,
            'status'       => $this->feedback->status_tindak_lanjut,
        ];
    }
}
