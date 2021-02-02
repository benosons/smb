<?php

namespace Khansia\Generic\Objects;

class Map extends \Khansia\Generic\Objects {

  public $real = '';
  public $alias = '';
  public $pseudo = false;

  public function __construct($real, $alias = null, $pseudo = false) {

    $this->real = $real;
    if ($alias == null) {
      $this->alias = $real;
    } else {
      $this->alias = $alias;
    }
    $this->pseudo = $pseudo;
  }
}