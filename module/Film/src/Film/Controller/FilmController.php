<?php

namespace Film\Controller;

use Zend\View\Model\ViewModel;

use Film\Model\Film;
use Film\Form\FilmForm;

class FilmController extends AbstractController
{
    protected $FilmTable;

    public function indexAction()
    {
        $this->layout()->setVariable('header_title', 'Accueil');
        return new ViewModel(array(
            'Films' => $this->getFilmTable()->fetchAllByUser($this->getUserId()),
        ));
    }

    public function addAction()
    {
        $form = new FilmForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $Film = new Film();
            $form->setInputFilter($Film->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $Film->exchangeArray($form->getData());
                $this->getFilmTable()->saveFilm($Film, $this->getUserId());

                // Redirect to list of Films
                return $this->redirect()->toRoute('Film');
            }
        }
        $this->layout()->setVariable('header_title', 'Ajouter un Film');
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Film', array(
                'action' => 'add'
            ));
        }

        // Get the Film with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $Film = $this->getFilmTable()->getFilm($id, $this->getUserId());
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('Film', array(
                'action' => 'index'
            ));
        }

        $form  = new FilmForm();
        $form->bind($Film);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($Film->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getFilmTable()->saveFilm($Film, $this->getUserId());

                // Redirect to list of Films
                return $this->redirect()->toRoute('Film');
            }
        }
        $this->layout()->setVariable('header_title', 'Editer un Film');

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('Film');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getFilmTable()->deleteFilm($id,$this->getUserId() );
            }

            // Redirect to list of Films
            return $this->redirect()->toRoute('Film');
        }

        $this->layout()->setVariable('header_title', 'Supprimer un Film');

        return array(
            'id'    => $id,
            'Film' => $this->getFilmTable()->getFilm($id)
        );
    }


    public function getFilmTable()
    {
        if (!$this->FilmTable) {
            $sm = $this->getServiceLocator();
            $this->FilmTable = $sm->get('Film\Model\FilmTable');
        }
        return $this->FilmTable;
    }
}