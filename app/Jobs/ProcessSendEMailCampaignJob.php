<?php

namespace App\Jobs;

use App\Mail\MailForCampaign;
use App\Models\Campaign;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProcessSendEMailCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 10000000;
    public $tries = 5;
    public $backoff = [5, 10, 30, 90, 270]; // seconds

    public $maxExceptions = 5;


    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->release(4);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recipients = $this->data['recipients'];
        //$fichier = fopen('email.txt', 'w');
        foreach ($recipients as $recipient) {
            Mail::to($recipient['email'], $recipient['name']/*$this->data['email']*/)->send(new MailForCampaign($this->data));
            //fwrite($fichier, $recipient['email'] . PHP_EOL);
        }
        //fclose($fichier);
        $id = Str::uuid()->toString();
        $channel = 'EMAIL_CHANNEL';
        $subject = $this->data['subject'];
        $message = $this->data['message'];
        $addresses = json_encode($recipients);
        $sender = $this->data['sender'];
        $send_at = Carbon::now();
        $campaign = Campaign::create(
            [
                'id' => $id,
                'channel' => $channel,
                'subject' => $subject,
                'message' => $message,
                'client_address' => $addresses,
                'sender' => $sender,
                'send_at' => $send_at,
            ]
        );
        //var_dump($campaign);
    }

    public function retryUntil()
    {
        return now()->addHour();
    }

    public function failed(Exception $exception)
    {
        // Code to run when the job fails
        Log::error('Job failed: ' . $exception->getMessage());
    }

}
