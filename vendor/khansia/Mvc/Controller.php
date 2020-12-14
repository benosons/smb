<?php
namespace Khansia\Mvc;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;
use Laminas\EventManager\EventManagerInterface;

class Controller extends \Laminas\Mvc\Controller\AbstractActionController {

  const POST = 0;
  const GET  = 1;

  protected $_db = null;
  protected $_config = array();

  	public function onDispatch(\Laminas\Mvc\MvcEvent $epent) {

		/* get config! */
		$this->_config = $epent->getApplication()->getServiceManager()->get('config');

		/* apply php settings */
		if (isset($this->_config['php'])) {
			$php = $this->_config['php'];
			foreach ($php as $key => $value) {
				ini_set($key, $value);
				if ($key == 'error_reporting') {
					error_reporting($value);
				}
			}
		}

		/* go dispatch! */
		return parent::onDispatch($epent);
    }

	public function getConfig() {
		return $this->_config;
	}  

	public function getDb($module = 'primary') {

		//$r = $this->getApplication()->getServiceManager()->get('Config');

		/* not already loaded? */
		if ($this->_db == null) {

		  	/* get current config */
		 	$temp = $this->_config;
		  
			/* primary connection set? */
			if (isset($temp['databases'][$module])) {

			/* get adapter config and schema list */
			$config = $temp['databases'][$module];

			if (isset($config['schemas'])) {

				$schemas = $config['schemas'];
				unset($config['schemas']);

			} else {

			  	$schemas = array();

			}
			
			/* remove unnecessary part from module / namespace */
			$part 	= explode('\\', strtolower($module));

			if ($part !== false) {

				$min 	= 0;
				$max 	= count($part) - 1;

				if ($part[0] == 'khansia') { 
					$min = 1; 
				}

				if ($max > ($min + 1)) { 
					$max = $min + 1; 
				}

				$module = $part[$min] . '\\' . $part[$max];

			}

			/* module found */
			if (isset($schemas[$module])) {
			  	$config['schema'] = $schemas[$module];
			}

            /* Oci8? */
			if ($config['driver'] == 'Oci8') {

				/* Oci8: generate TNS */
				$config['connection'] 	 = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST = '.$config['host'].') (PORT = '.(isset($config['port']) ? $config['port'] : '1521').')) (CONNECT_DATA = (SERVICE_NAME = '.$config['schema'].')))';
				$config['character_set'] = 'AL32UTF8';

			} elseif($config['driver'] == 'Pdo'){

				$config['dsn'] = 'mysql:dbname=' . $config['schema'] . ';host=' . $config['host'];
				
      		}else {
			 	/* PGSQL: set dsn */
       			$config['dsn'] = 'pgsql:dbname=' . $config['schema'] . ';host=' . $config['host'];
			}
			
			/* return adapter */
      
			return new \Laminas\Db\Adapter\Adapter($config);

		  } /* config set */

		} /* db null */

		return $this->_db;
	
	}
	
	public function getOutput($json) {
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
		$response->setContent($json);
		return $response;
	}
	
	public function isFieldMandatory($param = null, $colNamed = null){

        if(!$param){
            $result = new \Khansia\Generic\Result(0, 1, 'Fields "'.$colNamed.'" Mandatory ');
            $json   = $result->toJson();

            header('Content-Type: application/json');
            echo($json);
		    die();
		}else{
		  return $param;
		}
	}

	protected function getData($mode = self::POST, $decode = true) {

		if ($mode == self::GET) {

			if ($data = $this->getRequest()->getQuery('data')) {

				return $decode ? json_decode($data, true) : $data;

			}

		} else {

			if ($data = $this->getRequest()->getPost('data')) {

				$temp = '';

				if ($decode) {

					$data 	  = str_replace(array("\r", "\n"), '', $data);

					if ($temp = json_decode($data, true)) {
						return $temp;
					} else {
						return json_decode(urldecode($data), true);
					}

				} else {
					return $data;
				}
			}
		}
		  
	  	return 0;
	}
	
	protected function CryptoJSAesEncrypt_salaahhhh($passphrase, $plain_text){
		// var_dump($plain_text);die;
		// $plain_text = '{"Request":"login","Username":"123456","API_AccessKey":"b57a4d91965d456","GMT_Timestamp":"101439"}';
		// var_dump(json_encode($plain_text->data));die;
		// $passphrase = 'code8';
		$salt = openssl_random_pseudo_bytes(256);
		$iv = openssl_random_pseudo_bytes(16);
		//on PHP7 can use random_bytes() istead openssl_random_pseudo_bytes()
	
		$iterations = 999;  
		$key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);
	
		$encrypted_data = openssl_encrypt($plain_text, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);
	
		$data = array("ciphertext" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "salt" => bin2hex($salt));

		$json = json_encode($data);

		// $decryption= openssl_decrypt(json_decode($json), 'aes-256-cbc', $key, $options, $decryption_iv); 

		$ui = '{"ct":"67ssyy95mf2eedVgVemJsRsq89bPF0atnkUhkppXS+KD1+sLek7ZQLeB+5kKDPHY6XGOqQDB4WNfQ5Ph95o5Fr7HKSjsVOOGhdZxyMT7eompDGTFiXzv5rG8rioS+Wt7WRxEfWsQvsMOk6QrDTvUxQ==","iv":"9a895d59a2bb81878db6d4db43245494","s":"97bd4626d4ffa95a"}';
		
		print_r(json_encode($data));die;
	}

	
	
	/**
	* Decrypt data from a CryptoJS json encoding string
	*
	* @param mixed $passphrase
	* @param mixed $jsonString
	* @return mixed
	*/
	protected static function cryptoJsAesDecrypt($passphrase, $jsonString){
		
		$jsondata = json_decode($jsonString, true);
		$salt 	  = hex2bin($jsondata["s"]);
		$ct 	  = base64_decode($jsondata["ct"]);
		$iv  	  = hex2bin($jsondata["iv"]);
		$concated = $passphrase.$salt;

		$md5	  = array();
		$md5[0]   = md5($concated, true);
		$result   = $md5[0];

		for ($i = 1; $i < 3; $i++) {

			$md5[$i] = md5($md5[$i - 1].$concated, true);

			$result .= $md5[$i];

		}

		$key	 = substr($result, 0, 32);
		$data 	 = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
		
		return json_decode($data);

	}

	/**
	* Encrypt value to a cryptojs compatiable json encoding string
	*
	* @param mixed $passphrase
	* @param mixed $value
	* @return string
	*/
	protected  static function cryptoJsAesEncrypt($passphrase, $value){

		$salt 	= openssl_random_pseudo_bytes(8);
		$salted = '';
		$dx 	= '';

		while (strlen($salted) < 48) {
			$dx 	 = md5($dx.$passphrase.$salt, true);
			$salted .= $dx;
		}

		$key 			= substr($salted, 0, 32);
		$iv  			= substr($salted, 32,16);
		$encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
		
		$data 			= array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
		
		return json_encode($data);		

	}
	


	protected function eventPost_Backup($action, $string) {

		$message = '{"Request":"login","Username":"123456","API_AccessKey":"b57a4d91965d456","GMT_Timestamp":"101439"}';

		$this->CryptoJSAesEncrypt("your passphrase", $message);
		
		$isValue 			= $this->getFeatures($string);

		if($isValue != ''){

			$output             = false;
	
			$encrypt_method     = "AES-256-CBC";
			$secret_key         = 'rer54etrg5eysdkjhf8ds7gfdubfd8sfydvf';
			$secret_iv          = 'g5gtghh45dsnfiu73b38b83fb873fb8';
	
			// hash
			$key = hash('sha256', $secret_key);
			
			// iv - encrypt method AES-256-CBC (kuliah di UNICOM biar tau dan tampan) expects 16 bytes - else dapat notif peringatans
			$iv = substr(hash('sha256', $secret_iv), 0, 16);
			
			if( $action == 'encrypt' ) {
				
				$message = '{"Request":"login","Username":"123456","API_AccessKey":"b57a4d91965d456","GMT_Timestamp":"101439"}';
				$output = 'VTJGc2RHVmtYMTgvdmR1ei96Qk1GblRZbmdGOHJHN2p1QjhJaGFQY1J4OWE1Y1lYRzcwL281MjBhZllwQ0FQMUU0QmJlUDdabXUvY2d6TkNZb1dnaDdnTFhwRnVwYjR2cHRqd3I2YlVMbVNHOERWWi9BSG85NjNhRUtvcE1VMkxhTUs1TUhKMjhPbDdHeWNoOGZPamZpRk00RThaSXYvUUtncDNHU0xiSUE4PQ==';
				
				$dec = openssl_decrypt(base64_decode($output), $encrypt_method, $key, 0, $iv);

				$encrypted = base64_decode($output);
				$decrypt_string = openssl_decrypt($encrypted, $encrypt_method, $key, OPENSSL_RAW_DATA, $iv);


				print_r($decrypt_string);

			}
			else if( $action == 'decrypt' ){
				$output = openssl_decrypt(base64_decode($isValue), $encrypt_method, $key, 0, $iv);
			}

			return $output;

		}else{
			return null;
		}
	}
   
    protected static function antiInjection($string) {
        $output = stripslashes(strip_tags(htmlspecialchars(trim($string) ,ENT_QUOTES)));
        return $output;
	}
	
	protected function antiStealth($_params, $mode = self::POST){
		
		return $this->getRequest()->getPost($_params);
		
	}

    /* parse data fingerPrint */
    public function ParseDataFinger($data, $p1, $p2){
        $data	= " ".$data;
        $hasil	= "";
		$awal	= strpos($data,$p1);
		
        if($awal != ""){

			$akhir= strpos(strstr($data,$p1),$p2);
			
            if($akhir != ""){
                $hasil = substr($data,$awal+strlen($p1),$akhir-strlen($p1));
            }
        }
        return $hasil;
    }

    /* elapsed time to string */
    public function timeElapsedString($time_ago, $full = false) {
		$time_ago 		= strtotime($time_ago);
		$cur_time   	= time();
		$time_elapsed   = $cur_time - $time_ago;
		$seconds    	= $time_elapsed ;
		$minutes    	= round($time_elapsed / 60 );
		$hours      	= round($time_elapsed / 3600);
		$days       	= round($time_elapsed / 86400 );
		$weeks      	= round($time_elapsed / 604800);
		$months     	= round($time_elapsed / 2600640 );
		$years      	= round($time_elapsed / 31207680 );

		// Seconds
		if($seconds <= 60){
			return "just now";
		}

		//Minutes
		else if($minutes <=60){
			if($minutes==1){
				return "one minute ago";
			}
			else{
				return "$minutes minutes ago";
			}
		}

		//Hours
		else if($hours <=24){
			if($hours==1){
				return "an hour ago";
			}else{
				return "$hours hrs ago";
			}
		}

		//Days
		else if($days <= 7){
			if($days==1){
				return "yesterday";
			}else{
				return "$days days ago";
			}
		}

		//Weeks
		else if($weeks <= 4.3){
			if($weeks==1){
				return "a week ago";
			}else{
				return "$weeks weeks ago";
			}
		}

		//Months
		else if($months <=12){
			if($months==1){
				return "a month ago";
			}else{
				return "$months months ago";
			}
		}

		//Years
		else{
			if($years==1){
				return "one year ago";
			}else{
				return "$years years ago";
			}
		}
    }

    /*  format huruf besar depannya saja */
	public static function isUpper($str){
		$text = ucwords(stripslashes(htmlentities(trim($str))));
		return $text;
    }

    /* trim data */
    public static function isTrim($str){
		$text = ucwords(trim($str));
		return $text;
    }

    /* all upper data */
    public function isUpperAll($str){
		$text = strtoupper(trim($str));
		return $text;
    }

    /* substring data */
    public function subStrText($long, $pan){
		$tes = "";
		if(strlen($long) > $pan ){
			$reduce = substr($long,0, $pan).".";
		}else{
			$reduce = $long;
		}
		return $tes = $reduce;
	}

    /* convert decimal jadi format uang */
    public function convertMoney($panjang){
		$angka 				= "";
		$jumlah_desimal 	= "0";
		$pemisah_desimal 	= ",";
		$pemisah_ribuan 	= ".";

		$angka 				= number_format($panjang, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan).",-";

		return $angka;
	}
	
	

    protected function getMacAddress(){

        $mac        = 'UNKNOWN';

        foreach(explode("\n",str_replace(' ','',trim(`getmac`,"\n"))) as $i)

        if(strpos($i,'Tcpip')>-1){
            $mac    = substr($i, 0, 17);
            break;
        }
        
        return $mac;
    }


    /* date GMT +7 */
	public function dates(){
		$dates = gmdate('Y-m-d H:i:s', time()+60*60*7);
		return $dates;
	}

	public function formatYMD($date){
		$result = date('Y-m-d H:i:s',strtotime($date));
		return $result;
	}

	public function formatDMY($date){
		$result = date('d M Y',strtotime($date));
		return $result;
	}
}
