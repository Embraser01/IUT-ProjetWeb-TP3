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

    public function fetchAllByUser($user_id) {

        $resultSet = $this->tableGateway->select(function (Select $select) use ($user_id) {
            $select->where(array('user_id' => $user_id));
        });

        return $resultSet;
    }

    public function getFilm($id, $user_id) {
        $id = (int)$id;
        $user_id = (int)$user_id;

        $rowset = $this->tableGateway->select(function (Select $select) use ($user_id, $id) {
            $select->where(array('user_id' => $user_id, 'id' => $id));
        });


        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveFilm(Film $Film, $user_id) {
        $data = array(
            'artist' => $Film->artist,
            'title' => $Film->title,
            'user_id' => $user_id,
        );

        $id = (int)$Film->id;
        $user_id = (int) $user_id;

        if ($id == 0 && $user_id != 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getFilm($id, $user_id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Film id does not exist for this user');
            }
        }
    }

    public function deleteFilm($id, $user_id) {
        $this->tableGateway->delete(array('id' => (int)$id, 'user_id' => (int) $user_id));
    }
}