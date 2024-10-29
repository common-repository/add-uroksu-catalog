<?php
class uroksu
{
	var $page_title;
	var $menu_title;
	var $access_level;
	var $add_page_to;
	var $short_description;
	var $admin_options_name = 'uroksu_options';
	var $admin_options;

	function uroksu()
	{
	}

	function get_options()
	{
	}
	function update_options()
	{
	}

    function add_admin_menu()
	{
	global $path_to_php_file_plugin;
	if ( $this->add_page_to == 1 )
		add_menu_page($this->page_title,
			$this->menu_title, $this->access_level,
			$path_to_php_file_plugin, array(&$this, 'admin_page'));

	elseif ( $this->add_page_to == 2 )
		add_options_page($this->page_title,
			$this->menu_title, $this->access_level,
			$path_to_php_file_plugin, array(&$this, 'admin_page'));

	elseif ( $this->add_page_to == 3 )
		add_management_page($this->page_title,
			$this->menu_title, $this->access_level,
			$path_to_php_file_plugin, array(&$this, 'admin_page'));			

	elseif ( $this->add_page_to == 4 )
		add_theme_page($this->page_title,
			$this->menu_title, $this->access_level,
			$path_to_php_file_plugin, array(&$this, 'admin_page'));
	}


function admin_page()
{
	echo <<<EOF
<div class="wrap">
<h2>{$this->page_title}</h2>
<p>{$this->short_description}</p>
EOF;

	if (isset($_POST['UPDATE'])) {
	print($this->update_catalog());
	echo '</p>';
	}
	else {
		$this->view_options_page();
	}
	echo '</div>';
}

function update_catalog() {

$xmlUrl='http://urok.su/rss-feed.php';

    global $initvideo,$element1,$current_video,$videos;

	$xmlSource=getXML($xmlUrl);
   		//Echo $xmlSource;
			global $wpdb;

    //echo strlen($xmlSource)."<br>";
	$videos=xml_parser_init($xmlSource);

//echo '<pre>';
//print_r($videos);
//echo '</pre>';

$sc=0;
foreach ($videos as $value)
{

$title=$value['NAME']; $search = array("&", "'","┬Т", "<BR>","<br />","'"); $replace = array("&#38;", "&#39;", "&#39;", "", "", "&#39;"); $title = trim(str_replace($search,$replace,$title));

$framevideo=str_replace('"','\"',$value['FRAMEVIDEO']);

$the_post='<table><tr><td width="50%"><a href="'.$value['LINKVIDEO'].'" title="'.$value['NAME'].'" target="_blank"><img src="'.$value['SCREENSHOT'].'"></a></td><td><a href="'.$value['LINKVIDEO'].'" title="'.$value['DESCRIPTION'].'" target="_blank">'.$value['DESCRIPTION'].'</a></td></tr></table><br />'.$framevideo;

$tags = explode(" ",$value['TAGS']);

$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '".$title."'");

$descchannels=explode("|",$value['DESCCHANNELS']);

$esc=0; $category = array();
foreach ($descchannels as $key) {

$cat_id = $wpdb->get_var("SELECT term_id FROM $wpdb->terms WHERE name = '".$key."'");
if (!$cat_id) {
$my_cat=array('cat_name' => $key, 'category_description' => $key, 'category_nicename' => $key);
$cat_id=wp_insert_category($my_cat);
$category[$esc]=$cat_id;} else {$category[$esc]=$cat_id;}

$esc=$esc+1; }

if (!$post_id) {
$uroksu_post = array(
	'post_title' => $title,
	'post_name' => $title,
	'post_content' => $the_post,
	'tags_input' => $tags,
	'post_date' => $value['RELEASEDATE'],
	'post_category' => $category,
	'post_status' => 'publish'
);

$post_id=wp_insert_post($uroksu_post);
add_post_meta($post_id, 'rating', $value['RATING'],false);
add_post_meta($post_id, 'scrshot', $value['SCREENSHOT'],false);

echo 'Добавлен видеоролик: '.$value['NAME'].'</br>'; $sc=$sc+1; } else {update_post_meta($post_id, 'rating', $value['RATING'],false); }

}

echo '<br />Добавлено '.$sc.' видеороликов.<br /><br />';
if ($sc==0) {echo 'У вас самый свежий, актуальный на этот момент каталог видеороликов!<br />';} else {echo 'Добавление завершено успешно!<br />';}
echo '<b>Периодически запускайте этот плагин, чтобы добавить новые видеоролики и обновить рейтинг уже добавленных.<br />Но не рекомендуется запускать его чаще одного раза в сутки.</b><br />';

exit; }


function view_options_page()
{

//if (!$login) {$login='support';}

echo '
<form action="" method="POST">
<input type="submit" name="UPDATE" value="Добавить видеоролики">
</form>
';
}

}


function getXML($xmlUrl,$autofetch=FALSE)
		{
			
			//Echo "xmlurl=".$xmlUrl."<br>";
			$xmlSource =getRemoteXmlFile($xmlUrl);

       return $xmlSource;
       }


function getRemoteXmlFile($address)
		{
		$cu = curl_init ();
		curl_setopt($cu, CURLOPT_URL, $address);
		curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($cu, CURLOPT_HEADER, 0);
		curl_setopt ($cu, CURLOPT_TIMEOUT, 40);
		$xmlFileContents = curl_exec($cu);
  			curl_close ($cu);
		return $xmlFileContents;
		}

function xml_parser_init ($source)
		{
            global $initvideo,$element1,$current_video,$videos;

		$parser = xml_parser_create();
		xml_set_element_handler($parser, 'xml_start_element', 'xml_end_element');
		xml_set_character_data_handler( $parser, 'xml_cdata');
	        $status = xml_parse( $parser, $source );

//		die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($parser)), xml_get_current_line_number($parser)));

	        xml_parser_free($parser);
	        return $videos;
	    }

	    function xml_start_element($parser, $element, $attrs="")
	    {
	        global $initvideo,$element1,$current_video,$videos;

	        if ($element == 'VIDEO'){
	             $initvideo = true;
	        }
	        $element1 = $element;
	    }

	    function xml_cdata ($parser, $data)
	    {
	    	global $initvideo,$element1,$current_video,$videos;
	 		if($initvideo == true && $element1 != 'VIDEO'){
				if(empty($current_video)){
					$current_video = create_video_array();
				}
					if(!empty($data)){
						$current_video[''.$element1.''] .= trim($data);
					}
			}
		}

	    function xml_end_element($parser, $element, $attrs="")
	    {
	        global $initvideo,$element1,$current_video,$videos;
	    	if ($element == 'VIDEO'){
	            $videos[] = $current_video;
	            $current_video = create_video_array();
	            $initvideo = false;

	        }

	    }

	    function create_xml_parser($source, $out_enc="", $in_enc="", $detect="")
	    {
	     	return array(xml_parser_create(), $source);
	    }


	    function create_video_array()
	    {
			return array('ID'=>NULL,
					'RATING'=>NULL,
					'NAME'=>NULL,
					'DESCRIPTION'=>NULL,
					'TAGS'=>NULL,
					'CHANNELS'=>NULL,
					'DESCCHANNELS'=>NULL,
					'SCREENSHOT'=>NULL,
					'LINKVIDEO'=>NULL,
					'FRAMEVIDEO'=>NULL,
					'RELEASEDATE'=>NULL);
	    }
  
?>