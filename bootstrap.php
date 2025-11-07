<?php

namespace VNVE;
class Bootstrap
{
/**
 * Checks if the system requirements are met
 *
 * @return bool True if system requirements are met, false if not
 */
	const NAMESPACE = 'VNVE';
	const SHORTCODE = 'vnve';
	const NAME = 'vnve';
	const REQUIRED_PHP_VERSION = '7.0';
	const REQUIRED_WP_VERSION = '3.1';
	const PLUGINNAME = "vnve";
	const PLUGINURL = WP_PLUGIN_URL .'/' . self::PLUGINNAME;
	const PLUGINPATH = ABSPATH . 'wp-content/plugins/' . self::PLUGINNAME;
	#const PLUGINPATH = JPATH_SITE;	#joomla
	/** Definieer constantes for wordpress*/
	protected function DefineConstants()
	{
		define ('PRANA_PLUGINNAME' , 'vnve');
		define ('PRANA_CMS','wordpress');
		if(PRANA_CMS == 'wordpress')
		{
			define ('PRANA_PLUGINPATH',ABSPATH . 'wp-content/plugins/' . PRANA_PLUGINNAME);
			define ('PRANA_PLUGINURL' , WP_PLUGIN_URL .'/' . PRANA_PLUGINNAME);
			define ('PRANA_HOMEURL',home_url());
		}
		if(PRANA_CMS == 'joomla')
		{
			define ('PRANA_PLUGINPATH', JPATH_SITE);
			define('PRANA_HOMEURL',\JURI::current());	#joomla url from which the plugin has been started
		}
	}
	/**
	 * get the current url from with the plugin is started
	 */
	public static function CurrentUrl()
	{ 
			if(PRANA_CMS == 'joomla')
			{
					$url=\JURI::current();	#joomla url from which the plugin has been started
			}
			if(PRANA_CMS == 'wordpress')
			{
				GLOBAL $wp;
				$url= home_url(add_query_arg(array(), $wp->request));	#wordpres url
			}
			return($url);

	}
	public static function MenuUrl()
	{ 
			#$url=\JURI::current();	#joomla url from which the plugin has been started
			GLOBAL $wp;
			$url= home_url() . '/wp-admin/admin.php?page=' . self::NAME . '/classes/';	#wordpres url
			return($url);

	}
	public static function NameSpace()
	{ 
		return(self::NAMESPACE);
	}
	public static function PluginName()
	{ 
		return(self::PLUGINNAME);
	}
	protected function requirements() 
	{
		global $wp_version;

		if ( version_compare( PHP_VERSION, self::REQUIRED_PHP_VERSION, '<' ) ) {
			return false;
		}

		if ( version_compare( $wp_version, self::REQUIRED_WP_VERSION, '<' ) ) {
			return false;
		}
		return true;
	}
		/** Laadt functiebestanden */
	public function LoadFunctions() 
	{
		$self = new self();
		$files = glob($self::PLUGINPATH . '/functions/*.php');
		foreach ( $files as $file ) {
			require_once $file;
		}
	}

/**
 * Prints an error that the system requirements weren't met.
 */
	protected function requirements_error() 
	{
		global $wp_version;
		echo notices::requirements_error();
	}
	protected function trap()
	{
		$self = new self();
		echo notices::trap($self::PLUGINNAME . " in trap" . $self::NAMESPACE );
	}
	#
	# autoloader for the classes defined in map classes
	#
	protected function autoloader()
	{
		spl_autoload_register(function ($class_name)
		{
			$self = new self();
			#echo $class_name;
			$parts = explode( '\\', $class_name );
			if($parts[0] == $self::NAMESPACE)
			{
				$classfile=strtolower($parts[1]);
				require_once( dirname( __FILE__ ) . '/classes/' . $classfile . '.php' );
			}
		});
	}
	/**
	*	change theme settings
	*  set font
	*/
	public function  AddingStyles()
	{
		$html = '';	
		$fonturl = PRANA_PLUGINURL . "/" . "fonts";
		$cssurl = PRANA_PLUGINURL . '/css/';
		$mycssfile = PRANA_PLUGINPATH . '/css/fonts.css';
		#echo $mycssfile;
		$mycss = fopen($mycssfile, "w") or die("Unable to open file!");
		$html .= "@font-face {\n";
		$html .= "\tfont-family: 'Larke_Neue_Regular';\n";
		$html .= "\tfont-style: normal;\n";
		$html .= "\tfont-weight: normal;\n";
		$html .= "\tsrc: url('" . $fonturl . "/LarkeNeueRegular.woff') format('woff');\n";
		$html .= "\tsrc: url('" . $fonturl . "/LarkeNeueRegular.woff2') format('woff2');\n";
		$html .= "\tsrc: url('" . $fonturl . "/LarkeNeueRegular.ttf') format('truetype');\n";
		$html .= "}\n";
		fwrite($mycss, $html);
		#$html .= '<link rel="stylesheet" href="' . $cssurl . "nicc.css" . '">';
		$url =  $cssurl . "fonts.css";
		#echo $url;
		if(wp_register_style('my_stylesheet', $url) == FALSE)
		{
			echo "Cannot register style";
		}
		wp_enqueue_style('my_stylesheet');
		#echo $html;
	}
	function tu_full_post_search( $show_excerpt )
	{
		return false;
	
	}
	/**
	 * Zorgt er voor dat ingelogde gebruikers een andere homepage te zien krijgen
	 * dan niet ingelogde gebruikers
	 */
	function custom_redirect_home_page() 
	{
		if ( is_user_logged_in() ) {
			// Redirect ingelogde gebruikers naar een specifieke pagina
			wp_redirect( home_url('/home-leden') );
			exit;
		} else {
			// Redirect niet-ingelogde gebruikers naar een andere pagina
			wp_redirect( home_url('/') );
			exit;
		}
	}
	/**
	 * function to open the wordpress media library
	 */
	function my_enqueue_media_lib_uploader() {

		//Core media script
		wp_enqueue_media();
	
		// Your custom js file
		wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
		wp_enqueue_script( 'media-lib-uploader-js' );
	}
	/**
	 * display username
	 */
	function show_loggedin_function( $atts ) {

		global $current_user, $user_login;
		get_currentuserinfo();
		add_filter('widget_text', 'do_shortcode');
		if ($user_login) 
			return 'Welkom ' . $current_user->display_name . '!';
		else
			return '<a href="' . wp_login_url() . ' ">Login</a>';
	}
	/**
	* change reset password link test
	**/
	function my_retrieve_password_message( $message, $key, $user_login, $user_data ) 
	{
		// Start with the default content.
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$message = __( 'In verband met de overgang naar en nieuwe webzite is het nodig uw wachtwoord aan te passen.' ) . "\r\n\r\n";
		/* translators: %s: site name */
		$message .= sprintf( __( 'Site Name: %s' ), $site_name ) . "\r\n\r\n";
		/* translators: %s: user login */
		$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
		$message .= __( 'Om uw wachtwoord opnieuw op te geven, bezoek het volgende adres:' ) . "\r\n\r\n";
		$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";
		return $message;
	}
	/**
	 * start definitions and installations..
	 */
	public function init()
	{
		$self = new self();
		$this->DefineConstants();
		$this->LoadFunctions();
		$this->autoloader();	#start autoloader for loading classes automatically
		$dashboardmenu = new dashboardmenu();
		$frontend = new frontend();
		$scripts = new scripts();
		if ( $this->requirements() ) 
		{
			# set shortcode, so plugin can be started in an article like: [pluginname args]
			add_shortcode( 'show_loggedin_as', 'show_loggedin_function' );	#display username
			add_shortcode( $self::SHORTCODE, array($frontend ,'init') );
			$dashboardmenu->init();		#make dashboardmenu
			add_action( 'wp_enqueue_scripts', [ $this, 'AddingStyles'] );
			#dd_action('template_redirect', [ $this, 'custom_redirect_home_page']);
			add_action('admin_enqueue_scripts', [$this ,'my_enqueue_media_lib_uploader']); #media librray wordpress
			add_filter( 'retrieve_password_message',[$this, 'my_retrieve_password_message'], 10, 4 ); # change password reset message
		}  
		else 
		{
			add_action( 'admin_notices', array($this,'requirements_error') ); # error message 
		}
		add_filter( 'generate_show_excerpt',array($self,'tu_full_post_search' ));	#show full posts. (no read more)
	}
}