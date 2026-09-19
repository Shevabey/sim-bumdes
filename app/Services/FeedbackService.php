<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\Feedback;
use App\Notifications\FeedbackDiterima;
use Illuminate\Support\Facades\Notification;

class FeedbackService
{
    /**
     * Kirim notifikasi ke seluruh akun yang terkait BUMDes/Unit
     * saat feedback baru diterima.
     */
    public function kirimNotifikasi(Feedback $feedback): void
    {
        $query = Akun::where('id_bumdes', $feedback->ke_id_bumdes);

        // Jika feedback ditujukan ke unit tertentu, sertakan juga admin_unit unit tersebut
        if ($feedback->ke_id_unit) {
            $query->orWhere('id_unit', $feedback->ke_id_unit);
        }

        $akunTerkait = $query->get();

        Notification::send($akunTerkait, new FeedbackDiterima($feedback));
    }
}
