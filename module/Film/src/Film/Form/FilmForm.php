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
            'name' => 'name',
            'type' => 'Text',
        ));

        $this->add(array(
            'name' => 'kind',
            'type' => 'Text',
        ));

        $this->add(array(
            'name' => 'release_year',
            'type' => 'Zend\Form\Element\MonthSelect',
            'options' => array(
                'label' => 'Choisissez la date de sortie',
                'min_year' => 1907,
                'max_year' => 2019
            )
        ));
    }
}