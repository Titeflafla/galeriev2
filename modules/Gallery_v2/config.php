<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//

if (!defined("INDEX_CHECK")) die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");

define('GALLERY_V2_TABLE', $nuked['prefix'] .'_gallery_v2');
define('GALLERY_V2_CAT_TABLE', $nuked['prefix'] .'_gallery_v2_cat');
define('GALLERY_V2_CONFIG_TABLE', $nuked['prefix'] .'_gallery_v2_config');

$gallery_pref = array();
$sql_conf = mysql_query("SELECT name, value FROM ". GALLERY_V2_CONFIG_TABLE);
while ($row = mysql_fetch_array($sql_conf, MYSQL_ASSOC)) {
        $gallery_pref[$row['name']] = $row['value'];
}

function get_youtube_thumbs($id) {
	global $gallery_pref;
	$image = $gallery_pref['rep_img'] .'youtube_'.$id.'.jpg';
	$url_thumb = file_get_contents("http://img.youtube.com/vi/". $id ."/0.jpg"); // 1 2 3
 	$fp = fopen("$image", 'w+b');
 	fwrite($fp, $url_thumb);
 	fclose($fp);
}

function get_dailymotion_thumbs($id) {
	global $gallery_pref;
	$image = $gallery_pref['rep_img'] .'dailymotion_'.$id.'.jpg';
	$url_thumb = file_get_contents("http://www.dailymotion.com/thumbnail/video/". $id ."");
 	$fp = fopen("$image", 'w+b');
 	fwrite($fp, $url_thumb);
 	fclose($fp);
}

function create_from_ffmpeg($filename, $source, $duration) {
	global $gallery_pref;
	if (PHP_OS == 'WINNT') $patch_ffmpeg = 'C:/ImageMagick';
	else $patch_ffmpeg = '/usr/bin';
	$cmd = $patch_ffmpeg .'/ffmpeg -i '. $source .' -an -ss '. $duration .' -an -r 1 -vframes 1 -y '. $gallery_pref['rep_img'] . $filename;
	shell_exec($cmd);
}

function format_url_vdo($url) {
	//initialisation des variables
	$host = '';
	$id = '';
	$parse = '';
	$parse2 = '';
	$formated_url = '';
	//On détermine où est hebergée la vidéo (youtube, dailymotion, vimeo) et on extrait les données nécessaires au formatage du lien shadowbox
	$parse = parse_url($url);
	switch ($parse['host']) {
		case 'youtu.be':
		$host = 'youtube';
		$id = substr($parse['path'], 1);
		break;
		case 'www.youtube.com':
		$host = 'youtube';
		$parse2 = parse_str($parse['query'], $data);
		$id = $data['v'];
		break;
		case 'vimeo.com':
		$host = 'vimeo';
		$id = substr($parse['path'], 1);
		break;
		case 'www.dailymotion.com':
		$host = 'dailymotion';
		$id = substr($parse['path'], 7);
		break;
		default:
		break;
	}
	//On formate le lien selon l'hébergeur
	switch ($host) {
		case 'youtube':
		$formated_url = 'http://www.youtube.com/v/'. $id .';hl=en&amp;fs=1&amp;rel=0';
		break;
		case 'vimeo':
		$formated_url = 'http://player.vimeo.com/video/'. $id .'?title=0&amp;byline=0&amp;portrait=0';
		break;
		case 'dailymotion':
		$formated_url = 'http://www.dailymotion.com/swf/'. $id;
		break;
		default:
		break;
	}
	return $formated_url;
}

function aff_vote($vid) {
	$sql_vote = mysql_query("SELECT id, vote FROM ". VOTE_TABLE ." WHERE vid = '". $vid ."' AND module = 'Gallery_v2'");
	$count = mysql_num_rows($sql_vote);

	if ($count > 0) {
		while (list($id, $vote) = mysql_fetch_array($sql_vote)) {
	            	$total = $total + $vote / $count;
	            	$pourcent_arrondi = ceil($total);
	        }
	        $note = $pourcent_arrondi;
	        $aff_note = '';
	        for ($i = 2;$i <= $note;$i += 2) {
	            	$aff_note .= '<img src="modules/Gallery_v2/images/stars/z1.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
	            	$n++;
	        }

	        if (($note - $i) != -2) {
	            	$aff_note .= '<img src="modules/Gallery_v2/images/stars/z2.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
	            	$n++;
	        }

	        for ($z = $n;$z < 5;$z++) {
	            	$aff_note .= '<img src="modules/Gallery_v2/images/stars/z3.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
	        }
	} else $aff_note = '<img src="modules/Gallery_v2/images/stars/z0.png" alt="" title="'. _NOTEVAL .'" />';

	return $aff_note;}

?>