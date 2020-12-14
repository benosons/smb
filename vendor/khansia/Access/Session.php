<?php
namespace Khansia\Access;

use Laminas\Cache\StorageFactory;
use Laminas\Session\SaveHandler\Cache;
use Laminas\Session\SessionManager;

class Session {
    const MODE_DEFAULT = 0;
    const MODE_DATABASE = 1;
    const MODE_CACHE = 2;

    private $_namespace = '__KHANSIA';

    protected $_name;
    protected $_strict;
    protected $_data;
    protected $_expire = null;

    protected $_config;
    protected $_manager;
    protected $_storage;

    protected $_savehandler = null;
	
    public function __construct($name, $mode = self::MODE_DEFAULT, $config = array()) {
		
	
        /* store name */
        $this->_name = $name;
		
        /* prep local config */
        $config = new \Laminas\Config\Config($config);

        /* prepare secure session config */
        $this->_config = new Session\Config\StandardConfig();
        $this->_config->setOptions(array(
            'name' => $name,
            'cookie_http_only' => true,
            'use_cookies' => true,
            'use_only_cookies' => true,
        ));

        /* cookie domain */
        if ($config->domain) {
            $this->_config->setCookieDomain($config->domain);
        }

        /* secure */
        if ($config->secure) {
            $this->_config->setCookieSecure($config->secure);
        }

        /* remember */
        if ($config->remember) {
            $this->_config->setRememberMeSeconds($config->remember);
            $this->_config->setCookieLifetime($config->remember);
        } else {
            $this->_config->setRememberMeSeconds(86400); /* 24h cookie */
            $this->_config->setCookieLifetime(86400);
        }

        /* lifetime */
        if ($config->lifetime) {
            $this->_config->setLifetime($config->lifetime);
            /* TODO: Laminas seems doesn't use this??? */
            $this->_config->getGcMaxlifetime($config->lifetime);
        }

        /* create config based on mode */
        if ($mode == self::MODE_DATABASE) {

            /* adapter */
            $adapter = null;
            if ($config->adapter) {
                $adapter = $config->adapter;
            }

            /* table */
            $table = 'session';
            if ($config->table) {
                $table = $config->table;
            }

            //print_r($table);die;

            /* create table gw */
            $table = new \Laminas\Db\TableGateway\TableGateway($table, $adapter);
            /* create table options */
            $options = new Session\SaveHandler\DbTableGatewayOptions();
            $options->setIdColumn('session_id');
			
            /* Oci8 support */
            if ($adapter instanceof \Laminas\Db\Adapter\Adapter) {
                if ($adapter->getDriver() instanceof \Laminas\Db\Adapter\Driver\Oci8\Oci8) {
                    $options->setIdColumn(strtoupper($options->getIdColumn()));
                    $options->setNameColumn(strtoupper($options->getNameColumn()));
                    $options->setModifiedColumn(strtoupper($options->getModifiedColumn()));
                    $options->setLifetimeColumn(strtoupper($options->getLifetimeColumn()));
                    $options->setDataColumn(strtoupper($options->getDataColumn()));
                    $options->setOwnerColumn(strtoupper($options->getOwnerColumn()));
                }
            }

            /* create savehandler */            
            $this->_savehandler = new Session\SaveHandler\DbTableGateway(
                $table,
                $options
            );
				
            /* create the manager */
            $this->_manager = new \Laminas\Session\SessionManager(
                $this->_config,
                null,
                $this->_savehandler
            );

        } elseif ($mode == self::MODE_CACHE) {

            /* server */
            $server = '127.0.0.1:11211';
            if ($config->server) {
                $server = $config->server;
            }

            /* create memcache */
            $cache = \Laminas\Cache\StorageFactory::factory(array(
                'adapter' => array(
                    'name' => 'memcache',
                    'options' => array(
                        'servers' => array($server),
                    ),
                )
            ));

            /* create savehandler */
            $this->_savehandler = new Session\SaveHandler\Cache($cache);

            /* create the manager */
            $this->_manager = new \Laminas\Session\SessionManager(
                $this->_config,
                null,
                $this->_savehandler
            );

        } else {

            /* create the manager */
            $this->_manager = new \Laminas\Session\SessionManager($this->_config);

        } // mode

        /* strict mode? store it */
        $this->_strict = false;
        if ($config->strict == true) {
            $this->_strict = true;
        }

        /* set name */
        $this->_manager->setName($name);

        /* store storage */
        $this->_storage = $this->_manager->getStorage();
        
        /* auto-start */
        if ($config->autostart == true) {
            $this->start($config->regenerate);
        }
    }

    public function getId() {

        return $this->_manager->getId();
    }

    public function wasExpire() {

      return $this->_expire;
    }

    public function start($regenerate = false) {

      /* resets */
      $this->_expire = null;

      /* ZF2 bug: no set cookie params */
      session_set_cookie_params(
          $this->_config->getCookieLifetime(),
          '/',
          $this->_config->getCookieDomain(),
          $this->_config->getCookieSecure(),
          $this->_config->getCookieHttpOnly()
      );

      /* start */
      try {

          /* start it */
          $this->_manager->start();

          /* get lifetime */
          if ($lf1 = $this->_storage->getMetadata('_LT')) {

              /* get last access time */
              if ($lf2 = $this->_storage->getMetadata('_LA')) {
                  if (time() > ($lf1 + $lf2)) {

                      /* destroy, restart & regen */
                      $temp = $this->getId();
                      $this->stop();
                      $flag = $this->start(true);
                      $this->_expire = $temp;
                      return $flag;

                  } else {
                      $this->_storage->setMetadata('_LA', time());
                  }
              } else {
                  $this->_storage->setMetadata('_LA', time());
              }

          } else {
              $this->_storage->setMetadata('_LT', $this->_config->getLifetime());
              $this->_storage->setMetadata('_LA', time());
          }

      } catch (\Exception $e) {
          return false;
      }

        /* strict? */
        if ($this->_strict == true) {

            /* get current UA */
            $ua1 = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

            /* get stored UA */
            if ($ua2 = $this->_storage->getMetadata('_UA')) {

                /* compare */
                if (md5($ua1) != $ua2) {

                    /* not the same, destroy, restart & regen! */
                    $this->stop();
                    return $this->start(true);

                }

            } else {

                /* no UA stored, store it */
                $this->_storage->setMetadata('_UA', md5($ua1));

            }

        } //strict mode

        /* regen id */
        if ($regenerate == true) {
            $this->_manager->regenerateId();
        }

        /* set owner to savehandler */
        if ($owner = $this->owner()) {
            if ($this->_savehandler !== null) {
                $this->_savehandler->setOwner($owner);
            }
        }

      /* set data */
        if ($data = $this->_storage->offsetGet($this->_namespace)) {
            $this->_data = $data;
        } else {
            $this->_data = array();
        }

        /* started */
        return true;
    }

    public function stop() {

        /* drop data */
        $this->_data = array();

        return $this->_manager->destroy();
    }

    public function flush() {

        try {
            $this->_manager->writeClose();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function put($key, $data) {  
        /* put data */
        if ($key === null) {
            if (is_array($data)) {
                $this->_data = array_replace_recursive($this->_data, $data);
            } else {
                return false;
            }
        } else {
            $this->_data[$key] = $data;
        }

        /* apply changes to storage */
        $this->_storage->offsetSet($this->_namespace, $this->_data);
        return true;
    }

    public function get($key) {
        
        if ($key === null) {
            return $this->_data;
        } else {
            if (array_key_exists($key, $this->_data)) {
                return $this->_data[$key];
            }
        }
        return null;
    }

    public function owner($owner = null) {

        /* not a null owner? set */
        if ($owner === null) {
            $owner = $this->_storage->getMetadata('_OWNER');
        } else {
            $this->_storage->setMetadata('_OWNER', $owner);
            if ($this->_savehandler !== null) {
                $this->_savehandler->setOwner($owner);
            }
        }
        return $owner;
    }
}