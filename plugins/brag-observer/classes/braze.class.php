<?php

/**
 * Handle Braze requests
 */
class Braze
{
    protected $sdk_api_key;
    protected $sdk_endpoint;
    protected $api_key;
    protected $api_url;
    protected static $config;

    public function __construct()
    {

        self::$config = include __DIR__ . '/config.php';
        $this->sdk_api_key = self::$config['braze']['sdk_api_key'];
        $this->sdk_endpoint = self::$config['braze']['sdk_endpoint'];
        $this->api_key = self::$config['braze']['api_key'];
        $this->api_url = self::$config['braze']['api_url'];
    }

    public function setMethod($method = 'GET')
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setUrl($api_url)
    {
        $this->api_url = $api_url;
    }

    public function getUrl()
    {
        if (isset($this->api_url)) {
            return $this->api_url;
        }
        return FALSE;
    }

    public function setPayload($payload = NULL)
    {
        $this->payload = $payload;
    }

    public function getPayload()
    {
        if (isset($this->payload)) {
            return $this->payload;
        }
        return FALSE;
    }

    public function setParams($params = array())
    {
        $this->params = $params;
    }

    public function getParams()
    {
        if (isset($this->params)) {
            return $this->params;
        }
        return false;
    }

    public function buildQuery($params)
    {
        if (!empty($params)) {
            return \http_build_query($params);
        }
        return FALSE;
    }

    public function request($urlSuffix = '', $bulk = false)
    {
        $url = $this->getUrl();
        $url .= $urlSuffix;
        $payload = $this->getPayload();
        $method = $this->getMethod();
        $params = $this->getParams();
        $query = $this->buildQuery($params);
        if (!empty($query)) {
            $url = $url . '?' . $query;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout after 10 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if (strtoupper($method) == 'POST') {
            $data = json_encode($payload);
            $headers  = [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                'Authorization: Bearer ' . $this->api_key
            ];
            if ($bulk) {
                $header[] = 'X-Braze-Bulk: true';
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close($ch);

        return array(
            'response' => $result,
            'code' => $status_code,
        );
    }
}
