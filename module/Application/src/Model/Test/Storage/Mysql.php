<?php

namespace Application\Model\Test\Storage;
use Application\Model\Test\Storage;
use Khansia\Generic\Result as Result;
use Laminas\Db\ResultSet\ResultSet;

class Mysql extends \Khansia\Db\Storage implements Skeleton {
    
    private $_result;
	
	const LOP 				= 'lop_cluster';
	const JOIN_LEFT			= 'left';
	 
    public function array_change_key_case_recursive($input, $case = CASE_LOWER){ 
        if(!is_array($input)){ 
            trigger_error("Invalid input array '{$array}'",E_USER_NOTICE); exit; 
        } 
        // CASE_UPPER|CASE_LOWER 
        if(null === $case){ 
            $case = CASE_LOWER; 
        } 
        if(!in_array($case, array(CASE_UPPER, CASE_LOWER))){ 
            trigger_error("Case parameter '{$case}' is invalid.", E_USER_NOTICE); exit; 
        } 
        $input = array_change_key_case($input, $case); 
        foreach($input as $key=>$array){ 
            if(is_array($array)){ 
                $input[$key] = $this->array_change_key_case_recursive($array, $case); 
            } 
        } 
        return $input; 
    } 
    
    public function __construct(\Laminas\Db\Adapter\Adapter $adapter, $config = array()) {

		parent::__construct($adapter, $config);
		 /* get conn instance */
      $this->_conn = $adapter->getDriver()->getConnection()->getResource();
   
	  
        //print_r($config);
        if (isset($config['tables'])) {
            $tables = $config['tables'];
            foreach ($tables as $key => $value) {
                if (array_key_exists($key, $this->_tables) && $value) {
                    $this->_tables[$key] = $this->_($value);
                }
            }
        }
    }
    
    public function fetchAll(\Laminas\Db\Sql\Select $select, $raw = true){

        $statement = $this->_sql->prepareStatementForSqlObject($select);
        if ($result = $statement->execute()) {
            $resultset = new \Laminas\Db\ResultSet\ResultSet();
            $data = $resultset->initialize($result)->toArray();
            return $data;
        }

        return false;
    }
    public function loadDataTest(){
		$result = new Result();
        try {
            $sql = " select * from user_data_header limit 10 ";
            
			$stmt = $this->_db->query($sql);
			$resdata = $stmt->execute();
			
			$listdata = array();
			while($resdata->next()){
				$res = $resdata->current();
				 array_push($listdata,$res);
			}
			//print_r($paramGroup);die;
            if ($listdata) {
                $result->code = 0;
                $result->info = 'OK';
                $result->data = $listdata;
            } else {
                $result->code = 1;
                $result->info = 'nok';
            }
            
        }catch (\Laminas\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;
    }


}
