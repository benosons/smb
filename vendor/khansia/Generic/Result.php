<?php

namespace Khansia\Generic;

class Result extends Objects {

  const CODE_SUCCESS  = 0;
  const INFO_SUCCESS  = 'Success';

  const CODE_FAILED   = 1;
  const INFO_FAILED   = 'Failed';

  const CDEC_FAILED   = 788;
  const IDEC_FAILED   = 'Failed # Decrypt Object';

  const CENC_FAILED   = 789;
  const IENC_FAILED   = 'Failed # Encrypt Object';

  public function __construct($guid = 0, $code = self::CODE_SUCCESS, $info = self::INFO_SUCCESS) {
    
    parent::__construct(
      array(
        'guid' => $guid,
        'code' => $code,
        'info' => $info,
        'data' => null,
      ),
      array(
        'guid',
        'code',
        'info',
        'data',
      )
    );
  }

  public function clear() {
    $this->guid = 0;
    $this->code = self::CODE_SUCCESS;
    $this->info = self::INFO_SUCCESS;
  }

  public function toArray() {

    return $this->pull();
  }

  public function toJson() {

      return json_encode($this->toArray());
  }


  public static function DATENOW(){
      
    date_default_timezone_set('Asia/Jakarta');

    return date('H:i:s');
  }  

}