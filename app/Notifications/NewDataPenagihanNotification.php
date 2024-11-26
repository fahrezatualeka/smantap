<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewDataPenagihanNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $dataPenagihan;

    public function __construct($dataPenagihan)
    {
        $this->dataPenagihan = $dataPenagihan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Data penagihan baru telah ditambahkan ke laporan penagihan.',
            'dataPenagihan' => $this->dataPenagihan
        ];
    }
}