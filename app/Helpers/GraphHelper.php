<?php
namespace App\Models\Helpers;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http;
use Microsoft\Graph\Model;
use App\Models\ACL\User;
use GuzzleHttp\Client;

class GraphHelper {

  private static  $tokenClient;
  private static  $clientId = '';
  private static  $tenantId = '';
  private static  $graphUserScopes = '';
  private static  $userClient;
  private static  $clientSecret;
  private static  $appToken;
  private static  $appClient;

  public static function initializeGraphForAppOnlyAuth(): void {
      GraphHelper::$tokenClient = new Client();
      GraphHelper::$clientId = env('MS_CLIENT_ID');
      GraphHelper::$tenantId = env('MS_TENANT_ID');
      GraphHelper::$clientSecret = env('MS_CLIENT_SECRET');
      GraphHelper::$appClient = new Graph();
  }


  public static function getAppOnlyToken(): string {
    // If we already have a token, just return it
    // Tokens are valid for one hour, after that a new token needs to be
    // requested
    if (isset(GraphHelper::$appToken)) {
        return GraphHelper::$appToken;
    }

    // https://learn.microsoft.com/azure/active-directory/develop/v2-oauth2-client-creds-grant-flow
    $tokenRequestUrl = 'https://login.microsoftonline.com/'.GraphHelper::$tenantId.'/oauth2/v2.0/token';

    // POST to the /token endpoint
    $tokenResponse = GraphHelper::$tokenClient->post($tokenRequestUrl, [
        'form_params' => [
            'client_id' => GraphHelper::$clientId,
            'client_secret' => GraphHelper::$clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'https://graph.microsoft.com/.default'
        ],
        // These options are needed to enable getting
        // the response body from a 4xx response
        'http_errors' => false,
        'curl' => [
            CURLOPT_FAILONERROR => false
        ]
    ]);

    $responseBody = json_decode($tokenResponse->getBody()->getContents());
    if ($tokenResponse->getStatusCode() == 200) {
        // Return the access token
        GraphHelper::$appToken = $responseBody->access_token;
        return $responseBody->access_token;
    } else {
        $error = isset($responseBody->error) ? $responseBody->error : $tokenResponse->getStatusCode();
        throw new Exception('Token endpoint returned '.$error, 100);
    }
  }
  public static function getUsers(): Http\GraphCollectionRequest {

    $token = GraphHelper::getAppOnlyToken();
    //dd( $token );
    GraphHelper::$appClient->setAccessToken($token);

    // Only request specific properties
    $select = '$select=displayName,id,mail';
    // Sort by display name
    $orderBy = '$orderBy=displayName';
    // Filter date

    $requestUrl = '/users?'.$select.'&'.$orderBy;
    
    return GraphHelper::$appClient->createCollectionRequest('GET', $requestUrl )
                                  ->setReturnType(Model\User::class)
                                  ->setPageSize(25);

  }

  //

  public static function getMessagesByUserId($uid = '12bee787-f814-424e-b13f-4adb98ba2c27'){

    $token = GraphHelper::getAppOnlyToken();

    GraphHelper::$appClient->setAccessToken($token);
    
    $filter = '$filter=receivedDateTime ge ' . date("Y-m-d") . 'T00:00:00Z';
    $requestUrl = '/users/'. $uid .'/messages?' .$filter;

    return GraphHelper::$appClient->createCollectionRequest( 'GET', $requestUrl )
                                  ->setReturnType(Model\Message::class );


  }

  function displayAccessToken(): void {
    try {
        $token = GraphHelper::getAppOnlyToken();
        print('App-only token: '.$token.PHP_EOL.PHP_EOL);
    } catch (Exception $e) {
        print('Error getting access token: '.$e->getMessage().PHP_EOL.PHP_EOL);
    }
  }




}
