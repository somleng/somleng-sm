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
        $this->ngrok_address = "http://a6af158c.ngrok.io";
    }

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function showWelcome()
    {
        $response = new Twiml();
        $gather = $response->gather(
            [
                'numDigits' => 5,
//                'action' => route('menu-response', [], false)
                'action' => $this->ngrok_address . "/ivr/inputvalidation"
            ]
        );
        $gather->say('Please Enter 5 digits of input');
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
        $response->say(
            'input is ' . $input
        );
        // validate the input
        $this->validation_sound_file($input);

//      $response->redirect(route('welcome', [], false));
        //$response->redirect($this->ngrok_address . "/ivr/welcome");
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

    public function test_validation()
    {
       return $this->validation_sound_file("0.mp3");
    }

    public function validation_sound_file($sound_file_name)
    {
        $url = "http://itenure.net/sounds/";
        //echo(file_get_contents($url));
        dd(file_get_contents($url));
//        $lines = file_get_contents('http://itenure.net/sounds/');
//        echo $lines;

       /* // echo phpinfo();
        $sound_file_url = "http://itenure.net/sounds/";
        //$file_exist = file_exists($sound_file_url.$sound_file_name);
        $file_exist = fopen($sound_file_url.$sound_file_name, 'r');
        dd($file_exist);
        //$fp = fopen($sound_file_url, 'r');
        //dd($fp);
//        $fc = opendir($sound_file_url);
//        dd($fc);
//        foreach(scandir($sound_file_url) as $file){
//            print '<a href="'. $sound_file_url.$file.'">'.$file.'</a><br>';
//        }
        //$meta_data = stream_get_meta_data($fp);
        //dd($meta_data);*/

        /*$url = 'http://itenure.net/sounds/';
        $base = 'http://itenure.net/';

        // Pull in the external HTML contents
        $contents = file_get_contents( $url );
        dd($contents);
        // Use Regular Expressions to match all <img src="???" />
        preg_match_all( '/<img[^>]*src=[\"|\'](.*)[\"|\']/Ui', $contents, $out, PREG_PATTERN_ORDER);

        foreach ( $out[1] as $k=>$v ){ // Step through all SRC's

            // Prepend the URL with the $base URL (if needed)
            if ( strpos( $v, 'http://' ) !== true ) $v = $base . $v;

            // Output a link to the URL
            echo '<a href="' . $v . '">' . $v . '</a><br/>';
        }*/



    }


}