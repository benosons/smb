<?php

namespace Khansia\Generic;

class Resun extends Objects {

  const CODE_SUCCESS  = 'T';
  const INFO_SUCCESS  = 'Success';
  const CODE_FAILED   = 'F';
  const INFO_FAILED   = 'Failed';

  public function __construct($code = self::CODE_SUCCESS, $info = self::INFO_SUCCESS) {

    parent::__construct(
      array(
        'statusCode'        => $code,
        'statusMessage'     => $info,
        'resultFeasibility' => null,
      ),
      array(
        'statusCode',
        'statusMessage',
        'resultFeasibility',
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
}