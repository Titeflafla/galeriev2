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

global $nuked, $language, $user, $gallery_pref;

$visiteur = !$user ? 0 : $user[1];

translate('modules/Vote/lang/'. $language .'.lang.php');

include_once("modules/Gallery_v2/config.php");

echo '<script type="text/javascript" src="modules/Gallery_v2/gallery_v2.js"></script>'
. '<div class="g2_block">';

if($gallery_pref['block_type'] == '1') $orderby = 'date desc';
else $orderby = 'rand()';

$sql = mysql_query("SELECT titre, url, url_file, sid, type FROM ". GALLERY_V2_TABLE ." WHERE actif = '1' ORDER BY ". $orderby ." LIMIT 1");
list($titre, $url, $url_file, $vid, $type) = mysql_fetch_array($sql);

if($visiteur == 9) {
	$sql_galery = mysql_query("SELECT COUNT(sid) FROM ". GALLERY_V2_TABLE ." WHERE actif = '0'");
	list($id_galery) = mysql_fetch_array($sql_galery);
}

$aff_note = aff_vote($vid);

if (($type == "flv" || $type == "youtube" || $type == "dailymotion" || $type == "vimeo") && $url_file != '') {
	$ext = pathinfo($url_file, PATHINFO_EXTENSION);
	if(file_exists($gallery_pref['rep_img'] .'temp/block_'. str_replace('.'. $ext, '', $url_file) .'.png')) $img = $gallery_pref['rep_img'] .'temp/block_'. str_replace('.'. $ext, '', $url_file) .'.png';
	else $img = 'index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=b&amp;a_c=0&amp;image='. $url_file;
} elseif (($type == "flv" || $type == "youtube" || $type == "dailymotion" || $type == "vimeo") && $url_file == '') $img = '<img src="modules/Gallery_v2/images/video_block.png" alt="" />';
else {	$ext = pathinfo($url, PATHINFO_EXTENSION);
	if(file_exists($gallery_pref['rep_img'] .'temp/block_'. str_replace('.'. $ext, '', $url) .'.png')) $img = $gallery_pref['rep_img'] .'temp/block_'. str_replace('.'. $ext, '', $url) .'.png';
	else $img = 'index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=b&amp;a_c=0&amp;image='. $url;
}

echo '<img src="'. $img .'" alt="" /><br />'
. '<a href="index.php?file=Gallery_v2&amp;op=description&amp;sid='. $vid .'">'. htmlentities($titre) .'</a><br />'. $aff_note;
if($visiteur == 9) echo '<br />'. $id_galery .' suggestions en attente';
echo '</div>';

?>