<?php
namespace VNVE;

class Options
{
	function Parameters()
	{
		$params = array();
		$form = bootstrap::PLUGINPATH . '/forms/options.xml';						#read the formfields for options.
		$xml = simplexml_load_file($form) or die("Error: Cannot create object");
		foreach ($xml as $fieldset)
		{
            foreach($fieldset as $field)
            {
				$a = $field->attributes();
				$name = (string)$a->name;
				$params[$name] = get_option($name);
			}
		}
		return($params);
	}
	function register_settings() 
	{
		$form = bootstrap::PLUGINPATH . '/forms/options.xml';						#read the formfields for options.
		$xml = simplexml_load_file($form) or die("Error: Cannot create object");

		foreach ($xml as $fieldset)
		{
            foreach($fieldset as $field)
            {
				$a = $field->attributes();
				$name= '' . $a->name;
				register_setting( 'settings-group', $name );
			}
		}
	}

	function settings_page()
	{
		$html = '';
		$html .= '<div class="wrap">';
		$html .= '<h1>' . bootstrap::PluginName() . '</h1>';

		$html .= '<form method="post" action="options.php">';
		echo $html;
		settings_fields( 'settings-group' );
		do_settings_sections( 'settings-group' );
		
		$html = '';
		$html .= '<table class="form-table">';
		
		$form = bootstrap::PLUGINPATH . '/forms/options.xml';						#read the formfields for options.
		$xml = simplexml_load_file($form) or die("Error: Cannot create object");

		foreach ($xml as $fieldset)
		{
			$s = $fieldset->attributes();
			$inrow=FALSE;
			#$html .= '<br>' . $s->name;
            foreach($fieldset as $field)
            {
				$a = $field->attributes();
				$html .= '<tr valign="top">';
				$html .= '<th scope="row">' . $a->label . '</th>';
				$name = "" . $a->name;
				if($a->type == "radio")
				{
					$value = esc_attr(get_option($name));
					$html .= '<td>';
					$options = explode(',',$a->options);
					foreach($options as $option)
					{
						$selected="";
						if($value == $option) { $selected = " checked";}
						$html .= '<input type="' . $a->type . '" name="' . $name . '" value="' . $option . '"' . $selected . '/>' . $option . '<br>';
					}
					$html .= '</td>';
				}
				else
				{
					$value = esc_attr(get_option($name));
					if(!$value) { $value = $a->default;}
					$html .= '<td><input type="' . $a->type . '" name="' . $name . '" style="width:400px" value="' . $value . '" /></td>';
				}
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
		echo $html;
		submit_button();
	
		$html = '';
		$html .= '</form>';
		$html .= '</div>';
		echo $html;
	}
}

?>