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

global $nuked, $language, $user;
translate("modules/Gallery_v2/lang/" . $language . ".lang.php");
include("modules/Gallery_v2/config.php");

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1) {

    	compteur("Gallery_v2");
    	include ("modules/Vote/index.php");

    	function index() {

        	global $nuked, $bgcolor1, $bgcolor2, $bgcolor3, $gallery_pref;

        	if ($gallery_pref['title'] != "") $title = $gallery_pref['title'];
        	else $title = _GALLERY;

        	opentable();

                echo '<script type="text/javascript" src="modules/Gallery_v2/gallery_v2.js"></script>'
        	. '<br /><div class="g2_title">'. $title .'</div><br />'
        	. '<div class="centeredmenu"><div class="nav l_g"><ul>'
            	. "<li><a href=\"index.php?file=Gallery_v2\"><span>". _INDEXGALLERY ."</span></a></li>"
            	. "<li><a href=\"index.php?file=Gallery_v2&amp;op=classe&amp;orderby=news\"><span>". _NEWSIMG ."</span></a></li>"
            	. "<li><a href=\"index.php?file=Gallery_v2&amp;op=classe&amp;orderby=count\"><span>". _TOPIMG ."</span></a></li>"
            	. "<li><a href=\"index.php?file=Gallery_v2&amp;op=suggest\"><span>". _SUGGESTIMG . "</span></a></li></ul></div></div><div class=\"clear\"></div><br />";

                if (!$_REQUEST['p']) $_REQUEST['p'] = 1;

        	$sql_nbimages = mysql_query("SELECT sid FROM ". GALLERY_V2_TABLE ." WHERE actif = '1'");
        	$nb_images = mysql_num_rows($sql_nbimages);

        	$sql_nbcat = mysql_query("SELECT cid FROM ". GALLERY_V2_CAT_TABLE);
        	$nb_cat = mysql_num_rows($sql_nbcat);

                $sql_nbcatpriv = mysql_query("SELECT COUNT(cid) FROM ". GALLERY_V2_CAT_TABLE ." WHERE level >= '1'");
                list($sql_nbcatpriv) = mysql_fetch_array($sql_nbcatpriv);

                $nb_max_cat = $gallery_pref['max_cat'];

                $sql_nbcat_index = mysql_query("SELECT cid FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '0'");
                $nb_cat_index = mysql_num_rows($sql_nbcat_index);
                $start = $_REQUEST['p'] * $nb_max_cat - $nb_max_cat;

                if ($nb_cat_index > $nb_max_cat) number($nb_cat_index, $nb_max_cat, 'index.php?file=Gallery_v2');
                else echo '<br />';

        	if ($nb_cat > 0) {
            		$test = 0;
            		echo "<table style=\"width:98%;margin:auto;\" cellspacing=\"10\" cellpadding=\"10\">";
                        $i = '';
            		$sql_cat = mysql_query("SELECT cid, titre, description, image FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '0' ORDER BY position, titre LIMIT ". $start .", ". $nb_max_cat);
            		while ($r_sql = mysql_fetch_array($sql_cat, MYSQL_ASSOC)) {
	                	$titre = stripslashes($r_sql['titre']);
	                	$description = stripslashes($r_sql['description']);

	                	$titre = htmlentities($r_sql['titre']);

                                if($r_sql['image'] != "") $img_cat = "<div style=\"text-align: center;\"><br /><a href=\"index.php?file=Gallery_v2&amp;op=categorie&amp;cat=". $r_sql['cid'] ."\" title=\"". _VOIRCAT ." : ". $titre ."\"><img src=\"modules/Gallery_v2/images/cat/". $r_sql['image'] ."\" alt=\"\" /></a></div>";
                                else $img_cat = '<div style="text-align: center;"><br /><a href="index.php?file=Gallery_v2&amp;op=categorie&amp;cat='. $r_sql['cid'] .'" title="'. _VOIRCAT .' : '. $titre .'"><img src="modules/Gallery_v2/images/cat/default.png" alt="" /></a></div>';

	                	$sql_img = mysql_query("SELECT sid, date FROM ". GALLERY_V2_TABLE ." WHERE cat = '". $r_sql['cid'] ."' ORDER BY sid DESC LIMIT 0, 1");
	                	$nb_imgcat = mysql_num_rows($sql_img);
	                	list($sid, $date) = mysql_fetch_row($sql_img);

	                	$sql_img_tt = mysql_query("SELECT sid FROM ". GALLERY_V2_TABLE .", ". GALLERY_V2_CAT_TABLE ." WHERE cat = cid AND actif = '1' AND (parentid = '". $r_sql['cid'] ."' OR cid = '". $r_sql['cid'] ."')");
	                	$nb_imgcat_tt = mysql_num_rows($sql_img_tt);

	                	$sql_nbcat = mysql_query("SELECT cid FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '". $r_sql['cid'] ."'");
	                	$nb_nbcat = mysql_num_rows($sql_nbcat);

	                	if ($date != "") {
	                    		$date = strftime("%x", $date);
	                    		$last_date = _LASTADD ." ". $date;
	                	} else $last_date = "";

	                	$test++;

                  		if ($i == 0) {
                                        $bg = $bgcolor2;
                                        $i++;
                                } else {
                                        $bg = $bgcolor1;
                                        $i = 0;
                                }

	                	if ($test == 1) echo "<tr>";

	                	echo "<td valign=\"top\" class=\"g2_cadre_table g2_gradient\" style=\"width:33%!important;\"><img style=\"vertical-align: middle;\" src=\"modules/Gallery_v2/images/fleche.png\" alt=\"\" />&nbsp;<a href=\"index.php?file=Gallery_v2&amp;op=categorie&amp;cat=". $r_sql['cid'] ."\" title=\"". _VOIRCAT ." : ". $titre ."\">". $titre ."</a>"
	                	. $img_cat ."<br />Nb d'images : ". $nb_imgcat_tt;

	                	if ($nb_nbcat == 0) echo "<br />". $description;

	                	if ($nb_nbcat) echo "<br /><img style=\"vertical-align: middle;\" src=\"modules/Gallery_v2/images/mini_navigate_right.png\" alt=\"\" />&nbsp;". $nb_nbcat ."&nbsp;". _NBSOUSCAT;

	                	if ($test == 3) {
	                        	$test = 0;
	                        	echo "</tr>";
	                	}

	                	echo '</td>';
            		}

            		if ($test == 1) echo "<td style=\"width:33%;\"></td><td style=\"width:33%;\"></td></tr>";
            		if ($test == 2) echo "<td style=\"width:33%;\"></td></tr>";
            		echo "</table>";
        	} else echo "<br />";

        	classe("0");

                if ($nb_cat_index > $nb_max_cat) number($nb_cat_index, $nb_max_cat, 'index.php?file=Gallery_v2');

        	if ($nb_cat > 0 || $nb_images > 0) echo "<br /><div class=\"g2_info\">" . _THEREIS . "&nbsp;" . $nb_images . "&nbsp;" . _SCREENINDB . " &amp; " . $nb_cat . "&nbsp;" . _NBCAT . "&nbsp;" . _INDATABASE . "&nbsp;". _DONT ."&nbsp;". $sql_nbcatpriv ."&nbsp;". _NBCAT ."&nbsp;". _CATPRIV ."</div><br /><br />";
        	else echo "<div class=\"g2_warn\">" . _NOSCREENINDB . "</div><br /><br />";

        	closetable();
    	}

    	function cat($cat) {

        	global $nuked, $bgcolor1, $bgcolor2, $bgcolor3, $gallery_pref;

        	$counter = 0;

        	$sql = mysql_query("SELECT cid, titre, description FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '". $cat ."'");
        	$nb_subcat = mysql_num_rows($sql);

        	if ($nb_subcat > 0) {

        		$nb_s_cat = $gallery_pref['max_cat'];

        		$sql_nb = mysql_query("SELECT cid FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '". $cat ."'");
        		$nb_dl = mysql_num_rows($sql_nb);

        		if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        		$start = $_REQUEST['p'] * $nb_s_cat - $nb_s_cat;

        		if ($nb_dl > $nb_s_cat) number($nb_dl, $nb_s_cat, 'index.php?file=Gallery_v2&op=categorie&cat='. $cat);

            		echo "<table width=\"95%\" cellspacing=\"10\" cellpadding=\"10\" border=\"0\">";

            		$sql = mysql_query("SELECT cid, titre, description, image FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '". $cat ."' ORDER BY position, titre LIMIT ". $start .", ". $nb_s_cat);
                        $i = '';
            		while (list($catid, $parentcat, $parentdesc, $image) = mysql_fetch_array($sql)) {
                		$sql_img = mysql_query("SELECT sid, url, url_file, date FROM ". GALLERY_V2_TABLE ." WHERE cat = '". $catid ."' AND actif = '1' ORDER BY sid DESC");
                		$nb_imgcat = mysql_num_rows($sql_img);
                		list($sid, $url, $url2, $date) = mysql_fetch_array($sql_img);

                                if($image != "") $img_cat = "<div style=\"text-align: center;\"><br /><a href=\"index.php?file=Gallery_v2&amp;op=categorie&amp;cat=". $catid ."\" title=\"". _VOIRSOUSCAT ." : ". $parentcat ."\"><img src=\"modules/Gallery_v2/images/cat/". $image ."\" alt=\"\" /></a></div>";
                                else $img_cat = '<div style="text-align: center;"><br /><a href="index.php?file=Gallery_v2&amp;op=categorie&amp;cat='. $catid .'" title="'. _VOIRSOUSCAT .' : '. $parentcat .'"><img src="modules/Gallery_v2/images/cat/default.png" alt="" /></a></div>';

                		if ($date) {
                    			$date = strftime("%x", $date);
                    			$last_date = _LASTADD ."&nbsp;". $date;
                		} else $last_date = "";

                                $last_catid = "";
                		if ($catid != $last_catid) {
                    			$counter++;

                                        if ($i == 0) {
                                        	$bg = $bgcolor2;
                                        	$i++;
                                	} else {
                                        	$bg = $bgcolor1;
                                        	$i = 0;
                                	}

                    			if ($counter == 1) echo "<tr>";

                    			echo "<td valign=\"top\" class=\"g2_cadre_table g2_gradient\" style=\"width:33%!important;\"><img style=\"vertical-align: middle;\" src=\"modules/Gallery_v2/images/fleche.png\" alt=\"\" />&nbsp;<a href=\"index.php?file=Gallery_v2&amp;op=categorie&amp;cat=". $catid ."\" title=\"". _VOIRSOUSCAT ." : ". $parentcat ."\">". $parentcat ."</a>";

                    			if ($parentdesc != "") echo "<br />". $parentdesc;

                    			echo "<br />". $img_cat ."<br />". $nb_imgcat ."&nbsp;". _SCREENINDB;

                    			if ($last_date != "") echo "<br />". $last_date;

                    			$sql_nbcat = mysql_query("SELECT parentid FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '". $catid ."'");
                    			$nb_nbcat = mysql_num_rows($sql_nbcat);

                    			if ($nb_nbcat) echo "<br /><img style=\"vertical-align: middle;\" src=\"modules/Gallery_v2/images/mini_navigate_right.png\" alt=\"\" />&nbsp;". $nb_nbcat ."&nbsp;". _NBSOUSCAT;

                    			echo '</td>';
                    			if ($counter == 3) {
                        			$counter = 0;
                        			echo "</tr>";
                    			}

                    			$last_catid = $catid;
                		}
            		}

            		if ($counter == 1) echo "<td style=\"width: 33%;\">&nbsp;</td><td style=\"width: 33%;\">&nbsp;</td></tr>";
                        if ($counter == 2) echo "<td style=\"width: 33%;\">&nbsp;</td></tr>";
            		echo "</table>";

            		if ($nb_dl > $nb_s_cat) number($nb_dl, $nb_s_cat, 'index.php?file=Gallery_v2&op=categorie&cat='. $cat);
        	}
    	}

    	function categorie($cat) {

	        global $nuked, $gallery_pref, $visiteur;

	        opentable();

                $sql_cat = mysql_query("SELECT level FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '". $cat ."'");
                list($cat_level) = mysql_fetch_array($sql_cat);

                if ($gallery_pref['title'] != "") $title = $gallery_pref['title'];
		else $title = _GALLERY;

                echo "<br /><div class=\"g2_title\">". $title ."</div><br />";

                if ($visiteur >= $cat_level) {
		        $sql = mysql_query("SELECT titre, description, parentid FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '" . $cat . "'");
		        list($cat_titre, $cat_desc, $parentid) = mysql_fetch_array($sql);

		        $cat_desc = icon($cat_desc);

		        echo '<div class="tchoutchou"><a href="index.php?file=Gallery_v2">'. $title .'</a>';
		        echo selcat($cat);
                        echo '</div>';
		        if ($cat_desc != "") echo "<br /><div class=\"g2_info\">". $cat_desc ."</div><br />";

		        cat($cat);
		        classe($cat);

                } elseif($cat_level == 1 && $visiteur == 0) echo "<div class=\"g2_warn\">". _CAUTION_MEMBRE ."</div><br /><br />";
                else {
			echo "<div class=\"g2_warn\">". _CAUTION . "</div><br /><br />";
                        redirect("index.php?file=Gallery_v2", 5);
                }

	        closetable();
    	}

   	 function selcat($idCat) {

	        $ret = '';
	        $req = mysql_query("SELECT cid, titre, parentid FROM ". GALLERY_V2_CAT_TABLE ." WHERE cid = '". $idCat ."'");
	        while ($r_sql = mysql_fetch_array($req, MYSQL_ASSOC)) {
	        	if($r_sql['parentid'] != 0) $ret .= selcat($r_sql['parentid']);
	        	$ret .= ' > <a href="index.php?file=Gallery_v2&amp;op=categorie&amp;cat='. $r_sql['cid'] .'&p=1" style="text-decoration:none">'. htmlentities($r_sql['titre']) .'</a>';
	        }
	        return $ret;
        }

    	function classe($cat) {

    		global $nuked, $bgcolor1, $bgcolor2, $bgcolor3, $gallery_pref, $visiteur;

        	$nb_img_guest = $gallery_pref['mess_guest_page'];

        	if ($cat > 0) {
            		$sql = mysql_query("SELECT cid FROM ". GALLERY_V2_CAT_TABLE ." WHERE parentid = '". $cat ."'");
            		$nb_subcat = mysql_num_rows($sql);
        	} else $nb_subcat = 0;

		echo '<script type="text/javascript" src="modules/Gallery_v2/gallery_v2.js"></script>';

        	if ($_REQUEST['op'] == "classe") {

            		if ($gallery_pref['title'] != "") $title = $gallery_pref['title'];
            		else $title = _GALLERY;

            		echo "<br /><div class=\"g2_title\">". $title ."</div><br />"
            		. '<div class="centeredmenu"><div class="nav l_g"><ul>'
            		. "<li><a href=\"index.php?file=Gallery_v2\"><span>". _INDEXGALLERY ."</span></a></li>"
            		. "<li><a href=\"index.php?file=Gallery_v2&amp;op=classe&amp;orderby=news\"><span>". _NEWSIMG ."</span></a></li>"
            		. "<li><a href=\"index.php?file=Gallery_v2&amp;op=classe&amp;orderby=count\"><span>". _TOPIMG ."</span></a></li>"
            		. "<li><a href=\"index.php?file=Gallery_v2&amp;op=suggest\"><span>". _SUGGESTIMG . "</span></a></li></ul></div></div><div class=\"clear\"></div><br />";
        	}

        	if ($cat != "") $where = "WHERE L.cat = '". $cat ."'";
        	else $where = "WHERE ". $visiteur ." >= level ";

        	if ($_REQUEST['orderby'] == "name") $order = "ORDER BY L.titre";
        	else if ($_REQUEST['orderby'] == "count") $order = "ORDER BY L.count DESC";
        	else if ($_REQUEST['orderby'] == "note") $order = "ORDER BY note DESC";
        	else {
            		$_REQUEST['orderby'] = "news";
            		$order = "ORDER BY L.sid DESC";
        	}

        	$sql = mysql_query("SELECT L.sid, L.titre, L.url, L.url_file, L.date, L.type, AVG(V.vote) AS note  FROM " . GALLERY_V2_TABLE . " AS L LEFT JOIN " . VOTE_TABLE . " AS V ON L.sid = V.vid AND V.module = 'Gallery_v2' ". $where ." AND actif = '1'  GROUP BY L.sid " . $order);
        	$count = mysql_num_rows($sql);

        	if ($count > 1 && $cat != "") {
            		echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr>";

            		if($gallery_pref['dl_zip'] == '1' && $visiteur >= $gallery_pref['lvl_dl_zip']) {            			echo "<td align=\"left\"><small><a href=\"javascript:void(0);\" onclick=\"javascript:window.open('index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_zip&amp;cid=" . $cat . "','dl','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=650,height=300,top=30,left=0');return(false)\"><img style=\"border: 0;\" src=\"modules/Gallery_v2/images/make_zip.png\" alt=\"\" title=\"" . _DLTHISCAT . "\" /></a></small></td>";            		}

            		echo "<td align=\"right\">"
            		. '<div class="rightmenu"><div class="nav l_g"><ul>'
            		. "<li><a href=\"index.php?file=Gallery_v2&amp;op=" . $_REQUEST['op'] . "&amp;orderby=news&amp;cat=" . $cat . "\"><span>" . _DATE . "</span></a></li>"
            		. "<li><a href=\"index.php?file=Gallery_v2&amp;op=" . $_REQUEST['op'] . "&amp;orderby=name&amp;cat=" . $cat . "\"><span>" . _NAME . "</span></a></li>"
            		. "<li><a href=\"index.php?file=Gallery_v2&amp;op=" . $_REQUEST['op'] . "&amp;orderby=count&amp;cat=" . $cat . "\"><span>" . _TOPFILE . "</span></a></li>"
            		. "<li><a href=\"index.php?file=Gallery_v2&amp;op=" . $_REQUEST['op'] . "&amp;orderby=note&amp;cat=" . $cat . "\"><span>" . _NOTE . "</span></a>&nbsp;"
                        . '</ul></div></div><div class="clear"></div>'
            		. "</td></tr></table>\n";
        	}

        	if ($count > 0) {

            		if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
            		$start = $_REQUEST['p'] * $nb_img_guest - $nb_img_guest;

            		if ($count > $nb_img_guest) {
                		echo "<p>&nbsp;</p><table class=\"g2_cadre_table_page\" style=\"background: ". $bgcolor1 .";\" cellspacing=\"5\" cellpadding=\"5\">"
            			. "<tr><td>";
                		number($count, $nb_img_guest, "index.php?file=Gallery_v2&amp;op=". $_REQUEST['op'] ."&amp;cat=". $cat ."&amp;orderby=". $_REQUEST['orderby']);
               	 		echo "</td></tr></table><br /><br />";
            		} else echo '<br />';

            		echo "<table style=\"margin:auto;text-align:center;width:600px;\" cellpadding=\"5\" cellspacing=\"5\">";
                        $test_td = 0;

                        $seek = mysql_data_seek($sql, $start);
            		for($i = 0;$i < $nb_img_guest;$i++) {
                		if (list($sid, $titre, $url, $url_file, $date, $type) = mysql_fetch_row($sql)) {
	                                if (($type == "flv" || $type == "youtube" || $type == "dailymotion" || $type == "vimeo") && $url_file != '') {
                                 		$ext = pathinfo($url_file, PATHINFO_EXTENSION);
                                 		if($gallery_pref['make_thumb'] == '0') {
                                 			if (file_exists($gallery_pref['rep_img'] .'temp/mini_'. str_replace('.'. $ext, '', $url_file) .'.png')) $img_thumb = "<img src=\"". $gallery_pref['rep_img'] ."temp/mini_". str_replace('.'. $ext, '', $url_file) .".png\" alt=\"\" />";
                                 			else $img_thumb = '<img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=p&amp;a_c=0&amp;image='. $url_file .'" alt="" />';
                                 		} else {
                                 			if (file_exists($gallery_pref['rep_img'] .'temp/mini_cadre_'. str_replace('.'. $ext, '', $url_file) .'.png')) $img_thumb = "<img src=\"". $gallery_pref['rep_img'] ."temp/mini_cadre_". str_replace('.'. $ext, '', $url_file) .".png\" alt=\"\" />";
                                 			else $img_thumb = '<img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=pc&amp;a_c=1&amp;image='. $url_file .'" alt="" />';
                                 		}
                                 	} elseif (($type == "flv" || $type == "youtube" || $type == "dailymotion" || $type == "vimeo") && $url_file == '') $img_thumb = '<img src="modules/Gallery_v2/images/video.png" alt="" />';
                                  	else {
                                   		$ext = pathinfo($url, PATHINFO_EXTENSION);
                                   		if($gallery_pref['make_thumb'] == '0') {
                                   			if (file_exists($gallery_pref['rep_img'] .'temp/mini_'. str_replace('.'. $ext, '', $url) .'.png')) $img_thumb = "<img src=\"". $gallery_pref['rep_img'] ."temp/mini_". str_replace('.'. $ext, '', $url) .".png\" alt=\"\" />";
                                   			else $img_thumb = '<img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=m&amp;a_c=0&amp;image='. $url .'" alt="" />';
                                   		} else {
                                   			if (file_exists($gallery_pref['rep_img'] .'temp/mini_cadre_'. str_replace('.'. $ext, '', $url) .'.png')) $img_thumb = "<img src=\"". $gallery_pref['rep_img'] ."temp/mini_cadre_". str_replace('.'. $ext, '', $url) .".png\" alt=\"\" />";
                                   			else $img_thumb = '<img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=pc&amp;a_c=1&amp;image='. $url .'" alt="" />';
                                   		}
                                   	}

	                                if($url != "" && ($type == "" || $type == "flv")) $extension = pathinfo($url, PATHINFO_EXTENSION);
	                                elseif($url != "" && $type == "youtube") $extension = 'youtube';
	                                elseif($url != "" && $type == "dailymotion") $extension = 'dailymotion';
	                                elseif($url != "" && $type == "vimeo") $extension = 'vimeo';
	                                else $extension = 'files';

	                                if (is_file("modules/Gallery_v2/images/ext/". $extension .".png")) $img = "modules/Gallery_v2/images/ext/". $extension .".png";
	                                else $img = "modules/Gallery_v2/images/ext/files.png";

	                    		$titre = stripslashes($titre);
	                    		$titre = htmlentities($titre);

	                    		if ($date != "") $alt = _ADDTHE . "&nbsp;" . strftime("%x %H:%M", $date);
	                    		else $alt = $titre;

                        		$test_td++;
                        		if ($test_td == 1) {
                            			echo "<tr>";
                        		}
                                        echo '<td>'
                        		. "<table class=\"g2_cadre_table g2_gradient\" cellspacing=\"0\" cellpadding=\"0\">"
                        		. "<tr><td style=\"padding:10px;text-align:left;\">". $titre ."<div style=\"float:right;pading-right:10px;\"><img style=\"vertical-align:bottom;\" src=\"". $img ."\" alt=\"\" title=\"". _THISFILE ." ". $extension ."\" /></div></td></tr>"
                        		. "<tr><td style=\"padding:10px;\">"
                        		. "<a href=\"index.php?file=Gallery_v2&amp;op=description&amp;sid=". $sid ."&amp;orderby=". $_REQUEST['orderby'] ."\">". $img_thumb ."</a><br />";
                        		echo aff_vote($sid);
                        		echo "</td>"
                        		. "</tr></table>"
                                        . '</td>';
                        		if ($test_td == 2) {
                            			$test_td = 0;
                            			echo "</tr>";
                        		}
                       		}
                	}
                	if ($test_td == 1) echo "<td style=\"width:50%;\"></td></tr>";
                        echo '</table>';

            		if ($nb_subcat == 0 && $count == 0) echo _NOSCREEN;

            		if ($count > $nb_img_guest) {
                		echo "<p>&nbsp;</p><table class=\"g2_cadre_table_page\" style=\"background: ". $bgcolor1 .";\" cellspacing=\"5\" cellpadding=\"5\">"
            			. "<tr><td>";
                		number($count, $nb_img_guest, "index.php?file=Gallery_v2&amp;op=". $_REQUEST['op'] ."&amp;cat=". $cat ."&amp;orderby=". $_REQUEST['orderby']);
               	 		echo "</td></tr></table><p>&nbsp;</p>";
            		} else echo '<br />';

        	} else {
             		if ($nb_subcat == 0 && $cat > 0) echo "<br /><br /><div class=\"g2_warn\">". _NOSCREEN ."</div><br /><br />";
             		if ($_REQUEST['op'] == "classe") echo "<br /><br /><div class=\"g2_warn\">". _NOSCREENINDB ."</div><br /><br />";
        	}

    	}

    	function description($sid) {

        	global $nuked, $user, $visiteur, $bgcolor1, $bgcolor2, $bgcolor3, $gallery_pref, $language;

        	$sql_cat = mysql_query("SELECT CAT.level, CAT.titre, CAT.parentid FROM " . GALLERY_V2_CAT_TABLE . " AS CAT LEFT JOIN ". GALLERY_V2_TABLE ."  AS GAL ON CAT.cid = GAL.cat WHERE GAL.sid = '". $sid ."'");
                list($cat_level, $cat_name, $parentid) = mysql_fetch_array($sql_cat);

                opentable();
                if ($gallery_pref['title'] != "") $title = $gallery_pref['title'];
	        else $title = _GALLERY;

        	if ($visiteur >= $cat_level) {

	        	$upd = mysql_query("UPDATE " . GALLERY_V2_TABLE . " SET count = count + 1 WHERE sid = '" . $sid . "'");

	        	$sql = mysql_query("SELECT sid, cat, titre, description, autor, url, url_file, date, count, count_dl, taille, type, mot_cle, actif FROM ". GALLERY_V2_TABLE ." WHERE sid = '". $sid ."'");
	        	list($sid, $cat, $titre, $description, $autor, $url, $url_file, $date, $count, $count_dl, $taille, $type, $mot_cle, $actif) = mysql_fetch_array($sql);

	                // On affiche un message si le média n'existe pas
	                if ($sid == '') {
	                	echo "<br /><div class=\"g2_title\">". $title ."</div><br />"
	                	. "<div class=\"g2_warn\">". _MEDIANOFOUND ."<br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div><br />";
	                	closetable();
	                	footer();
	                	exit;
	                }
	                // On affiche un message si le média est désactivé
	                if ($actif == '0') {
	                	echo "<br /><div class=\"g2_title\">". $title ."</div><br />"
	                	. "<div class=\"g2_warn\">". _MEDIANOTACTIF ."<br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div><br />";
	                	closetable();
	                	footer();
	                	exit;
	                }
		        $titre = htmlentities($titre);
		        $autor = htmlentities($autor);

	        	if (!$name) $name = "N/A";

	        	if ($date) $date = strftime("%x %H:%M", $date);
	        	else $date = "N/A";

	        	$sql2 = mysql_query("SELECT titre, parentid FROM " . GALLERY_V2_CAT_TABLE . " WHERE cid = '" . $cat . "'");
	        	list($cat_name, $parentid) = mysql_fetch_array($sql2);
	        	$cat_name = stripslashes($cat_name);
	        	$cat_name = htmlentities($cat_name);

	        	if ($cat == 0) $category = _NONE;
	        	else if ($parentid > 0) {
	            		$sql3 = mysql_query("SELECT titre FROM " . GALLERY_V2_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
	            		list($parent_name) = mysql_fetch_array($sql3);
	            		$parent_name = stripslashes($parent_name);
	            		$parent_name = htmlentities($parent_name);

	            		$category = "<a href=\"index.php?file=Gallery_v2&amp;op=categorie&amp;cat=" . $parentid . "\">" . $parent_name . "</a> -&gt; <a href=\"index.php?file=Gallery_v2&amp;op=categorie&amp;cat=" . $cat . "\">" . $cat_name . "</a>";
	        	} else $category = "<a href=\"index.php?file=Gallery_v2&amp;op=categorie&amp;cat=" . $cat . "\">" . $cat_name . "</a>";

	                if ($gallery_pref['aff_prev_next'] == 'on') {
		        	if ($_REQUEST['orderby'] == "name") {
		            		$sql_next = mysql_query("SELECT sid FROM " . GALLERY_V2_TABLE . " WHERE cat = '" . $cat . "' AND titre > '" . $titre . "' ORDER BY titre LIMIT 0, 1");
		            		list($nextid) = mysql_fetch_array($sql_next);

		            		$sql_last = mysql_query("SELECT sid FROM " . GALLERY_V2_TABLE . " WHERE cat = '" . $cat . "' AND titre < '" . $titre . "' ORDER BY titre DESC LIMIT 0, 1");
		            		list($lastid) = mysql_fetch_array($sql_last);
		        	} else if ($_REQUEST['orderby'] == "count") {
		            		$sql_next = mysql_query("SELECT sid FROM " . GALLERY_V2_TABLE . " WHERE cat = '" . $cat . "' AND count < '" . $count . "' ORDER BY count DESC LIMIT 0, 1");
		            		list($nextid) = mysql_fetch_array($sql_next);

		            		$sql_last = mysql_query("SELECT sid FROM " . GALLERY_V2_TABLE . " WHERE cat = '" . $cat . "' AND count > '" . $count . "' ORDER BY count LIMIT 0, 1");
		            		list($lastid) = mysql_fetch_array($sql_last);
		        	} else if ($_REQUEST['orderby'] == "note") {
		            		$sql_note = mysql_query("SELECT AVG(vote) FROM " . VOTE_TABLE . " WHERE vid = '" . $sid . "' AND module = 'Gallery_v2'");
		            		list($note) = mysql_fetch_array($sql_note);

		            		$sql_next = mysql_query("SELECT L.sid, AVG(V.vote) AS note FROM " . GALLERY_V2_TABLE . " AS L LEFT JOIN " . VOTE_TABLE . " AS V ON L.sid = V.vid AND V.module = 'Gallery_v2' WHERE L.cat = '" . $cat . "' GROUP BY L.sid HAVING note < '" . $note . "' ORDER BY note DESC LIMIT 0, 1");
		            		list($nextid) = mysql_fetch_array($sql_next);

		            		$sql_last = mysql_query("SELECT L.sid, AVG(V.vote) AS note FROM " . GALLERY_V2_TABLE . " AS L LEFT JOIN " . VOTE_TABLE . " AS V ON L.sid = V.vid AND V.module = 'Gallery_v2' WHERE L.cat = '" . $cat . "' GROUP BY L.sid HAVING note > '" . $note . "' ORDER BY note LIMIT 0, 1");
		            		list($lastid) = mysql_fetch_array($sql_last);
		        	} else {
		            		$_REQUEST['orderby'] = "news";

		            		$sql_next = mysql_query("SELECT sid FROM " . GALLERY_V2_TABLE . " WHERE cat = '" . $cat . "' AND sid < '" . $sid . "' ORDER BY sid DESC LIMIT 0, 1");
		            		list($nextid) = mysql_fetch_array($sql_next);

		            		$sql_last = mysql_query("SELECT sid FROM " . GALLERY_V2_TABLE . " WHERE cat = '" . $cat . "' AND sid > '" . $sid . "' ORDER BY sid LIMIT 0, 1");
		            		list($lastid) = mysql_fetch_array($sql_last);
		        	}

		        	if ($nextid != "") $next = "<small><a href=\"index.php?file=Gallery_v2&amp;op=description&amp;sid=". $nextid ."&amp;orderby=". $_REQUEST['orderby'] . "\">" . _NEXTIMG . "</a> &gt;</small>";
		        	if ($lastid != "") $prev = "<small>&lt; <a href=\"index.php?file=Gallery_v2&amp;op=description&amp;sid=". $lastid ."&amp;orderby=". $_REQUEST['orderby'] . "\">" . _LASTIMG . "</a> &nbsp;</small>";
		        	$link_prev_next = "<br /><div style=\"text-align: center;margin:auto;width:100%;display:inline;\"><div style=\"float:right;\">". $next ."</div><div style=\"float:left;\">". $prev ."</div></div>";
	                } else $link_prev_next = '';

	        	if ($taille != "" && $taille < 1000) $size = $taille . "&nbsp;KO" ;
	         	else if ($taille != "" && $taille >= 1000) {
	         		$taille = $taille / 1000;
	         		$taille = (round($taille * 100)) / 100;
	         		$size = $taille. "&nbsp;MO";
	         	}

	        	if($type == "flv") $image = '<object type="application/x-shockwave-flash" data="modules/Gallery_v2/player.swf" width="640" height="385"><param name="FlashVars" value="flv='. $url .'&amp;s_color='. $gallery_pref['color_player'] .'" /><param name="allowFullScreen" value="true" /><param name="menu" value="false" /><param name="wmode" value="transparent" /><param name="quality" value="high" /><param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" /></object>';
	         	elseif($type == "youtube") $image = '<object type="application/x-shockwave-flash" style="width:640px; height:385px;" data="http://www.youtube.com/v/'. $url .'&amp;color2=0x'. $gallery_pref['color_player'] .'"><param name="movie" value="http://www.youtube.com/v/'. $url .'&amp;color2=0x'. $gallery_pref['color_player'] .'" /></object>';
	          	elseif($type == "dailymotion") $image = '<object type="application/x-shockwave-flash" style="width:640px; height:385px;" data="http://www.dailymotion.com/swf/video/'. $url .'&amp;highlight=%23'. str_replace("#", "", $bgcolor1) .'&amp;background=%23'. $gallery_pref['color_player'] .'"><param name="movie" value="http://www.dailymotion.com/swf/video/'. $url .'&amp;highlight=%23'. str_replace("#", "", $bgcolor1) .'&amp;background=%23'. $gallery_pref['color_player'] .'" /></object>';
	           	elseif($type == "vimeo") $image = '<object type="application/x-shockwave-flash" style="width:640px; height:385px;" data="http://vimeo.com/moogaloop.swf?clip_id='. $url .'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0"><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='. $url .'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" /></object>';
	           	else {
	            		$ext = pathinfo($url, PATHINFO_EXTENSION);
	              		if($gallery_pref['make_thumb'] == '0') {
	              		        if (file_exists($gallery_pref['rep_img'] .'temp/big_'. str_replace('.'. $ext, '', $url) .'.png')) $image = '<a href="'. $gallery_pref['rep_img'] . $url .'" rel="shadowbox" title="'. $titre .'"><img src="'. $gallery_pref['rep_img'] .'temp/big_'. str_replace('.'. $ext, '', $url) .'.png" alt="" /></a>';
	              			else $image = '<a href="'. $gallery_pref['rep_img'] . $url .'" rel="shadowbox" title="'. $titre .'"><img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=g&amp;a_c=0&amp;image='. $url .'" alt="" /></a>';
	              		} else {
	              			if (file_exists($gallery_pref['rep_img'] .'temp/big_cadre_'. str_replace('.'. $ext, '', $url) .'.png')) $image = '<a href="'. $gallery_pref['rep_img'] . $url .'" rel="shadowbox" title="'. $titre .'"><img src="'. $gallery_pref['rep_img'] .'temp/big_cadre_'. str_replace('.'. $ext, '', $url) .'.png" alt="" /></a>';
	              			else $image = '<a href="'. $gallery_pref['rep_img'] . $url .'" rel="shadowbox" title="'. $titre .'"><img src="index.php?file=Gallery_v2&amp;nuked_nude=index&amp;op=make_thumb&amp;t=gc&amp;a_c=1&amp;image='. $url .'" alt="" /></a>';
	              		}
	           	}

			// on dessine un lapin :) c'est zouli hein ^^
			// (\___/)
			// (='.'=)
			// (")_(")

	        	if ($visiteur >= admin_mod("Gallery_v2")) {
	            		echo"<script type=\"text/javascript\">\n"
	            		. "<!--\n"
	            		. "function delimg(titre, id){ if (confirm('" . _SCREENDELETE  . " '+titre+' ! " . _CONFIRM . "')){document.location.href = 'index.php?file=Gallery_v2&page=admin&op=del_screen&sid='+id;}}\n"
	            		. "//-->\n"
	            		. "</script>\n"
	            		. "<div style=\"text-align: right;\"><a href=\"index.php?file=Gallery_v2&amp;page=admin&amp;op=edit_screen&amp;sid=" . $sid . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>"
	            		. "&nbsp;<a href=\"javascript:delimg('" . addslashes($titre) . "', '" . $sid . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DEL . "\" /></a></div>\n";
	        	}

	                // On initialise la shadowbox :)
	                echo '<link rel="stylesheet" type="text/css" href="media/shadowbox/shadowbox.css">'
			. '<script type="text/javascript" src="media/shadowbox/shadowbox.js"></script>'
			. '<script type="text/javascript">'
			. 'Shadowbox.init();'
			. '</script><script type="text/javascript" src="modules/Gallery_v2/gallery_v2.js"></script>'
	        	. "<br /><div class=\"g2_title\">". $title ."</div><br />"
	         	. "<div class=\"tchoutchou\"><a href=\"index.php?file=Gallery_v2\" title=\"". $title ."\">". $title ."</a>";
	         	echo selcat($cat);
	         	echo "&nbsp;&gt;&nbsp;". $titre ."</div><br /><br />"
	        	. "<div style=\"text-align: center;\">". $image ."</div><br />\n"
	        	. "<table style=\"margin: auto;text-align: left;width:90%;\" cellpadding=\"3\" cellspacing=\"0\">\n"
	        	. "<tr><td>" . $link_prev_next . "</td></tr></table>"
	        	. "<table class=\"table_90_border\" cellspacing=\"0\" cellpadding=\"0\">"
	        	. "<tr style=\"background: " . $bgcolor2 . ";\"><td class=\"td_1_t_c\">" . $titre . "</td></tr>\n";

	        	if ($description != "") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_t_t_l\">". icon($description) ."</td></tr>";

	        	if ($autor != "") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_t_t_l\"><b>" . _AUTHOR . " :</b>  " . $autor . "</td></tr>";

	        	echo "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_t_t_l\"><b>" . _ADDTHE . " :</b>  " . $date . "</td></tr>"
	        	. "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_t_t_l\"><b>" . _SEEN . " :</b> " . $count . "&nbsp;" . _TIMES . "</td></tr>";

	                if ($size != "") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_t_t_l\"><b>" . _SIZE . " :</b>  " . $size . "</td></tr>";

	                if ($visiteur >= $gallery_pref['dl_lvl'] && $gallery_pref['dl_ok'] == 'on' && $type != "youtube" && $type != "dailymotion") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_t_t_l\"><a href=\"javascript:void(0);\" onclick=\"window.open('index.php?file=Gallery_v2&nuked_nude=index&op=do_dl&id=". $sid ."')\" title=\"". _DOWNFILE ."\"><b>". _DOWNFILE ."</b></a>&nbsp;&nbsp;&nbsp;(". _DOWNFILET ." : ". $count_dl ." ". _TIMES .")</td></tr>";

	                if ($mot_cle != "") {
		                $mot_cle_breadcrumbs = '';
		                $displayfolders = explode(',', $mot_cle);
		                for ($i=0; $i <= sizeof($displayfolders); $i++) {
			                if (isset($displayfolders[$i]) && $displayfolders[$i] != null) {
			                	//$mot_cle_breadcrumbs .= '<a href="tag.php?'. $displayfolders[$i] .'">'. $displayfolders[$i] .'</a>&nbsp;';  // Module tag :p
			                	$mot_cle_breadcrumbs .= '<div class="cloud"><div class="cloud_left">&nbsp;</div><div class="cloud_middle"><a href="index.php?file=Search&amp;op=mod_search&main='. $displayfolders[$i] .'">'. $displayfolders[$i] .'</a></div></div>&nbsp;';
			                }
	                	}
	                	echo "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_t_t_l\">". $mot_cle_breadcrumbs ."</td></tr>";
	                }

	        	if($visiteur >= nivo_mod('Vote') && nivo_mod('Vote') > -1){
	            		echo "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_t_t_l\">";
	           	 	vote_index("Gallery_v2", $sid);
	            		echo "</td></tr>";
	        	}

	        	echo "</table><br /><br />";
	        	$sql = mysql_query("SELECT active FROM " . $nuked['prefix'] . "_comment_mod WHERE module = 'gallery_v2'");
	        	list($active) = mysql_fetch_array($sql);

	        	if($active == 1 && $visiteur >= nivo_mod('Comment') && nivo_mod('Comment') > -1) {
	            		echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\"><tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px solid " . $bgcolor3 . ";\">";

	            		include("modules/Comment/index.php");
	            		com_index("Gallery_v2", $sid);

	            		echo "</td></tr></table><br /><br />";
	        	}

	  	} else echo "<br /><div class=\"g2_title\">". $title ."</div><br /><div class=\"g2_error\">". _CAUTION_MEMBRE ."</div><br /><br />";

	  	closetable();
    	}

        function do_dl($id) {

                global $gallery_pref, $visiteur, $nuked;

                if ($visiteur >= $gallery_pref['dl_lvl']) {
                        $sql = mysql_query("SELECT url, url_file, type, count_dl, cat FROM ". GALLERY_V2_TABLE ." WHERE sid = '". $id ."'");
                        list($url, $url_file, $type, $count_dl, $cat) = mysql_fetch_array($sql);

                        $new_count = $count_dl + 1;
                        $upd = mysql_query("UPDATE ". GALLERY_V2_TABLE ." SET count_dl = '". $new_count ."' WHERE sid = '". $id ."'");

                        if ($type != 'youtube' && $type != 'dailymotion' && $type != 'vimeo') {
                                if (file_exists($gallery_pref['rep_img'] . $url)) {
                                        $fichier_a_dl = $gallery_pref['rep_img'] . $url;
                                        header("Content-type: application/force-download");
                                        header("Content-Length: ". filesize($fichier_a_dl));
                                        header("Content-Disposition: attachment; filename=". basename($fichier_a_dl));
                                        readfile($fichier_a_dl);
                                } else {
                                        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
          				. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'
          				. '<head><title>'. _GALLERY .'</title>'
          				. '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'
          				. '<meta http-equiv="content-style-type" content="text/css" />'
          				. '<script type="text/javascript" src="modules/Gallery_v2/gallery_v2.js"></script>'
          				. '<link title="style" type="text/css" rel="stylesheet" href="themes/'. $theme .'/style.css" /></head><body>'
                                        . '<br /><br /><div class="g2_error">'. _NO_FOUND_DL .'</div><br /><br />'
                                        . '</body></html>';
                                }
                        }
                } else {
                        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
          		. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'
          		. '<head><title>'. _GALLERY .'</title>'
          		. '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'
          		. '<meta http-equiv="content-style-type" content="text/css" />'
          		. '<script type="text/javascript" src="modules/Gallery_v2/gallery_v2.js"></script>'
          		. '<link title="style" type="text/css" rel="stylesheet" href="themes/'. $theme .'/style.css" /></head><body>'
                        . "<br /><br /><div class=\"g2_warn\">". _USERENTRANCE ."</div><br /><br />"
                        . '</body></html>';
                }
        }

    	function make_thumb($image, $t, $a_c) {

                global $nuked, $gallery_pref;

                if($image == "" || (!file_exists($gallery_pref['rep_img'] . $image))) {
        		$img = 'modules/Gallery_v2/images/error.png';
        		$img_tmp = 'error';
		} else {
        		$img = $gallery_pref['rep_img'] . $image;
        		$ext = pathinfo($image, PATHINFO_EXTENSION);
        		$img_tmp = str_replace('.'. $ext, '', $image);
		}

		list($width, $height, $type) = getimagesize($img);
                switch ($t) {
                	case 'pc': // les petites minatures avec cadre
                	$new_width = 235;
			$new_height = 175;
			$sufix = 'mini_cadre_';
			break;
			case 'p': // les petites minatures sans cadre
                	$new_width = 235;
			$new_height = 175;
			$sufix = 'mini_';
			break;
	                case 'g': // les grosses minatures sans cadre                	$new_width = 549;
			$new_height = 314;
			$sufix = 'big_';
			break;
			case 'gc': // les grosses minatures avec cadre
                	$new_width = 549;
			$new_height = 314;
			$sufix = 'big_cadre_';
			break;
			case 'b': // les minatures du block
                	$new_width = 200;
			$new_height = 170;
			$sufix = 'block_';
			break;
			default: // par default on fabrique une mini sans cadre
            		$new_width = 235;
			$new_height = 175;
			$sufix = 'mini_';
            		break;
                }

		if ($type == 1) $type_img = imagecreatefromgif($img);
		elseif($type == 2) $type_img = imagecreatefromjpeg($img);
		elseif($type == 3) $type_img = imagecreatefrompng($img);
		else die('Image non reconnue !');

		$image_p = imagecreatetruecolor($new_width, $new_height);
		$image = $type_img;
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		imagepng($image_p, $gallery_pref['rep_img'] .'temp/'. $sufix . $img_tmp .'.png');
		if($a_c == '0') imagepng($image_p); // si on a une miniature sans cadre on l'affiche
		imagedestroy($image_p);

		if($a_c == '1') {  // on ajoute le cadre
			if($t == 'pc') $background = imagecreatefrompng('modules/Gallery_v2/images/cadre_gal_mini.png');
			else $background = imagecreatefrompng('modules/Gallery_v2/images/cadre_600.png');
			$insert = imagecreatefrompng($gallery_pref['rep_img'] .'temp/'. $sufix . $img_tmp .'.png');

			imagecolortransparent($insert,imagecolorexact($insert,255,0,255));
			imagealphablending($background,true);
			imagesavealpha($background ,true);
			$insert_x = imagesx($insert);
			$insert_y = imagesy($insert);
			if($t == 'pc') imagecopymerge($background,$insert,7,10,0,0,$insert_x,$insert_y,100);
	                else imagecopymerge($background,$insert,23,21,0,0,$insert_x,$insert_y,100);

			header("Content-type: image/png");
			imagepng($background);
			imagepng($background,$gallery_pref['rep_img'] .'temp/'. $sufix . $img_tmp .'.png');
			imagedestroy($background);
		}
    	}

        function make_zip($cid) {

        	global $nuked, $language, $gallery_pref;

        	if (!extension_loaded('zip')) die('Votre serveur ne supporte pas la fonction zip !');

                $sql_c = mysql_query("SELECT url FROM ". GALLERY_V2_TABLE ." WHERE cat = '". $cid ."' AND type = ''");
        	$nb_media = mysql_num_rows($sql_c);

        	if($nb_media == 0) die('Catégorie vide !');

                $n_zip = time() .'_catégorie_'. $cid .'.zip';
        	$zip = new ZipArchive();
        	if($zip->open($n_zip, ZipArchive::CREATE) === TRUE) {
        		while ($r_sql = mysql_fetch_array($sql_c, MYSQL_ASSOC)) {
        			$zip->addFile($gallery_pref['rep_img'] . $r_sql['url'], $r_sql['url']);
        		}
        		$zip->close();
        	}
        	header('Content-Type: application/force-download');
         	header("Content-Length: ". filesize($n_zip));
         	header("Content-Disposition: attachment; filename=". basename($n_zip));
         	readfile($n_zip);
         	unlink($n_zip);
        }

        function suggest() {

                global $nuked, $user, $gallery_pref, $bgcolor4, $bgcolor3, $bgcolor2, $bgcolor1, $visiteur;

                if ($gallery_pref['title'] != "") $title = $gallery_pref['title'];
        	else $title = _GALLERY;

                echo '<script type="text/javascript" src="modules/Gallery_v2/gallery_v2.js"></script><br /><div class="g2_title">'. $title .'</div><br />';

                if ($gallery_pref['suggest'] == '0' || $visiteur < $gallery_pref['lvl_suggest']) {
                        echo "<br /><br /><div class=\"g2_warn\">". _NOSUGGEST ."</div><br /><br />";
                        redirect("index.php?file=Gallery_v2", 2);
                        closetable();
                        footer();
                        exit();
                }

                define('EDITOR_CHECK', 1);

                echo "<script type=\"text/javascript\">\n"
                . "//<![CDATA[\n"
                . "function showtype(type){if (type == 'image'){document.getElementById('aff_type').innerHTML='.png .jpg .gif';document.getElementById('ydv').style.display='none';document.getElementById('um').style.display='inline';}else{document.getElementById('aff_type').innerHTML='';document.getElementById('ydv').style.display='inline';document.getElementById('um').style.display='none';}}\n"
                . 'function verifchamps(){
            		if (document.getElementById(\'titre\').value.length == 0){
                		alert(\'Titre vide !\');
                		return false;
            		}
            		return true;
        	}'
                . "//]]>\n"
                . "</script>"
        	. '<div class="centeredmenu"><div class="nav l_g"><ul>'
            	. "<li><a href=\"index.php?file=Gallery_v2\"><span>". _INDEXGALLERY ."</span></a></li>"
            	. "<li><a href=\"index.php?file=Gallery_v2&amp;op=classe&amp;orderby=news\"><span>". _NEWSIMG ."</span></a></li>"
            	. "<li><a href=\"index.php?file=Gallery_v2&amp;op=classe&amp;orderby=count\"><span>". _TOPIMG ."</span></a></li>"
            	. "<li><a href=\"index.php?file=Gallery_v2&amp;op=suggest\"><span>". _SUGGESTIMG . "</span></a></li></ul></div></div><div class=\"clear\"></div><p>&nbsp;</p>"

                . "<form method=\"post\" action=\"index.php?file=Gallery_v2&amp;op=add_sug\" enctype=\"multipart/form-data\" onsubmit=\"return verifchamps()\">"
                . "<table class=\"table_90_border\" cellspacing=\"0\" cellpadding=\"0\">"
                . "<tr style=\"background: " . $bgcolor2 . ";\"><td colspan=\"2\" class=\"td_1_t_c\">". _FILESUGGEST ." ". $gallery_pref['max_size'] ." Mo Max</td></tr>"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_rt_t_r\">". _TITLE ." :</td><td class=\"td_2_b_t_t_l\"><input id=\"titre\" type=\"text\" name=\"titre\" size=\"46\" value=\"\" /></td></tr>"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_rt_t_r\">". _CAT ." :</td><td class=\"td_2_b_t_t_l\"><select name=\"cat\"><option value=\"0\">* " . _NONE . "</option>";
                function sel_cat($idCat=0,$mere='') {
                        $ligne = '';
                        $req = mysql_query("SELECT cid,parentid,titre FROM " . GALLERY_V2_CAT_TABLE . " WHERE parentid='$idCat'");
                        while ($row = mysql_fetch_array($req)) {
                                $ligne .= '<option value="'. $row['cid'] .'">'. stripslashes($mere.$row['titre']) .'</option>'."";
                                $ligne .= sel_cat($row['cid'],$mere.$row['titre'].' > ');
                        }
                        return $ligne;
                }
                echo sel_cat();
                echo "</select></td></tr>"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_rt_t_r\">" . _TYPE . ":</td><td class=\"td_2_b_t_t_l\"><select class=\"styled\" name=\"type\" onclick=\"showtype(this.options[this.selectedIndex].value);\">"
                . "<option value=\"image\">". _IMAGE ."</option><option value=\"youtube\">Youtube</option>"
                . "<option value=\"dailymotion\">Dailymotion</option><option value=\"vimeo\">Vimeo</option></select>&nbsp;<div style=\"display:inline;\" id=\"aff_type\">.png .jpg .gif</div></td></tr>"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_rt_t_r\">". _MOTCLE ." :</td><td class=\"td_2_b_t_t_l\"><input type=\"text\" name=\"mot_cle\" size=\"46\" value=\"\" />&nbsp;". _MOTCLESEP ."</td></tr>"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_rt_t_r\" valign=\"top\">" . _DESCR . " :</td><td class=\"td_2_b_t_t_l\"><textarea id=\"e_basic\" name=\"description\" cols=\"65\" rows=\"12\"></textarea></td></tr>"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_rt_t_r\">". _UPMEDIA ." :</td><td class=\"td_2_b_t_t_l\"><div id=\"um\" style=\"display:inline;\"><input type=\"file\" name=\"fichiernom\" size=\"30\" /> Max ". $gallery_pref['max_size'] ." Mo</div></td></tr>"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td class=\"td_2_b_rt_t_r\">". _URLVIDEO ." :</td><td class=\"td_2_b_t_t_l\"><div id=\"ydv\" style=\"display:none;\"><input size=\"50\"id=\"url_video\" type=\"text\" name=\"url_video\" /></div></td></tr>"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td colspan=\"2\" class=\"td_2_b_t_t_c\"><input type=\"submit\" value=\"". _SEND ."\" /><input name=\"auteur\" type=\"hidden\" value=\"". $user[2] ."\"></td></tr>"
                . "</table></form><br /><br />";

        }

        function add_sug($titre, $description, $auteur, $cat, $type, $url_video, $mot_cle) {

                global $user, $module, $nuked, $user_ip, $gallery_pref;

                opentable();

                echo '<script type="text/javascript" src="modules/Gallery_v2/gallery_v2.js"></script>';

                if (!$user) {
                        echo "<br /><br /><div class=\"g2_warn\">". _USERENTRANCE ."</div><br /><br />";
                        redirect("index.php?file=Gallery_v2", 2);
                        closetable();
                        footer();
                        exit();
                }

                if ($gallery_pref['suggest'] == '0') {
                        echo "<br /><br /><div class=\"g2_warn\">". _NOSUGGEST ."</div><br /><br />";
                        redirect("index.php?file=Gallery_v2", 2);
                        closetable();
                        footer();
                        exit();
                }

                if (isset($_FILES['fichiernom']['name']) && $_FILES['fichiernom']['name'] != "") {
                        $ext = pathinfo($_FILES['fichiernom']['name'], PATHINFO_EXTENSION);
                        if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
                                $file_upload = "image";
                                $f_s = $gallery_pref['max_size']*1024*1024; //ta vue ta vue enfin spa de moi je sais pas compter haha :D
                        }
                        if (($file_upload == "image") && $_FILES['fichiernom']['size'] <= $f_s) {
                                $url_file = $gallery_pref['rep_img'] . time() .".". $ext;
                                $taille = $_FILES['fichiernom']['size'] / 1024;
                                $taille = (round($taille * 100)) / 100;
                                move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_file);
                        } else if ($_FILES['fichiernom']['size'] >= $f_s) {
                                echo "<br /><br /><div class=\"g2_error\">". _TOOBIG ."<br /><a href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />";
                                closetable();
                                footer();
                                exit();
                        } else {
                                echo "<br /><br /><div class=\"g2_error\">". _EXTNOTVALID ."<br /><a href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />";
                                closetable();
                                footer();
                                exit();
                        }
                }

                $url_file = str_replace($gallery_pref['rep_img'], '', $url_file);

                if ($type == "youtube") {
                        $type_aff = "youtube";
                        $url_file_img = get_id_video($url_video);
                        $url_file_swf = 'youtube_'. $url_file_img .'.jpg';
                        get_youtube_thumbs($url_file_img);
                } else if ($type == "dailymotion") {
                        $type_aff = "dailymotion";
                        $url_file_img = get_id_video($url_video);
                        $url_file_swf = 'dailymotion_'. $url_file_img .'.jpg';
                        get_dailymotion_thumbs($url_file_img);
                } else if ($type == "vimeo") {
                        $type_aff = "vimeo";
                        $url_file_swf = '';
                        $url_file_img = get_id_video($url_video);
                } else {
                        $type_aff = "";
                        $url_file_swf = '';
                        $url_file_img = $url_file;
                }

                $titre = mysql_real_escape_string(stripslashes($titre));
                $description = secu_html(html_entity_decode($description));
                $description = mysql_real_escape_string(stripslashes($description));
                $auteur = mysql_real_escape_string(stripslashes($auteur));
                $date = time();
                $mot_cle = mysql_real_escape_string(stripslashes($mot_cle));

                $upd = mysql_query("INSERT INTO ". GALLERY_V2_TABLE ." ( `sid`, `titre`, `description`, `url`, `url_file`, `cat`, `date`, `autor`, `type`, `taille`, `mot_cle` ) VALUES ( '', '". $titre ."', '". $description ."', '". $url_file_img ."', '". $url_file_swf ."', '". $cat ."', '". $date ."', '". $auteur ."', '". $type_aff ."', '". $taille ."', '". $mot_cle ."' )");

                if($nuked['mail'] != '') {
	                $from = "From: ". $nuked['name'] ." <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];
	                $date2 = strftime("%x %H:%M", $date);
	                $subject = _NEWSUGGEST .", ". $date2;
	                $corps = $user[2] ." ". _NEWSUBMIT ."\r\n" . $nuked['url'] . "/index.php?file=Admin\r\n\r\n" . $nuked['name'];
	                mail($nuked['mail'], $subject, $corps, $from);
                }

                echo "<br /><br /><div class=\"g2_succes\">". _THXPART ."&nbsp;&nbsp;". _YOURSUGGEST ."</div><br /><br />";
                redirect("index.php?file=Gallery_v2", 2);
                closetable();
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

    	switch ($_REQUEST['op']) {
        	case "view_screen":
            	view_screen($_REQUEST['sid']);
            	break;

        	case "description":
            	description($_REQUEST['sid']);
            	break;

        	case "categorie":
            	categorie($_REQUEST['cat']);
            	break;

        	case "classe":
            	opentable();
            	classe($_REQUEST['cat']);
            	closetable();
            	break;

                case "do_dl":
                do_dl($_REQUEST['id']);
                break;

        	case "make_thumb":
            	make_thumb($_REQUEST['image'], $_REQUEST['t'], $_REQUEST['a_c']);
            	break;

        	case "make_zip":
            	make_zip($_REQUEST['cid']);
            	break;

                case "suggest":
                opentable();
                suggest();
                closetable();
                break;

                case "add_sug":
                add_sug($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['auteur'], $_REQUEST['cat'], $_REQUEST['type'], $_REQUEST['url_video'], $_REQUEST['mot_cle']);
                break;

        	default:
            	index();
            	break;
    	}

} else if ($level_access == -1) {
    	opentable();
    	echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    	closetable();
} else if ($level_access == 1 && $visiteur == 0) {
    	opentable();
    	echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
    	closetable();
} else {
    	opentable();
    	echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    	closetable();
}

?>