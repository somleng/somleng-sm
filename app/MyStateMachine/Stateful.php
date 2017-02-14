<?php
/**
 * Created by PhpStorm.
 * User: phyrum
 * Date: 12/29/16
 * Time: 3:48 PM
 */

namespace App\MyStateMachine;


use Finite\StatefulInterface;
use Twilio\Rest\Client;
use Twilio\Twiml;


class Stateful implements StatefulInterface
{
    public $state;

    /*public function __construct()
    {
        $this->ngrok_address = "https://9fa794e3.ngrok.io";
    }*/

//    public $ngrok_url = "https://b2b96fe7.ngrok.io/";
    public function getFiniteState()
    {
        return $this->state;
    }

    public function setFiniteState($state)
    {
        $this->state = $state;
    }

    public function display()
    {

        //echo '<br> Play TwilML ', $this->state, ' state.', "\n";
        $sid = env('TWILIO_ACCOUNT_SID'); // Your Account SID from www.twilio.com/console
        $token = env('TWILIO_AUTH_TOKEN'); // Your Auth Token from www.twilio.com/console

        /*

            <Response>
                <Play>http://demo.twilio.com/hellomonkey/monkey.mp3</Play>
            </Response>
        */
        $client = new Client($sid, $token);
        // Read TwiML at this URL when a call connects (hold music)
        $call = $client->calls->create(
            '+85517696365', // Call this number
            env('TWILIO_NUMBER'), // From a valid Twilio number
            array(
                'url' => 'https://ee198af6.ngrok.io/welcomTwiMLCode'
            )
        );

        // To play sound file
        /*$response = new Twiml();
        $response->say('Hello');
        $response->play('https://api.twilio.com/cowbell.mp3', array("loop" => 5));
        //print $response;
        //dd($response);

        $response_1 = response($response,200);
        $response_1->header('Content-Type', 'text/xml');

         $client = new Client($sid, $token);

        // Read TwiML at this URL when a call connects (hold music)
        $call = $client->calls->create(
            '+85517696365', // Call this number
            env('TWILIO_NUMBER'), // From a valid Twilio number
            array(
                'url' => route('call.flow')
            )
        );*/


    }

}

