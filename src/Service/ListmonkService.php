<?php
namespace Mailing\Service;

use Laminas\Http\Client;
use Laminas\Http\Request;

class ListmonkService
{
    protected $apiUrl;
    protected $username;
    protected $token;
    protected $client;

    public function __construct($apiUrl, $username, $token)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->username = $username;
        $this->token = $token;
        $this->client = new Client();
    }

    protected function request($method, $endpoint, $data = null)
    {
        $url = $this->apiUrl . '/api' . $endpoint;
        
        $this->client->setUri($url);
        $this->client->setMethod($method);
        $this->client->setAuth($this->username, $this->token);
        $this->client->setHeaders([
            'Content-Type' => 'application/json',
        ]);

        if ($data !== null) {
            $this->client->setRawBody(json_encode($data));
        }

        try {
            $response = $this->client->send();
            return [
                'success' => $response->isSuccess(),
                'data' => json_decode($response->getBody(), true),
                'status' => $response->getStatusCode(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 0,
            ];
        }
    }

    public function getLists()
    {
        return $this->request(Request::METHOD_GET, '/lists');
    }

    public function getList($id)
    {
        return $this->request(Request::METHOD_GET, '/lists/' . $id);
    }

    public function getSubscribers($params = [])
    {
        $query = http_build_query($params);
        $endpoint = '/subscribers' . ($query ? '?' . $query : '');
        return $this->request(Request::METHOD_GET, $endpoint);
    }

    public function getSubscriber($id)
    {
        return $this->request(Request::METHOD_GET, '/subscribers/' . $id);
    }

    public function createSubscriber($email, $name, $listIds = [], $attributes = [])
    {
        $data = [
            'email' => $email,
            'name' => $name,
            'status' => 'enabled',
            'lists' => $listIds,
            'attribs' => $attributes,
        ];
        return $this->request(Request::METHOD_POST, '/subscribers', $data);
    }

    public function updateSubscriber($id, $data)
    {
        return $this->request(Request::METHOD_PUT, '/subscribers/' . $id, $data);
    }

    public function deleteSubscriber($id)
    {
        return $this->request(Request::METHOD_DELETE, '/subscribers/' . $id);
    }

    public function getCampaigns($params = [])
    {
        $query = http_build_query($params);
        $endpoint = '/campaigns' . ($query ? '?' . $query : '');
        return $this->request(Request::METHOD_GET, $endpoint);
    }

    public function getCampaign($id)
    {
        return $this->request(Request::METHOD_GET, '/campaigns/' . $id);
    }

    public function createCampaign($data)
    {
        return $this->request(Request::METHOD_POST, '/campaigns', $data);
    }

    public function updateCampaign($id, $data)
    {
        return $this->request(Request::METHOD_PUT, '/campaigns/' . $id, $data);
    }

    public function deleteCampaign($id)
    {
        return $this->request(Request::METHOD_DELETE, '/campaigns/' . $id);
    }

    public function startCampaign($id)
    {
        return $this->request(Request::METHOD_PUT, '/campaigns/' . $id . '/status', ['status' => 'running']);
    }

    public function testConnection()
    {
        $result = $this->getLists();
        return $result['success'];
    }
}
