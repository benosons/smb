<?php

namespace Application\Master;
use Khansia\Mvc\Controller;
use Khansia\Access\User;
use Khansia\Access\Session;



class GlobalActionController extends \Khansia\Mvc\Controller {

    const POST                  = 0;
    const GET                   = 1;
    const MINUTES_IDLE_SESSION  = 86400;
    const PHRASE                = 'Khansia Encrypt';
    const SKEY                  = 'DaniC3gxrRdrccXv8hbGdmk6';
    const DEFAULT_ERROR         = 'Silahkan masuk untuk melanjutkan';
    
    protected $_session         = null;

    protected function getSession() {
        try {

            if ($this->_session == null) {

                $session        = new Session('KHANSIA', Session::MODE_DATABASE,

                array(
                        'adapter'   => $this->getDb(),
                        'table'     => 'user_data_session',
                        'lifetime'  => self::MINUTES_IDLE_SESSION,
                        'secure'    => false
                ));

                $session->start(false);
                $this->_session = $session;
            }

            return $this->_session;

        } catch (\Exception $ex) {
            //error_log($ex->getMessage());
            return false;
        }
    }

    protected function isHaveAccess() {
        $controllerName = $this->params('controller');
        $actionName     = $this->params('action');
        $session        = $this->getSession();

        $explodeCtrl    = explode("\\", $controllerName);
        $getCtrlName    = $explodeCtrl[2];
        
        $haveAccess     = false;
        $valAccess      = 'TRUE';
        $roleData       = $session->get('accessdata');

        $responArr      = array();

        if($roleData) {
            
            $asData     = $roleData;

            foreach($asData as $res) {

                if($res['access_controller'] == $getCtrlName){
                    
                    if($res['access_action'] == $actionName){
                        
                        if($res['access_status'] == $valAccess){

                            $responArr = array('success');
                            break;

                        }else{

                            $responArr = array('failed');
                            break;

                        }
                    }else{

                        $responArr = array('failed');

                    }
                    
                }else{

                    $responArr = array('failed');

                }

            }
        }

        if (in_array("success", $responArr)) { 
            $haveAccess = true;
        }

        if(!$haveAccess) {
            return $this->redirect()->toRoute('forbidden');
        }
    }

    public function getDb($module = 'primary') {

        $adapter = parent::getDb($module);

        /* set date format as mysql standard */
        $formats = array(
          'NLS_TIME_FORMAT'         => "HH24:MI:SS",
          'NLS_DATE_FORMAT'         => "YYYY-MM-DD HH24:MI:SS",
          'NLS_TIMESTAMP_FORMAT'    => "YYYY-MM-DD HH24:MI:SS",
          'NLS_TIMESTAMP_TZ_FORMAT' => "YYYY-MM-DD HH24:MI:SS TZH:TZM"
        );

        return $adapter;
    }

    protected function isLoggedIn($anum=null) {
        $actionName     = $this->params('action');
        $controllerName = $this->params('controller');

        if(($controllerName != 'Application\Controller\ApiController' && $controllerName != 'Application\Controller\JsondataController')){
            $this->headScript->appendScript(' var actionControl = "' . $actionName . '"');
        }

        $this->headScript->appendScript(' var actionControl = "' . $actionName . '"');   
        $this->headScript->appendFile('/action-js/global-js/sha256.js');      
        $this->headScript->appendFile('/action-js/global-js/javaScriptGlobalCustom.js');

        try {

            $session = $this->getSession();

            if($session) {
                $owner = $session->owner();

                if (isset($owner)) {

                    if ($owner != null) {

                        $this->layout()->setVariable('session', $session);
                        
                        $username = $session->get('usernamed');

                        $this->layout()->setVariable('username', $username);
                        
                        return $session;

                    }

                }
                return $this->redirect()->toRoute('login');
            } else {
                return $this->redirect()->toRoute('login');
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('login');
        }
    }

    protected function STORAGE_NOW(){
        $storage    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());                    
        $model  	= new \Application\Model\Param($storage);

        return $model->getDateNow();
    }

    protected function generateSeed($digits = 16) {

        /* get key */
        $key = '' . $this->password;
        if (strlen($key) < 32) $key .= md5(time());

        /* gen seed */
        $seed = '';
        for ($i = 0; $i <= $digits; $i++) {
            if (($i % 2) == 0) {
                $seed .= substr($key, rand(0, 31), 1);
            } else {
                $seed .= password_hash(random_bytes(10), PASSWORD_DEFAULT);
            }
        }


        return $seed;
    }

    protected function authenticateApi($credential = null, $data = array()) {


        /* get device id */
        $this->deviceId = isset($data['devid']) ? trim($data['devid']) : null;
		
        /* validate device id tidak boleh kosong */
        if (strlen('' . $this->deviceId) <= 0) {
            return new \Khansia\Generic\Result(0, 35, 'user_auth_devid_required');
        }

        /* do standard auth */                      
        $storage    = \Khansia\Access\User\Storage::factory($this->getDb(), $this->getConfig());
        $user       =  new \Khansia\Access\User($storage);
        
        if($user->load($data['username'],  \Khansia\Access\User\Storage::LOADBY_CODE)){ // sukses load then

            $authResult = $user->authenticate($credential, null, \Khansia\Access\User::RETRIES_TRUE);
            
            /* std auth success? */
            if($authResult->code == $authResult::CODE_SUCCESS) {

                /* generate token */
                $seed               = $this->generateSeed();
                $adapter 	        = $this->getDb();                
                $storage            = \Application\Model\Param\Storage::factory($adapter, $this->getConfig());                    
                $model  	        = new \Application\Model\Param($storage);

                $this->accessToken  = md5($this->deviceId . '-' . $seed);
                $this->myData       = array('iduser' => $user->id, 'accessToken' => $this->accessToken, 'deviceid' => $this->deviceId, 'update_date' => $this->STORAGE_NOW());
                $this->findBy       = 'iduser='.$user->id;
                
                $getResults         = $model->updateGlobal('user_data_header', $this->myData,  $this->findBy);
                
                if($authResult->code == $authResult::CODE_SUCCESS) {

                    /* send seed for client */
                    $authResult->data = array(
                        'token'         => $this->accessToken,
                        'update_date'   => $this->STORAGE_NOW(),
                    );
                    
                }

            }else{
                $authResult->code = 99;
                $authResult->info = 'Incoorrect Username or Password.';
            }

        }else{
           $authResult->code = 99;
           $authResult->info = 'Incoorrect Username or Password.';
        }
        return $authResult;
    }

    protected function getGuid($mode = self::POST) {
        
        if ($mode == self::GET) {
            if ($data = $this->getRequest()->getQuery('guid')) {
                return $data;
            }
        } else {
            if ($guid = $this->getRequest()->getPost('guid')) {
                return $guid;
            }
        }
  
        return 0;
    }

    protected function getAppsUser() {

        /* get token from guid */
        $token      = $this->getGuid();

        /* load user by token */
        $storage    = \Khansia\Access\User\Storage::factory($this->getDb(), $this->getConfig());
        $user       =  new \Khansia\Access\User($storage);

        if ($user->load($token, \Khansia\Access\User\Storage::LOADBY_TOKEN)) {

            /* user found */
            return $user;

        } else {
            
            /* auth expire */
            $result = new \Khansia\Generic\Result(0, 403, 'user_auth_expire');

            $json   = $result->toJson();

            //$this->logRequest(1, 'Auth expire', $json);

            header('Content-Type: application/json');
            
            echo($json);

            die();
            
        }

        /* false default */
        return false;
    }

    public function checkCsrf(){

        header('Content-Type: application/json');

        //Mengirimkan Token Keamanan
        $session    = $this->getSession();

        $headers    = apache_request_headers();
        
        if (isset($headers['Csrf-Token'])) {

            $storage    = \Khansia\Access\User\Storage::factory($this->getDb(), $this->getConfig());
            $user       =  new \Khansia\Access\User($storage);
            
            if($user->load($session->get('user_id'),  \Khansia\Access\User\Storage::LOADBY_ID)){
               
                if ($headers['Csrf-Token'] !== $session->get('csrf_token')) {
    
                    $result = new \Khansia\Generic\Result(0, 91, 'Wrong CSRF token # CODE 1');
                    $json   = $result->toJson();
    
                    echo($json);die();
    
                }

                if ($headers['Csrf-Token'] !== $user->accessToken) {
    
                    $result = new \Khansia\Generic\Result(0, 92, 'Wrong CSRF token # CODE 2');
                    $json   = $result->toJson();
    
                    echo($json);die();
    
                }

                if ($user->accessToken !== $session->get('csrf_token')) {
    
                    $result = new \Khansia\Generic\Result(0, 93, 'Wrong CSRF token # CODE 3');
                    $json   = $result->toJson();
    
                    echo($json);die();
    
                }

            }else{
                $message = 'Time out session # Silahkan login kembali';

                $session->put(null, array('message' => $message));
  
                return $this->redirect()->toRoute('login');
            }

        } else {

            $result = new \Khansia\Generic\Result(0, 97, 'No CSRF token');
            $json   = $result->toJson();

            echo($json);die();

        }
    }

    protected static function url_encryptd($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC') {

        // hash im dulu gan
        $key = hash('sha256', $key);

        // buat iv  - encrypt dengan method AES-256-CBC  16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);

        // encrypt
        $output = openssl_encrypt($string, $method, $key, 0, $iv);
        
        // encode
        return base64_encode($output);
    }

    protected static function url_decryptd($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC') {

        // hash im dulu gan
        $key = hash('sha256', $key);

         // buat iv  - encrypt dengan method AES-256-CBC  16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);

        // decode
        $string = base64_decode($string);

        // decrypt boa edin
        return openssl_decrypt($string, $method, $key, 0, $iv);
    }


}
