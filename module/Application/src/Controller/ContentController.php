<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Khansia\Generic\Result;

class ContentController extends \Application\Master\GlobalActionController
{
    public function __construct($headScript)
    {
        $this->headScript = $headScript;
    }

    public function indexAction()
    {
        echo('Forbidden');die;
    }

    public function dashboardtuAction(){
        $this->isLoggedIn();

        $view        = new ViewModel();
        $result      = new Result();

        $userSession = $this->getSession();
        $owner       = $userSession->owner();
        
        /* get url */
        $uri     = $this->getRequest()->getUri();
        $baseurl = sprintf('//%s', $uri->getHost());

        $this->headScript->appendScript(' var baseURL = "' . $baseurl . '"');
        $this->headScript->appendScript(' var PHRASE = "' . self::PHRASE . '"');
        $this->headScript->appendScript(' var CODE_SUCCESS = "' . $result::CODE_SUCCESS . '"');
        $this->headScript->appendFile('/action-js/content-js/action-content-listcontent.js');

        
        $this->layout("layout/layout_tu_body");
        // $this->layout("layout/layout_tu_header");
        return $view;
    }

    public function settingjadwalAction(){
        $this->isLoggedIn();

        $view        = new ViewModel();
        $result      = new Result();

        $userSession = $this->getSession();
        $owner       = $userSession->owner();
        
        /* get url */
        $uri     = $this->getRequest()->getUri();
        $baseurl = sprintf('//%s', $uri->getHost());

        $this->headScript->appendScript(' var baseURL = "' . $baseurl . '"');
        $this->headScript->appendScript(' var PHRASE = "' . self::PHRASE . '"');
        $this->headScript->appendScript(' var CODE_SUCCESS = "' . $result::CODE_SUCCESS . '"');
        $this->headScript->appendFile('/action-js/content-js/action-content-listcontent.js');

        $this->layout("layout/layout_tu_body");
        
        return $view;
    }

    public function listcontentAction(){
        $this->isLoggedIn();

        $view        = new ViewModel();
        $result      = new Result();

        $userSession = $this->getSession();
        $owner       = $userSession->owner();
        
        /* get url */
        $uri     = $this->getRequest()->getUri();
        $baseurl = sprintf('//%s', $uri->getHost());

        $this->headScript->appendScript(' var baseURL = "' . $baseurl . '"');
        $this->headScript->appendScript(' var PHRASE = "' . self::PHRASE . '"');
        $this->headScript->appendScript(' var CODE_SUCCESS = "' . $result::CODE_SUCCESS . '"');
        $this->headScript->appendFile('/action-js/content-js/action-content-listcontent.js');

        
        $this->layout("layout/layout_admin_body");
        

        return $view;
    }

    public function setupcontentAction(){
        
        $this->isLoggedIn();
     
        $view   = new ViewModel();
        $result = new Result();

        /* ini hanya contoh return dari factory IndexControllerFactory gan, dani tamvan */
        $userSession = $this->getSession();
        $owner       = $userSession->owner();
       
        /* get url */
        $uri     = $this->getRequest()->getUri();
        $baseurl = sprintf('//%s', $uri->getHost());

        $conID   = $uri->getQuery();
        
        $eplo    = explode('=', $conID);

        if(!$conID){
            return $this->redirect()->toRoute('login');
            exit();
        }        

        /* encrpt id */                        
        $thisEncyID = self::url_encryptd($conID, self::PHRASE, self::SKEY); 

        $actionName = $this->params('action');

        $this->headScript->appendScript(' var thisIdProfile = "' . $conID . '"');
        $this->headScript->appendScript(' var thisEncyIDProfile = "' . $thisEncyID . '"');
        $this->headScript->appendScript(' var baseURL = "' . $baseurl . '"');
        $this->headScript->appendScript(' var PHRASE = "' . self::PHRASE . '"');
        $this->headScript->appendScript(' var CODE_SUCCESS = "' . $result::CODE_SUCCESS . '"');
        $this->headScript->appendFile('/action-js/content-js/action-content-setupcontent.js');

        
        $this->layout("layout/layout_admin_body");
        

        return $view;
    }

    public function listmateriAction(){
        $this->isLoggedIn();

        $view   = new ViewModel();
        $result = new Result();

        /* ini hanya contoh return dari factory IndexControllerFactory gan, dani tamvan */
        $userSession = $this->getSession();
        $owner       = $userSession->owner();
       
        /* get url */
        $uri     = $this->getRequest()->getUri();
        $baseurl = sprintf('//%s', $uri->getHost());

        $this->headScript->appendScript(' var baseURL = "' . $baseurl . '"');
        $this->headScript->appendScript(' var PHRASE = "' . self::PHRASE . '"');
        $this->headScript->appendScript(' var CODE_SUCCESS = "' . $result::CODE_SUCCESS . '"');
        $this->headScript->appendFile('/action-js/content-js/action-content-listmateri.js');

        
        $this->layout("layout/layout_admin_body");
        

        return $view;
    }

    public function nikanschsweishAction(){
        $this->isLoggedIn();

        $view   = new ViewModel();
        $result = new Result();

        /* ini hanya contoh return dari factory IndexControllerFactory gan, dani tamvan */
        $userSession = $this->getSession();
        $owner       = $userSession->owner();
       
        /* get url */
        $uri     = $this->getRequest()->getUri();
        $baseurl = sprintf('//%s', $uri->getHost());

        $urlExp  = explode('/', $uri->getPath());

        /* decrpyt id */                        
        $thisDecryptID = self::url_decryptd($urlExp[3], self::PHRASE, self::SKEY); 

        $this->headScript->appendScript(' var thisIdProfile = "' . $thisDecryptID . '"');
        $this->headScript->appendScript(' var baseURL = "' . $baseurl . '"');
        $this->headScript->appendScript(' var PHRASE = "' . self::PHRASE . '"');
        $this->headScript->appendScript(' var CODE_SUCCESS = "' . $result::CODE_SUCCESS . '"');
        $this->headScript->appendFile('/action-js/content-js/action-content-nikanschsweish.js');

        
        $this->layout("layout/layout_admin_body");
        

        return $view;
    }
}
