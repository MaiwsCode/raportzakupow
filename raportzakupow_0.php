<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class raportzakupow extends Module { 

public function settings(){

    }    

function changeOrder($order){
    if($order == 1){
        return "ASC";
    }if($order == 2){
        return "DESC";
    }
}
function changeOrderNumber($order){
    if($order == 0){
        return 1;
    }
    if($order == 1){ 
        return 2;
    }
    if($order == 2){
        return 0;
    }
}

public function body(){
    Base_ThemeCommon::install_default_theme($this->get_type());
    if(isset($_REQUEST['__jump_to_RB_table'])){    
        $rs = new RBO_RecordsetAccessor($_REQUEST['__jump_to_RB_table']);
        $rb = $rs->create_rb_module ( $this );
        $this->display_module ( $rb);
    }    

    $orders = array('planed_purchase_date' => 0 , 'status'=>0);
    if($this->get_module_variable("filters")){
        $filters = $this->get_module_variable("filters");
    }
    if($this->get_module_variable("orders")){
        $orders = $this->get_module_variable("orders");
    }
    if(isset($_REQUEST['client'])){
        $actions['client'] = $_REQUEST['client']; 
    }
    if(isset($_REQUEST['trader'])){
        $filters['trader'] = $_REQUEST['trader']; 
    }
    if(isset($_REQUEST['who'])){
        $filters['traderFullName'] = $_REQUEST['who']; 
    }
    if(isset($_REQUEST['month'])){
        $filters['month'] = $_REQUEST['month']; 
    }else if (!isset($filters['month'])){
        $filters['month'] = date("m");
    }
    if(isset($_REQUEST['year'])){
        $filters['year'] = $_REQUEST['year']; 
    }else if (!isset($filters['year'])){
        $filters['year'] = date("Y");
    }
    if(isset($_REQUEST['statusFilter'])){
        $orders['status'] = $_REQUEST['statusFilter']; 
    }
    if(isset($_REQUEST['dateFilter'])){
        $orders['planed_purchase_date'] = $_REQUEST['dateFilter']; 
    }
    $statusFilter = "<a ".$this->create_href(array('statusFilter' => $this->changeOrderNumber($orders['status']) ))."> Status </a>";
    $dateFilter = "<a ".$this->create_href(array('dateFilter' => $this->changeOrderNumber($orders['planed_purchase_date']) ))."> Data </a>";

    $this->set_module_variable("orders" , $orders);
    $this->set_module_variable("filters" , $filters);

    $theme = $this->init_module('Base/Theme');

    $rbo = new RBO_RecordsetAccessor ( 'contact' );

    $traders = $rbo->get_records(array('group' => 'trader'),array(),array());
    $select_options = "<li><a ".$this->create_href(array('trader' => 0,
        'who' => "Wszyscy"." Handlowcy"))."> Wszyscy Handlowcy  </a></li>";
    foreach($traders as $trader){
        $select_options .= "<li><a ".$this->create_href(array('trader' => $trader->id,
            'who' => $trader['first_name']. " ".$trader['last_name'])).">".$trader['first_name']." ".$trader['last_name']. " </a></li>";
    }

    $select = "<ul class='drops'>
                <li>
                    <a href='#'>Wybierz handlowca </a> <img src='data/Base_Theme/templates/default/planer/drop.png' width=25 height=25 />
                        <ul>".$select_options."
                    </ul></li></ul>";

    $months = "<li><a ".$this->create_href(array('month' => '0'))."> Cały rok </a></li>";
    for($i = 1;$i<=12;$i++){
        $name = date('F', mktime(0, 0, 0, $i, 10));
        $name = __($name);
        $months .= "<li><a ".$this->create_href(array('month' => $i)).">".$name. " </a></li>";
    }      
    $month = "<ul class='drops'>
                <li>
                    <a href='#'>Miesiąc </a> <img src='data/Base_Theme/templates/default/planer/drop.png' width=25 height=25 />
                        <ul>".$months."
                    </ul></li></ul>";

    $start = date("Y");
    $end = $start;;
    $years = "";
    for($i = $start-6;$i<=$end;$i++){
        $years .= "<li><a ".$this->create_href(array('year' => $i)).">".$i. " </a></li>";
    }

    $year = "<ul class='drops'>
                <li>
                <a href='#'>Rok </a> <img src='data/Base_Theme/templates/default/planer/drop.png' width=25 height=25 />
                    <ul>".$years."
                </ul></li></ul>";        
    if(isset($_REQUEST['__jump_to_RB_table'])){    
        $rs = new RBO_RecordsetAccessor($_REQUEST['__jump_to_RB_table']);
        $rb = $rs->create_rb_module ( $this );
        $this->display_module ( $rb);
    }          
      if(isset($filters['trader'])){
        $purchase = null;

        $rbo_purchase = new RBO_RecordsetAccessor("custom_agrohandel_purchase_plans");
        $id = $filters['trader'];
        $rbo_company = new RBO_RecordsetAccessor("company");
        if($id != 0){
            $companes = $rbo_company->get_records(array('account_manager' => $id),array(),array());
        }
        else{
            $companes = $rbo_company->get_records(array('!account_manager' => ''),array(),array());
        }
        $ids_company = array();
        foreach($companes as $company){
            $ids_company[] = $company->id;
        }
        $crits = array('(status' => 'purchased' , '|status' => 'purchased_waiting', 'company' => $ids_company);
        if($filters['month'] == 0){
            $tmp = 0;
            $crits['>=planed_purchase_date'] = $filters['year']."-01-01";
            $crits['<=planed_purchase_date'] = $filters['year']."-12-31";
        }else{
            $tmp = $filters['year'].'-'.$filters['month'].'-15';
            $crits['>=planed_purchase_date'] = date("Y-m-01", strtotime($tmp));
            $crits['<=planed_purchase_date'] = date("Y-m-t", strtotime($tmp));
        }
        $ordersArray = array(); 
        if($orders['planed_purchase_date'] != 0)
            $ordersArray['planed_purchase_date'] = $this->changeOrder($orders['planed_purchase_date']);
        if($orders['status'] != 0 )
            $ordersArray['status'] = $this->changeOrder($orders['status']); 
        $purchases = $rbo_purchase->get_records($crits,array(),$ordersArray);
        foreach($purchases as $purchase){
            $company = Utils_RecordBrowserCommon::get_record('company', $purchase['company']);
            $opiekun = Utils_RecordBrowserCommon::get_record('contact', $company['account_manager']);
            $ar = array("Opiekun: " => "<div class='custom_info'>".$opiekun['first_name']." ".$opiekun['last_name']."</div>");
            $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
            $infobox = Utils_TooltipCommon::create($purchase->get_val("company",false),$infobox,$help=true, $max_width=300);
            $purchase['company'] = $infobox;
        }
        $theme->assign("css", Base_ThemeCommon::get_template_dir());
        $theme->assign('purchases',$purchases);
        $theme->assign('who',$filters['traderFullName']);
        if($tmp == 0){
            $theme->assign('month', "Cały rok");

        }else{
            $theme->assign('month',__(date("F",strtotime($tmp))));
        }
        $theme->assign('year',$filters['year']);
        $theme->assign('select',$select);
        $theme->assign('orders',$orders);
        $theme->assign('yearSelect',$year);
        $theme->assign('monthSelect',$month);
        $theme->assign('statusOrder',$statusFilter);
        $theme->assign('dateOrder',$dateFilter);


      }
      else{
        $tmp = $filters['year'].'-'.$filters['month'].'-15';
        $theme->assign("css", Base_ThemeCommon::get_template_dir());
        $theme->assign('select',$select);
        $theme->assign('yearSelect',$year);
        $theme->assign('monthSelect',$month);
        if($tmp == 0){
            $theme->assign('month', "Cały rok");

        }else{
            $theme->assign('month',__(date("F",strtotime($tmp))));
        }
        $theme->assign('year',$filters['year']);
        $theme->assign('statusOrder',$statusFilter);
        $theme->assign('dateOrder',$dateFilter);
      } 
      $theme->display();
    }

    function set_related_fields($module,$varible, $name){
        foreach($varible as $edit){
            $edit[$name] = "<a ". $module->create_href(array('client' => $edit['company'])) ."> ".$edit->get_val($name, $nolink=true) ."</a>";
        }
        return $varible;
    }
}
