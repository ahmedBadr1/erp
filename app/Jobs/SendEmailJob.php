<?php

namespace App\Jobs;

use App\Mail\VerificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    public $tries = 4;
    public $retryAfter = 30;

    protected $data ,$mail;
    /**
     * Create a new job instance.
     */
    public function __construct($data,$mail)
    {
       $this->data = $data;
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mail = "App\Mail\\" . $this->mail ;
        $email = new $mail($this->data);
        Mail::to($this->data['email'])->send($email);
    }

    public function retryUntil()
    {
        return now()->addMinutes(5);
    }
}
