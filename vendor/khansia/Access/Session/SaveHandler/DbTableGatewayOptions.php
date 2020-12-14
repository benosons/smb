<?php
namespace Khansia\Access\Session\SaveHandler;

use Laminas\Session\Exception;

class DbTableGatewayOptions extends \Laminas\Session\SaveHandler\DbTableGatewayOptions {

    protected $ownerColumn = 'owner';

    public function setOwnerColumn($ownerColumn) {

        $ownerColumn = (string) $ownerColumn;
        if (strlen($ownerColumn) === 0) {
            throw new Exception\InvalidArgumentException('$ownerColumn must be a non-empty string');
        }
        $this->ownerColumn = $ownerColumn;
        return $this;
    }

    public function getOwnerColumn() {

        return $this->ownerColumn;
    }
}