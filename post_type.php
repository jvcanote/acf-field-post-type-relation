<?php

class acf_field_post_type extends acf_field
{
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	1.0
	*  @date	01/04/15
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'post_type';
		$this->label = __("Post Type",'acf');
		$this->category = __("Relational",'acf');
		$this->defaults = array(
			'multiple' 		=>	0,
			'allow_null' 	=>	0,
			'choices'		=>	array(),
		);
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// extra
		//add_filter('acf/update_field/type=select', array($this, 'update_field'), 5, 2);
		add_filter('acf/update_field/type=checkbox', array($this, 'update_field'), 5, 2);
		add_filter('acf/update_field/type=radio', array($this, 'update_field'), 5, 2);
	}

	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	1.0
	*  @date	01/04/15
	*/
	
	function create_field( $field )
	{
		// vars
		$optgroup = false;
		
		
		// determin if choices are grouped (2 levels of array)
		if( is_array($field['choices']) )
		{
			foreach( $field['choices'] as $k => $v )
			{
				if( is_array($v) )
				{
					$optgroup = true;
				}
			}
		}
		
		
		// value must be array
		if( !is_array($field['value']) )
		{
			// perhaps this is a default value with new lines in it?
			if( strpos($field['value'], "\n") !== false )
			{
				// found multiple lines, explode it
				$field['value'] = explode("\n", $field['value']);
			}
			else
			{
				$field['value'] = array( $field['value'] );
			}
		}
		
		
		// trim value
		$field['value'] = array_map('trim', $field['value']);
		
		
		// multiple select
		$multiple = '';
		if( $field['multiple'] )
		{
			// create a hidden field to allow for no selections
			echo '<input type="hidden" name="' . $field['name'] . '" />';
			
			$multiple = ' multiple="multiple" size="5" ';
			$field['name'] .= '[]';
		} 
		
		
		// html
		echo '<select id="' . $field['id'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '" ' . $multiple . ' >';	
		
		
		// null
		if( $field['allow_null'] )
		{
			echo '<option value="null">- ' . __("Select",'acf') . ' -</option>';
		}
		
		// loop through values and add them as options
		if( is_array($field['choices']) )
		{
			foreach( $field['choices'] as $key => $value )
			{
				if( $optgroup )
				{
					// this select is grouped with optgroup
					if($key != '') echo '<optgroup label="'.$key.'">';
					
					if( is_array($value) )
					{
						foreach($value as $id => $label)
						{
							$selected = in_array($id, $field['value']) ? 'selected="selected"' : '';
														
							echo '<option value="'.$id.'" '.$selected.'>'.$label.'</option>';
						}
					}
					
					if($key != '') echo '</optgroup>';
				}
				else
				{
					$selected = in_array($key, $field['value']) ? 'selected="selected"' : '';
					echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
				}
			}
		}

		echo '</select>';
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	1.0
	*  @date	01/04/15
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		$key = $field['name'];
		$fake=array();

		// implode choices so they work in a textarea
		if( is_array($field['choices']) )
		{		
			$fake['choices'] = array();
			foreach( $field['choices'] as $k => $v )
			{	
				$fake['choices'][ $k ] = $k;
			}
			$fake['choices'] = implode("\n", $fake['choices']);
		}

		?>

<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Post Type",'acf'); ?></label>
	</td>
	<td>
		<?php 
		
		$choices = array(
			'all'	=>	__("All",'acf')
		);
		$choices = apply_filters('acf/get_post_types', $choices);
		
		
		do_action('acf/create_field', array(
			'type'	=>	'select',
			'name'	=>	'fields['.$key.'][choices]',
			'value'	=>	$fake['choices'],
			'choices'	=>	$choices,
			'multiple'	=>	1,
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Allow Null?",'acf'); ?></label>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][allow_null]',
			'value'	=>	$field['allow_null'],
			'choices'	=>	array(
				1	=>	__("Yes",'acf'),
				0	=>	__("No",'acf'),
			),
			'layout'	=>	'horizontal',
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Select multiple values?",'acf'); ?></label>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][multiple]',
			'value'	=>	$field['multiple'],
			'choices'	=>	array(
				1	=>	__("Yes",'acf'),
				0	=>	__("No",'acf'),
			),
			'layout'	=>	'horizontal',
		));
		?>
	</td>
</tr>
<?php
		
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	1.0
	*  @date	01/04/15
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		if( $value == 'null' )
		{
			$value = false;
		}
		
		
		return $value;
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	1.0
	*  @date	01/04/15
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field, $post_id )
	{
		
		// check if is array. Normal back end edit posts a textarea, but a user might use update_field from the front end
		// if( is_array( $field['choices'] ))
		// {
		//     return $field;
		// }

		
		// vars
		$new_choices = array();
		
		
		// explode choices from each line
		if( $field['choices'] )
		{
						
			// key => value
			foreach($field['choices'] as $choice)
			{

				unset($post_type_object, $post_type_name);
				$post_type_object = get_post_type_object( $choice );
				$post_type_name = ($post_type_object) ? $post_type_object->labels->name : $choice;

				$new_choices[ trim($choice) ] = trim($post_type_name);

			}
		}
		
		
		// update choices
		$field['choices'] = $new_choices;
		
		
		return $field;
	}
	
}

new acf_field_post_type();

?>
