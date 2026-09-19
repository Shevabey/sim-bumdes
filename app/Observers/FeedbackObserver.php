<?php

namespace App\Observers;

use App\Models\Feedback;
use App\Services\FeedbackService;

class FeedbackObserver
{
    public function __construct(protected FeedbackService $feedbackService) {}

    /**
     * Saat feedback baru dibuat, kirim notifikasi ke akun terkait BUMDes/unit.
     * Sesuai FR-34: "Sistem mengirim notifikasi real-time kepada pengurus BUMDes/unit
     * terkait saat catatan/feedback baru diterima."
     */
    public function created(Feedback $feedback): void
    {
        $this->feedbackService->kirimNotifikasi($feedback);
    }
}
