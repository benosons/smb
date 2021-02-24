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

class JsondataController extends \Application\Master\GlobalActionController
{
    const INPUTEXT  = 1;
    const TEXTAREA  = 2;
    const SELECTOPT = 3;
    const RADIONBTN = 4;

    public function __construct($headScript)
    {
        $this->headScript = $headScript;
    }

    public function loadsalesAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);
                        // var_dump($isData->table);die;

                        switch ($isData->table) {
                          case 'bonum':
                                $total_traffic = $model->loadGlobal("sum(totalTransaction) as total_traffic", 'dma_digibiz_bonum_user', '');
                                $new_user      = $model->loadGlobal("count(registerDate) as new_user", 'dma_digibiz_bonum_user', 'MONTH(registerDate) = MONTH(CURRENT_DATE())');
                                $active_user   = $model->loadGlobal("count(lastLogin) as active_user", 'dma_digibiz_bonum_user', 'MONTH(lastLogin) = MONTH(CURRENT_DATE())');
                                $churn_rate    = $model->loadGlobal("round(count(*)/(select count(*) from dma_digibiz_bonum_user)) as churn_rate", 'dma_digibiz_bonum_user', 'MONTH(lastLogin) != MONTH(CURRENT_DATE())');
                                $total_expense = $model->loadGlobal("sum(product_totalModal) as total_expense", 'dma_digibiz_bonum_transaction', '');
                                $income        = $model->loadGlobal("sum(totalProfit) * sum(quantity) as income", 'dma_digibiz_bonum_transaction', '');
                            break;
                          case 'digi%20clinic':
                                $total_traffic = $model->loadGlobal("sum(`usage`) as total_traffic", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', '');
                                $total_poc     = $model->loadGlobal("sum(cnt_demo) as total_poc", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', "status = 'demo'");
                                $new_user      = $model->loadGlobal("sum(cnt_register) as new_user", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', "status = 'register'");
                                $active_user   = $model->loadGlobal("count(cnt_beroprasi) as active_user", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', 'cnt_beroprasi = 1');
                                $churn_rate    = $model->loadGlobal("count(cnt_beroprasi) as churn_rate", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', 'cnt_beroprasi = 0');
                                $total_expense = $model->loadGlobal("sum(price) as total_expense", 'dma_digibiz_mytds_dxb_digiclinic_dashboard_v', '');
                            break;
                          case 'digi%20erp':
                                $total_traffic = $model->loadGlobal("sum(total_user) as total_traffic", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', '');
                                $total_poc     = $model->loadGlobal("sum(jml_trial) as total_poc", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', '');
                                $new_user      = $model->loadGlobal("count(payment_date) as new_user", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', "payment_date != ''");
                                $active_user   = $model->loadGlobal("count(end_date) as active_user", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', 'is_active = 1');
                                $churn_rate    = $model->loadGlobal("count(end_date) as churn_rate", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', 'is_active = 0');
                                $total_expense = $model->loadGlobal("sum(total_price) as total_expense", 'dma_digibiz_mytds_dxb_digierp_dashboard_v', '');
                            break;
                          case 'digi%20hotel':
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

                        $result->data = $data;
                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadtableAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);

                        $where          = "table_schema = '".getenv("DB")."'";

                        $result         = $model->loadGlobal("table_name", 'information_schema.tables', $where);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadexpAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);

                        // $where          = "table_schema = '".getenv("DB")."'";
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
                          'image' => "/assets/images/avatars/Digi Hotel.jpg",
                          'date'  => $update_date
                        ];

                        $datas['digiclinic'] = [
                          'name'  => 'Digi Clinic',
                          'total' => 'Rp '.number_format($resdigiclinic->data[0]['total'],0,',','.'),
                          'image' => "/assets/images/avatars/Digi Clinic.jpg",
                          'date'  => $update_date
                        ];

                        $datas['digierp'] = [
                          'name'  => 'Digi ERP',
                          'total' => 'Rp '.number_format($resdigierp->data[0]['total'],0,',','.'),
                          'image' => "/assets/images/avatars/Digi ERP.jpg",
                          'date'  => $update_date
                        ];

                        $datas['bonum'] = [
                          'name'  => 'Bonum',
                          'total' => 'Rp '.number_format($resbonum->data[0]['total'],0,',','.'),
                          'image' => "/assets/images/avatars/Bonum.jpg",
                          'date'  => $update_date
                        ];

                        $datas['sakoo'] = [
                          'name'  => 'Sakoo',
                          'total' => 'Rp '.number_format($ressakoo->data[0]['total'],0,',','.'),
                          'image' => "/assets/images/avatars/Sakoo.jpg",
                          'date'  => $update_date
                        ];
                        $result->data = $datas;
                        /* encrypt dan return data */
                        if($result){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function saveprofileAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $userID 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                        $dataArr        = array(
                            'judul'             => ucwords(self::antiInjection($isData->judul ?? null)),
                            'deskripsi'         => self::antiInjection($isData->desk ?? null),
                            'userid'            => $userID,
                            'publish'           => 3,
                            'masa_aktif'        => 2,
                            'batas_isi'         => 1,
                            'limitx'            => 5,
                            'privasix'          => 2,
                            'header_view'       => 1,
                            'header_color'      => '#f9fafb',
                            'back_color'        => '#f5f5f5',
                            'judul_view'        => 1,
                            'deskripsi_view'    => 1,
                            'layout_size'       => 1,
                            'mandatori'         => 1,
                            'tipe_bobot'        => 1,
                            'bobot_value'       => 100,
                            'sort_data'         => 1,
                            'materi_view'       => 1,
                            'status_soal'       => 2,
                            'type_content'      => self::antiInjection($isData->tpForm ?? null),
                            'end_date'          => $this->STORAGE_NOW(),
                            'update_date'       => $this->STORAGE_NOW(),
                            'create_date'       => $this->STORAGE_NOW(),
                        );


                        $result         = $model->saveGlobal($dataArr, 'order_profile', true);

                        if($result->code == $result::CODE_SUCCESS){

                            $numsL          = 1;
                            $arrLevel       = ['VERY EASY', 'EASY', 'NORMAL', 'HARD', 'VERY HARD'];

                            foreach($arrLevel as $lvl){

                                $isLevelArr = array(
                                    'profileid'     => $result->data,
                                    'level_name'    => $lvl,
                                    'level_value'   => 1,
                                    'level_code'    => $numsL * 10,
                                    'create_date'   => $this->STORAGE_NOW(),
                                );

                                $numsL++;

                                $model->saveGlobal($isLevelArr, 'order_level', true);
                            }

                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadsettingAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);

                        $where          = "id_profile=".$ipoly;

                        $result         = $model->loadGlobal("*", 'order_profile', $where);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function generateformAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* tipe load */
                        $loadtype       = $isData->loadtype ?? 1;

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);


                        if($loadtype == 1){

                            $where          = "profile_id=".$ipoly." ORDER BY sortingx ASC";

                        }else{

                            $where          = "profile_id=".$ipoly." ";

                            // if($isData->status_soal == 1){ // soal active
                            //     $where      .= " AND x_status=1 ";
                            // }

                            if($isData->sort_data == 1){ // sorting
                                $where .= "  ORDER BY  RANDOM()";
                            }else{
                                $where .= "  ORDER BY sortingx ASC";
                            }

                        }

                        $result         = $model->loadGlobal("*", 'management_parameter', $where);
                        // print_r($where);die;
                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadelequeAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);
                        $lvl            = self::antiInjection($isData->ilvl ?? 0);

                        $result         = $model->getElementQue($ipoly, $lvl);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    private function sort_arr_of_obj($array, $sortby, $direction='asc') {

        $sortedArr = array();
        $tmp_Array = array();

        foreach($array as $k => $v) {

            $tmp_Array[] = strtolower($v->$sortby);
        }

        if($direction=='asc'){
            asort($tmp_Array);
        }else{
            arsort($tmp_Array);
        }

        foreach($tmp_Array as $k=>$tmp){
            $sortedArr[] = $array[$k];
        }

        return $sortedArr;
    }

    public function insertmanagementparamAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $adapter 	    = $this->getDb();
                        $connect        = $adapter->getDriver()->getConnection();

                        $storage 	    = \Application\Model\Param\Storage::factory($adapter, $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        $idata          = $isData->idata;
                        $typepoly       = $isData->ipoly;

                        $status         = array();
                        $statusChild    = array();
                        $statusFinal    = array();

                        /* sorting data */
                        $sorted      = $this->sort_arr_of_obj($idata,'position','asc');

                        /* begin */
                        $connect->beginTransaction();

                        $n          = 1;
                        foreach($idata as $item){

                            /* save header */
                            switch ((int) $item->parenttype){
                                case self::INPUTEXT:

                                    /* cek jenis inputan nya apah */
                                    if((int) $item->parenttyped == 'texts'){
                                        $typedatas = 1;
                                    }else if((int) $item->parenttyped == 'numbers'){
                                        $typedatas = 2;
                                    }

                                    $headArray  = array(
                                        'profile_id'        => $typepoly,
                                        'object_type'       => $item->parenttype,
                                        'object_label'      => $item->parentlabel,
                                        'data_type'         => $typedatas,
                                        'sortingx'          => $n,
                                        'has_parent'        => 0,
                                        'x_status'          => $item->x_status,
                                    );


                                    if($item->paramsend){ // is update
                                        $arrayMerge = array_merge($headArray, array('update_date' => $this->STORAGE_NOW()));
                                        $where      = "paramid = ".$item->paramsend;
                                        $resHeader  = $model->updateGlobal('management_parameter', $arrayMerge, $where);
                                    }else{
                                        $arrayMerge = array_merge($headArray, array('create_date' => $this->STORAGE_NOW()));
                                        $resHeader  = $model->saveGlobal($arrayMerge, 'management_parameter', true);
                                    }

                                    if($resHeader->code == $result::CODE_SUCCESS){
                                        $status = array('success');
                                    }else{
                                        $connect->rollback();
                                        array_push($status, array(
                                            'code'  => $resHeader->code,
                                            'info'  => $resHeader->info.' # Terjadi kesalahan insert Header case Input',
                                        ));
                                        break;
                                    }

                                break;

                                case self::TEXTAREA:

                                    $headArray  = array(
                                        'profile_id'        => $typepoly,
                                        'object_type'       => $item->parenttype,
                                        'object_label'      => $item->parentlabel,
                                        'data_type'         => 1,
                                        'has_parent'        => 0,
                                        'sortingx'          => $n,
                                        'x_status'          => $item->x_status,
                                    );

                                    if($item->paramsend){ // is update
                                        $arrayMerge = array_merge($headArray, array('update_date' => $this->STORAGE_NOW()));
                                        $where      = "paramid = ".$item->paramsend;
                                        $resHeader  = $model->updateGlobal('management_parameter', $arrayMerge, $where);
                                    }else{
                                        $arrayMerge = array_merge($headArray, array('create_date' => $this->STORAGE_NOW()));
                                        $resHeader  = $model->saveGlobal($arrayMerge, 'management_parameter', true);
                                    }

                                    if($resHeader->code == $result::CODE_SUCCESS){
                                        $status = array('success');

                                    }else{
                                        $connect->rollback();
                                        array_push($status, array(
                                            'code'  => $resHeader->code,
                                            'info'  => $resHeader->info.' # Terjadi kesalahan insert Header case Textarea',
                                        ));
                                        break;
                                    }

                                break;

                                case (self::SELECTOPT || RADIONBTN):

                                    $headArray  = array(
                                        'profile_id'        => $typepoly,
                                        'object_type'       => $item->parenttype,
                                        'object_label'      => $item->parentlabel,
                                        'data_type'         => 1,
                                        'has_parent'        => 0,
                                        'sortingx'          => $n,
                                        'x_status'          => $item->x_status,
                                    );


                                    if($item->paramsend){ // is update
                                        $arrayMerge = array_merge($headArray, array('update_date' => $this->STORAGE_NOW()));
                                        $where      = "paramid = ".$item->paramsend;
                                        $resHeader  = $model->updateGlobal('management_parameter', $arrayMerge, $where);
                                    }else{
                                        $arrayMerge = array_merge($headArray, array('create_date' => $this->STORAGE_NOW()));
                                        $resHeader  = $model->saveGlobal($arrayMerge, 'management_parameter', true);
                                    }

                                    if($resHeader->code == $result::CODE_SUCCESS){
                                        $status = array('success');
                                    }else{
                                        $connect->rollback();
                                        array_push($status, array(
                                            'code'  => $resHeader->code,
                                            'info'  => $resHeader->info.' # Terjadi kesalahan insert Header select option',
                                        ));
                                        break;
                                    }

                                    if($resHeader->code == $result::CODE_SUCCESS){

                                        if(isset($item->option)){

                                            foreach($item->option as $valSelect){

                                                if($item->paramsend!= null && $valSelect->paramsendank == ''){
                                                    $headerOptArray = array(
                                                        'profile_id'        => $typepoly,
                                                        'object_type'       => $item->parenttype,
                                                        'object_label'      => $valSelect->optionlabel,
                                                        'object_value'      => $valSelect->optionvalue,
                                                        'has_parent'        => $item->paramsend,
                                                        'data_type'         => 1,
                                                        'create_date'       => $this->STORAGE_NOW(),
                                                        'update_date'       => $this->STORAGE_NOW(),
                                                    );
                                                }else if( $valSelect->paramsendank== '' ){
                                                    $headerOptArray = array(
                                                        'profile_id'        => $typepoly,
                                                        'object_type'       => $item->parenttype,
                                                        'object_label'      => $valSelect->optionlabel,
                                                        'object_value'      => $valSelect->optionvalue,
                                                        'has_parent'        => $resHeader->data,
                                                        'data_type'         => 1,
                                                        'create_date'       => $this->STORAGE_NOW(),
                                                        'update_date'       => $this->STORAGE_NOW(),
                                                    );
                                                }else{
                                                    $headerOptArray = array(
                                                        'paramid'           => $valSelect->paramsendank,
                                                        'profile_id'        => $typepoly,
                                                        'object_type'       => $item->parenttype,
                                                        'object_label'      => $valSelect->optionlabel,
                                                        'object_value'      => $valSelect->optionvalue,
                                                        'has_parent'        => $item->paramsend,
                                                        'data_type'         => 1,
                                                        'create_date'       => $this->STORAGE_NOW(),
                                                        'update_date'       => $this->STORAGE_NOW(),
                                                    );
                                                }


                                                if($valSelect->paramsendank){
                                                    $where         = "paramid = ".$valSelect->paramsendank;
                                                    $resHeaderopt  = $model->updateGlobal('management_parameter', $headerOptArray, $where);

                                                }else{
                                                    $resHeaderopt  = $model->saveGlobal($headerOptArray, 'management_parameter', true);
                                                }

                                                if($resHeaderopt->code == $result::CODE_SUCCESS){
                                                    $status = array('success');
                                                }else{
                                                    $connect->rollback();
                                                    array_push($status, array(
                                                        'code'  => $resHeaderopt->code,
                                                        'info'  => $resHeaderopt->info.' # Terjadi kesalahan insert case option/radio',
                                                        'data'  => null
                                                    ));
                                                    break;
                                                }
                                            }
                                        }

                                    }

                                break;

                                default:
                                    array_push($status, array(
                                        'code'  => 901,
                                        'info'  => '# Terjadi kesalahan TYPE KOSONG',
                                        'data'  => null
                                    ));
                                break;
                            }

                            $n++;
                        }

                        /* sukses save header */
                        if (in_array("success", $status)) {
                            $connect->commit();
                            $result->code = $result::CODE_SUCCESS;
                            $result->info = $result::INFO_SUCCESS;
                            $result->data = 'MANTAPSX';

                        }else{
                            $result->code = $status[0]['code'];
                            $result->info = $status[0]['info'];
                            $result->data = $status[0]['data'];
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function deleteparameterAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $user_id 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                        $paramid        = self::antiInjection($isData->paramid ?? null);

                        $where          = "paramid in (".$paramid.")";

                        $result         = $model->deleteGlobal('management_parameter', $where);

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadquelevelAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);
                        $result         = $model->getQueSort($ipoly);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function saverulesoptAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $user_id 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                        $dataArr        = array(
                            self::antiInjection($isData->columns ?? null)   => self::antiInjection($isData->isvalue ?? null),
                        );

                        $where          = 'paramid = '.$isData->paramid;

                        $resHeader      = $model->updateGlobal('management_parameter', $dataArr, $where);

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function savesortinglevelAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $user_id 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $adapter 	    = $this->getDb();
                        $connect        = $adapter->getDriver()->getConnection();

                        $storage 	    = \Application\Model\Param\Storage::factory($adapter, $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* begin */
                        $connect->beginTransaction();

                        $status         = array();
                        foreach($isData->idata as $item){

                            $dataArr = array(
                                'sortingx'  => $item->position + 1,
                            );

                            $where      = "paramid = ".$item->idcategory;

                            $res        = $model->updateGlobal('management_parameter', $dataArr, $where);

                            if($res->code == $result::CODE_SUCCESS){
                                $status = array('success');
                            }else{
                                $connect->rollback();
                                array_push($status, array(
                                    'code'  => $res->code,
                                    'info'  => $res->info.' # Terjadi kesalahan update data',
                                ));
                                break;
                            }

                        }

                        /* sukses save header */
                        if (in_array("success", $status)) {
                            $connect->commit();
                            $result->code = $result::CODE_SUCCESS;
                            $result->info = $result::INFO_SUCCESS;

                        }else{
                            $result->code = $status[0]['code'];
                            $result->info = $status[0]['info'];
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadlevelAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);

                        $where          = "profileid=".$ipoly." ORDER BY levelid ASC";

                        $result         = $model->loadGlobal("*", 'order_level', $where);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt  = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function savepointbobotsAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $user_id 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){

                        $adapter 	    = $this->getDb();
                        $connect        = $adapter->getDriver()->getConnection();

                        $storage 	    = \Application\Model\Param\Storage::factory($adapter, $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* begin */
                        $connect->beginTransaction();

                        $status         = array();

                        /* update profile header */
                        $dataArr = array(
                            'bobot_value'  => self::antiInjection($isData->DefaultVal ?? 0),
                            'update_date'  => $this->STORAGE_NOW(),
                        );

                        $where      = "id_profile = ".self::antiInjection($isData->ProfileID ?? null);

                        $res        = $model->updateGlobal('order_profile', $dataArr, $where);

                        if($res->code == $result::CODE_SUCCESS){

                            /* save level poin bobot */
                            foreach($isData->actPoinLevel as $poin){

                                $arrLevel = array(
                                    'level_value'   => self::antiInjection($poin->this_val ?? 0),
                                    'update_date'   => $this->STORAGE_NOW(),
                                );

                                $where   = "levelid = ".self::antiInjection($poin->level_id ?? null);

                                $resPoin = $model->updateGlobal('order_level', $arrLevel, $where);

                                if($resPoin->code == $result::CODE_SUCCESS){

                                    $status = array('success');

                                }else{

                                    $connect->rollback();
                                    array_push($status, array(
                                        'code'  => $res->code,
                                        'info'  => $res->info.' # Terjadi kesalahan update level',
                                    ));
                                    break;

                                }

                            }

                        }else{
                            $connect->rollback();
                            array_push($status, array(
                                'code'  => $res->code,
                                'info'  => $res->info.' # Terjadi kesalahan update profile',
                            ));
                        }


                        /* sukses save header */
                        if (in_array("success", $status)) {
                            $connect->commit();
                            $result->code = $result::CODE_SUCCESS;
                            $result->info = $result::INFO_SUCCESS;

                        }else{
                            $result->code = $status[0]['code'];
                            $result->info = $status[0]['info'];
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function saverulesetupAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $user_id 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $adapter 	    = $this->getDb();
                        $connect        = $adapter->getDriver()->getConnection();

                        $storage 	    = \Application\Model\Param\Storage::factory($adapter, $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* begin */
                        $connect->beginTransaction();

                        $statusOne      = array('success');
                        $statusTwo      = array('success');
                        $statusTri      = array('success');

                        /* set status soal */
                        if($isData->actSoalArr){

                            foreach($isData->actSoalArr as $item){

                                $dataArr = array(
                                    'x_status'      => self::antiInjection($item->status ?? 2),
                                    'update_date'   => $this->STORAGE_NOW(),
                                );

                                $where      = "paramid = ".self::antiInjection($item->paramid ?? null);

                                $res        = $model->updateGlobal('management_parameter', $dataArr, $where);

                                if($res->code == $result::CODE_SUCCESS){

                                    $statusOne = array('success');

                                }else{

                                    $connect->rollback();
                                    array_push($statusOne, array(
                                        'code'  => $res->code,
                                        'info'  => $res->info.' # Terjadi kesalahan update status',
                                    ));
                                    break;

                                }
                            }

                        }

                        /* set jawaban */
                        if($isData->ansSoalArr){

                            foreach($isData->ansSoalArr as $item){

                                $dataArr = array(
                                    'x_answer'      => self::antiInjection($item->has_parent ?? null),
                                    'update_date'   => $this->STORAGE_NOW(),
                                );

                                $where      = "paramid = ".self::antiInjection($item->paramid ?? null);

                                $res        = $model->updateGlobal('management_parameter', $dataArr, $where);

                                if($res->code == $result::CODE_SUCCESS){

                                    $statusTwo = array('success');

                                }else{

                                    $connect->rollback();
                                    array_push($statusTwo, array(
                                        'code'  => $res->code,
                                        'info'  => $res->info.' # Terjadi kesalahan update jawaban',
                                    ));
                                    break;

                                }
                            }

                        }

                        /* set level */
                        if($isData->levSoalArr){

                            foreach($isData->levSoalArr as $item){

                                $dataArr = array(
                                    'x_difficult'   => self::antiInjection($item->levelcode ?? null),
                                    'update_date'   => $this->STORAGE_NOW(),
                                );

                                $where      = "paramid = ".self::antiInjection($item->paramid ?? null);

                                $res        = $model->updateGlobal('management_parameter', $dataArr, $where);

                                if($res->code == $result::CODE_SUCCESS){

                                    $statusTri = array('success');

                                }else{

                                    $connect->rollback();
                                    array_push($statusTri, array(
                                        'code'  => $res->code,
                                        'info'  => $res->info.' # Terjadi kesalahan update jawaban',
                                    ));
                                    break;

                                }
                            }

                        }

                        /* check status update */
                        $newStatus = array('succcess');

                        if($isData->actSoalArr){
                            if (in_array("success", $statusOne)) {
                                $newStatus = array('success');
                            }else{
                                array_push($newStatus, array(
                                    'code'  => $statusOne[0]['code'],
                                    'info'  => $statusOne[0]['info'],
                                ));
                            }
                        }

                        if($isData->ansSoalArr){
                            if (in_array("success", $statusTwo)) {
                                $newStatus = array('success');
                            }else{
                                array_push($newStatus, array(
                                    'code'  => $statusTwo[0]['code'],
                                    'info'  => $statusTwo[0]['info'],
                                ));
                            }
                        }

                        if($isData->levSoalArr){
                            if (in_array("success", $statusTri)) {
                                $newStatus = array('success');
                            }else{
                                array_push($newStatus, array(
                                    'code'  => $statusTri[0]['code'],
                                    'info'  => $statusTri[0]['info'],
                                ));
                            }
                        }

                        /* sukses save header */
                        if (in_array("success", $newStatus)) {
                            $connect->commit();
                            $result->code = $result::CODE_SUCCESS;
                            $result->info = $result::INFO_SUCCESS;

                        }else{
                            $result->code = $newStatus[0]['code'];
                            $result->info = $newStatus[0]['info'];
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    private function generateRandomCharacter($len = 10){
        $charset    = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $base       = strlen($charset);
        $result     = '';

        $now        = explode(' ', microtime())[1];

        while ($now >= $base){
          $i        = $now % $base;
          $result   = $charset[$i] . $result;
          $now     /= $base;
        }
        $ran        = bin2hex(random_bytes(10));
        $stbh       = substr($result, -5);

        return $ran.$stbh;
    }

    public function savemateriAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $fileupload      = $_FILES['fileupload']['tmp_name'];

                    if (!empty($fileupload)){

                        /* data upload */
                        $ImageName       = $_FILES['fileupload']['name'];
                        $tipes           = $_FILES['fileupload']['type'];
                        $size            = $_FILES['fileupload']['size'];

                        $userSession    = $this->getSession();

                        $user_id 		= $userSession->get('user_id');

                        $timeName       = microtime(true);
                        $micro          = sprintf("%06d", ($timeName - floor($timeName)) * 1000000);

                        $ImageExt       = substr($ImageName, strrpos($ImageName, '.'));
                        $ImageExt       = str_replace('.','',$ImageExt); // Extension
                        $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
                        $NewImageName   = str_replace(' ', '', $micro.'.'.$ImageExt);

                        $createRanName  = self::generateRandomCharacter();

                        if($tipes == 'video/mp4' || $tipes == 'video/x-matroska'){
                            $uploaddir = './public/data/materi/video/'; // directory file
                            $uploaddis = '/data/materi/video/'; // directory file
                        }else{
                            $uploaddir = './public/data/materi/other/'; // directory file
                            $uploaddis = '/data/materi/other/'; // directory file
                        }

                        $alamatfile    = $uploaddir.$createRanName.$NewImageName;

                        /* save data ke db */
                        $adapter 	    = $this->getDb();
                        $connect        = $adapter->getDriver()->getConnection();

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* begin */
                        $connect->beginTransaction();

                        $dataArr        = array(
                            'createid'      => $user_id,
                            'titles'        => self::antiInjection($this->antiStealth('titleMater') ?? null),
                            'ex_char'       => $this->antiStealth('ex_char') ?? null,
                            'status_ma'     => 1,
                            'create_date'   => $this->STORAGE_NOW(),
                        );

                        $result         = $model->saveGlobal($dataArr, 'materi_data', true);

                        if($result->code == $result::CODE_SUCCESS){

                            if (move_uploaded_file($_FILES['fileupload']['tmp_name'],$alamatfile)){

                                /* jika upload berhasil ke folder sever */
                                $dataAtt        = array(
                                    'createid'      => $user_id,
                                    'idmateri'      => $result->data,
                                    'file_name'     => $createRanName.$NewImageName,
                                    'file_type'     => $tipes,
                                    'file_size'     => $size / 1000000, // as mb
                                    'file_dir'      => $uploaddis,
                                    'file_dir_c'    => $alamatfile,
                                    'file_ext'      => $ImageExt,
                                    'create_date'   => $this->STORAGE_NOW(),
                                );

                                $result = $model->saveGlobal($dataAtt, 'materi_attachments', true);

                            }else{
                                $result->code = 17;
                                $result->info = 'FAILED UPLOAD FILE to SERVER';
                            }

                        }

                        if($result->code == $result::CODE_SUCCESS){
                            $connect->commit();
                        }else{
                            $connect->rollback();
                        }

                    } else {
                        $result->code = 11;
                        $result->info = 'MOHON MASUKAN FILE ATTACHMENTS!';
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function loadpartialmateriAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $search  = self::antiInjection($isData->search ?? null);

                        $col =  array(
                            0   	=>  'md.idmateri',
                            1   	=>  'file_name',
                            2   	=>  'titles',
                            3   	=>  'file_size',
                            4   	=>  'create_date',
                        );

                        $order      = $this->antiStealth('order');
                        $start      = $this->antiStealth('start');
                        $length     = $this->antiStealth('length');
                        $draw       = $this->antiStealth('draw');

                        $isearch    = $this->antiStealth('search') ?? null;
                        $search     = $isearch['value'] ?? null;


                        $orderCol       = $col[$order[0]['column']];

                        $orderDir       = $order[0]['dir'];


                        $result         = $model->getMateriPartial($col, $orderCol, $orderDir, $start, $length, $draw, $search);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt  = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){

                                $dataArray = array(
                                    "draw"              =>  intval($result->data['draw']),
                                    "recordsTotal"      =>  intval($result->data['recordsTotal']),
                                    "recordsFiltered"   =>  intval($result->data['recordsFiltered']),
                                    "data"              =>  $isEncrypt,
                                    "code"              =>  $result->code,
                                );

                                $result->data   = $dataArray;

                                return $this->getOutput(json_encode($result->data));
                                exit();

                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function savemapmateriAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $user_id 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $adapter 	    = $this->getDb();
                        $connect        = $adapter->getDriver()->getConnection();

                        $storage 	    = \Application\Model\Param\Storage::factory($adapter, $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        $isProfileID    = self::antiInjection($isData->profileid ?? 0);
                        $isIdMateri     = self::antiInjection($isData->idmateri ?? 0);
                        $istatus        = self::antiInjection($isData->istatus ?? 0);

                        if($istatus == 1){ // dia insert

                            /* check data di mapping  materi jika ada maka jangan insert */
                            $whereArr       = array(
                                'profileid' => $isProfileID,
                                'idmateri'  => $isIdMateri,
                            );

                            $result         = $model->checkDuplicateData('order_materi', $whereArr, '# Materi sudah dimappingkan. ');

                            /* jika tidak ada yang sama maka insert data */
                            if($result->code == $result::CODE_SUCCESS){

                                $dataArr = array(
                                    'profileid'      => $isProfileID,
                                    'idmateri'       => $isIdMateri,
                                    'create_date'    => $this->STORAGE_NOW(),
                                );

                                $result  = $model->saveGlobal($dataArr, 'order_materi', true);
                            }

                        }elseif($istatus == 2){ // dia delete

                            $result = $model->deleteGlobal('order_materi', 'profileid='.$isProfileID.' AND idmateri='.$isIdMateri);

                        }else{
                            $result->code == 999;
                            $result->info == 'Status tidak diketahui';
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadmappingmateriAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);

                        $result         = $model->getMappingMateri($ipoly);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }




    public function updateprofilesettingAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $user_id 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                        $dataArr        = array(
                            'id_profile'    => $isData->ipoly,
                            'judul'         => $isData->juduls,
                            'deskripsi'     => $isData->deskripsid,
                            'publish'       => $isData->publish,
                            'masa_aktif'    => $isData->masaAktif,
                            'end_date'      => $isData->dateAktif,
                            'batas_isi'     => $isData->batasIsi,
                            'limitx'        => $isData->bLimitx,
                            'privasix'      => $isData->privasi,
                            'header_view'   => $isData->viewheader,
                            'header_color'  => $isData->warnaHead,
                            'back_color'    => $isData->warnaBack,
                            'judul_view'    => $isData->view_judul,
                            'deskripsi_view'=> $isData->view_deskripsi,
                            'layout_size'   => $isData->layoutSize,
                            'tipe_bobot'    => $isData->tipe_bobot,
                            'status_soal'   => $isData->status_soal,
                            'sort_data'     => $isData->sort_data,
                            'materi_view'   => $isData->materi_view,
                            'tipe_kuiz'     => $isData->tipe_kuiz,
                            'update_date'   => $this->STORAGE_NOW(),
                        );

                        $where  = "id_profile = ".$isData->ipoly;

                        $result = $model->updateGlobal('order_profile', $dataArr, $where);

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function checkuserisisurveyAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $ipoly          = self::antiInjection($isData->ipoly ?? null);

                        $where          = "profile_id=".$ipoly." AND  create_id=".$isUserid." LIMIT 1";

                        $from           = "order_survey ";

                        $result         = $model->loadGlobal("param_id", $from, $where);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function savescheduleAction(){
        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{
                    $userSession        = $this->getSession();
                    $userID 		    = $userSession->get('user_id');

                    $isData             = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan
                    $tempjadwal         = self::antiInjection($isData->tempjadwal?? null);

                    // print_r($isData);die;

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);
                        if($tempjadwal){
                            $dataArr        = array(
                                'date_start_registration'   => self::antiInjection($isData->startregist?? null),
                                'date_finish_registration'  => self::antiInjection($isData->finishregist?? null),
                                'date_start_ta1'            => self::antiInjection($isData->startta1?? null),
                                'date_finish_ta1'           => self::antiInjection($isData->finishta1?? null),
                                'date_start_ta2'            => self::antiInjection($isData->startta2?? null),
                                'date_finish_ta2'           => self::antiInjection($isData->finishta2?? null),
                                'description'               => self::antiInjection($isData->description ?? null),
                                'update_date'               => $this->STORAGE_NOW(),
                                'id_user'                   => $userID,
                            );

                            $where = 'id_schedule = '.$tempjadwal;

                            $result = $model->updateGlobal('sipintar_schedule', $dataArr, $where);
                            $result->info = "Success Update";

                        }else{
                            $dataArr        = array(
                                'date_start_registration'   => self::antiInjection($isData->startregist?? null),
                                'date_finish_registration'  => self::antiInjection($isData->finishregist?? null),
                                'date_start_ta1'            => self::antiInjection($isData->startta1?? null),
                                'date_finish_ta1'           => self::antiInjection($isData->finishta1?? null),
                                'date_start_ta2'            => self::antiInjection($isData->startta2?? null),
                                'date_finish_ta2'           => self::antiInjection($isData->finishta2?? null),
                                'description'               => self::antiInjection($isData->description ?? null),
                                'create_date'               => $this->STORAGE_NOW(),
                                'id_user'                   => $userID,
                                'status'                    => 10
                            );

                            $result         = $model->saveGlobal($dataArr, 'sipintar_schedule', true);
                        }


                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function deletescheduleAction(){
        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $userID 		= $userSession->get('user_id');

                    $isData             = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan
                    $tempjadwal         = self::antiInjection($isData->tempjadwal?? null);

                    // print_r($isData);die;

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                        $dataArr = array(
                            'status'        => 30,
                            'id_user'       => $userID,
                            'update_date'   => $this->STORAGE_NOW(),
                        );

                        $where = 'id_schedule = '.$tempjadwal;

                        $result = $model->updateGlobal('sipintar_schedule', $dataArr, $where);

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function loadvalidationmahasiswaAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $search  = self::antiInjection($isData->search ?? null);
                        // $search  = self::antiInjection($isData->search ?? null);

                        $col =  array(
                            0   	=>  'id_mahasiswa_registration',
                            1   	=>  'nim',
                            2   	=>  'name_mahasiswa',
                            3       =>  'name_program_studi',
                            4       =>  'name_kelompok_keahlian',
                            5   	=>  'list_date',
                            6       =>  'Keterangan',
                            7   	=>  'status',
                        );

                        $order      = $this->antiStealth('order');
                        $start      = $this->antiStealth('start');
                        $length     = $this->antiStealth('length');
                        $draw       = $this->antiStealth('draw');

                        $isearch    = $this->antiStealth('search') ?? null;
                        $search     = $isearch['value'] ?? null;

                        $status = self::antiInjection($isData->status ?? null);
                        $schedule = self::antiInjection($isData->schedule ?? null);

                        $orderCol       = $col[$order[0]['column']];
                        $orderDir       = $order[0]['dir'];


                        $result         = $model->getValidasiMahasiswa($col, $orderCol, $orderDir, $start, $length, $draw, $search, $status, $schedule);


                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt  = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){

                                $dataArray = array(
                                    "draw"              =>  intval($result->data['draw']),
                                    "recordsTotal"      =>  intval($result->data['recordsTotal']),
                                    "recordsFiltered"   =>  intval($result->data['recordsFiltered']),
                                    "data"              =>  $isEncrypt,
                                    "code"              =>  $result->code,
                                );

                                $result->data   = $dataArray;

                                return $this->getOutput(json_encode($result->data));
                                exit();

                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function validasimhswAction(){
        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $userID 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan
                    $temp           = self::antiInjection($isData->temp?? null);

                    // print_r($isData);die;

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                            $dataArr        = array(
                                'status'    => self::antiInjection($isData->status?? null),
                                'note'    => self::antiInjection($isData->description?? null),
                            );

                            $where = 'id_mahasiswa_registration = '.$temp;
                            // print_r($dataArr);die;
                            $result = $model->updateGlobal('sipintar_mahasiswa_registration', $dataArr, $where);

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }


    public function updatejadwalAction(){
        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $userID 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan
                    $temp           = self::antiInjection($isData->temp?? null);

                    // print_r($isData);die;

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                            $dataArr        = array(
                                'status'        => self::antiInjection($isData->status?? null),
                                'update_date'   => $this->STORAGE_NOW(),
                            );

                            $where = 'id_schedule = '.$temp;
                            // print_r($dataArr);die;
                            $result = $model->updateGlobal('sipintar_ta_schedule', $dataArr, $where);

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function loadscheduletaAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $stat           = self::antiInjection($isData->param ?? null);
                        $tempjadwal     = self::antiInjection($isData->tempjadwal ?? null);

                        $result         = $model->getschedule($stat, $tempjadwal);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadmahasiswaAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result         = $model->getmahasiswa($param);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loaddosenAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri
                        $storage = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $search  = self::antiInjection($isData->search ?? null);
                        $temp  = self::antiInjection($isData->temp ?? null);

                        $col =  array(
                            0   	=>  'id_dosen',
                            1   	=>  'nid',
                            2   	=>  'name_dosen',
                            3       =>  'email',
                            4       =>  'name_program_studi',
                            5       =>  'name_kelompok_keahlian',
                            6       =>  'status',
                        );

                        $order      = $this->antiStealth('order');
                        $start      = $this->antiStealth('start');
                        $length     = $this->antiStealth('length');
                        $draw       = $this->antiStealth('draw');

                        $isearch    = $this->antiStealth('search') ?? null;
                        $search     = $isearch['value'] ?? null;

                        // $status = self::antiInjection($isData->status ?? null);
                        // $schedule = self::antiInjection($isData->schedule ?? null);

                        $orderCol       = $col[$order[0]['column']];

                        $orderDir       = $order[0]['dir'];

                        $result         = $model->getDosen($col, $orderCol, $orderDir, $start, $length, $draw, $search, $temp);

                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt  = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){

                                $dataArray = array(
                                    "draw"              =>  intval($result->data['draw']),
                                    "recordsTotal"      =>  intval($result->data['recordsTotal']),
                                    "recordsFiltered"   =>  intval($result->data['recordsFiltered']),
                                    "data"              =>  $isEncrypt,
                                    "code"              =>  $result->code,
                                );

                                $result->data   = $dataArray;

                                return $this->getOutput(json_encode($result->data));
                                exit();

                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadtopicAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $search  = self::antiInjection($isData->search ?? null);
                        $temp  = self::antiInjection($isData->temp ?? null);

                        $col =  array(
                            0   	=>  'id_topic',
                            1   	=>  'name_topic',
                            2   	=>  'description',
                            3       =>  'name',
                            4       =>  'name_program_studi',
                            5       =>  'name_kelompok_keahlian',
                            6       =>  'status',
                        );

                        $order      = $this->antiStealth('order');
                        $start      = $this->antiStealth('start');
                        $length     = $this->antiStealth('length');
                        $draw       = $this->antiStealth('draw');

                        $isearch    = $this->antiStealth('search') ?? null;
                        $search     = $isearch['value'] ?? null;

                        // $status = self::antiInjection($isData->status ?? null);
                        // $schedule = self::antiInjection($isData->schedule ?? null);

                        $orderCol       = $col[$order[0]['column']];
                        $orderDir       = $order[0]['dir'];


                        $result         = $model->getTopic($col, $orderCol, $orderDir, $start, $length, $draw, $search , $temp);


                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt  = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){

                                $dataArray = array(
                                    "draw"              =>  intval($result->data['draw']),
                                    "recordsTotal"      =>  intval($result->data['recordsTotal']),
                                    "recordsFiltered"   =>  intval($result->data['recordsFiltered']),
                                    "data"              =>  $isEncrypt,
                                    "code"              =>  $result->code,
                                );

                                $result->data   = $dataArray;

                                return $this->getOutput(json_encode($result->data));
                                exit();

                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadprogramstudiAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result         = $model->getprogramstudi();

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }
    public function loadkelompokkeahlianAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result         = $model->getkelompokkeahlian($param);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function savedosenAction(){
        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{
                    $userSession        = $this->getSession();
                    $userID 		    = $userSession->get('user_id');

                    $isData             = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan
                    $temp        = self::antiInjection($isData->temp?? null);

                    // print_r($isData);die;

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);
                        if($temp){
                            $dataArr        = array(
                                'nid'               => self::antiInjection($isData->nid?? null),
                                'name_dosen'        => self::antiInjection($isData->name?? null),
                                'email'             => self::antiInjection($isData->email?? null),
                                'program_studi'     => self::antiInjection($isData->programstudi?? null),
                                'kelompok_keahlian' => self::antiInjection($isData->kelompokkeahlian?? null),
                                'update_date'       => $this->STORAGE_NOW(),
                                'id_user'           => $userID,
                            );

                            $where = 'id_dosen = '.$temp;

                            $result = $model->updateGlobal('sipintar_dosen_pembimbing', $dataArr, $where);
                            $result->info = "Success Update";

                        }else{

                            $dataArr        = array(
                                self::antiInjection($isData->username?? null),
                                self::antiInjection($isData->name?? null),
                                20,
                                10,
                                self::antiInjection($isData->email?? null),
                                self::antiInjection($isData->nid?? null),
                                self::antiInjection($isData->kelompokkeahlian?? null),
                            );

                            $result         = $model->saveDosen($dataArr);
                        }


                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function savetopicAction(){
        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{
                    $userSession        = $this->getSession();
                    $userID 		    = $userSession->get('user_id');

                    $isData             = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan
                    $temp        = self::antiInjection($isData->temp?? null);

                    // print_r($isData);die;

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);
                        if($temp){
                            $dataArr        = array(
                                'nid'               => self::antiInjection($isData->nid?? null),
                                'name_dosen'        => self::antiInjection($isData->name?? null),
                                'email'             => self::antiInjection($isData->email?? null),
                                'program_studi'     => self::antiInjection($isData->programstudi?? null),
                                'kelompok_keahlian' => self::antiInjection($isData->kelompokkeahlian?? null),
                                'update_date'       => $this->STORAGE_NOW(),
                                'id_user'           => $userID,
                            );

                            $where = 'id_dosen = '.$temp;

                            $result = $model->updateGlobal('sipintar_dosen_pembimbing', $dataArr, $where);
                            $result->info = "Success Update";

                        }else{

                            $dataArr        = array(
                                'name_topic'    => self::antiInjection($isData->nametopic?? null),
                                'description'   => self::antiInjection($isData->description?? null),
                                'id_dosen'      => self::antiInjection($isData->dosen?? null),
                                'create_date'   => $this->STORAGE_NOW(),
                                'id_user'       => $userID,
                                'status'        => 10
                            );

                            $result         = $model->saveGlobal($dataArr, 'sipintar_topic_judul',true);
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function loadeditdosenAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);
                        $iparam     = self::antiInjection($isData->iparam ?? null);

                        $result         = $model->geteditdosen($param, $iparam);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function deletedosenAction(){
        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $userID 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan
                    $temp           = self::antiInjection($isData->temp?? null);

                    // print_r($isData);die;

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                            $dataArr        = array(
                                'status'    => 20,
                            );

                            $where = 'iduser = '.$temp;
                            // print_r($dataArr);die;
                            $result = $model->updateGlobal('user_data_header', $dataArr, $where);

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function deletetopicAction(){
        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $userID 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan
                    $temp           = self::antiInjection($isData->temp?? null);

                    // print_r($isData);die;

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());

                        $model   	    = new \Application\Model\Param($storage);

                            $dataArr        = array(
                                'status'        => 20,
                                'update_date'   => $this->STORAGE_NOW(),
                            );

                            $where = 'id_topic = '.$temp;
                            // print_r($dataArr);die;
                            $result = $model->updateGlobal('sipintar_topic_judul', $dataArr, $where);

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function loaddosenoptionAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->temp ?? null);
                        $select     = self::antiInjection($isData->select ?? null);
                        $select1     = self::antiInjection($isData->select1 ?? null);

                        // print_r($param);die;

                        $result         = $model->getdosenpotion($param, $select, $select1);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function loadmymahasiswaAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);
                        // print_r($isUserid);die;
                        $result         = $model->getmymahasiswa($isUserid);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function saveregistAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $fileupload      = $_FILES['fileupload']['tmp_name'];

                    if(!empty($fileupload)){

                        $userSession    = $this->getSession();
                        $user_id 		= $userSession->get('user_id');




                        $pdfName         = $_FILES['fileupload']['name'];
                        $tipes           = $_FILES['fileupload']['type'];
                        $size            = $_FILES['fileupload']['size'];

                        $timeName       = microtime(true);
                        $micro          = sprintf("%06d", ($timeName - floor($timeName)) * 1000000);

                        $PdfExt       = substr($pdfName, strrpos($pdfName, '.'));
                        $PdfExt       = str_replace('.','',$PdfExt); // Extension
                        $pdfName        = preg_replace("/\.[^.\s]{3,4}$/", "", $pdfName);
                        $NewPdfName     = str_replace(' ', '', $micro.'.'.$PdfExt);

                        $createRanName  = self::generateRandomCharacter();

                        $uploaddis = '/data/materi/registration/';
                        $uploaddir = './public/data/materi/registration/';

                        $alamatfile    = $uploaddir.$createRanName.$NewPdfName;

                        $adapter 	    = $this->getDb();
                        $connect        = $adapter->getDriver()->getConnection();

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        $connect->beginTransaction();

                        if(move_uploaded_file($_FILES['fileupload']['tmp_name'],$alamatfile)){
                            $dataAtt        = array(
                                'file_name'     => $createRanName.$NewPdfName,
                                'file_type'     => $tipes,
                                'file_size'     => $size / 1000000, // as mb
                                'file_dir'      => $uploaddis,
                                'file_dir_c'    => $alamatfile,
                                'file_ext'      => $PdfExt,
                                'create_date'   => $this->STORAGE_NOW(),
                                'id_user'       => $user_id,
                            );
                            // print_r($dataAtt);die;

                            $result = $model->saveGlobal($dataAtt, 'sipintar_registration_attachments', true);

                            if($result->code == $result::CODE_SUCCESS){
                                $dataArr    = array(
                                    'id_upload'     => $result->data,
                                    'id_user'       => $user_id,
                                    'status'        => 10,
                                    'create_date'   =>  $this->STORAGE_NOW(),
                                    'id_schedule'   => self::antiInjection($this->antiStealth('schedule') ?? null),
                                );

                                $result = $model->saveGlobal($dataArr, 'sipintar_mahasiswa_registration', true);

                            }

                        }else{
                            $result->code = 17;
                            $result->info = 'FAILED UPLOAD FILE to SERVER';
                        }
                        if($result->code == $result::CODE_SUCCESS){
                            $connect->commit();
                        }else{
                            $connect->rollback();
                        }
                        // print_r($alamatfile);die;
                    }else {
                        $result->code = 11;
                        $result->info = 'MOHON MASUKAN FILE ATTACHMENTS!';
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function loadregistmahasiswaAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $isData         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result         = $model->getregistmahasiswa($isUserid);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function getdataAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $table         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('table') ?? null); // buka bukaan
                    $idtable         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('id_table') ?? null); // buka bukaan
                    $status         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('status') ?? null); // buka bukaan
                    if($table){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result    = $model->getdata($isUserid, $table, $idtable, $status);


                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function statdaftarAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $id         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($id){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result    = $model->getstat($isUserid);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function getunggahanAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $id         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($id){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result    = $model->getunggahan($isUserid);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function getaktifitasAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');

                    $id         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($id){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result    = $model->getaktifitas($isUserid);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function simpandataAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();

                    $userID 		    = $userSession->get('user_id');

                    $data         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('data') ?? null); // buka bukaan
                    $isData       = json_decode(stripslashes($data));

                    if($isData){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        if($isData->table == 'pascasarjana_biodata'){
                          $dataArr        = array(
                            'userid'          => $userID,
                            'jenis_kelamin'   => $isData->jenis_kelamin,
                            'status_sipil'    => $isData->status_sipil,
                            'tgl_lahir'       => $isData->tgl_lahir,
                            'tempat_lahir'    => $isData->tempat_lahir,
                            'kabupten_lahir'  => $isData->kabupten_lahir,
                            'provinsi_lahir'  => $isData->provinsi_lahir,
                            'kewarganegaraan' => $isData->kewarganegaraan,
                            'alamat'          => $isData->alamat,
                            'rt_rw'           => $isData->rt_rw,
                            'desa'            => $isData->desa,
                            'kecamatan'       => $isData->kecamatan,
                            'kota_kabupaten'  => $isData->kota_kabupaten,
                            'provinsi'        => $isData->provinsi,
                            'kodepos'         => $isData->kodepos,
                            'telepon'         => $isData->telepon,
                            'hp'              => $isData->hp,
                            'pekerjaan'       => $isData->pekerjaan,
                            'jabatan'         => $isData->jabatan,
                            'alamat_kerja'    => $isData->alamat_kerja,
                            'telepon_kerja'   => $isData->telepon_kerja,
                            'ukuran_jaket'    => $isData->ukuran_jaket,
                            'status'          => $isData->status,
                            'create_date'     => $this->STORAGE_NOW(),
                            'update_date'     => $this->STORAGE_NOW(),
                          );
                          $proses = 'Biodata';
                        }else if($isData->table == 'pascasarjana_asal_pendidikan'){
                          $dataArr = array(
                            'userid'          => $userID,
                            'perguruan_tinggi_s' => $isData->perguruan_tinggi_s,
                            'prodi_s' => $isData->prodi_s,
                            'bulan_tahun_masuk_s' => $isData->bulan_tahun_masuk_s,
                            'bulan_tahun_keluar_s' => $isData->bulan_tahun_keluar_s,
                            'no_ijazah_s' => $isData->no_ijazah_s,
                            'ipk_s' => $isData->ipk_s,
                            'gelar_s' => $isData->gelar_s,
                            'akrediasi_pt_s' => $isData->akrediasi_pt_s,
                            'akreditasi_prodi_s' => $isData->akreditasi_prodi_s,
                            'status'          => $isData->status,
                            'create_date'     => $this->STORAGE_NOW(),
                            'update_date'     => $this->STORAGE_NOW(),
                          );
                          $proses = 'Asal Pendidikan';
                        }else if($isData->table == 'pascasarjana_prodi'){
                          $dataArr = array(
                            'userid'          => $userID,
                            'jenis_program' => $isData->jenis_program,
                            'fakultas' => $isData->fakultas,
                            'prodi' => $isData->prodi,
                            'subprodi' => $isData->subprodi,
                            'toefl' => $isData->toefl,
                            'tgl_toefl' => $isData->tgl_toefl,
                            'nilai_toefl' => $isData->nilai_toefl,
                            'tgl_tpa' => $isData->tgl_tpa,
                            'nilai_tpa' => $isData->nilai_tpa,
                            'sumber_dana' => $isData->sumber_dana,
                            'status'          => $isData->status,
                            'create_date'     => $this->STORAGE_NOW(),
                            'update_date'     => $this->STORAGE_NOW(),
                          );

                          $proses = 'Program Studi';
                        }

                        $result         = $model->saveGlobal($dataArr, $isData->table, true);

                        $dataact = array(
                            'userid' => $userID,
                            'name' => 'Input Data '. $proses,
                            'create_date' => $this->STORAGE_NOW(),
                            'update_date'=> $this->STORAGE_NOW()
                        );
                        $act = $model->saveGlobal($dataact, 'pascasarjana_aktifitas', true);
                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function unggahanAction(){

        $this->checkCsrf(); // jika false return code error

        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $fileupload      = $_FILES['file_data']['tmp_name'];

                    if(!empty($fileupload)){
                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);
                        $userSession    = $this->getSession();
                        $user_id 		= $userSession->get('user_id');
                        $getnodaftar    = $model->getnodaftar($user_id);
                        $nomordaftar = $getnodaftar->data[0]['nomor_pendaftaran'];

                        $pdfName         = $_FILES['file_data']['name'];
                        $tipes           = $_FILES['file_data']['type'];
                        $size            = $_FILES['file_data']['size'];

                        $timeName       = microtime(true);
                        $micro          = sprintf("%06d", ($timeName - floor($timeName)) * 1000000);

                        $PdfExt       = substr($pdfName, strrpos($pdfName, '.'));
                        $PdfExt       = str_replace('.','',$PdfExt); // Extension
                        $pdfName        = preg_replace("/\.[^.\s]{3,4}$/", "", $pdfName);
                        $namaUngghan        = preg_replace("/\.[^.\s]{3,4}$/", "", $pdfName);
                        // $NewPdfName     = str_replace(' ', '', $micro.'.'.$PdfExt);
                        // $createRanName  = self::generateRandomCharacter();
                        // print_r(strpos($pdfName, 'do'));die;
                        // if(!strpos($pdfName, 'asli') || !strpos($pdfName, 'do') || !strpos($pdfName, 'ijazah') || !strpos($pdfName, 'pernyataan_tujuan') || !strpos($pdfName, 'sanggup_biaya') || !strpos($pdfName, 'transkrip') ){
                        //   $result->code = 11;
                        //   $result->info = 'Nama File Tidak Sesuai!';
                        // }else{

                          $year = date("Y");
                          $month = date("m");
                          $day = date('d');

                          $pdfName = $pdfName.'.'.$PdfExt;
                          $uploaddis = '/data/pdf/pascasarjana/'.$year.'/'.$month.'/'.$day.'/'.$nomordaftar.'/';
                          $uploaddir = './public/data/pdf/pascasarjana/'.$year.'/'.$month.'/'.$day.'/'.$nomordaftar.'/';
                          if (!file_exists($uploaddir)) {
                              mkdir($uploaddir, 0777, true);
                          }

                          $alamatfile    = $uploaddir.$createRanName.$pdfName;

                          $adapter 	    = $this->getDb();
                          $connect        = $adapter->getDriver()->getConnection();

                          $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                          $model   	    = new \Application\Model\Param($storage);

                          $connect->beginTransaction();

                          if(move_uploaded_file($_FILES['file_data']['tmp_name'],$alamatfile)){
                              $dataAtt        = array(
                                  'file_name'     => $pdfName,
                                  'file_type'     => $tipes,
                                  'file_size'     => $size / 1000000, // as mb
                                  'file_dir'      => $uploaddis,
                                  'file_dir_c'    => $alamatfile,
                                  'file_ext'      => $PdfExt,
                                  'create_date'   => $this->STORAGE_NOW(),
                                  'id_user'       => $user_id,
                              );
                              // print_r($dataAtt);die;

                              $result = $model->saveGlobal($dataAtt, 'pascasarjana_registration_attachments', true);

                              if($result->code == $result::CODE_SUCCESS){
                                  $dataArr    = array(
                                      'id_upload'     => $result->data,
                                      'id_user'       => $user_id,
                                      'status'        => 10,
                                      'nama_unggahan' => $namaUngghan,
                                      'create_date'   =>  $this->STORAGE_NOW(),
                                  );

                                  $result = $model->saveGlobal($dataArr, 'pascasarjana_unggahan', true);
                                  $dataact = array(
                                      'userid' => $user_id,
                                      'name' => 'Upload file '. $pdfName,
                                      'create_date' => $this->STORAGE_NOW(),
                                      'update_date'=> $this->STORAGE_NOW()
                                  );
                                  $act = $model->saveGlobal($dataact, 'pascasarjana_aktifitas', true);

                              }

                          }else{
                              $result->code = 17;
                              $result->info = 'FAILED UPLOAD FILE to SERVER';
                          }
                          if($result->code == $result::CODE_SUCCESS){
                              $connect->commit();
                          }else{
                              $connect->rollback();
                          }
                          // print_r($alamatfile);die;
                        // }
                    }else {
                        $result->code = 11;
                        $result->info = 'MOHON MASUKAN FILE ATTACHMENTS!';
                    }

                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());
    }

    public function getdatapascasarjanaAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');
                    $role 		= $userSession->get('role');

                    $paramis         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('param') ?? null); // buka bukaan
                    if($paramis){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result    = $model->getdatapascasarjana($paramis, $isUserid, $role);


                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function approveAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');
                    $param         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('iparam') ?? null); // buka bukaan

                    if($param){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        if($param->mode == 'promotor'){
                          $table = 'pascasarjana_promotor';
                          $dataArr        = array(
                              'status'            => 1,
                              'update_date'       => $this->STORAGE_NOW(),
                              'create_date'       => $this->STORAGE_NOW(),
                          );

                          $where          = "userid = ".$param->id." and promotorid = ".$isUserid;


                        }



                        $result  = $model->updateGlobal($table, $dataArr, $where);

                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }

    public function getjadwalAction(){

        $this->checkCsrf(); // jika false return code error
        $result      = new Result();

        if($this->isLoggedIn()){

            $request     = $this->getRequest();

            if ($request->isPost()) {

                try{

                    $userSession    = $this->getSession();
                    $isUserid 		= $userSession->get('user_id');
                    $role 		= $userSession->get('role');

                    $paramis         = self::cryptoJsAesDecrypt(self::PHRASE, $this->antiStealth('param') ?? null); // buka bukaan

                    if($paramis){ // is true / istri

                        $storage 	    = \Application\Model\Param\Storage::factory($this->getDb(), $this->getConfig());
                        $model   	    = new \Application\Model\Param($storage);

                        /* check injeksi bisi karbu */
                        $param     = self::antiInjection($isData->param ?? null);

                        $result    = $model->getjadwal($paramis, $isUserid, $role);


                        /* encrypt dan return data */
                        if($result->code == $result::CODE_SUCCESS){

                            $isEncrypt = self::cryptoJsAesEncrypt(self::PHRASE, $result->toJson());

                            if($isEncrypt){
                                $result->data  = $isEncrypt;
                            }else{
                                $result->code = $result::CENC_FAILED;
                                $result->info = $result::IENC_FAILED;
                            }
                        }

                    }else{
                        $result->code = $result::CDEC_FAILED;
                        $result->info = $result::IDEC_FAILED;
                    }


                }catch (\Exception $exc) {
                    $result = new Result(0,1,$exc->getMessage() .'-'.$exc->getTraceAsString());
                }
            }else{
                $result = new Result(0,401, self::DEFAULT_ERROR);
            }
        }else{
            $result = new Result(0,401, self::DEFAULT_ERROR);
        }

        /* return data */
        return $this->getOutput($result->toJson());

    }



}
