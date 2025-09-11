<?php
if(!function_exists('display_plugin_option')) {
	function display_plugin_option($args) {
		$name = $args['name'];
		$value = get_option($name);
		$type = $args['type'];
		$options = $args['options'] ?? [];
		$attributes = $args['attributes'] ?? '';
		switch ($type) {
			case 'number':
			case 'float':
				$value = ($type == 'number' ? intval($value) : floatval($value));
				echo '<input type="number"'.($type == 'float' ? ' step="any"' : '').' id="'.$name.'" name="'.$name.'" value="'.$value.'" '.$attributes.'>';
				break;
			case 'text':
			case 'email':
			case 'password':
			case 'date':
			case 'time':
				echo '<input type="'.$type.'" id="'.$name.'" name="'.$name.'" value="'.$value.'" '.$attributes.'>';
				break;
			case 'checkbox':
				$checked = (boolval($value)?' checked':'');
				$value = (boolval($value)?'on':'off');
				echo '<input type="'.$type.'" id="'.$name.'" name="'.$name.'" value="'.$value.'" '.$attributes.$checked.'>';
				break;
			case 'radio':
				foreach ($options as $key => $label ) {
					$checked = ($key==$value?' checked':'');
					echo '<input type="'.$type.'" id="'.$key.'" name="'.$name.'" value="'.$key.'" '.$attributes.$checked.'>'.
							'&nbsp;<label for="'.$key.'">'.__($label, 'alesagglo-offres-emploi').'</label><br>';
				}
				break;
			case 'select':
				echo '<select id="'.$name.'" name="'.$name.'" '.$attributes.'>';
				foreach ($options as $key => $label ) {
					$selected = ($key==$value?' selected':'');
					echo '<option value="'.$key.'" '.$selected.'>'.__($label, 'alesagglo-offres-emploi').'</option>';
				}
				echo '</select>';
				break;

		}
	}
}
