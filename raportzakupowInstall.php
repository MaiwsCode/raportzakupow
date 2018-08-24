<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class raportzakupowInstall extends ModuleInstall {

    public function install() {
// Here you can place installation process for the module
    $ret = true;
    Base_ThemeCommon::install_default_theme($this->get_type());
    Base_ThemeCommon::install_default_theme('raportzakupow');
        return $ret; // Return false on success and false on failure
    }

    public function uninstall() {
// Here you can place uninstallation process for the module
        $ret = true;
        return $ret; // Return false on success and false on failure
    }

    public function requires($v) {
// Returns list of modules and their versions, that are required to run this module
        return array(); 
    }
    public function version() {
	// Return version name of the module
        return array('1.0'); 
    }
	public function info() { // Returns basic information about the module which will be available in the epesi Main Setup
		return array (
				'Author' => 'Mateusz Kostrzewski',
				'License' => 'MIT 1.0',
				'Description' => '' 
		);
	}
    public function simple_setup() {
// Indicates if this module should be visible on the module list in Main Setup's simple view
        return true; 
    }

}

?>