<?php
namespace App\Services\FCM;

use App\Models\System\Device;

class FCMService {

    public function sendNotification($to = [], $title = null, $body = null, $type = null, $id = null, $link = null, $toType = "Client" ) {

        if(count($to) == 0) {
            $to = Device::pluck('token');
        }

        fcm()
            ->to($to)
            ->priority('high')
            ->timeToLive(0)
            ->notification([
                'title' => $title,
                'body' => $body,
            ])
            ->data([
                "type" => $type, // service , project , other
                "id" => $id,
                "link" => $link
            ])
            ->send();
    }

}
