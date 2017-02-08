<?php
/**
 * Created by PhpStorm.
 * User: phyrum
 * Date: 1/25/17
 * Time: 10:56 AM
 */

namespace App\Http\Controllers;

use Finite\Exception\Exception;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Twiml;

class IVRCnt extends Controller
{
    public function __construct()
    {
        $this->ngrok_address = "https://9fa794e3.ngrok.io";
    }

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function showWelcome()
    {
        $response = new Twiml();
//        $gather = $response->gather(
//            [
//                'numDigits' => 3,
////                'action' => route('menu-response', [], false)
//                'action' => $this->ngrok_address . "/ivr/inputvalidation"
//            ]
//        );
//        $gather->say('Please Enter 3 digits of input');
        $response->say('Please Enter 3 digits of input');
//        $response->redirect($this->ngrok_address . "/ivr/welcome");
//        $gather->play(
//            'http://howtodocs.s3.amazonaws.com/et-phone.mp3',
//            ['loop' => 3]
//        );
        return $response;
    }
    public function gatherInput()
    {
        $response = new Twiml();
        $response->gather(
            [
                'numDigits' => 3,
//                'action' => route('menu-response', [], false)
                'action' => $this->ngrok_address . "/ivr/inputvalidation"
            ]
        );
//        $gather->say('Please Enter 3 digits of input');
//        $gather->play(
//            'http://howtodocs.s3.amazonaws.com/et-phone.mp3',
//            ['loop' => 3]
//        );
        return $response;
    }

    public function inputValidation(Request $request)
    {
        $input = $request->input('Digits');
        $response = new Twiml();
        // validate the input
        $validation_result = $this->validation_sound_file($input.".mp3");

//        if($validation_result == '0')
//        {
//            $response->say('input is incorrect');
//            $response->redirect($this->ngrok_address . "/ivr/welcome");
//        }
//        else  {
//            $response->play('http://itenure.net/sounds/' . $validation_result);
//        }
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
            array("url" => $this->ngrok_address . "/ivr/welcome")
        );
        echo $call->sid;
    }

//    public function test_validation()
//    {
//       return $this->validation_sound_file("0.mp3");
//    }

    public function validation_sound_file($sound_file_name)
    {
        $url = "http://itenure.net/sounds/";
        $header_response = get_headers($url.$sound_file_name, 1);
        if(strpos($header_response[0], "404")!==false )
        {
            // FILE DOES NOT EXIST
          return 0;
        }
        else
        {
            // FILE EXISTS
            return $sound_file_name;
        }
    }


}