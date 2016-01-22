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

    public function getFilmTable()
    {
        if (!$this->FilmTable) {
            $sm = $this->getServiceLocator();
            $this->FilmTable = $sm->get('Film\Model\FilmTable');
        }
        return $this->FilmTable;
    }
}