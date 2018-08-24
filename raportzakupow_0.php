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

 function set_related_fields($varible, $name){
        foreach($varible as $edit){
         $edit[$name] = ($edit->get_val($name));
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

    if(isset($_REQUEST['__jump_to_RB_table'])){    
        $rs = new RBO_RecordsetAccessor($_REQUEST['__jump_to_RB_table']);
        $rb = $rs->create_rb_module ( $this );
        $this->display_module ( $rb);
    }          
      if(isset($_REQUEST['trader'])){
        $purchase = null;
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
         '>=planed_purchase_date' => '2017-01-01', 
         '<=planed_purchase_date' =>  date('Y-m-d'),
         '(status' => 'purchased' , '|status' => 'purchased_waiting'
        ),array(),array('planed_purchase_date' => 'asc' , 'company' => 'asc'));

        $purchase = set_related_fields($purchase, 'company');
        $theme->assign("css", Base_ThemeCommon::get_template_dir());
        $theme->assign('purchases',$purchase);
        $theme->assign('who',$_REQUEST['who']);
        $theme->assign('select',$select);

      }
      else{
        $theme->assign("css", Base_ThemeCommon::get_template_dir());
        $theme->assign('select',$select);
      } 
      $theme->display();
    }
}
