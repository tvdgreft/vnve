<?php
namespace VNVE;
/**
 * Make a dashboardmenu
 */
class DashboardMenu
{
	function init()
	{
		// create custom plugin settings menu
		add_action('admin_menu', array($this,'main') );
		add_action('admin_menu', array($this,'settings') );
		add_action('admin_menu', array($this,'docman') );
		add_action('admin_menu', array($this,'grenzenloosbeheer') );
		add_action('admin_menu', array($this,'vnveinfo') );
		add_action('admin_menu', array($this,'vnvebackup') );
	}

	function main() 
	{
		//create new top-level menu
		$options = new options();
		$name=bootstrap::PluginName();
		add_menu_page(	
				$name,						#pagetitle
				$name, 									#menutitle
				'administrator',							#capability
				__FILE__ . "main",									#menuslug
				array($this,'intro'),				#function
				bootstrap::PLUGINURL . '/images/vnvemenu.png'	#icom
		);
	}
	function settings() 
	{
		//create new top-level menu
		$options = new options();
		$name=bootstrap::PluginName();
		add_submenu_page(	
				__FILE__ . "main",							#parentslug
				$name . ' settings',						#pagetitle
				$name . ' settings', 						#menutitle
				'administrator',							#capability
				__FILE__ . "settings",									#menuslug
				array($options,'settings_page') ,				#function
		);
	//call register settings function
		add_action( 'admin_init', array($options,'register_settings') );
	}
	/**
	 * document manager
	 */
	function docman() 
	{
		//create new top-level menu
		$backend = new backend();
		$name=bootstrap::PluginName();
		add_submenu_page(	
				__FILE__ . "main",									#parentslug
				$name . ' docman',						#pagetitle
				$name . ' docman', 									#menutitle
				'administrator',							#capability
				__FILE__ . "docman",									#menuslug
				array($this,'DocmanManager') ,				#function
		);
	}
	function DocmanManager()
	{
		$backend = new backend();
		$slug=bootstrap::MenuUrl() . 'dashboardmenu.phpdocman';
		$args = ["prefix"=>"vnve","form"=>"postzegel.xml","function"=>"docman","task"=>"manager","action"=>$slug];
		$html = $backend->init($args);
		echo $html;
		return;
	}
	function grenzenloosbeheer() 
	{
		//create new top-level menu
		$backend = new backend();
		$name=bootstrap::PluginName();
		add_submenu_page(	
				__FILE__ . "main",									#parentslug
				$name . ' grenzenloos',						#pagetitle
				$name . ' grenzenloos', 									#menutitle
				'administrator',							#capability
				__FILE__ . "grenzenloos",									#menuslug
				array($this,'GrenzenloosManager') ,				#function
		);
	}
	/**
	* Info about php version_compare
	**/
	function vnveinfo() 
	{
		//create new top-level menu
		$backend = new backend();
		$name=bootstrap::PluginName();
		add_submenu_page(	
				__FILE__ . "main",									#parentslug
				$name . ' info',						#pagetitle
				$name . ' info', 									#menutitle
				'administrator',							#capability
				__FILE__ . "vnveinfo",									#menuslug
				array($this,'InfoVNVE') ,				#function
		);
	}
	function vnvebackup() 
	{
		//create new top-level menu
		$backend = new backend();
		$name=bootstrap::PluginName();
		add_submenu_page(	
				__FILE__ . "main",									#parentslug
				$name . ' backup',						#pagetitle
				$name . ' backup', 									#menutitle
				'administrator',							#capability
				__FILE__ . "vnvebackup",									#menuslug
				array($this,'BackupVNVE') ,				#function
		);
	}

	function GrenzenloosManager()
	{
		$backend = new backend();
		$slug=bootstrap::MenuUrl() . 'dashboardmenu.phpgrenzenloos';
		$args = ["prefix"=>"grens","function"=>"grenzenloos","task"=>"manager","action"=>$slug];
		$html = $backend->init($args);
		echo $html;
		return;
	}
	/**
	 * backp vnve tables
	 */
	function BackupVNVE()
	{
		$backend = new backend();
		$slug=bootstrap::MenuUrl() . 'dashboardmenu.phpvnvebackup';
		$args = ["prefix"=>"grens","function"=>"backup","task"=>"manager","action"=>$slug];
		$html = $backend->init($args);
		echo $html;
		return;
	}
	/**
	 * php info
	 */
	function InfoVNVE()
	{
		$backend = new backend();
		$slug=bootstrap::MenuUrl() . 'dashboardmenu.phpvnvebackup';
		$args = ["prefix"=>"grens","function"=>"grenzenloos","task"=>"info","action"=>$slug];
		$html = $backend->init($args);
		echo $html;
		return;
	}

	function intro()
	{
		$html = '';
		$html = '<h1>Beheer functies VNVE</h1>';
		echo $html;
	}
}