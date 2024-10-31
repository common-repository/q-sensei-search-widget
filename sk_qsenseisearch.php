<?php
/*
Plugin Name: Q-Sensei Search Widget
Plugin URI: http://www.steffen-kuegler.de/web/q-sensei-search-plugin-for-wordpress/
Description: Allows the user to search Q-Sensei directly from wordpress blog.
Author: Steffen Kuegler
Version: 1.4
Author URI: http://www.steffen-kuegler.de/
License: GPL 2.0, @see http://www.gnu.org/licenses/gpl-2.0.html
*/

class sk_qsenseisearch {

    function init() {
    	// check for the required WP functions, die silently for pre-2.2 WP.
    	if (!function_exists('wp_register_sidebar_widget'))
    		return;
        
        // let WP know of this plugin's widget view entry
    	wp_register_sidebar_widget('sk_qsenseisearch', 'Q-Sensei Search', array('sk_qsenseisearch', 'widget'),
            array(
            	'classname' => 'sk_qsenseisearch',
            	'description' => 'Allows the user to search Q-Sensei directly drom wordpress blog.'
            )
        );
    
        // let WP know of this widget's controller entry
    	wp_register_widget_control('sk_qsenseisearch', 'Q-Sensei Search', array('sk_qsenseisearch', 'control'),
    	    array('width' => 300)
        );

        // short code allows insertion of sk_qsenseisearch into regular posts as a [sk_qsenseisearch] tag. 
        // From PHP in themes, call do_shortcode('sk_qsenseisearch');
        add_shortcode('sk_qsenseisearch', array('sk_qsenseisearch', 'shortcode'));
    }
    		
	// back end options dialogue
	function control() {
	    $options = get_option('sk_qsenseisearch');
		if (!is_array($options))
			$options = array('title'=> 'Q-Sensei Search', 'label'=> 'Search Q-Sensei', 'fieldwidth'=> '200');
		if ($_POST['sk_qsenseisearch-submit']) {
			$options['title'] = strip_tags(stripslashes($_POST['sk_qsenseisearch-title']));
			$options['label'] = strip_tags(stripslashes($_POST['sk_qsenseisearch-label']));
			update_option('sk_qsenseisearch', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$label = htmlspecialchars($options['label'], ENT_QUOTES);

		echo '<p style="text-align:right;"><label for="sk_qsenseisearch-title">' . 'Title:' .
		' <input style="width: 200px;" id="sk_qsenseisearch-title" name="sk_qsenseisearch-title" type="text" value="'.$title.'" /></label></p>';
		
        echo '<p style="text-align:right;"><label for="sk_qsenseisearch-label">' .'Label:' .
		' <input style="width: 200px;" id="sk_qsenseisearch-label" name="sk_qsenseisearch-label" type="text" value="'.$label.'" /></label></p>';
		
        echo '<input type="hidden" id="sk_qsenseisearch-submit" name="sk_qsenseisearch-submit" value="1" />';
	}

    function view($is_widget, $args=array()) {
    	if($is_widget) extract($args);
    
    	// get widget options
    	$options = get_option('sk_qsenseisearch');
    	$title = $options['title'];
    	$label = $options['label'];
        
    	// the widget's form
		$out[] = $before_widget . $before_title . $title . $after_title;
		$out[] = '<div style="margin-top:5px;">';
		$out[] = <<<FORM

<form id='sk_qsenseisearch_form' onsubmit="javascript: f = document.getElementById('sk_qsenseisearch_searchstring'); if(f.value == '{$label}') { f.value = ''; }" method='get' action="http://www.qsensei.com/search" target="_blank">
	<div style="padding: 3px; background: #DBE2E7;" id="sk_qsenseisearch_sf_wrapper"><table style="width: 100%; background: #DBE2E7; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;">
        <tr>
        <td style="width: 100%; padding: 0pt; vertical-align: top;">
        <input id='sk_qsenseisearch_searchstring' style="background-color:#FFFFFF; border:1px solid #999999; color:#333333; font-size:12px; height:20px; width: 100%; padding:5px 0 0 0;" type='text' name='q.0.tx' value="{$label}" onfocus="javascript: this.value=''" />
        </td>
        <td style="padding: 0pt; vertical-align: top;">
        <input style="margin-left: 4px; display: block;" type='image' src="http://www.qsensei.com/++resource++img/litsearch/search_button_general.gif" />
        </td>
        </tr>
        </table></div>    
</form>

FORM;
		$out[] = '</div>';
    	$out[] = $after_widget;
    	return join($out, "\n");
    }

    function shortcode($atts, $content=null) {
        return sk_qsenseisearch::view(false);
    }

    function widget($atts) {
        echo sk_qsenseisearch::view(true, $atts);
    }
}

add_action('widgets_init', array('sk_qsenseisearch', 'init'));

?>