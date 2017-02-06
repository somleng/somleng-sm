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
    private $state;

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

        echo '<br> Play TwilML ', $this->state, ' state.', "\n";
        $sid = "ACe888d537776f80870b2ae5d8bd37bf4c"; // Your Account SID from www.twilio.com/console
        $token = "d09aa9dd6a4440d6984c60cfe1e41881"; // Your Auth Token from www.twilio.com/console

        /*

            <Response>
                <Play>http://demo.twilio.com/hellomonkey/monkey.mp3</Play>
            </Response>
        */
        $client = new Client($sid, $token);
        // Read TwiML at this URL when a call connects (hold music)
        $call = $client->calls->create(
            '+85517696365', // Call this number
            '+12013800532', // From a valid Twilio number
            array(
                'url' => 'https://b2b96fe7.ngrok.io/welcomTwiMLCode'
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
            '+12013800532', // From a valid Twilio number
            array(
                'url' => route('call.flow')
            )
        );*/


    }

    public function showWelcome()
    {
        $sid = "ACe888d537776f80870b2ae5d8bd37bf4c"; // Your Account SID from www.twilio.com/console
        $token = "d09aa9dd6a4440d6984c60cfe1e41881"; // Your Auth Token from www.twilio.com/console
        $client = new Client($sid, $token);
        // Read TwiML at this URL when a call connects (hold music)
        $call = $client->calls->create(
            '+85517696365', // Call this number
            '+12013800532', // From a valid Twilio number
            array(
                'url' => 'https://b2b96fe7.ngrok.io/welcomTwiMLCode'
            )
        );

        $response = new Twiml();
        $gather = $response->gather(
            [
                'numDigits' => 1,
//                'action' => route('menu-response', [], false)
                'action' => 'https://b2b96fe7.ngrok.io/choose'
            ]
        );
    }
}

