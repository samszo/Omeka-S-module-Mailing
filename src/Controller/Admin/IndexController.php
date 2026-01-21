<?php
namespace Mailing\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $listmonkService = $this->getServiceLocator()->get('Mailing\ListmonkService');
        
        // Test connection
        $connectionStatus = $listmonkService->testConnection();
        
        $view = new ViewModel([
            'connectionStatus' => $connectionStatus,
        ]);
        
        return $view;
    }

    public function subscribersAction()
    {
        $listmonkService = $this->getServiceLocator()->get('Mailing\ListmonkService');
        
        $page = $this->params()->fromQuery('page', 1);
        $perPage = $this->params()->fromQuery('per_page', 20);
        
        $result = $listmonkService->getSubscribers([
            'page' => $page,
            'per_page' => $perPage,
        ]);
        
        $subscribers = [];
        $total = 0;
        
        if ($result['success'] && isset($result['data']['data'])) {
            $subscribers = $result['data']['data']['results'];
            $total = $result['data']['data']['total'];
        }
        
        $view = new ViewModel([
            'subscribers' => $subscribers,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ]);
        
        return $view;
    }

    public function listsAction()
    {
        $listmonkService = $this->getServiceLocator()->get('Mailing\ListmonkService');
        
        $result = $listmonkService->getLists();
        
        $lists = [];
        if ($result['success'] && isset($result['data']['data'])) {
            $lists = $result['data']['data']['results'];
        }
        
        $view = new ViewModel([
            'lists' => $lists,
        ]);
        
        return $view;
    }

    public function campaignsAction()
    {
        $listmonkService = $this->getServiceLocator()->get('Mailing\ListmonkService');
        
        $page = $this->params()->fromQuery('page', 1);
        $perPage = $this->params()->fromQuery('per_page', 20);
        
        $result = $listmonkService->getCampaigns([
            'page' => $page,
            'per_page' => $perPage,
        ]);
        
        $campaigns = [];
        $total = 0;
        
        if ($result['success'] && isset($result['data']['data'])) {
            $campaigns = $result['data']['data']['results'];
            $total = $result['data']['data']['total'];
        }
        
        $view = new ViewModel([
            'campaigns' => $campaigns,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ]);
        
        return $view;
    }
}
