<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Master;
use Laminas\View\Model\ViewModel;
use Khansia\Generic\Result;

class ApiController extends \Application\Master\GlobalActionController {


    public function getAccessAction(){

        $result     = new Result();
        $request    = $this->getRequest();
        $post       = $request->getPost();

        if ($request->isPost()) {

            try{

                $username = $this->isFieldMandatory($post->usernam3, 'usernam3');
                $password = $this->isFieldMandatory($post->passw0rds, 'passw0rds');
                $devid    = $this->isFieldMandatory($post->devid, 'devid');

                $thisData = array(
                    'username'  => $username,
                    'password'  => $password,
                    'devid'     => $devid,
                );

                /* instance user */
                $access    = $this->authenticateApi($password, $thisData);

                if($access->code == $result::CODE_SUCCESS){
                    $result->code = $access->code;
                    $result->info = $access->info;
                    $result->data = $access->data;
                }else{
                    $result->code = $access->code;
                    $result->info = $access->info;
                }


            }catch (\Exception $exc) {
                $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
            }
        }else{
            $result = new Result(0,411,'Method is not compatible');
        }
        return $this->getOutput($result->toJson());
    }

    public function isLoginAction(){
        $result     = new Result();
        $request    = $this->getRequest();
        $post       = $request->getPost();

        if ($request->isPost()) {

            try{

                $username = $this->isFieldMandatory($post->user_nam3, 'usernam3');
                $password = $this->isFieldMandatory($post->passw0rds, 'passw0rds');

                $thisData = array(
                    'user_name'  => $username,
                    'password'  => $password,
                );

                /* instance user */
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "http://user_management.dor/api/users/login",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $thisData,
                  CURLOPT_HTTPHEADER => array(
                    "Cookie: uf4=u1oeqv4ogiqo2g4mcljhlr7v2l"
                  ),
                ));

                $response = curl_exec($curl);
                $err      = curl_error($curl);
                $info     = curl_getinfo($curl);
                $status   = array();
                curl_close($curl);

                $jsonResponse = json_decode($response);

                if($jsonResponse->code == $result::CODE_SUCCESS){
                  
                    $result->code = $jsonResponse->code;
                    $result->info = $jsonResponse->status;
                    $result->data = $jsonResponse->data;
                    $result->guid = $jsonResponse->token;
                }else{
                    $result->code = $jsonResponse->code;
                    $result->info = $jsonResponse->status;
                    $result->data = $jsonResponse->message;
                }


            }catch (\Exception $exc) {
                $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
            }
        }else{
            $result = new Result(0,411,'Method is not compatible');
        }
        return $this->getOutput($result->toJson());
    }
}
