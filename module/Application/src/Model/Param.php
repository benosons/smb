<?php
namespace Application\Model;

use Khansia\Generic\Objects\Map;
use Khansia\Generic\Objects\Mapper;
use Khansia\Generic\Set;
use Khansia\Generic\Result as Result;

class Param extends Mapper {

	private $_storage;

    public function __construct(\Application\Model\Param\Storage\Skeleton $storage) {
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


    public function getParam($paramtype = null, $param_val3 = null, $param_parent = null){
        $data = $this->_storage->loadParam($paramtype, $param_val3, $param_parent);
        return $data;
    }

    public function loadGlobal($table, $from, $where = null){
        $data = $this->_storage->loadTable($table, $from, $where);
        return $data;
    }

    public function getDateNow(){
        $data = $this->_storage->loadDateNow();
        return $data->data;
    }


    public function checkDuplicateData($table, $column, $msg){
        $data = $this->_storage->checkDuplicateData($table, $column, $msg);
        return $data;
    }


    public function saveGlobal($param, $table, $seq = null){
        $data = $this->_storage->saveGlobal($param, $table, $seq);
        return $data;
    }

    public function updateGlobal($tabel, $data, $where){
        $data = $this->_storage->updateGlobal($tabel, $data, $where);
        return $data;
    }

    public function getLastSeqPostgree($tabel, $column){
        $data = $this->_storage->getLastSeqPostgree($tabel, $column);
        return $data;
    }

    public function deleteGlobal($tabel, $where){
        $data = $this->_storage->deleteGlobal($tabel, $where);
        return $data;
    }

    public function insertCoordinate($param, $type){
        $data = $this->_storage->saveCoordinate($param, $type);
        return $data;
    }

    public function getMaxParamVal($_long){
        $data = $this->_storage->loadMaxParamVal($_long);
        return $data;
    }

    public function getElementQue($param, $lvl){
        $data = $this->_storage->loadElementQue($param, $lvl);
        return $data;
    }

    public function getQueSort($param){
        $data = $this->_storage->loadQueSort($param);
        return $data;
    }

    public function loadSurveyProfilePartialAdmin($judul = null, $publish = null, $start = null, $limit = null, $count = null, $order = null, $sort = null){
        $data = $this->_storage->loadSurveyProfilePartialAdmin($judul, $publish, $start, $limit, $count, $order, $sort);
        return $data;
    }

    public function getschedule($stat = null, $tempjadwal = null){
        $data = $this->_storage->loadschedule($stat, $tempjadwal);
        return $data;
    }

    public function getMateriPartial($col = null, $orderCol = null, $orderDir = null, $start = null, $length = null, $draw = null, $search = null){
        $data = $this->_storage->loadMateriPartial($col, $orderCol, $orderDir, $start, $length, $draw, $search);
        return $data;
    }
    public function getValidasiMahasiswa($col = null, $orderCol = null, $orderDir = null, $start = null, $length = null, $draw = null, $search = null, $status = null, $schedule = null){
        $data = $this->_storage->loadValidasiMahasiswa($col, $orderCol, $orderDir, $start, $length, $draw, $search, $status, $schedule);
        return $data;
    }

    public function getDosen($col = null, $orderCol = null, $orderDir = null, $start = null, $length = null, $draw = null, $search = null, $temp= null){
        $data = $this->_storage->loadDosen($col, $orderCol, $orderDir, $start, $length, $draw, $search, $temp);
        return $data;
    }
    public function getTopic($col = null, $orderCol = null, $orderDir = null, $start = null, $length = null, $draw = null, $search = null, $temp=null){
        $data = $this->_storage->loadTopic($col, $orderCol, $orderDir, $start, $length, $draw, $search, $temp);
        return $data;
    }

    public function getMappingMateri($param){
        $data = $this->_storage->loadMappingMateri($param);
        return $data;
    }
    public function getmahasiswa($param){
        $data = $this->_storage->loadmahasiswa($param);
        return $data;
    }
    public function getprogramstudi($param=null){
        $data = $this->_storage->loadprogramstudi($param);
        return $data;
    }
    public function getdosenpotion($param=null, $select=null, $select1=null){
        $data = $this->_storage->loaddosenoption($param, $select, $select1);
        return $data;
    }
    public function getkelompokkeahlian($param=null){
        $data = $this->_storage->loadkelompokkeahlian($param);
        return $data;
    }
    public function geteditdosen($param=null, $iparam = null){
        $data = $this->_storage->loadeditdosen($param, $iparam);
        return $data;
    }
    public function saveDosen($dataarr=null){
        $data = $this->_storage->saveDosen($dataarr);
        return $data;
    }
    public function getmymahasiswa($param=null){
        $data = $this->_storage->loadmymahasiswa($param);
        return $data;
    }

    public function getregistmahasiswa($param=null){
        $data = $this->_storage->loadregistmahasiswa($param);
        return $data;
    }

    public function getdata($param=null, $param1 = null, $param2 = null, $param3 = null){

        $data = $this->_storage->getdata($param, $param1, $param2, $param3);
        return $data;
    }
    public function getdatapascasarjana($param=null, $param1=null, $param2=null){

        $data = $this->_storage->getdatapascasarjana($param, $param1, $param2);
        return $data;
    }

    public function getstat($param=null, $param1 = null, $param2 = null, $param3 = null){

        $data = $this->_storage->getstat($param, $param1, $param2, $param3);
        return $data;
    }

    public function getunggahan($param=null, $param1 = null, $param2 = null, $param3 = null){

        $data = $this->_storage->getunggahan($param, $param1, $param2, $param3);
        return $data;
    }

    public function getaktifitas($param=null, $param1 = null, $param2 = null, $param3 = null){

        $data = $this->_storage->getaktifitas($param, $param1, $param2, $param3);
        return $data;
    }

    public function getnodaftar($param=null, $param1 = null, $param2 = null, $param3 = null){

        $data = $this->_storage->getnodaftar($param, $param1, $param2, $param3);
        return $data;
    }

	public function loadActiveUser(){

        $data = $this->_storage->loadActiveUser();
        return $data;
    }

    public function loadMonthlyUser(){

        $data = $this->_storage->loadMonthlyUser();
        return $data;
    }

    public function loadWeeklyUser(){

        $data = $this->_storage->loadWeeklyUser();
        return $data;
    }


}
