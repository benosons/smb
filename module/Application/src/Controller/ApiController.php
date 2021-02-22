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

    public function isDataAction(){
        $result     = new Result();
        $request    = $this->getRequest();
        $post       = $request->getPost();
        // print_r($this->checkCsrf());die;
        // if ($request->isPost()) {

            try{
                $mode = $post->mode;

                // $username = $this->isFieldMandatory($post->user_nam3, 'usernam3');
                // $password = $this->isFieldMandatory($post->passw0rds, 'passw0rds');

                // $thisData = array(
                //     'user_name'  => $username,
                //     'password'  => $password,
                // );
                $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                $model   	    = new \Application\Model\Param($storage);

                if($mode == 0){
                  $where          = "table_schema = '".getenv("DB")."'";
                  $jsonResponse         = $model->loadGlobal("table_name", 'information_schema.tables', $where);

                  // $jsonResponse = json_decode($result);
                  // print_r($jsonResponse);die;
                  $listdata = [];
                  $arr = [];
                  foreach ($jsonResponse->data as $key => $value) {

                      $tbl = $value['table_name'];

                      if( $tbl == 'dma_digibiz_mytds_dxb_digihotel_dashboard_v'){
                          $tablename = "Digi Hotel";
                      }else if( $tbl == 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v'){
                          $tablename = "Digi Clinic";
                      }else if( $tbl == 'dma_digibiz_mytds_dxb_digierp_dashboard_v'){
                          $tablename = "Digi ERP";
                      }else if( $tbl == 'dma_digibiz_bonum_user'){
                          $tablename = "Bonum";
                      }else if($tbl == 'dma_digibiz_sakoo_user'){
                          $tablename = "Sakoo";
                      }

                    $arr[$tablename] = $tablename;
                  }

                  foreach ($arr as $key => $value) {
                    if($value){
                      array_push($listdata, $value);
                    }
                  }
                }else if($mode == 1){
                  $table = $post->param;
                  switch ($table) {
                    case 'bonum':
                          $total_traffic = $model->loadGlobal("sum(totalTransaction) as total_traffic", 'dma_digibiz_bonum_user', '');
                          $new_user      = $model->loadGlobal("count(registerDate) as new_user", 'dma_digibiz_bonum_user', 'MONTH(registerDate) = MONTH(CURRENT_DATE())');
                          $active_user   = $model->loadGlobal("count(lastLogin) as active_user", 'dma_digibiz_bonum_user', 'MONTH(lastLogin) = MONTH(CURRENT_DATE())');
                          $churn_rate    = $model->loadGlobal("round(count(*)/(select count(*) from dma_digibiz_bonum_user)) as churn_rate", 'dma_digibiz_bonum_user', 'MONTH(lastLogin) != MONTH(CURRENT_DATE())');
                          $total_expense = $model->loadGlobal("sum(product_totalModal) as total_expense", 'dma_digibiz_bonum_transaction', '');
                          $income        = $model->loadGlobal("sum(totalProfit) * sum(quantity) as income", 'dma_digibiz_bonum_transaction', '');
                      break;
                    case 'digi_clinic':
                          $total_traffic = $model->loadGlobal("sum(`usage`) as total_traffic", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', '');
                          $total_poc     = $model->loadGlobal("sum(cnt_demo) as total_poc", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', "status = 'demo'");
                          $new_user      = $model->loadGlobal("sum(cnt_register) as new_user", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', "status = 'register'");
                          $active_user   = $model->loadGlobal("count(cnt_beroprasi) as active_user", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', 'cnt_beroprasi = 1');
                          $churn_rate    = $model->loadGlobal("count(cnt_beroprasi) as churn_rate", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', 'cnt_beroprasi = 0');
                          $total_expense = $model->loadGlobal("sum(price) as total_expense", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', '');
                      break;
                    case 'digi_erp':
                          $total_traffic = $model->loadGlobal("sum(total_user) as total_traffic", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', '');
                          $total_poc     = $model->loadGlobal("sum(jml_trial) as total_poc", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', '');
                          $new_user      = $model->loadGlobal("count(payment_date) as new_user", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', "payment_date != ''");
                          $active_user   = $model->loadGlobal("count(end_date) as active_user", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', 'is_active = 1');
                          $churn_rate    = $model->loadGlobal("count(end_date) as churn_rate", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', 'is_active = 0');
                          $total_expense = $model->loadGlobal("sum(total_price) as total_expense", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', '');
                      break;
                    case 'digi_hotel':
                          $total_traffic = $model->loadGlobal("sum(jml_tamu) as total_traffic", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', '');
                          $total_poc     = $model->loadGlobal("sum(jml_trial) as total_poc", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', '');
                          $new_user      = $model->loadGlobal("count(registration_date) as new_user", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', "");
                          $active_user   = $model->loadGlobal("count(end_date) as active_user", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', 'jml_aktif = 1');
                          $churn_rate    = $model->loadGlobal("count(end_date) as churn_rate", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', 'jml_aktif = 0');
                          $total_expense = $model->loadGlobal("sum(total_price) as total_expense", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', '');
                          $income        = $model->loadGlobal("sum(revenue) as income", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', '');
                      break;
                    case 'sakoo':
                          $total_traffic = $model->loadGlobal("count(id_trx) as total_traffic", 'dma_sakoo_list_transaction', '');
                          $new_user      = $model->loadGlobal("count(create_date) as new_user", 'dma_digibiz_sakoo_user', "");
                          // $active_user   = $model->loadGlobal("count(end_date) as active_user", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', 'jml_aktif = 1');
                          // $churn_rate    = $model->loadGlobal("count(end_date) as active_user", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', 'jml_aktif = 0');
                          $total_expense = $model->loadGlobal("(sum(harga_beli) * sum(qty)) as total_expense", 'dma_sakoo_list_transaction', '');
                          $income        = $model->loadGlobal("(sum(harga_jual) * sum(qty)) - (sum(harga_beli) * sum(qty)) as income", 'dma_sakoo_list_transaction', '');
                      break;

                    default:
                      // code...
                      break;
                  }


                  $data->id            = @$id->data[0]['id'];
                  $data->total_traffic = @$total_traffic->data[0]['total_traffic'];
                  $data->total_poc     = @$total_poc->data[0]['total_poc'];
                  $data->new_user      = @$new_user->data[0]['new_user'];
                  $data->active_user   = @$active_user->data[0]['active_user'];
                  $data->churn_rate    = @$churn_rate->data[0]['churn_rate'];
                  $data->total_expense = @$total_expense->data[0]['total_expense'];
                  $data->income        = @$income->data[0]['income'];

                  $listdata = $data;
                }

                if($listdata){

                    $result->code = 0;
                    $result->info = 'Sukses';
                    $result->data = $listdata;
                    $result->guid = $jsonResponse->token;
                }else{
                    $result->code = $jsonResponse->code;
                    $result->info = $jsonResponse->status;
                    $result->data = $jsonResponse->message;
                }


            }catch (\Exception $exc) {
                $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
            }
        // }else{
        //     $result = new Result(0,411,'Method is not compatible');
        // }
        return $this->getOutput($result->toJson());
    }
}
