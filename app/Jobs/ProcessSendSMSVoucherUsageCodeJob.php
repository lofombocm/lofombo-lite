<?php

namespace App\Jobs;

use App\Mail\MailForVoucherGenerated;
use App\Mail\MailForVoucherSendUsageCode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Psr\Http\Message\ResponseInterface;

class ProcessSendSMSVoucherUsageCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 20;
    public $tries = 5;

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
        /// TODO Send SMS
        $client = new Client([
            'base_uri' => '',
            'timeout'  => 60.0,
        ]);
        $headers = [
            'Authorization' => 'App fcf4cec9739d5ef829adebe68ab92124-7009db67-fe42-4d94-bbee-e480a78aa0ea',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        $bodyObject = [
            'messages'=>[
                [
                    'destinations'=>[
                        [
                            'to' => $this->data['to'],
                        ]
                    ],
                    'from' => '447491163443',
                    'text' => $this->data['message'],
                ]
            ]
        ];

        $body = json_encode($bodyObject);
        $request = new Request('POST', 'https://api.infobip.com/sms/2/text/advanced', $headers, $body);
        try {
            $response = $client->send($request);
            $responseBody = $response->getBody();
            $file=fopen('sms.txt','w');
            fwrite($file, json_encode($this->data));
            fwrite($file, $responseBody->getContents());
            fclose($file);
            //dd($responseBody);
        }catch (RequestException $e) {
            $file=fopen('sms-error.txt','w');
            fwrite($file, $e->getMessage());
            fclose($file);
        }

        //dd("NKALLA", $body);

       /* $h = fopen('sms.txt', 'w');
        fwrite($h, json_encode($this->data));
        fwrite($h, $responseBody);
        fclose($h);*/
        /*$promise->then(
            function (ResponseInterface $res) {
                $result= 'HTTP CODE: '.$res->getStatusCode() . '  HTTP message: '.$res->getBody()->getContents();
                Log::info($result);

                $h = fopen('test.txt', 'w');
                fwrite($h, json_encode($this->data));
                fwrite($h, $result);
                fclose($h);
                //echo $res->getStatusCode() ;
            },
            function (RequestException $e) {
                Log::error($e->getMessage() . ' Cause: ' . $e->getResponse()->getBody()->getContents());
                $h = fopen('test.txt', 'w');
                fwrite($h, json_encode($this->data));
                fwrite($h, $e->getMessage() . ' Cause: ' . $e->getResponse()->getBody()->getContents());
                fclose($h);
                //echo $e->getMessage() . "\n";
                //echo $e->getRequest()->getMethod();
            }
        );*/
        /*$h = fopen('test.txt', 'w+');
        fwrite($h, json_encode($this->data));
        fclose($h);*/

        //Mail::to($this->data['email'])->send(new MailForVoucherSendUsageCode($this->data));
    }
}
