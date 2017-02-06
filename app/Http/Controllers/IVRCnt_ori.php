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
        $this->_thankYouMessage = 'Thank you for calling the ET Phone Home' .
            ' Service - the adventurous alien\'s first choice' .
            ' in intergalactic travel.';
    }

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function showWelcome()
    {
//        $response = new Twiml();
//        $response->say("this is twilio");
//        return $response;

        $response = new Twiml();
        $gather = $response->gather(
            [
                'numDigits' => 1,
//                'action' => route('menu-response', [], false)
                'action' => "http://8319131f.ngrok.io/ivr/menu-response"
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

        switch ($selectedOption) {
            case 1:
                return $this->_getReturnInstructions();
            case 2:
                return $this->_getPlanetsMenu();
        }

        $response = new Twiml();
        $response->say(
            'Returning to the main menu',
            ['voice' => 'Alice', 'language' => 'en-GB']
        );
//        $response->redirect(route('welcome', [], false));
        $response->redirect("http://8319131f.ngrok.io/ivr/welcome");

        return $response;
    }

    /**
     * Responds with a <Dial> to the caller's planet
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showPlanetConnection(Request $request)
    {
        $response = new Twiml();
        $response->say(
            $this->_thankYouMessage,
            ['voice' => 'Alice', 'language' => 'en-GB']
        );
        $response->say(
            "You'll be connected shortly to your planet",
            ['voice' => 'Alice', 'language' => 'en-GB']
        );

//        $planetNumbers = [
//            '2' => '+12024173378',
//            '3' => '+12027336386',
//            '4' => '+12027336637'
//        ];
        $planetNumbers = [
            '2' => '+85589555127',
            '3' => '+85589555127',
            '4' => '+85589555127'
        ];
        $selectedOption = $request->input('Digits');

        $planetNumberExists = isset($planetNumbers[$selectedOption]);

        if ($planetNumberExists) {
            $selectedNumber = $planetNumbers[$selectedOption];
            $response->dial($selectedNumber);

            return $response;
        } else {
            $errorResponse = new Twiml();
            $errorResponse->say(
                'Returning to the main menu',
                ['voice' => 'Alice', 'language' => 'en-GB']
            );
//            $errorResponse->redirect(route('welcome', [], false));
            $errorResponse->redirect("http://8319131f.ngrok.io/ivr/welcome");
            return $errorResponse;
        }
    }

    /**
     * Responds with instructions to mothership
     * @return Services_Twilio_Twiml
     */
    private function _getReturnInstructions()
    {
        $response = new Twiml();
        $response->say(
            'To get to your extraction point, get on your bike and go down the' .
            ' street. Then Left down an alley. Avoid the police cars. Turn left' .
            ' into an unfinished housing development. Fly over the roadblock. Go' .
            ' passed the moon. Soon after you will see your mother ship.',
            ['voice' => 'Alice', 'language' => 'en-GB']
        );
        $response->say(
            $this->_thankYouMessage,
            ['voice' => 'Alice', 'language' => 'en-GB']
        );

        $response->hangup();

        return $response;
    }

    /**
     * Responds with instructions to choose a planet
     * @return Services_Twilio_Twiml
     */
    private function _getPlanetsMenu()
    {
        $response = new Twiml();
//        $gather = $response->gather(
//            [
//                'numDigits' => '1',
//                'action' => route('planet-connection', [], false)
//            ]
//        );
        $gather = $response->gather(
            [
                'numDigits' => '1',
                'action' => "http://8319131f.ngrok.io/ivr/planet-connection"
            ]
        );
        $gather->say(
            'To call the planet Brodo Asogi, press 2. To call the planet' .
            ' Dugobah, press 3. To call an Oober asteroid to your location,' .
            ' press 4. To go back to the main menu, press the star key',
            ['voice' => 'Alice', 'language' => 'en-GB']
        );

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
            // array("url"=>"http://demo.twilio.com/docs/voice.xml")
            // array("url" => route('welcome'))
            array("url" => "http://8319131f.ngrok.io/ivr/welcome")
        );
        echo $call->sid;

//
//        $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
//
//        $call = $client->calls->create(
//            $my_phone_number, // Call this number
//            env('TWILIO_NUMBER'), // From a valid Twilio number
//            array("url" => "http://demo.twilio.com/docs/voice.xml")
//        //array("url" => route('welcome'))
//        );
//        echo $call->sid;
    }


}