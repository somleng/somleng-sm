<?php
/**
 * Created by PhpStorm.
 * User: phyrum
 * Date: 1/25/17
 * Time: 10:56 AM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Twiml;

class IVRCnt extends Controller
{
    public function __construct()
    {
        $this->ngrok_address = "http://2ce4abee.ngrok.io";
    }
    public function showWelcome()
    {
        $response = new Twiml();
        $gather = $response->gather(
            [
                'numDigits' => 1,
//                'action' => route('menu-response', [], false)
                'action' => $this->ngrok_address . "/ivr/menu-response"
            ]
        );

        $gather->play(
            'http://howtodocs.s3.amazonaws.com/et-phone.mp3',
            ['loop' => 3]
        );

        return $response;
    }

    /**
     * Responds to selection of an option by the caller
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showMenuResponse(Request $request)
    {
        $selectedOption = $request->input('Digits');

        $response = new Twiml();
        $response->say(
            'input is ' . $selectedOption
        );
//        $response->redirect(route('welcome', [], false));
        $response->redirect($this->ngrok_address . "/ivr/welcome");
        return $response;
    }

    public function makeCall()
    {
        // array("url" => "http://demo.twilio.com/docs/voice.xml")
        $test_phone_number = "+85589555127";
        $twilio_sid = env('TWILIO_ACCOUNT_SID');
        $twilio_token = env('TWILIO_AUTH_TOKEN');
        $twilio_phone_number = env('TWILIO_NUMBER');

        $client = new Client($twilio_sid, $twilio_token);
        $call = $client->calls->create(
            $test_phone_number,
            $twilio_phone_number,
            // array("url" => route('welcome'))
            array("url" => $this->ngrok_address . "/ivr/welcome")
        );
        echo $call->sid;

    }


}