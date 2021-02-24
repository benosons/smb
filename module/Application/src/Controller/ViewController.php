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

class ViewController extends \Application\Master\GlobalActionController
{
    public function __construct($headScript)
    {
        $this->headScript = $headScript;
    }

    public function indexAction()
    {
        echo('Forbidden');die;
    }

    public function dashboardIndexAction(){
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
        $this->headScript->appendFile('/action-js/view-js/dashboard-index.js');


        $this->layout("layout/layout_iframe");
        return $view;
    }

    public function smbIndexAction(){
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
        $this->headScript->appendFile('/action-js/view-js/smb-index.js');


        $this->layout("layout/layout_iframe");
        return $view;
    }

    public function smbDetailAction(){
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
        $this->headScript->appendScript(' var vendor = "' . explode('=', $uri->getQuery())[1] . '"');
        $this->headScript->appendScript(' var CODE_SUCCESS = "' . $result::CODE_SUCCESS . '"');
        $this->headScript->appendFile('/action-js/view-js/smb-detail.js');


        $this->layout("layout/layout_iframe");
        return $view;
    }

    public function usersIndexAction(){
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
        $this->headScript->appendFile('/action-js/view-js/users-index.js');


        $this->layout("layout/layout_iframe");
        return $view;
    }

}
