<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PipraPay
{
    protected $api_key;
    protected $base_url;
    protected $currency;

    public function __construct($params = [])
    {
        $this->api_key = $params['api_key'] ?? '';
        $this->base_url = rtrim($params['base_url'] ?? '', '/');
        $this->currency = $params['currency'] ?? 'BDT';
    }

    public function setCredentials($api_key, $base_url, $currency = 'BDT')
    {
        $this->api_key = $api_key;
        $this->base_url = rtrim($base_url, '/');
        $this->currency = $currency;
    }

    public function createCharge($data = [])
    {
        $postData = [
            'full_name' => $data['full_name'] ?? '',
            'email_mobile' => $data['email_mobile'] ?? '',
            'amount' => $data['amount'] ?? '',
            'metadata' => $data['metadata'] ?? [],
            'redirect_url' => $data['redirect_url'] ?? '',
            'return_type' => $data['return_type'] ?? 'GET',
            'cancel_url' => $data['cancel_url'] ?? '',
            'webhook_url' => $data['webhook_url'] ?? '',
            'currency' => $this->currency
        ];

        return $this->post('/api/create-charge', $postData);
    }

    public function verifyPayment($pp_id)
    {
        return $this->post('/api/verify-payments', ['pp_id' => $pp_id]);
    }

    public function handleWebhook($expected_api_key)
    {
        $headers = getallheaders();
        $received_api_key = '';

        if (isset($headers['mh-piprapay-api-key'])) {
            $received_api_key = $headers['mh-piprapay-api-key'];
        } elseif (isset($headers['Mh-Piprapay-Api-Key'])) {
            $received_api_key = $headers['Mh-Piprapay-Api-Key'];
        } elseif (isset($_SERVER['HTTP_MH_PIPRAPAY_API_KEY'])) {
            $received_api_key = $_SERVER['HTTP_MH_PIPRAPAY_API_KEY'];
        }

        if ($received_api_key !== $expected_api_key) {
            return ['status' => false, 'message' => 'Unauthorized'];
        }

        $data = json_decode(file_get_contents('php://input'), true);
        return ['status' => true, 'data' => $data];
    }

    private function post($endpoint, $data)
    {
        $url = $this->base_url . $endpoint;
        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'mh-piprapay-api-key: ' . $this->api_key
        ];

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['status' => false, 'error' => $error];
        }

        return json_decode($response, true);
    }
}
