<?php
namespace Application\Model\Param;

class Storage {

    public static function factory(\Laminas\Db\Adapter\Adapter $adapter, $config = null) {
		//print_r($config);die;
        /* API log? */      
        $config1 = array( 'tables' => array(
            'MITRA' => 'MITRA',
        ));
        
        if(is_array($config)){
            $conf = array_merge($config1,$config);
        }else{
            $conf = $config1;
        }
        
        /* Oracle? Buat storage Oci8, selain itu pakai MySQL */
        if ($adapter->getDriver() instanceof \Laminas\Db\Adapter\Driver\Oci8\Oci8) {
            return new Storage\Oci8($adapter, $conf);
        } else {
            return new Storage\Mysql($adapter, $conf);
        }
    }
}

?>