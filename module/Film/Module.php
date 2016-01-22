<?php

namespace Film;

use Film\Model\Film;
use Film\Model\FilmTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as DbTableAuthAdapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {
    protected $whitelist = array("auth/login", "auth/login/process", "auth/signup", "auth/signup/process");
    private $_salt = "42jeej42";

    public function onBootstrap(MvcEvent $e) {
        $list = $this->whitelist;

        $eventManager = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();


        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $auth = $serviceManager->get('AuthService');

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function ($e) use ($auth, $list) {
            if (!$auth->hasIdentity()) {
                $name = $e->getRouteMatch()->getMatchedRouteName();
                if (in_array($name, $list)) {
                    return null;
                } else {
                    $routeMatch = new RouteMatch(
                        array(
                            'controller' => 'Film\Controller\Auth',
                            'action' => 'login'
                        )
                    );
                    $e->setRouteMatch($routeMatch);
                }
            }
        }, -1000000);
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }


    public function getServiceConfig() {

        return array(
            'factories' => array(
                'Film\Model\FilmTable' => function ($sm) {
                    $tableGateway = $sm->get('FilmTableGateway');
                    $table = new FilmTable($tableGateway);
                    return $table;
                },
                'FilmTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Film());
                    return new TableGateway('z2_Film', $dbAdapter, null, $resultSetPrototype);
                },
                'Film\Model\MyAuthStorage' => function ($sm) {
                    return new \Film\Model\MyAuthStorage('Film');
                },

                'AuthService' => function ($sm) {
                    //My assumption, you've alredy set dbAdapter
                    //and has users table with columns : user_name and pass_word
                    //that password hashed with md5
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter,
                        'z2_User', 'username', 'password', "sha2(CONCAT(sha2(?), '$this->_salt'))");

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Film\Model\MyAuthStorage'));

                    return $authService;
                },
            ),
        );
    }
}