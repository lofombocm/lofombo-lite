<?php

namespace App\Jobs;

use App\Mail\MailForVoucherGenerated;
use App\Mail\MailForVoucherSendUsageCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessSendEMailVoucherUsageCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 20;
    public $tries = 3;

    protected $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /*$h = fopen('test.txt', 'w+');
        fwrite($h, json_encode($this->data));
        fclose($h);*/

        Mail::to($this->data['email'])->send(new MailForVoucherSendUsageCode($this->data));
    }
}
