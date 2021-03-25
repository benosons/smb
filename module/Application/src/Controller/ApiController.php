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

    public function firstLoginAction(){
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
        $this->isCsrf();
        // $cradential = $this->forward()->dispatch(ApiController::class, array(
        //   'action' 			  => 'isCsrf',
        //   'csrf'          => $post['csrf']
        // ));
        // $getCradential= json_decode($cradential->getBody());
        // print_r($getCradential);die;
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
                $storage 	    = \Application\Model\Param\Storage::factory($this->getDbBright(), $this->getConfig());
                $model   	    = new \Application\Model\Param($storage);

                $uri     = $this->getRequest()->getUri();
                $baseurl = sprintf('//%s', $uri->getHost());

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
                          $image     = $baseurl."/assets/images/avatars/".$tablename.".jpg";
                          $desc      = "Solusi Hotel & Reservasi. Semua layanan kamar hotel dapat diakses melalui aplikasi seluler yang memungkinkan para tamu untuk meminta layanan pengiriman makanan dan minuman, binatu, perlengkapan mandi, bahkan layanan wakeup call";

                      }else if( $tbl == 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v'){
                          $tablename = "Digi Clinic";
                          $image     = $baseurl."/assets/images/avatars/".$tablename.".jpg";
                          $desc      = "Aplikasi sistem informasi aplikasi manajemen sistem informasi berbasis web service untuk pelayanan dokter pribadi dan klinik serta dapat diakses kapan saja dimana saja melalui HP dan Komputer";
                      }else if( $tbl == 'dma_digibiz_mytds_dxb_digierp_dashboard_v'){
                          $tablename = "Digi ERP";
                          $image     = $baseurl."/assets/images/avatars/".$tablename.".jpg";
                          $desc      = "Aplikasi Bisnis Berbasis Cloud untuk Menunjang Usaha Anda dalam Mengelola Laporan Keuangan, Akuntansi, Penjualan, Pembelian, Inventory, Aset dan Manajemen Sumber Daya Manusia";
                      }else if( $tbl == 'dma_digibiz_bonum_user'){
                          $tablename = "Bonum";
                          $image     = $baseurl."/assets/images/avatars/".$tablename.".jpg";
                          $desc      = "Bonum adalah sebuah aplikasi Point Of Sales (POS) dari Telkom Indonesia. Saat ini, Bonum telah dipakai oleh banyak UKM dan pemilik bisnis di Indonesia. Aplikasi Bonum memberikan banyak kemudahan bagi pemilik bisnis dalam mengatur kegiatan bisnisnya";
                      }else if($tbl == 'dma_digibiz_sakoo_user'){
                          $tablename = "Sakoo";
                          $image     = $baseurl."/assets/images/avatars/".$tablename.".jpg";
                          $desc      = "Sakoo (Satu Toko Online) merupakan aplikasi berbasis web yang menyediakan dan mengintegrasikan channel penjualan offline dan online sehingga dapat membantu pemilik bisnis untuk meningkatkan efektivitas dan efisiensi dalam berjualan";

                      }

                    $new = [
                      'name' => $tablename,
                      'image' => $image,
                      'desc'  => $desc
                    ];

                    $arr[$tablename] = $new;
                  }

                  foreach ($arr as $key => $value) {
                    if($value['name']){

                      array_push($listdata, $value);
                    }
                  }

                }else if($mode == 1){
                  $table = $post->param;
                  switch ($table) {
                    case 'bonum':
                          $total_traffic         = $model->loadGlobal("sum(totalTransaction) as total_traffic", 'dma_digibiz_bonum_user', '');
                          $new_user              = $model->loadGlobal("count(registerDate) as new_user", 'dma_digibiz_bonum_user', 'MONTH(registerDate) = MONTH(CURRENT_DATE())');
                          $active_user           = $model->loadGlobal("count(lastLogin) as active_user", 'dma_digibiz_bonum_user', 'MONTH(lastLogin) = MONTH(CURRENT_DATE())');
                          $churn_rate            = $model->loadGlobal("round(count(*)/(select count(*) from dma_digibiz_bonum_user)) as churn_rate", 'dma_digibiz_bonum_user', 'MONTH(lastLogin) != MONTH(CURRENT_DATE())');
                          $total_expense         = $model->loadGlobal("sum(product_totalModal) as total_expense", 'dma_digibiz_bonum_transaction', '');
                          $income                = $model->loadGlobal("sum(totalProfit) * sum(quantity) as income", 'dma_digibiz_bonum_transaction', '');
                          $total_merchant        = $model->loadGlobal("count(*) as total_merchant", 'dma_digibiz_mytds_dxb_bonum_dashboard_v', '');
                          $total_merchant_ver    = $model->loadGlobal("count(*) as total_merchant_ver", 'dma_digibiz_mytds_dxb_bonum_dashboard_v', "status = 'Verifikasi'");
                          $total_merchant_regis  = $model->loadGlobal("count(*) as total_merchant_regis", 'dma_digibiz_mytds_dxb_bonum_dashboard_v', "status = 'Register'");
                          $total_merchant_aktif  = $model->loadGlobal("count(*) as total_merchant_aktif", 'dma_digibiz_mytds_dxb_bonum_dashboard_v', "lastdatetransaction != ''");
                          $total_teman           = $model->loadGlobal("count(*) as total_teman", 'dma_digibiz_mytds_dxb_bonum_dashboard_v', "refferalcode != ''");
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

                  $data->id                     = @$id->data[0]['id'];
                  $data->total_traffic          = @$total_traffic->data[0]['total_traffic'];
                  $data->total_poc              = @$total_poc->data[0]['total_poc'];
                  $data->new_user               = @$new_user->data[0]['new_user'];
                  $data->active_user            = @$active_user->data[0]['active_user'];
                  $data->churn_rate             = @$churn_rate->data[0]['churn_rate'];
                  $data->total_expense          = @$total_expense->data[0]['total_expense'];
                  $data->income                 = @$income->data[0]['income'];
                  $data->total_merchant         = @$total_merchant->data[0]['total_merchant'];
                  $data->total_merchant_ver     = @$total_merchant_ver->data[0]['total_merchant_ver'];
                  $data->total_merchant_regis   = @$total_merchant_regis->data[0]['total_merchant_regis'];
                  $data->total_merchant_aktif   = @$total_merchant_aktif->data[0]['total_merchant_aktif'];
                  $data->total_teman            = @$total_teman->data[0]['total_teman'];

                  $listdata = $data;

                }else if($mode == 2){
                  $datas = [];
                  $resdigihotel   = $model->loadGlobal("sum(total_price) as total", 'dma_digibiz_mytds_dxb_digihotel_dashboard_v', '');
                  $resdigiclinic  = $model->loadGlobal("sum(price) as total", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', '');
                  $resdigierp     = $model->loadGlobal("sum(total_price) as total", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', '');
                  $resbonum       = $model->loadGlobal("sum(product_totalModal) as total", 'dma_digibiz_bonum_transaction', '');
                  $ressakoo       = $model->loadGlobal("sum(harga_beli) * sum(qty) as total", 'dma_sakoo_list_transaction', '');
                  $mydate=getdate(date("U"));
                  $update_date    = $mydate['mday'] .'-'. $mydate['mon'] .'-'. $mydate['year'];

                  $datas['digihotel'] = [
                    'name'  => 'Digi Hotel',
                    'total' => 'Rp '.number_format($resdigihotel->data[0]['total'],0,',','.'),
                    'image' => $baseurl."/assets/images/avatars/Digi Hotel.jpg",
                    'date'  => $update_date
                  ];

                  $datas['digiclinic'] = [
                    'name'  => 'Digi Clinic',
                    'total' => 'Rp '.number_format($resdigiclinic->data[0]['total'],0,',','.'),
                    'image' => $baseurl."/assets/images/avatars/Digi Clinic.jpg",
                    'date'  => $update_date
                  ];

                  $datas['digierp'] = [
                    'name'  => 'Digi ERP',
                    'total' => 'Rp '.number_format($resdigierp->data[0]['total'],0,',','.'),
                    'image' => $baseurl."/assets/images/avatars/Digi ERP.jpg",
                    'date'  => $update_date
                  ];

                  $datas['bonum'] = [
                    'name'  => 'Bonum',
                    'total' => 'Rp '.number_format($resbonum->data[0]['total'],0,',','.'),
                    'image' => $baseurl."/assets/images/avatars/Bonum.jpg",
                    'date'  => $update_date
                  ];

                  $datas['sakoo'] = [
                    'name'  => 'Sakoo',
                    'total' => 'Rp '.number_format($ressakoo->data[0]['total'],0,',','.'),
                    'image' => $baseurl."/assets/images/avatars/Sakoo.jpg",
                    'date'  => $update_date
                  ];

                  $listdata = $datas;
                }else if($mode == 3){
                    $datas = [];
                    $active_user    = $model->loadActiveUser()->data[0]['total_active_user'];
                    $monthly_user   = $model->loadMonthlyUser()->data;
                    $weekly_user    = $model->loadWeeklyUser()->data[0]['total'];

                    $datas['active_user'] = $active_user;
                    $datas['monthly_active_user'] = $monthly_user;
                    $datas['weekly_active_user'] = $weekly_user;
                    $listdata = $datas;
                }

                if($listdata){

                    $result->code = 0;
                    $result->info = 'Sukses';
                    $result->data = $listdata;
                    $result->guid = $post['csrf'];
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

    public function isLoginAction(){

        try {
            $result     = new Result();

            $uri     = $this->getRequest()->getUri();
            $baseurl = sprintf('//%s', $uri->getHost());

            $post    = $this->getRequest()->getPost();
            $session = $this->getSession();

            $username = $post['uname'];
            $password = $post['pass'];

            $storage = \Khansia\Access\User\Storage::factory($this->getDb(), $this->getConfig());
            $user    =  new \Khansia\Access\User($storage);

            if($user->load($username,  \Khansia\Access\User\Storage::LOADBY_CODE)){ // sukses load then
                $authResult = $user->authenticate($password, null, \Khansia\Access\User::RETRIES_TRUE);

                if($authResult->code == $authResult::CODE_SUCCESS) {

                    /* update tokensss */
                    $this->userId = $user->id;

                    $this->updateTokenApi();

                    $data = array(
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
                        'csrf_token'        => $this->accessToken, // buat csrf token na hela gans biar gege
                    );

                    $result->code = 0;
                    $result->info = 'Sukses';
                    $result->data = $data;
                    $result->guid = $this->accessToken;
                    /* direct data */

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

                    $result->code = $authResult->code;
                    $result->info = $message;
                }
            }else{
              $message = 'Invalid username or password';

              $result->code = 1;
              $result->info = $message;
            }

        } catch (\Exception $ex) {
            $message = htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8');

            $result->code = 1;
            $result->info = $message;
        }
        return $this->getOutput($result->toJson());
    }

    private function updateTokenApi(){

        $this->deviceId     = $this->getMacAddress();
        $this->accessToken  = bin2hex(random_bytes(32));
        $this->myData       = array('iduser' => $this->userId, 'accessToken' => $this->accessToken, 'deviceid' => $this->deviceId, 'update_date' => $this->STORAGE_NOW());
        $this->findBy       = 'iduser='.$this->userId;

        $_storage           = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
        $_model  	          = new \Application\Model\Param($_storage);

        $getResults         = $_model->updateGlobal('user_data_header', $this->myData,  $this->findBy);
            /* send seed for client */
        return true;

    }

    private function isCsrf(){

      $request    = $this->getRequest();
      $post       = $request->getPost();
      if (isset($post['csrf'])) {

          $storage    = \Khansia\Access\User\Storage::factory($this->getDb(), $this->getConfig());
          $user       =  new \Khansia\Access\User($storage);

          if($user->load($post['id'],  \Khansia\Access\User\Storage::LOADBY_ID)){

              if ($post['csrf'] !== $user->accessToken) {

                  $result = new \Khansia\Generic\Result(0, 92, 'Wrong CSRF token #');
                  $json   = $result->toJson();

                  echo($json);die();

              }

          }else{
              $message = 'Time out session # Silahkan login kembali';
              $result = new \Khansia\Generic\Result(0, 97, $message);
              $json   = $result->toJson();

              echo($json);die();
          }

      } else {

          $result = new \Khansia\Generic\Result(0, 97, 'No CSRF token');
          $json   = $result->toJson();

          echo($json);die();

      }

        return true;
    }


}
