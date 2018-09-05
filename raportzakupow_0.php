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
    

public function body(){
    Base_ThemeCommon::install_default_theme($this->get_type());
    $actions = array();
    $actions['excel'] = 'import';
    if(isset($_REQUEST['client'])){
        $actions['client'] = $_REQUEST['client']; 
    }
    if(isset($_REQUEST['trader'])){
        $actions['trader'] = $_REQUEST['trader']; 
    }
    Base_ActionBarCommon::add(
        Base_ThemeCommon::get_template_file($this->get_type(), 'excel.png'),
        'Exportuj do excela', 
        $this->create_href ( $actions),
            null,
            0
    );
 function set_related_fields($module,$varible, $name){
        foreach($varible as $edit){
         $edit[$name] = "<a ". $module->create_href(array('client' => $edit['company'])) ."> ".$edit->get_val($name, $nolink=true) ."</a>";
     }
     return $varible;
    }
    $theme = $this->init_module('Base/Theme');

    $rbo = new RBO_RecordsetAccessor ( 'contact' );

    $traders = $rbo->get_records(array('group' => 'trader'),array(),array());
    foreach($traders as $trader){
        $select_options .= "<li><a ".$this->create_href(array('trader' => $trader->id,
         'who' => $trader['first_name']. " ".$trader['last_name'])).">".$trader['first_name']." ".$trader['last_name']. " </a></li>";
    }

    $select = "<ul class='drops'>
                <li>
                    <a href='#'>Wybierz handlowca </a> <img src='data/Base_Theme/templates/default/planer/drop.png' width=25 height=25 />
                        <ul>".$select_options."
                    </ul></li></ul>";
    if($_REQUEST['excel'] == "import"){

        Base_ActionBarCommon::add(
            Base_ThemeCommon::get_template_file($this->get_type(), 'download.png'),
            'Pobierz', 
            $this->create_href ( array ('excel' => 'download')),
                null,
                0
        );

    }
    if($_REQUEST['excel'] == "download"){
        if(isset($_REQUEST['client'])){
            $_REQUEST['client'] = $_REQUEST['client']; 
        }
        if(isset($_REQUEST['trader'])){
             $_REQUEST['trader'] = $_REQUEST['trader']; 
        }
        Base_ActionBarCommon::add(
            Base_ThemeCommon::get_template_file($this->get_type(), 'download.png'),
            'Pobierz', 
            Epesi::redirect($_SERVER['document_root']."/excel.xls"),
                null,
                0
        );

    }
    if(isset($_REQUEST['__jump_to_RB_table'])){    
        $rs = new RBO_RecordsetAccessor($_REQUEST['__jump_to_RB_table']);
        $rb = $rs->create_rb_module ( $this );
        $this->display_module ( $rb);
    }          
      if(isset($_REQUEST['trader'])){
        $purchase = null;
        $_SESSION['trader'] = $_REQUEST['who'];

        $rbo_purchase = new RBO_RecordsetAccessor("custom_agrohandel_purchase_plans");
        $id = intval($_REQUEST['trader']);
        $rbo_company = new RBO_RecordsetAccessor("company");
        $companes = $rbo_company->get_records(array('account_manager' => $id),array(),array());
        $ids_company = array();
        foreach($companes as $company){
            $ids_company[] = $company->id;
        }
        $purchase = $rbo_purchase->get_records(array(
         'company' => $ids_company, 
         '>=planed_purchase_date' => '2017-02-10', 
         '<=planed_purchase_date' =>  date('Y-m-d'),
         '(status' => 'purchased' , '|status' => 'purchased_waiting'
        ),array(),array('planed_purchase_date' => 'asc' , 'company' => 'asc'));
        $purchase = set_related_fields($this,$purchase, 'company');
        if($_REQUEST['excel'] == 'import'){
            unlink('excel.xls');
            $excel = fopen("excel.xls", "a");
            $line = "Data\tFirma\tStatus";
            fwrite($excel , $line."\n" );
            foreach($purchase as $p){
                $line = $p['planed_purchase_date']."\t".trim(strip_tags($p['company']))."\t".__($p->get_val('status',true));
                fwrite($excel , $line."\n" );
            }
            fclose($excel);

        }
        $theme->assign("css", Base_ThemeCommon::get_template_dir());
        $theme->assign('purchases',$purchase);
        $theme->assign('who',$_REQUEST['who']);
        $theme->assign('select',$select);

      }
      else if (isset($_REQUEST['client']) && isset($_SESSION['trader'])){
        $purchase = null;
        $rbo_purchase = new RBO_RecordsetAccessor("custom_agrohandel_purchase_plans");
        $id = intval($_REQUEST['trader']);
        $rbo_company = new RBO_RecordsetAccessor("company");
        $companes = $rbo_company->get_records(array('account_manager' => $id),array(),array());
        $ids_company = array();
        $purchase = $rbo_purchase->get_records(array(
         'company' => $_REQUEST["client"], 
         '>=planed_purchase_date' => '2017-02-10', 
         '<=planed_purchase_date' =>  date('Y-m-d'),
         '(status' => 'purchased' , '|status' => 'purchased_waiting'
        ),array(),array('planed_purchase_date' => 'asc' , 'company' => 'asc'));

        $purchase = set_related_fields($this,$purchase, 'company');
        if($_REQUEST['excel'] == 'import'){
            unlink('excel.xls');
            $excel = fopen("excel.xls", "a");
            $line = "Data\tFirma\tStatus";
            fwrite($excel , $line."\n" );
            foreach($purchase as $p){
                $line = $p['planed_purchase_date']."\t".trim(strip_tags($p['company']))."\t".__($p->get_val('status',true));
                fwrite($excel , $line."\n" );
            }
            fclose($excel);

        }
        $theme->assign("css", Base_ThemeCommon::get_template_dir());
        $theme->assign('purchases',$purchase);
        $theme->assign('who',$_SESSION['trader']);
        $theme->assign('select',$select);
      }
      else{
        $theme->assign("css", Base_ThemeCommon::get_template_dir());
        $theme->assign('select',$select);
      } 
      $theme->display();
    }
}
