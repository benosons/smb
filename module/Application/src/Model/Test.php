<?php
namespace Application\Model;

use Khansia\Generic\Objects\Map;
use Khansia\Generic\Objects\Mapper;
use Khansia\Generic\Set;
use Khansia\Generic\Result as Result;

class Test extends Mapper {
	
	private $_storage;

    public function __construct(\Application\Model\Test\Storage\Skeleton $storage) {
		$this->_storage = $storage;
        $this->_result = new Result();

		parent::__construct(
                array(),
                array(
					// this mapper table on db
                ),
				parent::CASE_INSENSITIVE
			);
	}	
	
	
    public function getDataTest(){
        $data = $this->_storage->loadDataTest();
        return $data;
    }

}
