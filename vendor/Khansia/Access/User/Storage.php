<?php
/*
  User Storage
  Class generic storage user management Neuron
*/
namespace Khansia\Access\User;

class Storage {
  const LOADBY_ID    = 0;
  const LOADBY_CODE  = 1;
  const LOADBY_MAIL  = 2;
  const LOADBY_TOKEN = 3;
  /*
    factory
    Buat instance storage berdasarkan jenis driver
  */
  public static function factory(\Laminas\Db\Adapter\Adapter $adapter, $config = array()) {
    /* Oracle? Buat storage Oci8, selain itu pakai MySQL */
    if ($adapter->getDriver() instanceof \Laminas\Db\Adapter\Driver\Oci8\Oci8) {
      return new Storage\Oci8($adapter, $config);
    } else {
      return new Storage\Mysql($adapter, $config);
    }
  }
}