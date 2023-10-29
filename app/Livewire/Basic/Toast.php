<?php

namespace App\Livewire\Basic;

trait Toast {

    public $lang = 'ar';

    public function swal($message,$type= 'success', $position = 'center')
    {
        $this->dispatch('swal',
            message: __($message),
            type: $type,
            position: $position
        );
    }

//    public function notice($message,$type= 'success', $position = 'center')
//    {
//        $this->dispatch('toast', [
//            'message' =>  __($message),
//            'type' => $type,
//            'position' => $position,
//        ]);
//    }

    public function toast($message,$type= 'success')
    {
        $this->dispatch('notice',
            text:  $message,
            type: $type,
        );
    }
}
