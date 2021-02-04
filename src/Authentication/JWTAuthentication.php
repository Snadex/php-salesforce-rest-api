<?php

namespace bjsmasth\Salesforce\Authentication;

use bjsmasth\Salesforce\Exception\SalesforceAuthentication;

class JWTAuthentication implements AuthenticationInterface
{
    protected $client;
    protected $endPoint;
    protected $options;
    protected $access_token;
    protected $instance_url;

    public function __construct(array $options)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->endPoint = 'https://login.salesforce.com/';
        $this->options = $options;
    }

    public function authenticate()
    {

        //Json Header
        $header = $this->base64url_encode(json_encode(["alg" => "RS256", "typ" => "JWT"]));

        //Create JSon Claim/Payload
        $c = array(
            "iss" => $this->options['client_id'],
            "sub" => $this->options['username'],
            "aud" => $this->endPoint,
            "exp" => strval(time() + (5 * 60))
        );
        $payload = $this->base64url_encode(json_encode($c));

        $private_key = $this->options['pkey'];

        $s = "";
        $algo = "SHA256";
        // Sign the header and payload
        openssl_sign($header.'.'.$payload, $s, $private_key, $algo);

        // Base64 encode the result
        $secret = $this->base64url_encode($s);
        
        $token = $header . '.' . $payload . '.' . $secret;

        $token_url = $this->endPoint.'/services/oauth2/token';

        $post_fields = array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $token
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $token_request_body = curl_exec($ch);

        $response = json_decode($token_request_body, true);

        if ($response) {
            $this->access_token = $response['access_token'];
            $this->instance_url = $response['instance_url'];

            $_SESSION['salesforce'] = $response;
        } else {
            throw new SalesforceAuthentication($response);
        }
    }

    public function setEndpoint($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function getInstanceUrl()
    {
        return $this->instance_url;
    }

    public function base64url_encode( $string ){
        $find = array(
            '=',
            '+',
            '/'
        );

        $replace = array(
            '',
            '-',
            '_'
        );

        $string = base64_encode( $string );

        return( str_replace( $find, $replace, $string ) );
    }
}

?>
