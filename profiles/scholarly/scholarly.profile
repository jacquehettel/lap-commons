<?php

/**
 * Implements hook_install_tasks_alter().
 */
function scholarly_install_tasks_alter(&$tasks, $install_state) {
	$install_configure_form = (array) $tasks['install_configure_form'];
	$install_import_locales_remaining = (array) $tasks['install_import_locales_remaining'];
	$install_finished = (array) $tasks['install_finished'];

	unset(
		$tasks['install_configure_form'],
		$tasks['install_import_locales_remaining'],
		$tasks['install_finished']
	);

	$tasks['scholarly_content'] = array(
	'display_name' => st('Install profile content'),
	'type' => 'normal',
	'run' => INSTALL_TASK_RUN_IF_NOT_COMPLETED,
	);

	$tasks['install_configure_form'] = $install_configure_form;
	$tasks['install_import_locales_remaining'] = $install_import_locales_remaining;
	$tasks['install_finished'] = $install_finished;
}

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * Allows the profile to alter the site configuration form.
 */
function scholarly_form_install_configure_form_alter(&$form, $form_state) {
	$form['site_information']['site_name']['#default_value'] = 'Scholarly';
    $form['#submit'][] = 'scholarly_install_configure_form_submit';
}

/**
 * Forms API submit for the site configuration form.
 */
function scholarly_install_configure_form_submit($form, &$form_state) {
	global $user;
	
	variable_set('site_name', $form_state['values']['site_name']);
	variable_set('site_mail', $form_state['values']['site_mail']);
	variable_set('date_default_timezone', $form_state['values']['date_default_timezone']);
	variable_set('site_default_country', $form_state['values']['site_default_country']);
	
	// Enable update.module if this option was selected.
	if ($form_state['values']['update_status_module'][1]) {
		module_enable(array('update'), FALSE);
		
		// Add the site maintenance account's email address to the list of
		// addresses to be notified when updates are available, if selected.
		if ($form_state['values']['update_status_module'][2]) {
		  variable_set('update_notify_emails', array($form_state['values']['account']['mail']));
		}
	}
  
	// We precreated user 1 with placeholder values. Let's save the real values.
	$account = user_load(1);
	$merge_data = array('init' => $form_state['values']['account']['mail'], 'roles' => !empty($account->roles) ? $account->roles : array(), 'status' => 1, 'timezone' => $form_state['values']['date_default_timezone']);
	user_save($account, array_merge($form_state['values']['account'], $merge_data));
	// Load global $user and perform final login tasks.
	
	db_insert('users_roles')
    ->fields(array('uid' => 1, 'rid' => '3'))
    ->execute();
	
	$user = user_load(1);
	user_login_finalize();

	// Record when this install ran.
	variable_set('install_time', $_SERVER['REQUEST_TIME']);
  
	theme_enable(array('scholarly', 'seven'));
	variable_set('theme_default', 'scholarly');
	variable_set('admin_theme', 'seven');
	variable_set('file_temporary_path', 'sites/default/files/tmp');
	
	drupal_set_message("Scholarly Easy Installation was a Success!");

}

function scholarly_content(&$install_state) {
	$sql_file=dirname(__FILE__).'/scholarly.sql';
	$count=scholarly_import_sql($sql_file);
}

function scholarly_import_sql($filename){
  $info = Database::getConnectionInfo();
  $sqlserver = $info['default']['host'];
  $port = (empty($info['default']['port']) ? "3306" : ($info['default']['port']));
  $user = $info ['default'] ['username'];
  $password = $info ['default'] ['password'];
  $database = $info ['default'] ['database'];
  $driver = $info ['default'] ['driver'];
  $prefix = $info ['default'] ['prefix']['default'];
  
  if ($driver == 'mysql') {
    $connection = mysqli_connect($sqlserver, $user, $password, $database, $port);
    
    $buffer='';
    $count=0;
    $handle = @fopen($filename, "r");
    if ($handle) {
      while (!feof($handle)) {
        $line = fgets($handle);
        $buffer.=$line;
        if(preg_match('|;$|', $line)){
          $count++;
          mysqli_query($connection, $buffer);
          $buffer='';
        }
      }
      fclose($handle);
    }
    mysqli_close($connection);
    cache_clear_all();
  }
  return $count;
}