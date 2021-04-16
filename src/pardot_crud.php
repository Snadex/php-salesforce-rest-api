<?php

namespace bjsmasth\Salesforce;

use GuzzleHttp\Client;
use Exception;

class PARDOT_CRUD
{
    protected $instance_url;
    protected $access_token;
    protected $business_unit_id;
    protected $headers;
    protected $debug;

    public function __construct($business_unit_id, $instance_url = 'https://pi.pardot.com', $debug = false)
    {
        if (!isset($_SESSION) and !isset($_SESSION['salesforce'])) {
            throw new Exception('Access Denied', 403);
        }

        $this->instance_url = $instance_url;
        $this->access_token = $_SESSION['salesforce']['access_token'];
        $this->business_unit_id = $business_unit_id;
        $this->headers = [
            'Authorization' => "Bearer {$this->access_token}",
            'Pardot-Business-Unit-Id' => "{$this->business_unit_id}"
        ];
        $this->debug = $debug;
    }

    public function query($object, $query)
    {
        $url = "{$this->instance_url}/api/{$object}/version/4/do/query";

        $query['format'] = 'json';

        $client = new Client();
        $request = $client->request('GET', $url, [
            'debug' => $this->debug,
            'headers' => $this->headers,
            'query' => $query
        ]);

        return json_decode($request->getBody(), true);

    }

    public function get($object, $operation, $query)
    {
        $url = "{$this->instance_url}/api/{$object}/version/4/do/{$operation}";

        $query['format'] = 'json';

        $client = new Client();
        $request = $client->request('GET', $url, [
            'debug' => $this->debug,
            'headers' => $this->headers,
            'query' => $query
        ]);

        return json_decode($request->getBody(), true);

    }

    public function get_id_field($object, $operation, $id_field, $id, $query)
    {
        $url = "{$this->instance_url}/api/{$object}/version/4/do/{$operation}/{$id_field}/$id";

        $query['format'] = 'json';

        $client = new Client();
        $request = $client->request('GET', $url, [
            'debug' => $this->debug,
            'headers' => $this->headers,
            'query' => $query
        ]);

        return json_decode($request->getBody(), true);

    }

    public function post($object, $operation, $query)
    {
        $url = "{$this->instance_url}/api/{$object}/version/4/do/{$operation}";

        $query['format'] = 'json';

        $client = new Client();
        $request = $client->request('POST', $url, [
            'debug' => $this->debug,
            'headers' => $this->headers,
            'query' => $query
        ]);

        return json_decode($request->getBody(), true);

    }

    public function post_id_field($object, $operation, $id_field, $id, $query)
    {
        $url = "{$this->instance_url}/api/{$object}/version/4/do/{$operation}/{$id_field}/$id";

        $query['format'] = 'json';

        $client = new Client();
        $request = $client->request('POST', $url, [
            'debug' => $this->debug,
            'headers' => $this->headers,
            'query' => $query
        ]);

        return json_decode($request->getBody(), true);

    }

}
