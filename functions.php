//PART OF THE FILE FUNCTIONS.PHP (with including function) 

/**add lead to amoCRM from site
*
*/
add_action('wpcf7_before_send_mail', 'new_lead_amo_crm');

function new_lead_amo_crm($wpcf7){

	//add script with integration logic
	require_once( get_template_directory() . '/inc/wp-amocrm/wp-amo-script.php' );

};
