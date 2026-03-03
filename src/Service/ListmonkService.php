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
    protected $propsMail;
    protected $propsData;
    protected $api;
    protected $logger;
    protected $acl;

    public function __construct($apiUrl, $username, $token, $propsMail, $propsData, $api, $logger, $acl)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->username = $username;
        $this->token = $token;
        $this->propsMail = $propsMail;
        $this->propsData = $propsData;
        $this->client = new Client();
        $this->api = $api;
        $this->logger = $logger;
        $this->acl = $acl;
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

    public function getSubscriberByMail($email)
    {
        $query = "query=subscribers.email='".$email."'";
        return $this->request(Request::METHOD_GET, '/subscribers?' . $query);
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
        return $this->request(Request::METHOD_PUT, '/subscribers/' . $id, $this->prepareData($data));
    }

    public function prepareData($data){
        //prepare data
        $lists = [];
        foreach ($data["lists"] as $l) {
            $lists[]=$l["id"];
        }
        $updData = [
            "email"=>$data["email"],
            "name"=>$data["name"],
            "status"=>$data["status"],
            "lists"=>$lists,
            "attribs"=>$data["attribs"]
        ];
        return $updData;
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

    public function mergeSubscripter($params)
    {

        $rs = $this->acl->userIsAllowed(null, 'create');
        if (!$rs) {
            throw new Exception\RuntimeException("You haven't permission to do that.");
        }

        $item = $params['item'];
        //$this->logger->info('mergeSubscripter of '.$item->id());

        //création des datas associé au subsriber
        $data = [];
        foreach ($this->propsData as $p){
            $values = $item->value($p, ['all' => true]);
            $nb = count($values);
            $vals=[];
            for ($i = 0; $i < $nb; $i++) {    
                $v = $values[$i];
                if($v->type()=="literal")
                    $vals[] = $v->__toString();     
                else
                    $vals[] = $v->valueResource()->displayTitle();

            }   
            if(count($vals))$data[$p] = implode($vals);
        }
        //ajoute l'identifiant omk
        $omkBase = explode("/",$item->adminUrl())[1];
        $data[$omkBase."Id"]=$item->id();
        //ajoute les sites
        $sites = $item->sites();
        foreach ($sites as $s) {
            $data[$omkBase."_".$s->slug()]=$item->siteUrl($s->slug(),true);
        }


        //vérifie la présence d'un mail
        $mail = "";
        foreach ($this->propsMail as $p){
            $mails = $item->value($p, ['all' => true]);
            $nb = count($mails);
            for ($i = 0; $i < $nb; $i++) {            
                $m = $mails[$i]->__toString();
                //récupère le subscriber
                $sub = $this->getSubscriberByMail($m);
                if($sub['success']){
                    //mise à jour des datas
                    //on garde les attribs défini dans la liste
                    $s = $sub['data']['data']['results'][0];
                    foreach($data as $dp=>$dv){
                        $s['attribs'][$dp]=$dv;
                    }
                    $r = $this->updateSubscriber($s['id'],$s);
                }else{
                    //création du subscriber
                    $r = $this->createSubscriber($m, $item->displayTitle(), [], $data);
                }
                $this->logger->info('mergeSubscripter result for '.$item->id(),$r);
            }
        }
    }

    public function testConnection()
    {
        $result = $this->getLists();
        return $result['success'];
    }
}
