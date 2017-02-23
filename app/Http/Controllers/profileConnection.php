<?php

namespace App\Http\Controllers;

use Aws\Sqs\SqsClient;
use Finite\Exception\Exception;
use Illuminate\Http\Request;

use App\Http\Requests;

use Aws\Command\Aws;

class profileConnection extends Controller
{
    public function connectToSqs()
    {
        try{
            $sqs_credentials = array(
                'region' => env(AWS_QUEUE_REGION),
                'version' => 'latest',
                'credentials' => array(
                    'key' => env(AWS_QUEUE_ACCESS_KEY),
                    'secret' => env(AWS_QUEUE_SECRET_KEY),
                )
            );
            // Instantiate the client
            $sqs_client = new SqsClient($sqs_credentials);

            // Create the queue
            $queue_options = array(
                'QueueName' => 'our_queue'
            );
            $sqs_client->createQueue($queue_options);
        }
        catch (Exception $e){
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
}
