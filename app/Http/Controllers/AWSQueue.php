<?php
/**
 * Created by PhpStorm.
 * User: phyrum
 * Date: 2/17/17
 * Time: 3:20 PM
 */

namespace App\Http\Controllers;


use Aws\Sqs\SqsClient;

class AWSQueue extends Controller
{
    public function testSqs()
    {
        $sqs_client = new SqsClient ([
//            'profile' => '',
            'region' => env('SQS_REGION'),
            'version' => 'latest'
        ]);
        dd($sqs_client);
    }
}