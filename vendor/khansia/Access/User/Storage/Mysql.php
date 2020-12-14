<?php

namespace Khansia\Access\User\Storage;
use Khansia\Generic\Result as Result;

/* extends dari khansia\Db\Storage */
class Mysql extends \Khansia\Db\Storage implements Skeleton {

  const CONFIG_CODE = 'USER';
	const ORDER = 'users';
	const USER_TABLE = 'user_data_header';

    /*
      constructor
    */
    public function __construct(\Laminas\Db\Adapter\Adapter $adapter, $config = array()) {

      /* set nama2 tabel default yg digunakan */
      $this->_tables = array(
        'users' => 'users',  /* tabel user */
      );

      /* construct parent --> khansia \Db\Storage */
      parent::__construct($adapter, $config);

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

    public function fetchRow(\Laminas\Db\Sql\Select $select) {

      if ($this->_driver != self::DRIVER_OCI8) {
          $select->limit(1);
      }

      if ($result = $this->fetchAll($select)) {
          foreach ($result as $row) {
              return $row;
          }
      }
      return null;
    }

    /*
      load
      Select data user dari tabel berdasarkan user id

      $id: user id
    */
    public function load($id, $mode = \Khansia\Access\User\Storage::LOADBY_ID) {
     
      $select = $this->select()
                ->from(self::USER_TABLE);

      if ($mode == \Khansia\Access\User\Storage::LOADBY_CODE) {
          $select->where($this->__(array('username' => $id)));
      } elseif ($mode == \Khansia\Access\User\Storage::LOADBY_MAIL) {
          $select->where($this->__(array('email' => $id)));
      } elseif ($mode == \Khansia\Access\User\Storage::LOADBY_TOKEN) {
          $select->where($this->__(array('accessToken' => $id)));
      } else {
          $select->where($this->__(array('iduser' => $id)));
      }
      $return = $this->fetchRow($select);
      
	    // print_r($this->fetchRow($select));die;
      //echo str_replace('"','',$select->getSqlString());
      return $return;
    }



	public function checkemail($email) {
        $select = $this->select()
                ->from($this->_tables['users'])
                ->where($this->__(array('email' => $email)), \Laminas\Db\Sql\Where::OP_OR);
		//var_dump($select);
		//die;
                //->where($this->__(array('user_status_id' => 2)), \Laminas\Db\Sql\Where::OP_OR);
      //echo str_replace('"','',$select->getSqlString());
	  return $this->fetchRow($select);
    }

    public function check($userName, $eMail) {
        $select = $this->select()
                ->from($this->_tables['users'])
                ->where($this->__(array('code' => $userName)))
                ->where($this->__(array('email' => $eMail)), \Laminas\Db\Sql\Where::OP_OR);
		//var_dump($select);
		//die;
                //->where($this->__(array('user_status_id' => 2)), \Laminas\Db\Sql\Where::OP_OR);
      //echo str_replace('"','',$select->getSqlString());
	  return $this->fetchRow($select);
    }

	 public function checkusername($username) {
        $select = $this->select()
                ->from($this->_tables['users'])
                ->where($this->__(array('user_id' => $username)), \Laminas\Db\Sql\Where::OP_OR);
		//var_dump($select);
		//die;
                //->where($this->__(array('user_status_id' => 2)), \Laminas\Db\Sql\Where::OP_OR);
      //echo str_replace('"','',$select->getSqlString());
	  return $this->fetchRow($select);
    }

    /*
      save
      Insert / update data user ke tabel
    */

    public function save(\Khansia\Access\User $user, $update = false) {
      /* mode update? */

        $result = new Result();
        try{
          if ($update == true) {
                    //print_r($this->__($user->pull()));die;
            $data = $this->__($user->pull());
            $fields = array();
              foreach ($data as $key => $value) {
                if (!empty($value)) {
                $fields[$key] = $value;
                }
              }

          $update = $this->update()
                ->table(self::USER_TABLE)
                ->set($fields)
                ->where($this->__(array('iduser' => $fields['iduser'])));
              $this->execute($update);

            /* update sukses, return user id */
          if (!empty($fields['iduser'])) {
                        $result->code = 0;
                        $result->data = $fields['iduser'];
                        $result->info = 'update_success';
                    } else {
                        $result->code = 1;
                        $result->info = 'update_fail';
                    }

          } else {
            $insert = $this->insert()
                  ->table(self::USER_TABLE)
                  ->autoincrement($this->_('iduser'))
                  ->values($data = $this->__($user->pull()));

            $id = $this->execute($insert);
              /* insert sukses, return user id */
            $lastid = array("lastId" =>$id);
            //array_push($lastid, array("lastId" =>$id));
            array_push($data, $lastid);
            if(!empty($id)){
              $result->code = 0;
              $result->info = 'REGISTER OK';
              $result->data = $data;
            }else{
              $result->code = 2;
              $result->info = 'FAILED';
            }
          }
        } catch (\Exception $e) {
            $result->code = 4;
            $result->info = 'ERROR : ' . $e->getMessage();
        }

		return $result;
	}

    public function checkDuplicate($id, $email) {

      //query dari tabel yg nama user / e-mailnya ada
      $select = $this->select()
                ->from($this->_tables['users'])
                ->where($this->__(array('user_id' => $id)))
                ->where($this->__(array('email' => $email)), \Laminas\Db\Sql\Where::OP_OR);
      return $this->fetchRow($select);
    }
    public function register(\Khansia\Actor\User $user){
        $insert = $this->insert()
                  ->into($this->_tables['users'])
                  ->values($this->__($user->pull()));

        if ($this->execute($insert)) {

          return true;
          //return $user->id;

        }
         /* default gagal */
      return false;
    }

    /* get data access user  */
    public function getAccess($id, $postgree = false){
      $result = new Result();
  
      try{
  
        $sql = "    SELECT
                      ua.access_controller,
                      ua.access_action,
                      ur.access_role_code role_code,
                      ur.NAME,
                      ua.access_name,
                      ua.access_code,
                      um.access_status 
                    FROM
                      user_data_header ad,
                      user_data_role ur,
                      user_data_map um,
                      user_data_access ua 
                    WHERE
                      ad.ROLE = ur.access_role_code 
                      AND ad.ROLE = um.role_code 
                      AND um.access_code = ua.access_code 
                      AND ad.iduser = '$id' ";
  
        $stmt     = $this->_db->query($sql);
  
        $resdata  = $stmt->execute();
        
        
        $listdata = array();
  
        if($postgree == true){
  
          foreach($resdata as $val){
            
            // print_r($val);die;
            array_push($listdata, $val);
  
         }
  
        }else{
    
          while($resdata->next()){
            $res = $resdata->current();
            array_push($listdata,$res);
          }
  
        }
        // print_r($sql);die;
        if ($listdata) {
            $result->code = 0;
            $result->info = 'OK';
            $result->data = $listdata;
        } else {
            $result->code = 1;
            $result->info = 'nok';
        }
  
      }catch (\Exception $ex) {
        $result->code = 2;
        $result->info = 'Error:' . $ex->getMessage();
        $result->data = $ex->getMessage();
      }
  
      return $result;
      }
}
