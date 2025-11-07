<?php
#
# load scripts
#
namespace VNVE;

class Scripts
{
	public $javascripts = 
	[
    
    	'https://code.jquery.com/jquery-3.5.1.js',				#jquery datepicker
		'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js',
		'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js',	#bootstrap for responsive site
		'https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js',		#java script for tables
		'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js',
		'https://code.jquery.com/jquery-1.12.4.js',										#datepicker
		'https://code.jquery.com/ui/1.12.1/jquery-ui.js',								#datepicker
	];
	public $localjavascripts =
	[
		'mdt_tables.js',
		'forms.js',			#pranamas functies voor formulieren.
		'exportcsv.js',		#export csv files
		'grenzenloos.js',	#scripts special for this plugin
		'owlcarousel.js',	#owl carousel 
		'mediauploader.js'	#upload media library wordpress
	];
	public $stylesheets =
	[
		'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css',		#bootstrap css
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
		'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
		'https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css'	#css for tables
	];
	public $localstylesheets =
	[
		'prana.css',
		'owlcarousel.css',
	];
	public $sitestylesheets =
	[
		'vnve.css',
	];
	public function OwlCarousel()
	{
		$html = '';
		$owl= bootstrap::PLUGINURL . '/vendor/owlcarousel/';
		$html .= '<link rel="stylesheet" href="' . $owl . 'dist/assets/owl.carousel.css">';
		$html .= '<link rel="stylesheet" href="' . $owl . 'dist/assets/owl.carousel.min.css">';
		$html .= '<script src="' . $owl . 'dist/owl.carousel.js' . '"></script>';
		$html .= '<script src="' . $owl . 'dist/owl.carousel.min.js' . '"></script>';
		return $html;
	}

	public $cssurl = bootstrap::PLUGINURL . '/css/';
	public $jsurl = bootstrap::PLUGINURL . '/javascript/';

	/**
	 * load the scripts
	 */
	public function LoadScripts() 
	{
		$html = '';
		$html .= '<meta charset="utf-8">';
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
		#
		# external scripts
		#
		foreach ($this->javascripts as $js) { $html .= '<script src="' . $js . '"></script>'; }
		foreach ($this->stylesheets as $css) { $html .= '<link rel="stylesheet" href="' . $css . '">'; }
		foreach ($this->localjavascripts as $js) { $html .= '<script src="' . $this->jsurl . $js . '"></script>'; }
		foreach ($this->localstylesheets as $css) { $html .= '<link rel="stylesheet" href="' . $this->cssurl . $css . '">'; }
		$html .= $this->OwlCarousel();
		return($html);
    }
		/**
	 * load the scripts
	 */
	public function LoadSiteScripts() 
	{
		$html = '';
		#
		# external scripts
		#
		foreach ($this->sitestylesheets as $css) 
		{ 
			echo $this->cssurl . $css;
			$html .= '<link rel="stylesheet" href="' . $this->cssurl . $css . '">'; 
		}
		echo $html;
    }
}