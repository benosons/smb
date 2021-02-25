<?php

namespace Application\Model\Param\Storage;
use Application\Model\Param\Storage;
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

    public function deleteGlobal($tabel, $where){

        $result = new Result();

        try {

            $stmt   = $this->_db->query("delete from $tabel where $where ");

            $stmt->execute();

            $getData = $this->loadTable('1', $tabel, $where);

            if($getData->code == $result::CODE_SUCCESS){
                /**
                 * jika data ditemukan
                 * @return code failed
                 * rollback data
                 */
                // $transaction->rollback();
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }else{
                /**
                 * jika data tidak diteumkan maka
                 * @return code error
                 * commit data
                 */
                // $transaction->commit();
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }

        return $result;

    }

    public function loadTable($column, $tabel,  $where = null){

        $result = new Result();

        try {

            $sql      = " SELECT $column FROM $tabel ";

            if($where){
                $sql .= " WHERE $where ";
            }

			$stmt     = $this->_db->query($sql);
			$getData  = $stmt->execute();

			$listdata = array();

            foreach($getData as $val){

                array_push($listdata, $val);

            }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;
    }

    /* cek duplikasi data */
    public function loadDateNow(){

        $result = new Result();

        try {

            // $stmt      = $this->_db->query(" SELECT TO_CHAR(NOW(), 'YYYY-MM-DD HH12:MI:SS') AS DATES ");
            $stmt      = $this->_db->query(" SELECT CURRENT_TIMESTAMP AS DATES ");

            $proced    = $stmt->execute();

            $res       = $proced->current();

            if ($res) {

                // $this->date = date('Y-m-d H:i:s', strtotime($res['dates']));

                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $res['DATES'];


            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
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


    public function saveGlobal($atribut, $table, $posgre = null){

        $result     = new Result();

        try {

            $insert     = $this->_sql->insert()
                            ->into($table)
                            ->values($atribut);
                            // echo str_replace('"','',$insert->getSqlString());die;

            $res        = $this->execute($insert);
            // if($res){

                // $sequence  = $table.'_seq';
                //
                // $stmt      = $this->_db->query(" SELECT Currval('$sequence') LIMIT 1 ");
                //
                // $proced    = $stmt->execute();
                //
                // $res       = $proced->current();

                if($res){
                    $result->code = $result::CODE_SUCCESS;
                    $result->info = $result::INFO_SUCCESS;
                    $result->data = $res;
                }else{
                    $result->code = $result::CODE_FAILED;
                    $result->info = $result::INFO_FAILED;
                }

                return $result;

            // }else{
            //     if($res->getGeneratedValue()){
            //         $result->code = $result::CODE_SUCCESS;
            //         $result->info = $result::INFO_SUCCESS;
            //         $result->data = $res->getGeneratedValue();
            //     }else{
            //         $result->code = $result::CODE_FAILED;
            //         $result->info = $result::INFO_FAILED;
            //     }
            // }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }

        return $result;

    }

    public function updateGlobal($tabel, $data, $where){

        $result = new Result();

        try {

            $update = $this->_sql->update()
                        ->table($tabel)
                        ->set($data)
                        ->where($where);

            // echo str_replace('"','',$update->getSqlString());die;
            // print_r($update);die;
            $return = $this->execute($update);

            if($return->getAffectedRows()){
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
            }else{
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }

        return $result;
    }

    public function getLastSeqPostgree($tabel, $column){

        $result     = new Result();

        try {

            $sql        = " select max($column) as total from $tabel ";

            $stmt       = $this->_db->query($sql);

            $proced     = $stmt->execute();

            $seq        = $proced->current();
            // print_r($seq);die;
            if ($seq) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $seq['total'];
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = 'Seq error';
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }

        return $result;

    }

    /* cek duplikasi data */
    public function checkDuplicateData($table, $column, $msg){

        $result = new Result();

        try {
            $select = $this->select()
                    ->from($table)
                    ->where($column);
            //echo str_replace('"','',$select->getSqlString());die;
            $return = $this->fetchAll($select);
            if ($return) {

                $result->code = 100;
                $result->info = 'DUPLICATE "'.$msg.'" ';
                $result->data = $return;


            } else {
                $result->code = 0;
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


    public function loadParam($paramtype = NULL, $param_val3 = null, $param_parent = null){

        $result = new Result();

        try {
				$select = $this->select()
						->from(array('' => 'master_parameter'));
                if($paramtype){
                    $select->where(array($this->_('param_type', '') . " = '" .$paramtype."'"));
                }
                if($param_val3){
                    $select->where(array($this->_('param_val3', '') . " = '" .$param_val3."'"));
                }
                if($param_parent){
                    $select->where(array($this->_('param_parent', '') . " = '" .$param_parent."'"));
                }

                $select->order(array('idm_parameter ASC'));
			// echo str_replace('"','',$select->getSqlString());die;

			$return = $this->fetchAll($select);

            if ($return) {

                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $return;


            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
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

    public function loadSurveyProfilePartialAdmin($judul = null, $publish = null, $start = null, $limit = null, $count = null, $order = null, $sort = null){

        $result = new Result();

        try{

            if($order == 1){
                $order_name = 'judul';
            }else{
                $order_name = 'create_date';
            }

            if($sort == 1){
                $name_sort = 'ASC';
            }else{
                $name_sort = 'DESC';
            }



            $listdata = array();

            if($count){

                $sql = "SELECT COUNT(*) AS total FROM order_profile WHERE id_profile IS NOT NULL";

                if($judul){
                    $sql .= " AND judul ILIKE '%$judul%' ";
                }

                if($publish){
                    $sql .= " AND publish ='$publish' ";
                }

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    // print_r($val);die;

                    array_push($listdata, $val['total']);

                }

            }else{
                $sql = "SELECT * FROM order_profile WHERE id_profile IS NOT NULL";

                if($judul){
                    $sql .= " AND judul ILIKE '%$judul%' ";
                }

                if($publish){
                    $sql .= " AND publish ='$publish' ";
                }

                if($start != null){
					$sql .= " ORDER BY $order_name $name_sort LIMIT $limit OFFSET $start  ";
				}else{
					$sql .= " ORDER BY $order_name $name_sort  ";
				}
                // print_r($sql);die;
                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){

                    array_push($listdata, $val);

                }
            }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;


    }
    public function loadschedule($stat = null, $tempjadwal = null){

        // print_r($tempjadwal);die;

        $result = new Result();
        try{
            $listdata = array();
            if($stat == 'ACTIVE'){
                $sql = "SELECT sc.id_schedule,
                            TO_CHAR(sc.date_start_registration, 'dd Monthyyyy') as date_start_registration,
                            TO_CHAR(sc.date_finish_registration, 'dd Monthyyyy') as date_finish_registration,
                            TO_CHAR(sc.date_start_ta1, 'dd Monthyyyy') as date_start_ta1,
                            TO_CHAR(sc.date_finish_ta1, 'dd Monthyyyy') as date_finish_ta1,
                            TO_CHAR(sc.date_start_ta2, 'dd Monthyyyy') as date_start_ta2,
                            TO_CHAR(sc.date_finish_ta2, 'dd Monthyyyy') as date_finish_ta2,
                            sc.description
                        FROM sipintar_schedule sc
                        INNER JOIN master_parameter mp
                        on mp.param_val2 = sc.status
                        WHERE mp.param_type = 'TA_SCHEDULE' AND mp.param_val1 = '$stat'";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }

            }else {
                $sql = "SELECT sc.id_schedule, date_start_registration,
                            date_finish_registration,
                            date_start_ta1,
                            date_finish_ta1,
                            date_start_ta2,
                            date_finish_ta2,
                            sc.description
                        FROM sipintar_schedule sc
                        WHERE sc.id_schedule = $tempjadwal";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadElementQue($profileID, $lvl){

        $result = new Result();

        try{

            $sql = " SELECT
                        j.paramid,
                        j.object_label AS questions,
                        string_agg ( DISTINCT b.object_label, '<br/> ' ) AS object_string,
                        string_agg ( DISTINCT b.object_value, '<br/> ' ) AS object_value,
                        string_agg ( DISTINCT b.object_value, ',' ) AS is_opt,
                        string_agg ( DISTINCT b.paramid::VARCHAR, ', ' ) AS is_values,
                        j.x_answer,
                        j.x_difficult,
                        j.x_quality,
                        j.x_status,
                        j.sortingx
                    FROM
                        (
                        SELECT DISTINCT
                            op.id_profile,
                            mp.paramid,
                            mp.object_label,
                            mp.x_answer,
                            mp.x_quality,
                            mp.x_difficult,
                            mp.x_status,
                            mp.sortingx
                        FROM
                            order_profile op,
                            management_parameter mp
                        WHERE
                            op.id_profile = mp.profile_id
                            AND id_profile = $profileID AND has_parent = 0 ";

                            if($lvl != 0){
                                $sql .= " AND mp.x_difficult=$lvl ";
                            }

                            $sql .= " ORDER BY mp.sortingx ASC ";

            $sql .=     ") j,
                        ( SELECT has_parent, paramid, object_label, object_value FROM management_parameter WHERE profile_id = $profileID AND has_parent != 0 ) b
                    WHERE
                        j.paramid = b.has_parent
                    GROUP BY
                        j.paramid,
                        j.object_label,
                        j.x_answer,
                        j.x_difficult,
                        j.x_quality,
                        j.x_status,
                        j.sortingx ";

            $stmt     = $this->_db->query($sql);
            $getData  = $stmt->execute();

            $listdata = array();
            foreach($getData as $val){
                array_push($listdata, $val);
            }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadQueSort($profileID){

        $result = new Result();

        try{

            $sql = " SELECT DISTINCT
                        op.id_profile,
                        mp.paramid,
                        mp.object_label,
                        mp.x_answer,
                        mp.x_quality,
                        mp.x_difficult,
                        mp.sortingx,
                        mp.x_status
                    FROM
                        order_profile op,
                        management_parameter mp
                    WHERE
                        op.id_profile = mp.profile_id
                        AND id_profile = $profileID AND has_parent = 0 ORDER BY mp.sortingx ASC ";

            $stmt     = $this->_db->query($sql);
            $getData  = $stmt->execute();

            $listdata = array();
            foreach($getData as $val){
                array_push($listdata, $val);
            }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;
    }

    public function loadMateriPartial($col = null, $orderCol = null, $orderDir = null, $start = null, $length = null, $draw = null, $search = null){

        $result = new Result();

        try{

            $rows = 0;

            $sql1 = " SELECT COUNT(*) as total FROM materi_data WHERE idmateri != 0";

            if($search){
                $sql1 .= " AND titles LIKE '%$search%' ";
            }

            $stmt     = $this->_db->query($sql1);

            $getData  = $stmt->execute();

            foreach($getData as $val){

                $rows   = $val['total'];

            }

            $totalFilter = $rows;

            $sql = " SELECT
                            md.idmateri,
                            md.titles,
                            md.create_date,
                            ma.file_name,
                            ma.file_type,
                            ma.file_dir,
                            CONCAT(ma.file_size,' Mb') AS file_size,
                            ma.file_dir_c,
                            ma.file_ext
                        FROM
                            materi_data md,
                            materi_attachments ma
                        WHERE
                            md.idmateri != 0
                            AND md.idmateri = ma.idmateri";

            if($search){
                $sql .= " AND md.titles LIKE '%$search%' ";
            }

            if($length){
                if($length == -1){
                    $length = $totalFilter; // show all
                }
                $sql   .= " ORDER BY ".$orderCol." ".$orderDir."  LIMIT ". $length."  OFFSET ".$start."  ";
            }

            // print_r($sql);die;
            $stmt     = $this->_db->query($sql);
            $getData  = $stmt->execute();

            $listdata = array();

            foreach($getData as $val){

                array_push($listdata, $val);

            }

            $dataArray =array(
				"draw"              =>  intval($draw),
				"recordsTotal"      =>  intval($totalFilter),
				"recordsFiltered"   =>  intval($totalFilter),
				"data"              =>  $listdata
            );

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $dataArray;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadMappingMateri($profileID){

        $result = new Result();

        try{

            $sql = "  SELECT
                        md.idmateri,
                        md.titles,
                        md.create_date,
                        ma.file_name,
                        ma.file_type,
                        ma.file_dir,
                        CONCAT ( ma.file_size, ' Mb' ) AS file_size,
                        ma.file_dir_c,
                        ma.file_ext
                    FROM
                        order_materi mo,
                        materi_data md,
                        materi_attachments ma
                    WHERE
                        mo.idmateri = md.idmateri
                        AND md.idmateri = ma.idmateri
                        AND mo.profileid = $profileID
                    ORDER BY
                        mo.create_date DESC ";

            $stmt     = $this->_db->query($sql);
            $getData  = $stmt->execute();

            $listdata = array();
            foreach($getData as $val){
                array_push($listdata, $val);
            }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadValidasiMahasiswa($col = null, $orderCol = null, $orderDir = null, $start = null, $length = null, $draw = null, $search = null, $status = null, $schedule){

        $result = new Result();

        try{

            $rows = 0;

            $sql1 ="SELECT COUNT(*) as total FROM sipintar_mahasiswa_registration mr
                    INNER JOIN master_parameter mp
                    ON mr.status = mp.param_val2
                    WHERE mp.param_type = 'MAHASISWA_STATUS' AND mr.id_schedule = $schedule AND mp.param_val2 = $status";

            if($search){
                $sql1 .= " AND nim LIKE '%$search%' ";
            }

            $stmt     = $this->_db->query($sql1);
            $getData  = $stmt->execute();

            foreach($getData as $val){
                $rows   = $val['total'];
            }

            $totalFilter = $rows;
            if($status){
                $sql = "SELECT smr.id_mahasiswa_registration, smr.nim, name_mahasiswa, fps.name_program_studi, fkk.name_kelompok_keahlian, smr.list_date, smr.note, smr.status
                        FROM sipintar_mahasiswa_registration smr
                        INNER JOIN sipintar_ta_schedule ts
                        ON ts.id_schedule = smr.id_schedule
                        INNER JOIN master_parameter mp
                        ON mp.param_val2 = smr.status
                        INNER JOIN ftmd_program_studi fps
                        ON fps.id_program_studi = smr.program_studi
                        INNER JOIN ftmd_kelompok_keahlian fkk
                        ON fkk.id_kelompok_keahlian = smr.kelompok_keahlian
                        WHERE mp.param_type ='MAHASISWA_STATUS' AND smr.id_schedule = $schedule  AND mp.param_val2 = $status";

                if($search){
                    $sql .= " AND tmr.nim LIKE '%$search%' ";
                }

                if($length){
                    if($length == -1){
                        $length = $totalFilter;
                    }
                    $sql   .= " ORDER BY ".$orderCol." ".$orderDir."  LIMIT ". $length."  OFFSET ".$start."  ";
                }

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                $listdata = array();

                foreach($getData as $val){

                    array_push($listdata, $val);

                }
            }
            $dataArray =array(
				"draw"              =>  intval($draw),
				"recordsTotal"      =>  intval($totalFilter),
				"recordsFiltered"   =>  intval($totalFilter),
				"data"              =>  $listdata
            );

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $dataArray;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadmahasiswa($tempmhs = null){

        $result = new Result();

        try{
            $listdata = array();

            if($tempmhs){
                $sql = "SELECT id_mahasiswa_registration, nim, name_mahasiswa FROM sipintar_mahasiswa_registration
                WHERE id_mahasiswa_registration = $tempmhs";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadDosen($col = null, $orderCol = null, $orderDir = null, $start = null, $length = null, $draw = null, $search = null, $temp){

        $result = new Result();

        try{

            $rows = 0;

            $sql1 ="SELECT COUNT(*) as total FROM sipintar_dosen_pembimbing WHERE kelompok_keahlian = $temp ";

            if($search){
                $sql1 .= " AND nid LIKE '%$search%' ";
            }

            $stmt     = $this->_db->query($sql1);
            $getData  = $stmt->execute();

            foreach($getData as $val){
                $rows   = $val['total'];
            }


            $totalFilter = $rows;

            // print_r($totalFilter);die;


            $sql = "SELECT
                    udh.iduser,
                    sdp.id_dosen,
                    sdp.nid,
                    udh.name as name_dosen,
                    udh.email,
                    fps.name_program_studi,
                    kk.name_kelompok_keahlian,
                    udh.status
                    FROM
                        sipintar_dosen_pembimbing sdp
                        INNER JOIN ftmd_kelompok_keahlian kk ON kk.id_kelompok_keahlian = sdp.kelompok_keahlian
                        INNER JOIN ftmd_program_studi fps ON fps.id_program_studi = kk.id_program_studi
                        INNER JOIN user_data_header udh ON udh.iduser = sdp.id_user
                    WHERE
                        sdp.id_dosen != 0
                        AND udh.status = 10
                        AND kk.id_kelompok_keahlian = $temp ";

            if($search){
                $sql .= " AND sdp.nid LIKE '%$search%'  ";
            }

            if($length){
                if($length == -1){
                    $length = $totalFilter;
                }
                $sql   .= " ORDER BY ".$orderCol." ".$orderDir."  LIMIT ". $length."  OFFSET ".$start."  ";
            }

            $stmt     = $this->_db->query($sql);
            $getData  = $stmt->execute();

            $listdata = array();

            foreach($getData as $val){

                array_push($listdata, $val);

            }



            $dataArray =array(
				"draw"              =>  intval($draw),
				"recordsTotal"      =>  intval($totalFilter),
				"recordsFiltered"   =>  intval($totalFilter),
				"data"              =>  $listdata
            );

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $dataArray;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadTopic($col = null, $orderCol = null, $orderDir = null, $start = null, $length = null, $draw = null, $search = null, $temp = null){

        $result = new Result();

        try{

            $rows = 0;

            $sql1 ="SELECT COUNT
                        ( * ) AS total
                    FROM
                        sipintar_topic_judul stj
                        INNER JOIN sipintar_dosen_pembimbing sdp ON sdp.id_dosen = stj.id_dosen
                    WHERE sdp.kelompok_keahlian = $temp";

            if($search){
                $sql1 .= " AND nid LIKE '%$search%' ";
            }

            $stmt     = $this->_db->query($sql1);
            $getData  = $stmt->execute();

            foreach($getData as $val){
                $rows   = $val['total'];
            }


            $totalFilter = $rows;


            $sql = "SELECT
                        stj.id_topic,
                        stj.name_topic,
                        stj.description,
                        udh.name,
                        ps.name_program_studi,
                        kk.name_kelompok_keahlian,
                        stj.status
                    FROM
                        sipintar_topic_judul stj
                        INNER JOIN sipintar_dosen_pembimbing sdp ON stj.id_dosen = sdp.id_dosen
                        INNER JOIN master_parameter mp ON mp.param_val2 = stj.status
                        INNER JOIN ftmd_kelompok_keahlian kk ON sdp.kelompok_keahlian = kk.id_kelompok_keahlian
                        INNER JOIN ftmd_program_studi ps ON ps.id_program_studi = kk.id_program_studi
                        INNER JOIN user_data_header udh ON udh.iduser = sdp.id_user
                    WHERE
                        mp.param_type = 'TOPIC_STATUS'
                        AND mp.param_val1 = 'ACTIVE'
                        AND sdp.kelompok_keahlian = $temp ";

            if($search){
                $sql .= " AND stj.name_topic LIKE '%$search%' ";
            }

            if($length){
                if($length == -1){
                    $length = $totalFilter;
                }
                $sql   .= " ORDER BY ".$orderCol." ".$orderDir."  LIMIT ". $length."  OFFSET ".$start."  ";
            }

            $stmt     = $this->_db->query($sql);
            $getData  = $stmt->execute();

            $listdata = array();

            foreach($getData as $val){

                array_push($listdata, $val);

            }



            $dataArray =array(
				"draw"              =>  intval($draw),
				"recordsTotal"      =>  intval($totalFilter),
				"recordsFiltered"   =>  intval($totalFilter),
				"data"              =>  $listdata
            );

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $dataArray;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadprogramstudi($temp = null){

        $result = new Result();

        try{
            $listdata = array();


                $sql = "SELECT * FROM ftmd_program_studi";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }


            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadkelompokkeahlian($temp = null){

        $result = new Result();

        try{
            $listdata = array();

            if($temp){
                $sql = "SELECT * FROM ftmd_kelompok_keahlian WHERE id_program_studi = $temp";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }
    public function loaddosenoption($temp = null, $select = null, $select1=null){
        // print_r($temp);die;
        $result = new Result();

        try{
            $listdata = array();

            if($temp){

                $sql = "SELECT sdp.id_dosen, udh.name FROM sipintar_dosen_pembimbing sdp
                INNER JOIN  user_data_header udh ON udh.iduser = sdp.id_user
                WHERE sdp.kelompok_keahlian = $temp";

                if($select){
                    $sql.="AND id_dosen != $select";
                }
                if($select1){
                    $sql.="AND id_dosen != $select1";
                }

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();



                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadeditdosen($temp = null, $itemp = null){

        $result = new Result();

        try{
            $listdata = array();

            if($temp){
                $sql = "SELECT
                sdp.id_user,
                sdp.id_dosen,
                udh.username,
                udh.NAME,
                udh.email,
                sdp.nid,
                fkp.id_program_studi,
                sdp.kelompok_keahlian
            FROM
                sipintar_dosen_pembimbing sdp
                INNER JOIN user_data_header udh ON sdp.id_user = udh.iduser
                INNER JOIN ftmd_kelompok_keahlian fkp ON fkp.id_kelompok_keahlian = sdp.kelompok_keahlian
            WHERE
                id_dosen = $itemp AND id_user = $temp";

                $stmt     = $this->_db->query($sql);

                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function saveDosen($datarr = null){

        $result = new Result();

        try{
            $listdata = array();

            if($datarr){
                $sql = "SELECT insertdosen(array[('$datarr[0]', '$datarr[1]',$datarr[2], $datarr[3], '$datarr[4]', $datarr[5], 6 )]::input_user[]);
                ";

                $stmt      = $this->_db->query($sql);

                $proced    = $stmt->execute();

                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;

            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadmymahasiswa($temp = null){
        // print_r($temp);die;
        $result = new Result();

        try{
            $listdata = array();

            if($temp){
                $sql = "SELECT
                sm.nim,
                udh.NAME,
                fkh.id_program_studi,
                fps.name_program_studi,
                fkh.id_kelompok_keahlian,
                fkh.name_kelompok_keahlian
            FROM
                sipintar_mahasiswa sm
                INNER JOIN user_data_header udh ON udh.iduser = sm.id_user
                INNER JOIN ftmd_kelompok_keahlian fkh ON sm.kelompok_keahlian = fkh.id_kelompok_keahlian
                INNER JOIN ftmd_program_studi fps ON fps.id_program_studi = fkh.id_program_studi
                WHERE udh.iduser = $temp";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadregistmahasiswa($temp = null){
        // print_r($temp);die;
        $result = new Result();

        try{
            $listdata = array();

            if($temp){
                $sql = "SELECT spm.status, spm.note FROM sipintar_mahasiswa_registration spm
                WHERE spm.id_user = $temp";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function getdata($user_id = null, $table = null, $id_table = null, $status = null){

        $result = new Result();

        try{
            $listdata = array();

            if($user_id){
                $sql = 'SELECT * from '.$table.' where userid = '.$user_id;

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }
    public function getdatapascasarjana($param, $param1, $param2){
        ;
        $result = new Result();
        try{
            $listdata = array();

                $sql = "select * from user_data_header ud
                        INNER JOIN pascasarjana_biodata bp on bp.userid = ud.iduser
                        inner join pascasarjana_asal_pendidikan ap on ap.userid = ud.iduser
                        inner JOIN pascasarjana_prodi pp on pp.userid = ud.iduser
                        inner join pascasarjana_strata ps on ps.userid = ud.iduser";

                if($param == 'S3'){
                  $sql .= " inner join pascasarjana_promotor pr on pr.userid = ud.iduser";
                }

                $sql .= " where ps.strata = '".$param."'";

                if($param == 'S3' && $param2 != '70'){
                  $sql .= " and pr.promotorid = '".$param1."'";
                }


                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    unset($val['password']);
                    unset($val['accessToken']);
                    unset($val['deviceid']);
                    unset($val['authKey']);
                    unset($val['retries']);
                    array_push($listdata, $val);
                }

            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function getstat($user_id = null){

        $result = new Result();

        try{
            $listdata = array();

            if($user_id){
                $sql = '
                    select
                    (select status from pascasarjana_biodata where userid = '.$user_id.') as pascasarjana_biodata,
                    (select status from pascasarjana_asal_pendidikan where userid = '.$user_id.') as pascasarjana_asal_pendidikan,
                    (select status from pascasarjana_prodi where userid = '.$user_id.') as pascasarjana_prodi';

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function getunggahan($user_id = null){

        $result = new Result();

        try{
            $listdata = array();

            if($user_id){
                $sql = '
                    select * from pascasarjana_unggahan up
                    INNER JOIN pascasarjana_registration_attachments pr on pr.idupload = up.id_upload
                    where up.id_user = '.$user_id;

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function getaktifitas($user_id = null){

        $result = new Result();

        try{
            $listdata = array();

            if($user_id){
                $sql = 'select * from pascasarjana_aktifitas where userid = '.$user_id.' order by create_date asc';

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function getnodaftar($user_id = null){

        $result = new Result();

        try{
            $listdata = array();

            if($user_id){
                $sql = 'select * from nomor_pendaftaran where userid = '.$user_id;

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function getjadwal($user_id = null){

        $result = new Result();

        try{
            $listdata = array();

            if($user_id){
                $sql = 'select jd.*, jg.gelombang, jg.start_date, jg.end_date from pascasarjana_jadwaltest jd
                        inner join pascasarjana_jadwal_gelombang jg on jg.id_jadwal = jd.id';

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
            }



            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadActiveUser(){

        $result = new Result();

        try{
            $listdata = array();

                $sql = "select (
                    (select count(jml_aktif) from dma_digibiz_mytds_dxb_digihotel_dashboard_v WHERE jml_aktif=1) + 
                    
                    (select count(mulai_aktif) from dma_digibiz_mytds_dxb_digiclinic_dashboard_v where mulai_aktif != '') +
                    
                    (select count(is_active) from dma_digibiz_mytds_dxb_digierp_dashboard_v where is_active = 1) +
                    
                    (select count(lastLogin) from dma_digibiz_bonum_user WHERE DATE_FORMAT(lastLogin,'%H:%i:%s') != '00:00:00') + 
                    
                    (select count(active_until) from dma_sakoo_user_api)) as total_active_user";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }


            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadMonthlyUser(){

        $result = new Result();

        try{
            $listdata = array();
            $listdata_digihotel = array();
            $listdata_digiclinic = array();
            $listdata_digierp = array();
            $listdata_bonum = array();
            $listdata_sakoo = array();

                $sql_digihotel = "select MONTHNAME(end_date), count(*) as total from dma_digibiz_mytds_dxb_digihotel_dashboard_v where jml_aktif = 1
                                    GROUP BY MONTHNAME(end_date)";

                $stmt_digihotel     = $this->_db->query($sql_digihotel);
                $getData_digihotel  = $stmt_digihotel->execute();

                foreach($getData_digihotel as $val){
                    array_push($listdata_digihotel, $val);
                }


                $sql_digiclinic = "select MONTHNAME(mulai_aktif), count(*) as total from dma_digibiz_mytds_dxb_digiclinic_dashboard_v where mulai_aktif != ''
                                    GROUP BY MONTHNAME(mulai_aktif)";

                $stmt_digiclinic     = $this->_db->query($sql_digiclinic);
                $getData_digiclinic  = $stmt_digiclinic->execute();

                foreach($getData_digiclinic as $val){
                    array_push($listdata_digiclinic, $val);
                }


                $sql_digierp = "select MONTHNAME(end_date), count(*) as total from dma_digibiz_mytds_dxb_digierp_dashboard_v where is_active = 1
                                GROUP BY MONTHNAME(end_date)";

                $stmt_digierp     = $this->_db->query($sql_digierp);
                $getData_digierp  = $stmt_digierp->execute();

                foreach($getData_digierp as $val){
                    array_push($listdata_digierp, $val);
                }


                $sql_bonum = "select MONTHNAME(lastLogin), count(*) as total from dma_digibiz_bonum_user WHERE DATE_FORMAT(lastLogin,'%H:%i:%s') != '00:00:00'
                                GROUP BY MONTHNAME(lastLogin)";

                $stmt_bonum     = $this->_db->query($sql_bonum);
                $getData_bonum  = $stmt_bonum->execute();

                foreach($getData_bonum as $val){
                    array_push($listdata_bonum, $val);
                }

                $sql_sakoo = "select MONTHNAME(active_until), count(*) as total from dma_sakoo_user_api
                                GROUP BY MONTHNAME(active_until)";

                $stmt_sakoo     = $this->_db->query($sql_sakoo);
                $getData_sakoo  = $stmt_sakoo->execute();

                foreach($getData_sakoo as $val){
                    array_push($listdata_sakoo, $val);
                }

                
                $listdata = count($listdata_digihotel)+count($listdata_digiclinic)+count($listdata_digierp)+count($listdata_bonum)+count($listdata_sakoo);
                
            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }

    public function loadWeeklyUser(){

        $result = new Result();

        try{
                $listdata = array();

                $sql = "select 
                ( select count(*) as total from dma_digibiz_mytds_dxb_digihotel_dashboard_v where jml_aktif = 1 and DATE(end_date) = DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) +
                
                ( select count(*) as total from dma_digibiz_mytds_dxb_digiclinic_dashboard_v where mulai_aktif != '' and DATE(mulai_aktif) = DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) +
                
                ( select count(*) as total from dma_digibiz_mytds_dxb_digierp_dashboard_v where is_active = 1 and DATE(end_date) = DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) +
                
                ( select count(*) as total from dma_digibiz_bonum_user WHERE DATE_FORMAT(lastLogin,'%H:%i:%s') != '00:00:00' and DATE(lastLogin) = DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) +
                
                ( select count(*) as total from dma_sakoo_user_api where DATE(active_until) = DATE_SUB(CURDATE(), INTERVAL 7 DAY) ) as total";

                $stmt     = $this->_db->query($sql);
                $getData  = $stmt->execute();

                foreach($getData as $val){
                    array_push($listdata, $val);
                }
                
            if ($listdata) {
                $result->code = $result::CODE_SUCCESS;
                $result->info = $result::INFO_SUCCESS;
                $result->data = $listdata;
            } else {
                $result->code = $result::CODE_FAILED;
                $result->info = $result::INFO_FAILED;
            }

        }catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            $result->code = 3;
            $result->info = 'ERROR : ' . $ex->getMessage();
        } catch (\Exception $ex) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $ex->getMessage();
        }
        return $result;

    }
}
