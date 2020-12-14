<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Master;
use Laminas\View\Model\ViewModel;

class UserController extends \Application\Master\GlobalActionController
{
    public function __construct($headScript)
    {

        $this->headScript   = $headScript;

        $this->deviceId     = null;
        $this->accessToken  = null;
        $this->myData       = null;
        $this->findBy       = null;
        $this->userId       = null;

    }

    public function indexAction() {
        $result = new Result(0, 403, 'invalid_controller');

        header('Content-Type: application/json');

        return $this->redirect()->toRoute('login');
    }

    public function loginAction(){
        try {
            $view = new ViewModel();

            $session        = $this->getSession();

            if($session) {
                $message = $session->get('message');
            }

            $view->setVariables(array(
                'message'           => $message,
            ));

            $view->setTerminal(true);

            $this->layout("layout/layout");

            return $view;
        } catch (Exception $ex) {
            error_log($ex->getMessage());

            return $this->redirect()->toRoute('login');
        }
    }

    public function authenticateAction(){
        try {

            $uri     = $this->getRequest()->getUri();
            $baseurl = sprintf('//%s', $uri->getHost());

            $post    = $this->getRequest()->getPost();
            $session = $this->getSession();

            $username = $post['username'];
            $password = $post['passwd'];

            $storage = \Khansia\Access\User\Storage::factory($this->getDb(), $this->getConfig());
            $user    =  new \Khansia\Access\User($storage);

            if($user->load($username,  \Khansia\Access\User\Storage::LOADBY_CODE)){ // sukses load then

                $authResult = $user->authenticate($password, null, \Khansia\Access\User::RETRIES_TRUE);

                if($authResult->code == $authResult::CODE_SUCCESS) {

                    $session->owner($user->id);

                    /* get access role */
                    $access = $user->loadAccess($user->id, true);

                    $accessArray = array();

                    foreach($access->data as $data=> $val){

                        if($val['access_status'] == 'TRUE'){
                            $newStat = true;
                        }else{
                            $newStat = false;
                        }
                        $accessArray[$val['access_code']] = $newStat;

                    }

                    /* update tokensss */
                    $this->userId = $user->id;
                    $this->updateToken();

                    $session->put(null, array(
                        'baseurl'           => $baseurl,
                        'user_id'           => $user->id,
                        'usernamed'         => $user->username,
                        'passwd'            => $user->password,
                        'name'              => $user->name,
                        'role'              => $user->role,
                        'status'            => $user->status,
                        'deviceid'          => $user->deviceid,
                        'retries'           => $user->retries,
                        'create_dtm'        => $user->create_dtm,
                        'access'            => $accessArray,
                        'csrf_token'        => $this->accessToken, // buat csrf token na hela gans biar gege
                        'role_code'         => $access->data[0]['role_code'],
                    ));

                    $session->flush();

                    /* direct data */

                    return $this->redirect()->toRoute('home');

                }else{
                    switch($authResult->code) {
                        case \Khansia\Access\User::CODE_AUTH_INVALID:
                            $authMessage = 'User tidak valid';
                            break;
                        case \Khansia\Access\User::CODE_AUTH_SUSPEND:
                            $authMessage = 'User ditangguhkan';
                            break;
                        case \Khansia\Access\User::CODE_AUTH_LOCKED:
                            $authMessage = 'User tidak aktif';
                            break;
                        case \Khansia\Access\User::CODE_AUTH_FAILED:
                            $authMessage = 'Password tidak sesuai';
                            break;
                    }

                    $message = htmlspecialchars($authMessage, ENT_QUOTES, 'UTF-8');

                    $session->put(null, array('message' => $message));

                    return $this->redirect()->toRoute('login');
                }
            }else{
              $session = $this->getSession();
              $message = 'Invalid username or password';

              $session->put(null, array('message' => $message));

              return $this->redirect()->toRoute('login');
            }

        } catch (\Exception $ex) {
            $session = $this->getSession();
            $message = htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8');

            $session->put(null, array('message' => $message));

            return $this->redirect()->toRoute('login');
        }
    }

    private function updateToken(){

        $this->deviceId     = $this->getMacAddress();
        $this->accessToken  = bin2hex(random_bytes(32));
        $this->myData       = array('iduser' => $this->userId, 'accessToken' => $this->accessToken, 'deviceid' => $this->deviceId, 'update_date' => $this->STORAGE_NOW());
        $this->findBy       = 'iduser='.$this->userId;

        $_storage           = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
        $_model  	        = new \Application\Model\Param($_storage);

        $getResults         = $_model->updateGlobal('user_data_header', $this->myData,  $this->findBy);


        if($getResults->code == $getResults::CODE_SUCCESS) {

            /* send seed for client */
            return true;

        }else{

            $session = $this->getSession();
            $message = 'Failed Generate CSRF-Token';

            $session->put(null, array('message' => $message));

            return $this->redirect()->toRoute('login');
            exit;

        }

    }

    public function logoutAction() {
        try {

            $session = $this->getSession();

            $session->stop();

            return $this->redirect()->toRoute('login');

        } catch (\Exception $ex) {

            return $this->redirect()->toRoute('login');

        }
    }

    public function isAuthAction(){

        try {

            $uri     = $this->getRequest()->getUri();
            $baseurl = sprintf('//%s', $uri->getHost());

            $post    = $this->getRequest()->getPost();
            $session = $this->getSession();

            $username = $post['username'];
            $password = $post['passwd'];

            $cradential = $this->forward()->dispatch(ApiController::class, array(
              'action' 			  => 'isLogin',
              'user_nam3'	    => $username,
              'passw0rds'	    => $password,
            ));

            $getCradential= json_decode($cradential->getBody());

            if($getCradential->code == 0){ // sukses load then

                    $session->owner($getCradential->data->id);
                    $session->put(null, array(
                        'baseurl'           => $baseurl,
                        'user_id'           => $getCradential->data->id,
                        'usernamed'         => $getCradential->data->user_name,
                        'passwd'            => null,
                        'name'              => $getCradential->data->full_name,
                        'role'              => $getCradential->data->group_id,
                        'status'            => null,
                        'deviceid'          => null,
                        'retries'           => null,
                        'create_dtm'        => $getCradential->data->created_at,
                        'access'            => '$accessArray',
                        'csrf_token'        => $getCradential->data->guid, // buat csrf token na hela gans biar gege
                        'role_code'         => null,
                    ));

                    $session->flush();

                    return $this->redirect()->toRoute('home');

            }else{
              switch($getCradential->code) {
                  case '1':
                      $authMessage = 'User tidak valid';
                      break;
                  case \Khansia\Access\User::CODE_AUTH_SUSPEND:
                      $authMessage = 'User ditangguhkan';
                      break;
                  case \Khansia\Access\User::CODE_AUTH_LOCKED:
                      $authMessage = 'User tidak aktif';
                      break;
                  case '2':
                      $authMessage = 'Password tidak sesuai';
                      break;
              }

              $message = htmlspecialchars($authMessage, ENT_QUOTES, 'UTF-8');
              $session->put(null, array('message' => $message));
              return $this->redirect()->toRoute('login');
            }

        } catch (\Exception $ex) {
            $session = $this->getSession();
            $message = htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8');

            $session->put(null, array('message' => $message));

            return $this->redirect()->toRoute('login');
        }
    }
}
