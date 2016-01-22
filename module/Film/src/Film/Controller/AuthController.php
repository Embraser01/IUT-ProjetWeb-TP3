<?php
namespace Film\Controller;

use Zend\Form\Annotation\AnnotationBuilder;

use Film\Model\User;

class AuthController extends AbstractController {
    protected $form;
    protected $storage;

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()
                ->get('Film\Model\MyAuthStorage');
        }

        return $this->storage;
    }

    public function getForm() {
        if (!$this->form) {
            $user = new User();
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($user);
        }

        return $this->form;
    }

    public function loginAction() {
        //if already login, redirect to success page
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('success');
        }

        $form = $this->getForm();

        $this->setTitleName("Se connecter");
        return array(
            'form' => $form,
            'messages' => $this->flashmessenger()->getMessages()
        );
    }


    public function processAction() {
        $form = $this->getForm();
        $redirect = 'auth';

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($request->getPost());
            if ($form->isValid()) {
                //check authentication...
                $this->getAuthService()->getAdapter()
                    ->setIdentity($request->getPost('username'))
                    ->setCredential($request->getPost('password'));

                $result = $this->getAuthService()->authenticate();
                foreach ($result->getMessages() as $message) {
                    //save message temporary into flashmessenger
                    $this->flashmessenger()->addMessage($message);
                }

                if ($result->isValid()) {
                    $redirect = 'success';
                    //check if it has rememberMe :
                    if ($request->getPost('rememberme') == 1) {
                        $this->getSessionStorage()
                            ->setRememberMe(1);
                        //set storage again
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->getStorage()->write(array(
                        'id' => $this->getAuthService()->getAdapter()->getResultRowObject()->id,
                        'username' => $request->getPost('username')));
                }
            }
        }

        return $this->redirect()->toRoute($redirect);
    }

    /* Il n'y a pas d'inscription car aucun moyen de savoir comme utiliser le AuthService pour ajouter un utilisateur

    public function signupAction() {
        //if already login, redirect to success page
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('success');
        }

        $form = $this->getForm();

        return array(
            'form' => $form,
            'messages' => $this->flashmessenger()->getMessages()
        );
    }


    public function processSignupAction() {
        $form = $this->getForm();
        $redirect = 'signup';

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                //check authentication...
                $this->getAuthService()->getAdapter()
                    ->setIdentity($request->getPost('username'))
                    ->setCredential($request->getPost('password'));

                $result = $this->getAuthService()->authenticate();
                foreach ($result->getMessages() as $message) {
                    //save message temporary into flashmessenger
                    $this->flashmessenger()->addMessage($message);
                }

                if ($result->isValid()) {
                    $redirect = 'success';
                    //check if it has rememberMe :
                    if ($request->getPost('rememberme') == 1) {
                        $this->getSessionStorage()
                            ->setRememberMe(1);
                        //set storage again
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->getStorage()->write(array(
                        'id' => $this->getAuthService()->getAdapter()->getResultRowObject()->id,
                        'username' => $request->getPost('username')));
                }
            }
        }

        return $this->redirect()->toRoute($redirect);
    }*/

    public function logoutAction() {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('login');
    }
}