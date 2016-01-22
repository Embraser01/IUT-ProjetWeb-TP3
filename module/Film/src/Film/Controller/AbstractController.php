<?php

namespace Film\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AbstractController extends AbstractActionController {

    protected $user_id;
    protected $authservice;


    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()
                ->get('AuthService');
        }

        return $this->authservice;
    }

    public function getUserId(){
        if(!$this->user_id) {
            $this->user_id = $this->getAuthService()->getStorage()->read()['id'];
        }
        return $this->user_id;
    }

    public function hasIdentity(){
        return $this->getAuthService()->hasIdentity();
    }

    public function setTitleName($name){
        $this->layout()->setVariable('header_title', $name);
    }
}
