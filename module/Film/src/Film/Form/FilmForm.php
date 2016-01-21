<?php

namespace Film\Form;

use Zend\Form\Form;

class FilmForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('Film');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
        ));
        $this->add(array(
            'name' => 'artist',
            'type' => 'Text',
        ));
    }
}