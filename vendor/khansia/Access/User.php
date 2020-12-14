<?php

namespace Khansia\Access;

use Khansia\Generic\Result;
use Khansia\Generic\Objects\Map;

/* extends dari mapper agar bisa mapping properti class ke tabel */
class User extends \Khansia\Generic\Objects\Mapper {

    const STATUS_ACTIVE     = 10;
    const STATUS_NOTACTIVE  = 20;
    const STATUS_BLOCKED    = 30;

    const CODE_AUTH_INVALID = 31;
    const CODE_AUTH_SUSPEND = 32;
    const CODE_AUTH_LOCKED  = 33;
    const CODE_AUTH_FAILED  = 34;

    const RETRIES_TRUE      = TRUE;
    const RETRIES_FALSE     = FALSE;

    protected $_storage;
    protected $_config;
    protected $_loaded = false;

    public function __construct(User\Storage\Skeleton $storage) {

        /* simpan user storage */
        $this->_storage = $storage;

        /* load config */
        //$this->_config = $this->_storage->fetchConfig('USER');
        /*
          map properti class ke tabel
          sehingga nama field di tabel dapat diwakili oleh property class
        */
        parent::__construct(
            array(),
            array(
                new Map('iduser','id'),
                new Map('username'),
                new Map('password'),
                new Map('name'),
                new Map('role'),
                new Map('status'),
                new Map('deviceid'),
                new Map('create_dtm'),
                new Map('retries'),
                new Map('email'),
                new Map('tokenjwt'),
                new Map('lifetime'),
                new Map('update_date'),
                new Map('accessToken'),
            ),
            parent::CASE_SENSITIVE
        );
    }

    /**
      save
    */
    public function save($update = false) {

      /* simpan via storage */
      $result = $this->_storage->save($this, $update);
        if ($result->code == 0) {
            $this->id = $result->data;
        }
        return $result;
    }

    public function load($id, $mode = User\Storage::LOADBY_ID){

        /* load dari storage */
        if ($data = $this->_storage->load($id, $mode)) {
            
            /* load sukses, set data properti class dari hasil query */
            $this->push($data);

            /* set loaded */
            $this->_loaded = true;
            return true;

        } else {

            $this->_loaded = false;

        }
    }

    public function loadAccess($id,  $postgree = false){

        /* load access data */
        $acc = $this->_storage->getAccess($id, $postgree);
        return $acc;

    }

    public function authenticate($credential, $data = array(), $retriesMode = self::RETRIES_FALSE){
        $result = new Result();

        $retries = 0;

        /* loaded */
        if ($this->id && $this->_loaded) {

            /* jika user aktif */
            if (($this->status == self::STATUS_ACTIVE)) {

                if($this->password == md5($credential)){
                    $authenticated = true;
                }else{
                    $authenticated = false;
                }

                if ($authenticated) {

                    $this->retries = 'NULL';
                    $this->save(true);

                    /* QA: auth success */
                    $result->code = $result::CODE_SUCCESS;
                    $result->info = 'user_auth_success';
                }else{

                    /* cek user jika gagal password sebanyak 3x */
                    if($retriesMode == self::RETRIES_TRUE){

                        if($this->retries == 'NULL'){
                            $ret_data = 0;
                        }else{
                            $ret_data = (int) $this->retries;
                        }

                        if($ret_data < 3){
                            $this->retries = $ret_data + 1;
                        }

                        if((int) $this->retries == 3){ // jika sudah mencapai 3x then
                            $this->status = self::STATUS_BLOCKED;
                        }

                        $this->save(true);
                    }
                    /* QA: fail invalid passwd */
                    $result->code = self::CODE_AUTH_FAILED;
                    $result->info = 'user_auth_failed';
                }

            }else{
                /* QA: user is locked */
                $result->code = self::CODE_AUTH_LOCKED;
                $result->info = 'user_auth_locked';
            }
        }else{

            /* QA: user not loaded */
            $result->code = self::CODE_AUTH_INVALID;
            $result->info = 'user_auth_invalid';
        }

        /* return result */
        return $result;

    }

}
