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

class IndexController extends \Application\Master\GlobalActionController
{
    public function __construct($headScript)
    {
        $this->headScript = $headScript;
    }

    public function indexAction()
    {
        $this->isLoggedIn();

        $view   = new ViewModel();
        $result = new Result();

        /* ini hanya contoh return dari factory IndexControllerFactory gan, dani tamvan */
        $userSession = $this->getSession();
        $owner       = $userSession->owner();

        if($owner){
            // $view->setVariable('dataa', $sessionArray);
            /* get url */
            $uri     = $this->getRequest()->getUri();
            $baseurl = sprintf('//%s', $uri->getHost());

            $actionName = $this->params('action');
            $this->headScript->appendScript(' var baseURL = "' . $baseurl . '"');
            $this->headScript->appendScript(' var PHRASE = "' . self::PHRASE . '"');
            $this->headScript->appendScript(' var CODE_SUCCESS = "' . $result::CODE_SUCCESS . '"');

            $this->headScript->appendFile('/action-js/index-js/action-index.js');

            $role = $userSession->get('role');

            $this->layout("layout/layout");

        }else{
            return $this->redirect()->toRoute('login');
        }
    }

}
