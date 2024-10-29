<?php
/*
Plugin Name: Add UROK.su Catalog
Plugin URI: http://www.urok.su/xml-tools.php
Description: Этот плагин добавляет каталог видеороликов из <a href="http://www.urok.su>UROK.su</a> на ваш сайт.
Author: UROK.su XML Team
Version: 1.04
Author URI: http://www.urok.su/
*/

include_once( 'urok.su.class.php' );
$uroksu = new uroksu();

$path_to_php_file_plugin = 'urok_su_wp/urok_su_wp.php';

$uroksu->page_title = 'Add UROK.su Catalog';

$uroksu->menu_title = 'UROK.su';

$uroksu->short_description = 'Добавить каталог видеороликов на ваш сайт';

$uroksu->access_level = 5; // access level

// 1=main menu 2=options 3=manage 4=templates
$uroksu->add_page_to = 2;

add_action('admin_menu', array(&$uroksu, 'add_admin_menu'));

