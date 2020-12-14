<?php
namespace PARTNER\Model;

use Khansia\Access;

class User extends Access\User {
    
    protected $_logevent;
    
    public function __construct(Access\User\Storage\Skeleton $storage) {
        
        parent::__construct($storage);
        
        $this->map('user_id','id');
        $this->map('username');
        $this->map('password');
        $this->map('name');
        $this->map('regional_id');
        $this->map('status','statusId');
        $this->map('role');
        $this->map('role','newrole');
        $this->map('divisi_witel_id');
        $this->map('auth_type');
        $this->map('profiles');
        $this->map('auth_user','authType');
        $this->map('id_cae');
        $this->map('create_date');
        $this->map('partnerid');
        $this->map('file_name');
        $this->map('id_mitra');
        
        $this->_logevent = new Logger(Log\Storage::factory($this->_storage->getAdapter(), 'EVENT'));
    }
    
    private function getProfilePicture() {
        
        $path = __DIR__;
        //echo 'path = '.$path;
        $sep = (strstr($path, '\\')) ? '\\' : '/';
        $path .= "{$sep}..{$sep}..{$sep}..{$sep}..{$sep}..{$sep}data{$sep}profiles{$sep}";
        //$path .= "{$sep}..{$sep}..{$sep}..{$sep}Data{$sep}profiles{$sep}";
        
        if (strlen($this->profiles) > 0) {
            if ($profile = @file_get_contents($path . $this->profiles)) {
                return $profile;
            }
        }
        return @file_get_contents($path . 'default.img.b64');
    }
    
    private function generateSeed($digits = 16) {

        /* get key */
        $key = '' . $this->password;
        if (strlen($key) < 32) $key .= md5(time());

        /* gen seed */
        $seed = '';
        for ($i = 0; $i <= $digits; $i++) {
            if (($i % 2) == 0) {
                $seed .= substr($key, rand(0, 31), 1);
            } else {
                $seed .= rand(0, 9);
            }
        }

        return $seed;
    }
    
    public function add($code, $fullname, $email, $password, $data, $pic) {
        $connection = $this->_storage->getAdapter()->getDriver()->getConnection();
        $connection->beginTransaction();
        
        try {
            //$type = isset($data['user_type_id']) ? trim($data['user_type_id']) : self::TYPE_CUSTOMER;
            $type = isset($data['user_type_id']) ? trim($data['user_type_id']) : self::TYPE_AGENTRETAIL;
            if (!is_numeric($type)) $type = self::TYPE_AGENTRETAIL;
            if (($type != self::TYPE_ADMIN) && ($type != self::TYPE_SPV) && ($type != self::TYPE_AGENTBGES)) {
                $type = self::TYPE_AGENTRETAIL;
            }
            
            $this->typeId = $type;
            $result = parent::register($code, $fullname, $email, $password);
            
            if ($result->code === $result::CODE_SUCCESS && $this->id) {
                /* log to event */
                $this->_logevent->setSubject($this->id);
                $this->_logevent->setIdentity($email);
                $this->_logevent->write(
                    Logger::EVENT_USER_REGISTRATION,
                    Logger::GROUP_USER,
                    'Registrasi user ' . $this->name,
                    $pic
                );
                $connection->commit();
                
                return array(
                    'success' => true,
                    'message' => 'OK'
                );
            } else {
                $connection->rollback();
                return array(
                    'success' => false,
                    'message' => $result->info
                );
            }
        } catch (\Exception $ex) {
            $connection->rollback();
            error_log('PARTNER\Model\User->add');
            error_log('Code: '.$ex->getCode());
            error_log('Message: '.$ex->getMessage());
            
            return array(
                'success' => false,
                'message' => $ex->getMessage()
            );
        }
    }
    
    public function lists($type, $status){
        $data = $this->_storage->lists($type, $status);
        return $data;
    }
	
	public function getusercaecap($id, $role = null, $tipe, $regional, $witel){
        $data = $this->_storage->getuserid($id, $role, $tipe, $regional, $witel);
        return $data;
    }
	
	#RESET PASSWORD BY USER_ID
    public function resetpass($user_id){
        $data = $this->_storage->resetpass($user_id);
        return $data;
    }
	
	#GANTI STATUS BY USER_ID
    public function gantistatus($user_id){
        $data = $this->_storage->gantistatus($user_id);
        return $data;
    }
	
	#UBAH PASSWORD BY USER_ID
    public function changepassword($user_id){
        $data = $this->_storage->changepassword($user_id);
        return $data;
    }

    public function listcustomer($user_id){
        $data = $this->_storage->listcustomer($user_id);
        return $data;
    }

    public function userprofile($user_id){
        $data = $this->_storage->userprofile($user_id);
        return $data;
    }
	
	#CEK  EMAIL
    public function checkemail($email){
        $data = $this->_storage->checkemail($email);
        return $data;
    }
	#CEK CONTACT
    public function checkcontact($mobile){
        $data = $this->_storage->checkcontact($mobile);
        return $data;
    }
	
	#CEK USERNAME & EMAIL
    public function check($userName, $eMail){
        $data = $this->_storage->check($userName, $eMail);
        return $data;
    }
	
	#CEK USERNAME 
	public function checkusername($username){
        $data = $this->_storage->checkusername($username);
        return $data;
    }
	
	/* get data cap yang belum dimappingkan di project gan */
	public function listcapnotexists($param = null){
        $data = $this->_storage->listcapnotexists($param);
        return $data;
    }
	
	public function loadUser($userid = null, $role = null, $tipeuser = null, $assign = null, $treg = null, $regional = null, $witel = null, $mode = null, $periode = null, $sessionuserid = null){
        $data = $this->_storage->loaduser($userid, $role, $tipeuser, $assign, $treg, $regional, $witel, $mode, $periode, $sessionuserid);
        return $data;
    }
	
	/* Mapping Cae Cap */
	public function listOfMapping($iduser = null, $param = null,  $search = null, 
			$chose_fcapcae = null, $idcaecap = null, $mode =null
		)
	{
			$data = $this->_storage->listMapping($iduser, $param, $search, $chose_fcapcae, $idcaecap, $mode);
			return $data;
    }
	
		
    public function authenticateApps($credential, $data = array()) {
        //print_r($this->pull());die;
        /* get device id */
        $this->deviceId = isset($data['devid']) ? trim($data['devid']) : null;

        /* validate device id tidak boleh kosong */
        if (strlen('' . $this->deviceId) <= 0) {
            return new Result(0, 35, 'user_auth_devid_required');
        }

        /* validate device id tidak boleh kosong */
        /*if ($this->statusId == self::STATUS_CANDIDATE) {
            return new Result(0, 36, 'user_auth_not_verified');
        }*/

        /* do standard auth */
        $result = parent::authenticate($credential, $data);
        //print_r($result);die;
        /* std auth success? */
        if ($result->code == $result::CODE_SUCCESS) {
			//print_r('masuk gan');die;

            //TODO: auth thru API

            /* generate token */
            $seed = $this->generateSeed();
			//print_r($seed);die;
            $this->token = md5($this->deviceId . '-' . $seed);
            //print_r($this->pull());die;
            $x = $this->save(true);
           // print_r($x);die;
            //echo "PROF = ".$this->getProfilePicture();die;
            /* send seed for client */
            $result->data = array(
              'token' => $seed,
              'account' => $this->accountId,
              'email' => $this->email,
              'fullname' => $this->name,
              'msisdn' => $this->msisdn,
              'lang' => $this->language,
              'ncli' => $this->ncli,
              'role' => 'user',
              'gender' => strlen($this->gender) > 0 ? $this->gender : '',
              'profile' => $this->getProfilePicture(),
              'address' => '',//$this->getAddress(),
              'city' => ''//$this->getCity(),
            );

        }
        return $result;
    }
    
	
}