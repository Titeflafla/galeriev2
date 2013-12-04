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

global $user, $language;
translate("modules/Gallery_v2/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
include("modules/Gallery_v2/config.php");

if($_REQUEST['op'] != 'show_icon' && $_REQUEST['op'] != 'make_zip') {
	admintop();
}

$gallery_pref = array();
$sql_conf = mysql_query("SELECT name, value FROM ". GALLERY_V2_CONFIG_TABLE);
while ($row = mysql_fetch_array($sql_conf, MYSQL_ASSOC)) {
        $gallery_pref[$row['name']] = $row['value'];
}

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);

if ($visiteur >= $level_admin && $level_admin > -1) {

    	function add_screen() {

	        global $language, $user;

                echo "<script type=\"text/javascript\">\n"
                . "<!--\n"
                . "function showtype(type){\n"
                . "if (type == 'image'){\n"
                . "document.getElementById('aff_type').innerHTML='.png .jpg .gif';\n"
                . "}else if (type == 'video'){\n"
                . "document.getElementById('aff_type').innerHTML='.flv .mp4 .mov .f4v';\n"
                . "}else{\n"
                . "document.getElementById('aff_type').innerHTML='';\n"
                . "}}\n"
                . "//-->\n"
                . "</script>"
	        . "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery_v2.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div></div>\n"
	        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Gallery_v2&amp;page=admin\">" . _GALLERY . "</a> | "
	        . "</b>" . _ADDSCREEN . "<b> | "
	        . "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
	        . "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
	        . "<form method=\"post\" action=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=send_screen\" enctype=\"multipart/form-data\" onsubmit=\"backslash('img_texte');\">\n"
	        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
	        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"44\" /></td></tr>\n"
	        . "<tr><td><b>" . _CAT . "</b> : <select name=\"cat\">\n";
	        echo select_cat();
	        echo "</select>"
	        . "</td></tr><tr><td><b>". _TYPE ."</b> : <select name=\"type\" onclick=\"showtype(this.options[this.selectedIndex].value);\">"
                . "<option value=\"image\">". _IMAGE ."</option><option value=\"video\">". _VIDEO ."</option><option value=\"youtube\">Youtube</option>"
                . "<option value=\"dailymotion\">Dailymotion</option><option value=\"vimeo\">Vimeo</option></select>&nbsp;<div style=\"display:inline;\" id=\"aff_type\">.png .jpg .gif</div></td></tr>"
                . "<tr><td><b>" . _AUTHOR . " :</b> <input type=\"text\" name=\"auteur\" size=\"30\" value=\"". $user[2] ."\" /></td></tr>\n";

	        echo "</td></tr><tr><td><b>" . _DESCR . " :</b></td></tr>\n"
	        . "<tr><td><textarea class=\"editor\" id=\"img_texte\" name=\"description\" cols=\"66\" rows=\"10\"></textarea></td></tr>\n"
	        . "<tr><td>&nbsp;</td></tr>\n"
	        . "<tr><td>Mot cl&eacute; : <input type=\"text\" name=\"mot_cle\" size=\"46\" value=\"\" />&nbsp;S&eacute;par&eacute; par une ,</td></tr>"
	        . "<tr><td>Uploader votre m&eacute;dia : <input type=\"file\" name=\"fichiernom\" /></td></tr>"
                . "<tr><td>Uploader votre thumbail : <input type=\"file\" name=\"fichierthumb\" /></td></tr>"
                . "<tr><td>Url de la vid&eacute;o : <input size=\"46\" id=\"url_video\" type=\"text\" name=\"url_video\" /> Youtube, Dailymotion, Vimeo</td></tr>"
	        . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _ADDSCREEN . "\" /></td></tr></table>\n"
	        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery_v2&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    	}

    	function send_screen($titre, $description, $auteur, $mot_cle, $cat, $type, $url_video) {

        	global $nuked, $user, $gallery_pref;

                $rep_img = $gallery_pref['rep_img'];

                if (isset($_FILES['fichiernom']['name']) && $_FILES['fichiernom']['name'] != '') {
                        $fichier = basename($_FILES['fichiernom']['name']);
                        $ext = pathinfo($fichier, PATHINFO_EXTENSION);

                        if ($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "png" || $ext == "flv" || $ext == "mp4" || $ext == "f4v" || $ext == "mov") $file_upload = "ok";
                        else $file_upload = "no";

                        if ($file_upload == "ok") {
                                $url_file = $rep_img . time() . "." . $ext;
                                $filesize = $_FILES['fichiernom']['size'];
                                $taille = $filesize / 1024;
                                $taille = (round($taille * 100)) / 100;
                                move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_file);
                                $url_file = str_replace($rep_img, '', $url_file);
                        } else {
                                echo "<div class=\"notification error png_bg\"><div>Error to upload !</div></div>";
                    		redirect("index.php?file=Gallery_v2&page=admin&op=add_screen", 2);
                    		adminfoot();
                    		exit();
                        }
                }

                if (isset($_FILES['fichierthumb']['name']) && $_FILES['fichierthumb']['name'] != '') {
                	$fichier = basename($_FILES['fichierthumb']['name']);
                        $ext = pathinfo($fichier, PATHINFO_EXTENSION);

                        if ($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "png") $thumb_upload = "ok";
                        else $thumb_upload = "no";

                        if ($thumb_upload == "ok") {
                                if($type == "video") $url_file = $rep_img .'flv_'. time() .".". $ext;
                                elseif($type == "youtube") $url_file = $rep_img .'youtube_'. time() .".". $ext;
                                elseif($type == "dailymotion") $url_file = $rep_img .'dailymotion_'. time() .".". $ext;
                                elseif($type == "vimeo") $url_file = $rep_img .'vimeo_'. time() .".". $ext;
                                else $url_file = $rep_img .'th_'. time() .".". $ext;

                                $filesize = $_FILES['fichierthumb']['size'];
                                $taille = $filesize / 1024;
                                $taille = (round($taille * 100)) / 100;
                                move_uploaded_file($_FILES['fichierthumb']['tmp_name'], $url_file);
                                $url_file_th = str_replace($rep_img, '', $url_file);
                        } else {
                                echo "<div class=\"notification error png_bg\"><div>Error to upload !</div></div>";
                    		redirect("index.php?file=Gallery_v2&page=admin&op=add_screen", 2);
                    		adminfoot();
                    		exit();
                        }
                } else $url_file_th == '';

                if($type == "video") {
                        $type_aff = "flv";
                        $url_file_img = $url_file;
                        if($url_file_th == '') {
                        	$url_file_swf = 'flv_'. str_replace('.'.$ext, '.jpg', $url_file);
                        	create_from_ffmpeg($url_file_swf, $rep_img .'/'. $url_file, '00:00:06');
                        } else $url_file_swf = $url_file_th;
                } else if ($type == "youtube") {
                        $type_aff = "youtube";
                        $url_file_img = get_id_video($url_video);
                        if($url_file_th == '') {
                        	$url_file_swf = 'youtube_'. $url_file_img .'.jpg';
                        	get_youtube_thumbs($url_file_img);
                        } else $url_file_swf = $url_file_th;
                } else if ($type == "dailymotion") {
                        $type_aff = "dailymotion";
                        $url_file_img = get_id_video($url_video);
                        if($url_file_th == '') {
                        	$url_file_swf = 'dailymotion_'. $url_file_img .'.jpg';
                        	get_dailymotion_thumbs($url_file_img);
                        } else $url_file_swf = $url_file_th;
                } else if ($type == "vimeo") {
                        $type_aff = "vimeo";
                        $url_file_swf = '';
                        if($url_file_th == '') $url_file_img = get_id_video($url_video);
                        else $url_file_img = $url_file_th;
                } else {
                        $type_aff = '';
                        $url_file_swf = '';
                        if($url_file_th == '') $url_file_img = $url_file;
                        else $url_file_img = $url_file_th;
                }

                $titre = mysql_real_escape_string(stripslashes($titre));
                $description = html_entity_decode($description);
                $description = mysql_real_escape_string(stripslashes($description));
                $auteur = mysql_real_escape_string(stripslashes($auteur));
                $mot_cle = mysql_real_escape_string(stripslashes($mot_cle));
                $date = time();

                $sql = mysql_query("INSERT INTO ". GALLERY_V2_TABLE ." ( `sid`, `titre`, `description`, `url`, `url_file`, `cat`, `date`, `autor`, `type`, `taille`, `mot_cle`, `actif` ) VALUES ( '', '". $titre ."', '". $description ."', '". $url_file_img ."', '". $url_file_swf ."', '". $cat ."', '". $date ."', '". $auteur ."', '" . $type_aff . "', '" . $taille . "', '". $mot_cle ."', '1')");

                // Action
                $texteaction = _ACTIONADDGAL ." ". $titre;
                $acdate = time();
                $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
                // Fin action
                echo "<div class=\"notification success png_bg\"><div>". _SCREENADD ."</div></div>";
                redirect("index.php?file=Gallery_v2&page=admin", 2);
    	}

    	function del_screen($sid) {

	        global $nuked, $user;

	        $sql_rep_img = mysql_query("SELECT value FROM ". GALLERY_V2_CONFIG_TABLE ." WHERE name = 'rep_img'");
                list($rep_img) = mysql_fetch_array($sql_rep_img);

	        $sqls = mysql_query("SELECT titre, url, url_file FROM ". GALLERY_V2_TABLE ." WHERE sid = '". $sid ."'");
	        list($titre, $url, $url2) = mysql_fetch_array($sqls);
	        $titre = mysql_real_escape_string($titre);
	        $sql = mysql_query("DELETE FROM ". GALLERY_V2_TABLE ." WHERE sid = '". $sid ."'");
	        $del_com = mysql_query("DELETE FROM ". COMMENT_TABLE ." WHERE im_id = '". $sid ."' AND module = 'Gallery_v2'");
	        $del_vote = mysql_query("DELETE FROM ". VOTE_TABLE ." WHERE vid = '". $sid ."' AND module = 'Gallery_v2'");
	        // On supprime l'image + la miniature
	        if(is_file($rep_img . $url)) {
                        $ext = pathinfo($url, PATHINFO_EXTENSION);
                        unlink($rep_img . $url);
                        unlink($rep_img .'temp/'. str_replace('.'. $ext, '', $url) .'.png');
                }
                if(is_file($rep_img . $url2)) {
                        $ext = pathinfo($url2, PATHINFO_EXTENSION);
                        unlink($rep_img . $url2);
                        unlink($rep_img .'temp/'. str_replace('.'. $ext, '', $url2) .'.png');
                }
	        // Action
	        $texteaction = _ACTIONDELGAL ." ". $titre;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>" . _SCREENDEL . "</div></div>\n";
	        redirect("index.php?file=Gallery_v2&page=admin", 1);
    	}

    	function edit_screen($sid) {

        	global $nuked, $language, $gallery_pref;

        	$sql = mysql_query("SELECT cat, titre, description, autor, url, url_file, type, mot_cle FROM ". GALLERY_V2_TABLE ." WHERE sid = '". $sid ."'");
        	list($cat, $titre, $description, $autor, $url, $url_file, $type, $mot_cle) = mysql_fetch_array($sql);

        	if ($cat > 0) {
            		$sql2 = mysql_query("SELECT titre FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '". $cat ."'");
            		list($cat_name) = mysql_fetch_array($sql2);
            		$cat_name = htmlentities($cat_name);
        	} else $cat_name = _NONE;

                if ($type == "flv") {
                        $image = '<object type="application/x-shockwave-flash" data="modules/Gallery_v2/player.swf" width="640" height="385"><param name="FlashVars" value="flv='. $url .'&amp;s_color='. $gallery_pref['color_player'] .'" /><param name="allowFullScreen" value="true" /><param name="menu" value="false" /><param name="wmode" value="transparent" /><param name="quality" value="high" /><param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" /></object>';
                        $url_image = $url_file;
                } else if ($type == "youtube") {
                        $image = '<object width="640" height="385"><param name="movie" value="http://www.youtube.com/v/'. $url .'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'. $url .'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="385"></embed></object>';
                        $url_youtube = $url;
                } else if ($type == "dailymotion") {
                        $image = '<object width="560" height="314"><param name="movie" value="http://www.dailymotion.com/swf/video/'. $url .'"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param><embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/video/'. $url .'" width="560" height="314" allowfullscreen="true" allowscriptaccess="always"></embed></object>';
                        $url_dailymotion = $url;
                } else if ($type == "vimeo") {
                        $image = '<object type="application/x-shockwave-flash" style="width:640px; height:385px;" data="http://vimeo.com/moogaloop.swf?clip_id='. $url .'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0"><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='. $url .'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" /></object>';
                        $url_dailymotion = $url;
                } else {
            		$ext = pathinfo($url, PATHINFO_EXTENSION);
              		if (file_exists($gallery_pref['rep_img'] .'temp/'. str_replace('.'. $ext, '', $url) .'.png')) $image = '<a href="'. $gallery_pref['rep_img'] . $url .'" rel="shadowbox" title="'. $titre .'"><img src="'. $gallery_pref['rep_img'] .'temp/'. str_replace('.'. $ext, '', $url) .'.png" alt="" /></a>';
              		else $image = '<a href="'. $gallery_pref['rep_img'] . $url .'" rel="shadowbox" title="'. $titre .'"><img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=g&amp;a_c=0&amp;image='. $url .'" alt="" /></a>';
			$url_image = $url;
                }

                if ($type == "flv" || $type == "image") $input_url = '<input type="hidden" name="url_video" value="" />';
                else $input_url = '';

        	echo '<link rel="stylesheet" type="text/css" href="media/shadowbox/shadowbox.css">
		<script type="text/javascript" src="media/shadowbox/shadowbox.js"></script>
		<script type="text/javascript">
		Shadowbox.init();
		</script>'
        	. "<div class=\"content-box\">\n"
        	. "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        	. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery_v2.php\" rel=\"modal\">\n"
        	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        	. "</div></div>\n"
        	. "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=modif_img\" enctype=\"multipart/form-data\" onsubmit=\"backslash('img_texte');\">\n"
        	. "<table style=\"margin-left: auto;margin-right: auto;\" cellpadding=\"10\" cellspacing=\"0\" border=\"0\">\n"
        	. "<tr><td style=\"text-align: center;\">" . $image . "</td></tr></table><br />\n"
        	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
        	. "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"44\" value=\"" . $titre . "\" /></td></tr>\n"
        	. "<tr><td><b>" . _CAT . "</b>: <select name=\"cat\"><option value=\"" . $cat . "\">" . $cat_name . "</option>\n";
        	echo select_cat();
        	echo "</select></td></tr><tr><td><b>" . _AUTHOR . " :</b> <input type=\"text\" name=\"auteur\" size=\"30\" value=\"" . $autor . "\" /></td></tr>\n"
        	. "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
        	. "<tr><td><textarea class=\"editor\" id=\"img_texte\" name=\"description\" cols=\"66\" rows=\"10\" onselect=\"storeCaret('img_texte');\" onclick=\"storeCaret('img_texte');\" onkeyup=\"storeCaret('img_texte');\">" . $description . "</textarea></td></tr>\n"
        	. "<tr><td>Mot cl&eacute; : <input type=\"text\" name=\"mot_cle\" size=\"46\" value=\"" . $mot_cle . "\" />&nbsp;S&eacute;par&eacute; par une ,</td></tr>";
                if ($type == "youtube") echo "<tr><td>Url Youtube : <input class=\"login_input_big\" id=\"url_video\" type=\"text\" name=\"url_video\" value=\"". $url ."\" /></td></tr>";
                if ($type == "dailymotion") echo "<tr><td>Url Dailymotion : <input class=\"login_input_big\" id=\"url_video\" type=\"text\" name=\"url_video\" value=\"". $url ."\" /></td></tr>";
                if ($type == "vimeo") echo "<tr><td>Url Vimeo : <input class=\"login_input_big\" id=\"url_video\" type=\"text\" name=\"url_video\" value=\"". $url ."\" /></td></tr>";
        	echo "<tr><td>&nbsp;<input type=\"hidden\" name=\"sid\" value=\"" . $sid . "\" />". $input_url ."</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _MODIFTHISSCREEN . "\" /></td></tr></table>\n"
        	. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery_v2&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    	}

    	function modif_img($sid, $titre, $description, $auteur, $url_video, $mot_cle, $cat) {

	        global $nuked, $user;

	        $titre = mysql_real_escape_string(stripslashes($titre));
	        $description = html_entity_decode($description);
	        $description = mysql_real_escape_string(stripslashes($description));
	        $auteur = mysql_real_escape_string(stripslashes($auteur));

                if ($url_video != "") $update_url = ", url = '". $url_video ."'";
                else $update_url = '';

	        $sql = mysql_query("UPDATE ". GALLERY_V2_TABLE ." SET titre = '" . $titre . "', description = '" . $description . "', autor = '" . $auteur . "', cat = '" . $cat . "', mot_cle = '" . $mot_cle . "' ". $update_url ." WHERE sid = '" . $sid . "'");
	        // Action
	        $texteaction = _ACTIONMODIFGAL ." ". $titre;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action

	        echo "<div class=\"notification success png_bg\"><div>" . _SCREENMODIF . "</div></div>";
	        redirect("index.php?file=Gallery_v2&page=admin", 2);
    	}

    	function main() {

        	global $nuked, $language, $gallery_pref;

        	$nb_media_admin = $gallery_pref['mess_admin_page'];

        	if($_REQUEST['affcat'] != "on") $sql3 = mysql_query("SELECT sid FROM ". GALLERY_V2_TABLE);
                else {
                        if(is_numeric($_REQUEST['id_cat'])) $sql3 = mysql_query("SELECT sid FROM ". GALLERY_V2_TABLE ." WHERE cat = '". $_REQUEST['id_cat'] ."' ");
                        else $sql3 = mysql_query("SELECT sid FROM ". GALLERY_V2_TABLE ." WHERE type = '". $_REQUEST['id_cat'] ."' ");
                }

        	$count = mysql_num_rows($sql3);

        	if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        	$start = $_REQUEST['p'] * $nb_media_admin - $nb_media_admin;

        	echo"<script type=\"text/javascript\">\n"
        	."<!--\n"
        	. "function del_img(titre, id){\n"
        	. "if (confirm('" . _SCREENDELETE . " '+titre+' ! " . _CONFIRM . "')){\n"
        	. "document.location.href = 'index.php?file=Gallery_v2&page=admin&op=del_screen&sid='+id;}\n"
        	. "}\n"
        	. "//-->\n"
        	. "</script>\n"

        	. "<div class=\"content-box\">\n"
        	. "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        	. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery_v2.php\" rel=\"modal\">\n"
        	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        	. "</div></div>\n"
        	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _GALLERY . "<b> | "
        	. "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=add_screen\">" . _ADDSCREEN . "</a> | "
        	. "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
        	. "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n";

        	if ($_REQUEST['orderby'] == "date") $order_by = "G.sid DESC";
        	else if ($_REQUEST['orderby'] == "name") $order_by = "G.titre";
        	else if ($_REQUEST['orderby'] == "cat") $order_by = "GC.titre, GC.parentid";
        	else $order_by = "G.sid DESC";

        	echo "<form action=\"index.php?file=Gallery_v2&page=admin\" method=\"post\"><table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">\n"
    		. "<tr><td style=\"text-align:left;\">" . _ORDERBY . " : ";

        	if ($_REQUEST['orderby'] == "date" || !$_REQUEST['orderby']) echo "<b>" . _DATE . "</b> | ";
        	else echo "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;orderby=date\">" . _DATE . "</a> | ";

        	if ($_REQUEST['orderby'] == "name") echo "<b>" . _TITLE . "</b> | ";
        	else echo "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;orderby=name\">" . _TITLE . "</a> | ";

        	if ($_REQUEST['orderby'] == "cat") echo "<b>" . _CAT . "</b>";
        	else echo "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;orderby=cat\">" . _CAT . "</a>";

        	echo '</td><td style="text-align:right;">'
                . "<select class=\"styled\" name=\"id_cat\">"
                . '<option value="">*---- Type ----*</option><option value="">Image</option><option value="youtube">Youtube</option><option value="dailymotion">Dailymotion</option><option value="vimeo">Vimeo</option><option value="flv">Flash</option><option value="">*---- '. _CAT .' ----*</option>';
                echo select_cat();
                echo "</select>&nbsp;&nbsp;<input class=\"connexion_input\" type=\"submit\" value=\"Ok\">"
                . "<input name=\"orderby\" type=\"hidden\" value=\"". $_REQUEST['orderby'] ."\"><input name=\"affcat\" type=\"hidden\" value=\"on\">"
                . "</td></tr></table></form>";

        	if ($count > $nb_media_admin) number($count, $nb_media_admin, "index.php?file=Gallery_v2&amp;page=admin&amp;orderby=". $_REQUEST['orderby']);

        	echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        	. "<tr>\n"
        	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        	. "<td style=\"width: 30%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
        	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _ACTIF . "</b></td>\n"
        	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        	if($_REQUEST['affcat'] != "on") $sql = mysql_query("SELECT G.sid, G.titre, G.cat, G.url, G.url_file, G.date, G.type, G.actif, GC.parentid, GC.titre FROM ". GALLERY_V2_TABLE ." AS G LEFT JOIN " . GALLERY_V2_CAT_TABLE . " AS GC ON GC.cid = G.cat ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_media_admin);
                else {
                        if (is_numeric($_REQUEST['id_cat'])) $sql = mysql_query("SELECT G.sid, G.titre, G.cat, G.url, G.url_file, G.date, G.type, G.actif, GC.parentid, GC.titre FROM ". GALLERY_V2_TABLE ." AS G LEFT JOIN " . GALLERY_V2_CAT_TABLE . " AS GC ON GC.cid = G.cat WHERE cat= '". $_REQUEST['id_cat'] ."' ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_media_admin);
                        else $sql = mysql_query("SELECT G.sid, G.titre, G.cat, G.url, G.url_file, G.date, G.type, G.actif, GC.parentid, GC.titre FROM ". GALLERY_V2_TABLE ." AS G LEFT JOIN " . GALLERY_V2_CAT_TABLE . " AS GC ON GC.cid = G.cat WHERE type= '". $_REQUEST['id_cat'] ."' ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_media_admin);
                }
                while (list($sid, $titre, $cat, $url, $url_file, $date, $type, $actif, $parentid, $namecat) = mysql_fetch_array($sql)) {

            		$titre = htmlentities($titre);
            		$date = nkDate($date);

            		if ($cat == "0") $categorie = _NONE;
            		else if ($parentid == 0) $categorie = $namecat;
            		else {
                		$sql3 = mysql_query("SELECT titre FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '". $parentid ."' ORDER BY position, titre");
                		list($parentcat) = mysql_fetch_array($sql3);
                		$categorie = $parentcat .' -> '. $namecat;
                		$categorie = htmlentities($categorie);
            		}

                        if ($actif == 0) $img_actif = '<a href="index.php?file=Gallery_v2&amp;page=admin&amp;op=active_screen&amp;sid='. $sid .'" title="Activer ce m&eacute;dia"><img src="modules/Gallery_v2/images/off.png" alt="" /></a>';
                        else $img_actif = '<a href="index.php?file=Gallery_v2&amp;page=admin&amp;op=desactive_screen&amp;sid='. $sid .'" title="D&eacute;sactiver ce m&eacute;dia"><img src="modules/Gallery_v2/images/on.png" alt="" /></a>';

                        if (($type == "flv" || $type == "youtube" || $type == "dailymotion" || $type == "vimeo") && $url_file != '') {
                                 $ext = pathinfo($url_file, PATHINFO_EXTENSION);
                                 if (file_exists($gallery_pref['rep_img'] .'temp/'. str_replace('.'. $ext, '', $url_file) .'.png')) $img_thumb = "<img src=\"". $gallery_pref['rep_img'] ."temp/". str_replace('.'. $ext, '', $url_file) .".png\" alt=\"\" />";
                                 else $img_thumb = '<img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=p&amp;a_c=0&amp;image='. $url_file .'" alt="" />';
                        } elseif (($type == "flv" || $type == "youtube" || $type == "dailymotion" || $type == "vimeo") && $url_file == '') {
                        	$img_thumb = '<img src="modules/Gallery_v2/images/video.png" alt="" />';
                        } else {
                                   $ext = pathinfo($url, PATHINFO_EXTENSION);
                                   if (file_exists($gallery_pref['rep_img'] .'temp/'. str_replace('.'. $ext, '', $url) .'.png')) $img_thumb = "<img src=\"". $gallery_pref['rep_img'] ."temp/". str_replace('.'. $ext, '', $url) .".png\" alt=\"\" />";
                                   else $img_thumb = '<img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=p&amp;a_c=0&amp;image='. $url .'" alt="" />';
                        }

            		echo "<tr>\n"
            		. "<td><a href=\"javascript:void(0);\" onmouseover=\"AffBulle('" . mysql_real_escape_string(stripslashes($titre)) . "', '". htmlentities($img_thumb) ."', 200)\" onmouseout=\"HideBulle()\">" . $titre . "</a></td>\n"
            		. "<td align=\"center\">" . $date . "</td>\n"
            		. "<td align=\"center\">" . $categorie . "</td>\n"
            		. "<td align=\"center\">" . $img_actif . "</td>\n"
            		. "<td align=\"center\"><a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=edit_screen&amp;sid=" . $sid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISSCREEN . "\" /></a></td>\n"
            		. "<td align=\"center\"><a href=\"javascript:del_img('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $sid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISSCREEN . "\" /></a></td></tr>\n";
        	}

        	if ($count == 0) echo "<tr><td colspan=\"5\" align=\"center\">" . _NOSCREENINDB . "</td></tr>";

        	echo "</table>";

        	if ($count > $nb_media_admin) number($count, $nb_media_admin, "index.php?file=Gallery_v2&amp;page=admin&amp;orderby=" . $_REQUEST['orderby']);

        	echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    	}

        function active_screen($sid) {

                $bdd = mysql_query("UPDATE ". GALLERY_V2_TABLE ." SET actif = '1' WHERE sid = '". $sid ."'");
                redirect($_SERVER["HTTP_REFERER"], 0);
        }

        function desactive_screen($sid) {

                $bdd = mysql_query("UPDATE ". GALLERY_V2_TABLE ." SET actif = '0' WHERE sid = '". $sid ."'");
                redirect($_SERVER["HTTP_REFERER"], 0);
        }

    	function main_cat() {

	        global $nuked, $language;

	        echo "<script type=\"text/javascript\">\n"
	        . "<!--\n"
	        . "function delcat(titre, id){\n"
	        . "if (confirm('" . _SCREENDELETE  . " '+titre+' ! " . _CONFIRM . "')){\n"
	        . "document.location.href = 'index.php?file=Gallery_v2&page=admin&op=del_cat&cid='+id;}\n"
	        . "}\n"
	        . "//-->\n"
	        . "</script>\n"
	        . "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery_v2.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div></div>\n"
	        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Gallery_v2&amp;page=admin\">" . _GALLERY . "</a> | "
	        . "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=add_screen\">" . _ADDSCREEN . "</a> | "
	        . "</b>" . _CATMANAGEMENT . "<b> | "
	        . "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
	        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
	        . "<tr>\n"
	        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
	        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _CATPARENT . "</b></td>\n"
	        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
	        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DL . "</b></td>\n"
	        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
	        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

	        $sql = mysql_query("SELECT cid, titre, parentid, position FROM " . GALLERY_V2_CAT_TABLE . " ORDER BY parentid, position");
	        $nbcat = mysql_num_rows($sql);

	        if ($nbcat > 0) {
	            	while (list($cid, $titre, $parentid, $position) = mysql_fetch_array($sql)) {
		                $titre = htmlentities($titre);

		                echo "<tr>\n"
		                . "<td style=\"width: 35%;\" align=\"center\">" . $titre . "</td>\n"
		                . "<td style=\"width: 35%;\" align=\"center\">\n";

		                if ($parentid > 0) {
		                    	$sql2 = mysql_query("SELECT titre FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '". $parentid ."'");
		                    	list($pnomcat) = mysql_fetch_array($sql2);
		                    	echo "<i>" . htmlentities($pnomcat) . "</i>";
		                } else echo _NONE;

		                echo "</td><td style=\"width: 10%;\" align=\"center\">"; if($position != "0") echo "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=down\" title=\"" . _MOVEDOWN . "\">&lt;</a>";
		                echo "&nbsp;" . $position . "&nbsp;<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=up\" title=\"" . _MOVEUP . "\">&gt;</a></td>\n"
		                . "<td align=\"center\"><a href=\"javascript:void(0);\" onclick=\"javascript:window.open('index.php?file=Gallery_v2&amp;page=admin&amp;nuked_nude=admin&amp;op=make_zip&amp;cid=" . $cid . "','dl','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=650,height=300,top=30,left=0');return(false)\"><img style=\"border: 0;\" src=\"modules/Gallery_v2/images/make_zip.png\" alt=\"\" title=\"" . _DLTHISCAT . "\" /></a></td>\n"
		                . "<td align=\"center\"><a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
		                . "<td align=\"center\"><a href=\"javascript:delcat('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
	            	}
	        } else echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";

	        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=add_cat\"><b>" . _ADDCAT . "</b></a> ]</div>\n"
	        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery_v2&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    	}

    	function add_cat() {

	        global $language, $nuked;

	        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery_v2.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div></div>\n"
	        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Gallery_v2&amp;page=admin\">" . _GALLERY . "</a> | "
	        . "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=add_screen\">" . _ADDSCREEN . "</a> | "
	        . "</b>" . _CATMANAGEMENT . "<b> | "
	        . "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
	        . "<form method=\"post\" action=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=send_cat\">\n"
	        . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
	        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
	        . "<tr><td>Url de l'image : <input id=\"image\" type=\"text\" name=\"image\" />&nbsp;<a href=\"javascript:void(0);\" onclick=\"javascript:window.open('index.php?file=Gallery_v2&page=admin&nuked_nude=admin&op=show_icon','img','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=650,height=300,top=30,left=0');return(false)\">". _SEEICON ."</a></td></tr>"
	        . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>\n";
	        echo select_cat();
	        echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"0\" /></td></tr>\n"
	        . "<tr><td><b>" . _LEVEL . "</b> : <select name=\"level\">"
                . "<option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option></select>"
	        . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
	        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
	        . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _CREATECAT . "\" /></div>\n"
	        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    	}

    	function send_cat($titre, $description, $parentid, $position, $image, $level) {

	        global $nuked, $user;

	        $titre = mysql_real_escape_string(stripslashes($titre));
                $image = mysql_real_escape_string(stripslashes($image));

	        if (empty($titre)) {
	            	echo "<div class=\"notification error png_bg\"><div>" . _TITLECATFORGOT . "</div></div>\n";
	            	redirect("index.php?file=Gallery_v2&page=admin&op=main_cat", 2);
	        } else {
	            	$description = html_entity_decode($description);
	            	$description = mysql_real_escape_string(stripslashes($description));

                        $sql_lvl = mysql_query("SELECT level FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '". $parentid ."'");
                	list($niveau) = mysql_fetch_array($sql_lvl);

                        if ($level < $niveau) $level = $niveau;
                        else $level = $level;

	            	$sql = mysql_query("INSERT INTO ". GALLERY_V2_CAT_TABLE ." ( `parentid`, `titre`, `image`, `description`, `position`, `level` ) VALUES ('". $parentid ."', '". $titre ."', '". $image ."', '". $description ."', '". $position ."', '". $level ."')");
	            	// Action
	            	$texteaction = _ACTIONADDCATGAL ." ". $titre;
	            	$acdate = time();
	            	$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	            	//Fin action
	            	echo "<div class=\"notification success png_bg\"><div>". _CATADD ."</div></div>\n";
	            	redirect("index.php?file=Gallery_v2&page=admin&op=main_cat", 2);
	        }
    	}

    	function edit_cat($cid) {

	        global $nuked, $language;

	        $sql = mysql_query("SELECT titre, description, parentid, position, level, image FROM " . GALLERY_V2_CAT_TABLE . " WHERE cid='". $cid ."'");
	        list($titre, $description, $parentid, $position, $level, $image) = mysql_fetch_array($sql);

	        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery_v2.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div></div>\n"
	        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=modif_cat\">\n"
	        . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
	        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
	        . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>";
	        echo select_cat();
	        echo "</select></td></tr>"
	        . "<tr><td>Url de l'image : <input id=\"image\" type=\"text\" name=\"image\" value=\"". $image ."\" />&nbsp;<a  href=\"javascript:void(0);\" onclick=\"javascript:window.open('index.php?file=Gallery_v2&page=admin&nuked_nude=admin&op=show_icon','img','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=650,height=300,top=30,left=0');return(false)\">". _SEEICON ."</a></td></tr>"
	        . "<tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" /></td></tr>\n"
	        . "<tr><td><b>" . _LEVEL . "</b> : <select name=\"level\"><option>" . $level . "</option>"
                . "<option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option></select>"
	        . "<tr><td><b>" . _DESCR . " :</b><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr>\n"
	        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr>\n"
	        . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /></div>\n"
	        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    	}

    	function modif_cat($cid, $titre, $description, $parentid, $position, $image, $level) {

        	global $nuked, $user;

        	$titre = mysql_real_escape_string(stripslashes($titre));

        	if (empty($titre)) {
            		echo "<div class=\"notification error png_bg\"><div>" . _TITLEARTFORGOT . "</div></div>\n";
            		redirect("index.php?file=Gallery_v2&page=admin&op=main_cat", 2);
        	} else {
            		$description = html_entity_decode($description);
            		$description = mysql_real_escape_string(stripslashes($description));

                        $sql1 = mysql_query("SELECT level FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '". $parentid ."'");
	        	list($niveau) = mysql_fetch_array($sql1);

	        	if ($level < $niveau) $level = $niveau;
	        	else $level = $level;

            		$sql = mysql_query("UPDATE ". GALLERY_V2_CAT_TABLE ." SET parentid = '". $parentid ."', titre = '". $titre ."', description = '". $description ."', position = '". $position ."', level = '". $level ."', image = '". $image ."' WHERE cid = '". $cid ."'");
            		$sql2 = mysql_query("UPDATE ". GALLERY_V2_TABLE ." SET level = '". $level ."' WHERE cat = '". $cid ."'");

            		// Action
            		$texteaction = _ACTIONMODIFCATGAL ." ". $titre;
            		$acdate = time();
            		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
            		//Fin action
            		echo "<div class=\"notification success png_bg\"><div>". _CATMODIF ."</div></div>";
            		redirect("index.php?file=Gallery_v2&page=admin&op=main_cat", 2);
        	}
    	}

    	function select_cat($idCat=0,$mere='') {
	        $ligne = '';
	        $req = mysql_query("SELECT cid,titre FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '". $idCat ."'");
	        while ($row = mysql_fetch_array($req, MYSQL_ASSOC)) {
	        	$ligne .= '<option value="'. $row['cid'] .'">'. $mere . $row['titre'] .'</option>';
	        	$ligne .= select_cat($row['cid'], $mere . $row['titre'].' > ');
	        }
	        return $ligne;
    	}

    	function del_cat($cid) {

	        global $nuked, $user;

	        $sqlq = mysql_query("SELECT titre FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid='". $cid ."'");
	        list($titre) = mysql_fetch_array($sqlq);
	        $titre = mysql_real_escape_string($titre);
	        $sql = mysql_query("DELETE FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '". $cid ."'");
	        $sql = mysql_query("UPDATE ". GALLERY_V2_CAT_TABLE ." SET parentid = '0' WHERE parentid = '". $cid ."'");
	        $sql = mysql_query("UPDATE ". GALLERY_V2_TABLE ." SET cat = '0' WHERE cat = '". $cid ."'");
	        // Action
	        $texteaction =  _ACTIONDELCATGAL ." ". $titre;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>". _CATDEL ."</div></div>\n";
	        redirect("index.php?file=Gallery_v2&page=admin&op=main_cat", 2);
    	}

        function make_zip($cid) {

        	global $nuked, $language, $gallery_pref;

        	if (!extension_loaded('zip')) die('Votre serveur ne supporte pas la fonction zip !');

                $sql_c = mysql_query("SELECT url FROM ". GALLERY_V2_TABLE ." WHERE cat = '". $cid ."' AND type = ''");
        	$nb_media = mysql_num_rows($sql_c);

        	if($nb_media == 0) die('Catégorie vide !');

                $n_zip = 'catégorie_'. $cid .'.zip';
        	$zip = new ZipArchive();
        	if($zip->open($n_zip, ZipArchive::CREATE) === TRUE) {
        		while ($r_sql = mysql_fetch_array($sql_c, MYSQL_ASSOC)) {
        			$zip->addFile($gallery_pref['rep_img'] . $r_sql['url']);
        		}
        		$zip->close();
        	}
        	header('Content-Type: application/force-download');
         	header("Content-Length: ". filesize($n_zip));
         	header("Content-Disposition: attachment; filename=". basename($n_zip));
         	readfile($n_zip);
         	unlink($n_zip);
        }

    	function main_pref() {

	        global $nuked, $language, $gallery_pref;

                echo '<link rel="stylesheet" media="screen" type="text/css" href="media/colorpicker/css/colorpicker.css" />'
		. '<script type="text/javascript" src="media/colorpicker/js/colorpicker.js"></script>'
	        . "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery_v2.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div></div>\n"
	        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Gallery_v2&amp;page=admin\">" . _GALLERY . "</a> | "
	        . "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=add_screen\">" . _ADDSCREEN . "</a> | "
	        . "<a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
	        . "</b>" . _PREFS . "</div><br />\n"
	        . "<form method=\"post\" action=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=change_pref\">\n"
	        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
	        . "<tr><td align=\"center\" colspan=\"2\"><big>" . _PREFS . "</big></td></tr>\n"
	        . "<tr><td>" . _GALLERYTITLE . " : </td><td> <input type=\"text\" name=\"title\" size=\"40\" value=\"" . $gallery_pref['title']. "\" /></td></tr>\n"
	        . "<tr><td>" . _NUMBERCAT . " : </td><td><input type=\"text\" name=\"max_cat\" size=\"2\" value=\"" . $gallery_pref['max_cat'] . "\" /></td></tr>\n"
	        . "<tr><td>" . _NUMBERIMG . " : </td><td><input type=\"text\" name=\"mess_guest_page\" size=\"2\" value=\"" . $gallery_pref['mess_guest_page'] . "\" /></td></tr>\n"
	        . "<tr><td>" . _NUMBERIMGADMIN . " : </td><td><input type=\"text\" name=\"mess_admin_page\" size=\"2\" value=\"" . $gallery_pref['mess_admin_page'] . "\" /></td></tr>\n"
	        . "<tr><td>" . _COLORPLAYER . " : </td><td><input type=\"text\" id=\"couleur\" name=\"color_player\" value=\"". $gallery_pref['color_player'] ."\" /></td></tr>\n"
	        . "<tr><td>" . _MEDIAPREVNEXT . " :</td><td><select class=\"styled\" size=\"1\" name=\"aff_prev_next\">"
                . "<option value=\"on\""; if($gallery_pref['aff_prev_next'] == 'on') { echo "selected=\"selected\""; } echo ">Oui</option>"
                . "<option value=\"off\""; if($gallery_pref['aff_prev_next'] == 'off') { echo "selected=\"selected\""; } echo ">Non</option></td></tr>"
	        . "<tr><td>" . _GALLERYREP . " : </td><td> <input type=\"text\" name=\"rep_img\" size=\"30\" value=\"" . $gallery_pref['rep_img'] . "\" /></td></tr>\n"

	        . "<tr><td>" . _DLOK . " :</td><td><select size=\"1\" name=\"dl_ok\"><option value=\"on\""; if($gallery_pref['dl_ok'] == 'on') { echo "selected=\"selected\""; } echo ">Oui</option><option value=\"off\""; if($gallery_pref['dl_ok'] == 'off') { echo "selected=\"selected\""; } echo ">Non</option></select></td></tr>"
                . "<tr><td>" . _DLLVL . " :</td><td><select size=\"1\" name=\"dl_lvl\">"
                . "<option value=\"0\""; if($gallery_pref['dl_lvl'] == '0') { echo "selected=\"selected\""; } echo ">0</option>"
                . "<option value=\"1\""; if($gallery_pref['dl_lvl'] == '1') { echo "selected=\"selected\""; } echo ">1</option>"
                . "<option value=\"2\""; if($gallery_pref['dl_lvl'] == '2') { echo "selected=\"selected\""; } echo ">2</option>"
                . "<option value=\"3\""; if($gallery_pref['dl_lvl'] == '3') { echo "selected=\"selected\""; } echo ">3</option>"
                . "<option value=\"4\""; if($gallery_pref['dl_lvl'] == '4') { echo "selected=\"selected\""; } echo ">4</option>"
                . "<option value=\"5\""; if($gallery_pref['dl_lvl'] == '5') { echo "selected=\"selected\""; } echo ">5</option>"
                . "<option value=\"6\""; if($gallery_pref['dl_lvl'] == '6') { echo "selected=\"selected\""; } echo ">6</option>"
                . "<option value=\"7\""; if($gallery_pref['dl_lvl'] == '7') { echo "selected=\"selected\""; } echo ">7</option>"
                . "<option value=\"8\""; if($gallery_pref['dl_lvl'] == '8') { echo "selected=\"selected\""; } echo ">8</option>"
                . "<option value=\"9\""; if($gallery_pref['dl_lvl'] == '9') { echo "selected=\"selected\""; } echo ">9</option></select></td></tr>"
                . "<tr><td>". _ACTIVE_SUGGEST ." :</td><td><select size=\"1\" name=\"suggest\">"
                . "<option value=\"1\""; if($gallery_pref['suggest'] == '1') { echo "selected=\"selected\""; } echo ">Oui</option>"
                . "<option value=\"0\""; if($gallery_pref['suggest'] == '0') { echo "selected=\"selected\""; } echo ">Non</option></td></tr>"
                . "<tr><td>". _LVL_SUGGEST ." :</td><td><select size=\"1\" name=\"lvl_suggest\">"
                . "<option value=\"0\""; if($gallery_pref['lvl_suggest'] == '0') { echo "selected=\"selected\""; } echo ">0</option>"
                . "<option value=\"1\""; if($gallery_pref['lvl_suggest'] == '1') { echo "selected=\"selected\""; } echo ">1</option>"
                . "<option value=\"2\""; if($gallery_pref['lvl_suggest'] == '2') { echo "selected=\"selected\""; } echo ">2</option>"
                . "<option value=\"3\""; if($gallery_pref['lvl_suggest'] == '3') { echo "selected=\"selected\""; } echo ">3</option>"
                . "<option value=\"4\""; if($gallery_pref['lvl_suggest'] == '4') { echo "selected=\"selected\""; } echo ">4</option>"
                . "<option value=\"5\""; if($gallery_pref['lvl_suggest'] == '5') { echo "selected=\"selected\""; } echo ">5</option>"
                . "<option value=\"6\""; if($gallery_pref['lvl_suggest'] == '6') { echo "selected=\"selected\""; } echo ">6</option>"
                . "<option value=\"7\""; if($gallery_pref['lvl_suggest'] == '7') { echo "selected=\"selected\""; } echo ">7</option>"
                . "<option value=\"8\""; if($gallery_pref['lvl_suggest'] == '8') { echo "selected=\"selected\""; } echo ">8</option>"
                . "<option value=\"9\""; if($gallery_pref['lvl_suggest'] == '9') { echo "selected=\"selected\""; } echo ">9</option></select></td></tr>"
                . "<tr><td>". _SIZE_MAX_MO ." : </td><td><input type=\"text\" id=\"max_size\" name=\"max_size\" class=\"login_input_petit\" value=\"" . $gallery_pref['max_size'] . "\" /></td></tr>"
                . "<tr><td>". _DL_ZIP ." :</td><td><select size=\"1\" name=\"dl_zip\">"
                . "<option value=\"1\""; if($gallery_pref['dl_zip'] == '1') { echo "selected=\"selected\""; } echo ">Oui</option>"
                . "<option value=\"0\""; if($gallery_pref['dl_zip'] == '0') { echo "selected=\"selected\""; } echo ">Non</option></td></tr>"
	        . "<tr><td>". _LVL_ZIP ." :</td><td><select size=\"1\" name=\"lvl_dl_zip\">"
                . "<option value=\"0\""; if($gallery_pref['lvl_dl_zip'] == '0') { echo "selected=\"selected\""; } echo ">0</option>"
                . "<option value=\"1\""; if($gallery_pref['lvl_dl_zip'] == '1') { echo "selected=\"selected\""; } echo ">1</option>"
                . "<option value=\"2\""; if($gallery_pref['lvl_dl_zip'] == '2') { echo "selected=\"selected\""; } echo ">2</option>"
                . "<option value=\"3\""; if($gallery_pref['lvl_dl_zip'] == '3') { echo "selected=\"selected\""; } echo ">3</option>"
                . "<option value=\"4\""; if($gallery_pref['lvl_dl_zip'] == '4') { echo "selected=\"selected\""; } echo ">4</option>"
                . "<option value=\"5\""; if($gallery_pref['lvl_dl_zip'] == '5') { echo "selected=\"selected\""; } echo ">5</option>"
                . "<option value=\"6\""; if($gallery_pref['lvl_dl_zip'] == '6') { echo "selected=\"selected\""; } echo ">6</option>"
                . "<option value=\"7\""; if($gallery_pref['lvl_dl_zip'] == '7') { echo "selected=\"selected\""; } echo ">7</option>"
                . "<option value=\"8\""; if($gallery_pref['lvl_dl_zip'] == '8') { echo "selected=\"selected\""; } echo ">8</option>"
                . "<option value=\"9\""; if($gallery_pref['lvl_dl_zip'] == '9') { echo "selected=\"selected\""; } echo ">9</option></select></td></tr>"
                . "<tr><td>". _THUMB_CADRE ." :</td><td><select size=\"1\" name=\"make_thumb\">"
                . "<option value=\"1\""; if($gallery_pref['make_thumb'] == '1') { echo "selected=\"selected\""; } echo ">Avec cadre</option>"
                . "<option value=\"0\""; if($gallery_pref['make_thumb'] == '0') { echo "selected=\"selected\""; } echo ">Sans cadre</option></td></tr>"
                . "<tr><td>". _AFF_BLOCK ." :</td><td><select size=\"1\" name=\"block_type\">"
                . "<option value=\"1\""; if($gallery_pref['block_type'] == '1') { echo "selected=\"selected\""; } echo ">Le dernier média</option>"
                . "<option value=\"0\""; if($gallery_pref['block_type'] == '0') { echo "selected=\"selected\""; } echo ">Un média aléatoire</option></td></tr>"

	        . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
	        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery_v2&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n"
	        . '<script type="text/javascript">
            	//<![CDATA[
            	$(\'#couleur\').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {$(el).val(hex);$(el).ColorPickerHide();},
		onBeforeShow: function () {$(this).ColorPickerSetColor(this.value);}})
		.bind(\'keyup\', function(){$(this).ColorPickerSetColor(this.value);});
		//]]>
		</script>';
    	}

    	function change_pref($title, $max_cat, $mess_guest_page, $mess_admin_page, $color_player, $aff_prev_next, $rep_img, $dl_ok, $dl_lvl, $max_size, $suggest, $lvl_suggest, $dl_zip, $lvl_dl_zip, $make_thumb, $block_type) {

	        global $nuked, $user, $gallery_pref;

                if(!is_numeric($max_cat) || !is_numeric($mess_guest_page) || !is_numeric($mess_admin_page) || !is_numeric($max_size)) {
                	echo "<div class=\"notification error png_bg\"><div>" . _NUMBERCAT . ", " . _NUMBERIMG . ", " . _NUMBERIMGADMIN . ", " . _SIZE_MAX_MO . " ". _NOT_NUMERIQUE ."</div></div>\n";
                	redirect("index.php?file=Gallery_v2&page=admin", 2);
                	exit();
                }

	        $upd1 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". mysql_real_escape_string(stripslashes($title)) ."' WHERE name = 'title'");
	        $upd2 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $max_cat ."' WHERE name = 'max_cat'");
	        $upd3 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $mess_guest_page ."' WHERE name = 'mess_guest_page'");
	        $upd4 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $mess_admin_page ."' WHERE name = 'mess_admin_page'");
	        $upd5 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $color_player ."' WHERE name = 'color_player'");
	        $upd6 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $aff_prev_next ."' WHERE name = 'aff_prev_next'");
	        $upd7 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". mysql_real_escape_string(stripslashes($rep_img)) ."' WHERE name = 'rep_img'");
                $upd8 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $dl_ok ."' WHERE name = 'dl_ok'");
                $upd9 = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $dl_lvl ."' WHERE name = 'dl_lvl'");
                $upda = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $max_size ."' WHERE name = 'max_size'");
	        $updb = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $suggest ."' WHERE name = 'suggest'");
	        $updc = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $lvl_suggest ."' WHERE name = 'lvl_suggest'");
	        $updd = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $dl_zip ."' WHERE name = 'dl_zip'");
	        $upde = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $lvl_dl_zip ."' WHERE name = 'lvl_dl_zip'");
	        $updf = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $make_thumb ."' WHERE name = 'make_thumb'");
	        $updg = mysql_query("UPDATE ". GALLERY_V2_CONFIG_TABLE ." SET value = '". $block_type ."' WHERE name = 'block_type'");
	        // Action
	        $texteaction = _ACTIONPREFGAL;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>". _PREFUPDATED ."</div></div>\n";
	        redirect("index.php?file=Gallery_v2&page=admin", 2);
    	}

    	function modif_position($cid, $method) {

	        global $nuked, $user;

	        $sqlq = mysql_query("SELECT titre, position FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid='". $cid ."'");
	        list($titre, $position) = mysql_fetch_array($sqlq);
	        if ($position <= 0 && $method == "down") {
	            	echo "<div class=\"notification error png_bg\"><div>" . _CATERRORPOS . "</div></div>\n";
	            	redirect("index.php?file=Gallery_v2&page=admin&op=main_cat", 2);
	            	exit();
	        }
	        if ($method == "up") $upd = mysql_query("UPDATE ". GALLERY_V2_CAT_TABLE ." SET position = position + 1 WHERE cid = '" . $cid . "'");
	        else if ($method == "down") $upd = mysql_query("UPDATE ". GALLERY_V2_CAT_TABLE ." SET position = position - 1 WHERE cid = '" . $cid . "'");
	        // Action
	        $texteaction = _ACTIONPOSCATGAL ." ". $titre;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>" . _CATMODIF . "</div></div>\n";
	        redirect("index.php?file=Gallery_v2&page=admin&op=main_cat", 2);
    	}

	function get_id_video($url) {
		//initialisation des variables
		$host = '';
		$id = '';
		$parse = '';
		$parse2 = '';
		$formated_url = '';
		//On détermine où est hebergée la vidéo (youtube, dailymotion, vimeo) et on extrait les données nécessaires au formatage du lien
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
			$formated_url = $id;
			break;
			case 'vimeo':
			$formated_url = $id;
			break;
			case 'dailymotion':
			$formated_url = $id;
			break;
			default:
			break;
		}
		return $formated_url;
	}

        function show_icon() {

                global $bgcolor2, $theme, $nuked;

                $a_img  = array();
                $imgdir = 'modules/Gallery_v2/images/cat';
                $col    = 3;
                $maxrow = 3;
                $dimg   = opendir($imgdir);

                if (isset($_REQUEST['del']) && $_REQUEST['del'] != "") {
                        if (file_exists($imgdir .'/'. $_REQUEST['del']) && $_REQUEST['del'] != 'default.png') {
                                unlink($imgdir .'/'. $_REQUEST['del']);
                                echo '<div style="text-align:center;">L\'image '. $_REQUEST['del'] .' a etait supprim&eacute; !</div><br />';
                        } else if ($_REQUEST['del'] == 'default.png') echo '<div style="text-align:center;">Vous ne pouvez pas supprimer ce fichier !</div><br />';
                        else echo '<div style="text-align:center;">Ce fichier n\'existe plus !</div><br />';
                }

                if (isset($_FILES["fichier"]) && $_FILES["fichier"] != "") {
                        $fichier = basename($_FILES['fichier']['name']);
                        $taille_maxi = 1048576;
                        $taille = filesize($_FILES['fichier']['tmp_name']);
                        $extensions = array('.png', '.gif', '.jpg', '.jpeg', '.PNG', '.GIF', '.JPG', '.JPEG');
                        $extension = strrchr($_FILES['fichier']['name'], '.');
                        if( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $fichier) ) $erreur = '<div style="text-align:center;">Nom de fichier non valide !</div>';
                        if(!in_array($extension, $extensions)) $erreur = '<div style="text-align:center;">Vous devez uploader un fichier de type png, gif, jpg ou jpeg !</div>';
                        if($taille > $taille_maxi) $erreur = '<div style="text-align:center;">Le fichier est trop gros !</div>';
                        if(!isset($erreur)) {
                                $ext = pathinfo($fichier, PATHINFO_EXTENSION);
                                $fichier = time() .".". $ext;
                                if(move_uploaded_file($_FILES['fichier']['tmp_name'], $imgdir .'/'. $fichier)) echo '<div style="text-align:center;">Image Upload&eacute; effectu&eacute; avec succ&egrave;s !</div><br />';
                                else echo '<div style="text-align:center;">Echec de l\'upload !</div><br />';
                        } else echo $erreur;
                }

                echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
          	. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'
          	. '<head><title>'. _GALLERY .'</title>'
          	. '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'
          	. '<meta http-equiv="content-style-type" content="text/css" />'
          	. '<link title="style" type="text/css" rel="stylesheet" href="themes/'. $theme .'/style.css" /></head><body>'
                . "<body>"
                . "<script type=\"text/javascript\">\n"
                . "//<![CDATA[\n"
                . "function go(img){opener.document.getElementById('image').value=img;}\n"
                . "function del_img(titre, img){if(confirm('Supprimer '+titre+' ! ". _CONFIRM ."')){document.location.href = 'index.php?file=Gallery_v2&page=admin&nuked_nude=admin&op=show_icon&del='+img+'';}}\n"
                . "//]]>\n"
                . "</script>\n"
                . "<br /><div style=\"text-align:center;\">Cliquez sur une image pour la s&eacute;lectionner<br />Format .png .gif .jpg et inf&eacute;rieur a 1Mo</div><br />"
                . '<form action="index.php?file=Gallery_v2&page=admin&nuked_nude=admin&op=show_icon" method="post" enctype="multipart/form-data">'
                . '<input type="hidden" name="MAX_FILE_SIZE" value="1048576">'
                . '<input type="file" name="fichier" />'
                . '<input class="connexion_input" type="submit" value="Envoyer" />'
                . '</form><br /><span></span></div><br />';

                while($imgfile = readdir($dimg)) {
                        if ((substr($imgfile,-3)=="png") || (substr($imgfile,-3)=="jpg") || (substr($imgfile,-3)=="gif")) {
                                $a_img[count($a_img)] = $imgfile;
                                sort($a_img);
                                reset($a_img);
                        }
                }

                $totimg = count($a_img);
		$totxpage = $col*$maxrow;
		$totpages = $totimg%$totxpage == 0 ? (int)$totimg/$totxpage : (int)($totimg/$totxpage)+1;

                if($_REQUEST['p'] == "" || $_REQUEST['p'] == 1) {
        		$x = 0;
        		$p = 1;
        		$r = 0;
        	} else {
        		$x = ($_REQUEST['p']-1)*$totxpage;
        		$r = 0;
                }
                if ($totimg > $totxpage) number($totimg, $totxpage, "index.php?file=Gallery_v2&page=admin&nuked_nude=admin&op=show_icon");
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;width:80%;\" cellpadding=\"10\" cellspacing=\"10\">";
                foreach($a_img as $key => $val) {
                        if(($x%$col)==0) echo "<tr>";
                        if(isset($a_img[$x])) {
                        	echo "<td style=\"text-align:center;\"><a href=\"javascript:void(0);\" onclick=\"javascript:go('". $a_img[$x] ."');self.close();\"><img src=\"modules/Gallery_v2/images/cat/". $a_img[$x] ."\" alt=\"\" title=\"". $a_img[$x] ."\" /></a>"
                        	. "<br /><a href=\"javascript:del_img('". $a_img[$x] ."','". $a_img[$x] ."');\" title=\"Supprimer ". $a_img[$x] ."\"><img src=\"images/del.gif\" alt=\"\" /></a>"
                        	. "</td>";
                        }
                        if(($x%$col) == ($col-1)) {
                                echo "</tr>";
                        	$r++;
                        }
                        if($r==$maxrow) break;
                        else $x++;
                }
                echo '</table></body></html>';
        }

   	switch ($_REQUEST['op']) {
        	case "add_screen":
            	add_screen();
            	break;

        	case "del_screen":
            	del_screen($_REQUEST['sid']);
            	break;

        	case "send_screen":
            	send_screen($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['auteur'], $_REQUEST['mot_cle'], $_REQUEST['cat'], $_REQUEST['type'], $_REQUEST['url_video']);
            	break;

        	case "edit_screen":
            	edit_screen($_REQUEST['sid']);
            	break;

        	case "modif_img":
            	modif_img($_REQUEST['sid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['auteur'], $_REQUEST['url_video'], $_REQUEST['mot_cle'], $_REQUEST['cat']);
            	break;

        	case "send_cat":
            	send_cat($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['parentid'], $_REQUEST['position'], $_REQUEST['image'], $_REQUEST['level']);
            	break;

        	case "add_cat":
            	add_cat();
            	break;

        	case "main_cat":
            	main_cat();
            	break;

        	case "active_screen":
            	active_screen($_REQUEST['sid']);
            	break;

        	case "desactive_screen":
            	desactive_screen($_REQUEST['sid']);
            	break;

        	case "edit_cat":
            	edit_cat($_REQUEST['cid']);
            	break;

        	case "modif_cat":
            	modif_cat($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['parentid'], $_REQUEST['position'], $_REQUEST['image'], $_REQUEST['level']);
            	break;

        	case "del_cat":
            	del_cat($_REQUEST['cid']);
            	break;

        	case "make_zip":
            	make_zip($_REQUEST['cid']);
            	break;

        	case "main_pref":
            	main_pref();
            	break;

        	case "change_pref":
            	change_pref($_REQUEST['title'], $_REQUEST['max_cat'], $_REQUEST['mess_guest_page'], $_REQUEST['mess_admin_page'], $_REQUEST['color_player'], $_REQUEST['aff_prev_next'], $_REQUEST['rep_img'], $_REQUEST['dl_ok'], $_REQUEST['dl_lvl'], $_REQUEST['max_size'], $_REQUEST['suggest'], $_REQUEST['lvl_suggest'], $_REQUEST['dl_zip'], $_REQUEST['lvl_dl_zip'], $_REQUEST['make_thumb'], $_REQUEST['block_type']);
            	break;

        	case "modif_position":
            	modif_position($_REQUEST['cid'], $_REQUEST['method']);
            	break;

        	case "show_icon":
            	show_icon();
            	break;

        	default:
            	main();
            	break;
    	}

} else if ($level_admin == -1) {
    	echo "<div class=\"notification error png_bg\">\n"
    	. "<div>\n"
    	. "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    	. "</div>\n"
    	. "</div>\n";
} else if ($visiteur > 1) {
    	echo "<div class=\"notification error png_bg\">\n"
    	. "<div>\n"
    	. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    	. "</div>\n"
    	. "</div>\n";
} else {
    	echo "<div class=\"notification error png_bg\">\n"
    	. "<div>\n"
    	. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    	. "</div>\n"
    	. "</div>\n";
}

if($_REQUEST['op'] != 'show_icon' && $_REQUEST['op'] != 'make_zip') {
	adminfoot();
}

?>
