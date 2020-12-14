<?php
namespace Khansia\Access\Session\SaveHandler;

class DbTableGateway extends \Laminas\Session\SaveHandler\DbTableGateway {

    protected $owner = null;

    public function setOwner($owner) {

        $this->owner = $owner;
    }

    public function getOwner() {

        return $this->owner;
    }

    /* override read, no lifetime check
    public function read($id, $destroyExpired = true) {
        $rows = $this->tableGateway->select(array(
            $this->options->getIdColumn()   => $id,
            $this->options->getNameColumn() => $this->sessionName,
        ));

        if ($row = $rows->current()) {
            // if ($row->{$this->options->getModifiedColumn()} +
            //    $row->{$this->options->getLifetimeColumn()} > time()) {
                return $row->{$this->options->getDataColumn()};
            // }
            // $this->destroy($id);
        }
        return '';
    }
     */

    public function read($id, $destroyExpired = true){
        $rows = $this->tableGateway->select(array(
            $this->options->getIdColumn()   => $id,
            $this->options->getNameColumn() => $this->sessionName,
        ));

        if ($row = $rows->current()) {
            // if ($row->{$this->options->getModifiedColumn()} +
            //    $row->{$this->options->getLifetimeColumn()} > time()) {
                return $row->{$this->options->getDataColumn()};
            // }
            // $this->destroy($id);
        }
        return '';
    }

    /* override write, to handle owner */
    public function write($id, $data) {

        $data = array(
            $this->options->getModifiedColumn() => time(),
            $this->options->getDataColumn()     => (string) $data,
            $this->options->getOwnerColumn()    => $this->owner,
        );

        $rows = $this->tableGateway->select(array(
            $this->options->getIdColumn()   => $id,
            $this->options->getNameColumn() => $this->sessionName,
        ));

        if ($row = $rows->current()) {
            return (bool) $this->tableGateway->update($data, array(
                $this->options->getIdColumn()   => $id,
                $this->options->getNameColumn() => $this->sessionName,
            ));
        }
        $data[$this->options->getLifetimeColumn()] = $this->lifetime;
        $data[$this->options->getIdColumn()]       = $id;
        $data[$this->options->getNameColumn()]     = $this->sessionName;

        return (bool) $this->tableGateway->insert($data);
    }

}
