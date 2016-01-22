<?php

namespace Film\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class FilmTable {
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchAllWith($user_id) {

        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->join('z2_FilmUser', 'z2_FilmUser.film_id = z2_Film.id', array(), 'left');


        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function fetchAllByUser($user_id) {

        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->join('z2_FilmUser', 'z2_FilmUser.film_id = z2_Film.id', array(), 'right');
        $sqlSelect->where(array('user_id' => $user_id));

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function saveFilm(Film $Film, $user_id) {
        $data = array(
            'name' => $Film->name,
            'kind' => $Film->kind,
            'release_year' => $Film->release_year . '-01'
        );

        $id = (int)$Film->id;
        $user_id = (int)$user_id;

        if ($id == 0 && $user_id != 0) {
            $this->tableGateway->insert($data);
            $this->saveUserData($this->tableGateway->lastInsertValue, $user_id);
        } else {
            if ($this->getFilm($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Film id does not exist for this user');
            }
        }
    }

    public function saveUserData($film_id, $user_id) {

        $user_id = (int)$user_id;
        $film_id = (int)$film_id;

        $data = array(
            'film_id' => $film_id,
            'user_id' => $user_id,
            'show_date' => date('Y-m-d'),
        );

        if (!is_null($this->getFilm($film_id))) {

            $sqlStr = "INSERT IGNORE INTO `z2_FilmUser` (user_id, film_id, show_date)"
                . " VALUES ("
                . $data['user_id'] . ','
                . $data['film_id'] . ',"'
                . $data['show_date'] . '")';

            $this->tableGateway->getAdapter()->query($sqlStr, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        }
    }

    public function getFilm($id) {
        $id = (int)$id;

        $rowset = $this->tableGateway->select(function (Select $select) use ($id) {
            $select->where(array('id' => $id));
        });
        $row = $rowset->current();

        return $row;
    }
}