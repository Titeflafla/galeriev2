<?php

//------------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal							//
//  http://www.nuked-klan.org							//
//------------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify	//
//  it under the terms of the GNU General Public License as published by	//
//  the Free Software Foundation; either version 2 of the License.        	//
//------------------------------------------------------------------------------//

define("INDEX_CHECK", 1);

if (is_file('globals.php')) include ("globals.php");
else die('<br /><br /><div style=\"text-align: center;\"><b>install.php must be near globals.php</b></div>');
if (is_file('conf.inc.php')) include ("conf.inc.php");
else die('<br /><br /><div style=\"text-align: center;\"><b>install.php must be near conf.inc.php</b></div>');
if (is_file('nuked.php')) include('nuked.php');
else die('<br /><br /><div style=\"text-align: center;\"><b>install.php must be near nuked.php</b></div>');

function top() {

	global $nuked;

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    	<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>' . $nuked['name'] . ' - Installation</title>
        <link rel="stylesheet" href="modules/Admin/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="modules/Admin/css/style.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="modules/Admin/css/invalid.css" type="text/css" media="screen" />
        <style type="text/css">
        .css3button {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #050505;
	padding: 5px 20px;
	background: -moz-linear-gradient(top,#ffffff 0%,#c7d95f 50%,#add136 50%,#6d8000);
	background: -webkit-gradient(linear, left top, left bottom,from(#ffffff),color-stop(0.50, #c7d95f),color-stop(0.50, #add136),to(#6d8000));
	border-radius: 12px;
	-moz-border-radius: 12px;
	-webkit-border-radius: 12px;
	border: 1px solid #6d8000;
	-moz-box-shadow:0px 1px 3px rgba(000,000,000,0.5),inset 0px 0px 2px rgba(255,255,255,1);
	-webkit-box-shadow:0px 1px 3px rgba(000,000,000,0.5),inset 0px 0px 2px rgba(255,255,255,1);
	text-shadow:0px -1px 0px rgba(000,000,000,0.2),0px 1px 0px rgba(255,255,255,0.4);
	}
	</style>';
}

function index() {

	global $nuked;

	top();

        echo '<body id="login">
        <div id="login-wrapper" class="png_bg">
        <div id="login-top">
        <h1>' . $nuked['name'] . ' - Installation</h1>
        <img id="logo" src="modules/Admin/images/logo.png" alt="NK Logo" />
        </div>';
	//Correction par Sekuline
	$version = $nuked['version'];
	$last = $version[0] . '.' . $version[2] . '.' . $version[4];

    	if ($last == '1.7.9' || $version == 'SP4.6 Beta') {

		echo '<div class="content-box" style="width:700px!important;margin:auto;">',"\n" //<!-- Start Content Box -->
        	. '<div class="content-box-header"><h3>Installation Module Galerie V2</h3></div>',"\n"
        	. '<div class="tab-content" id="tab2"><table style="margin:auto;width:80%;color:black;" cellspacing="0" cellpadding="0" border="0">';

		//Vérification si INSTALLATION ou REINSTALLATION du module afin de ne pas dupliquer le liens dans l'admin
		$test = mysql_query("SELECT id FROM " . $nuked['prefix'] . "_modules WHERE nom='Gallery_v2'");
		$req = mysql_num_rows($test);
		if($req == 1) echo '<tr><td style="text-align:center;"><span style="color:red; font-weight:bold;">Attention L\'installation remettra la configuration par défault du module.</span></td></tr>';

		echo '<tr>
		<td>
		Vous allez installer le module <strong>Galerie V2</strong> <br /><br />
		Créé par <a href="http://www.titeflafla.net" target="_blank">Kipcool</a> Pour <a href="http://www.nuked-klan.eu" target="_blank">Nuked-Klan</a><br /><br />
		Merci à <a href="http://kotshiro.free.fr" target="_blank">inconnue_team</a> pour la fonction recherche, les bugs corigés et les suggestions ^^
		</td>
		</tr>
		<tr>
		<td style="text-align:center;">
		<input type="button" name="yes" onclick="document.location.href=\'install.php?op=update\';" value="Installer" class="css3button"/>&nbsp;&nbsp;
		<input type="button" name="No" onclick="document.location.href=\'install.php?op=nan\';" value="Ne pas installer" class="css3button"/>
		</td>
		</tr>
		</table>
		</div></div>
		</div>
        	</body>
    		</html>';
	}
	else echo 'Bad version, Only for NK 1.7.9';
}

function update() {

	global $nuked;

	//Efface les tables
	$req = mysql_query("DROP TABLE IF EXISTS ". $nuked['prefix'] ."_gallery_v2");
	$req = mysql_query("DROP TABLE IF EXISTS ". $nuked['prefix'] ."_gallery_v2_cat");
	$req = mysql_query("DROP TABLE IF EXISTS ". $nuked['prefix'] ."_gallery_v2_config");
	$req = mysql_query("DELETE FROM ". $nuked['prefix'] ."_modules WHERE nom = 'Gallery_v2'");

	$sql = "CREATE TABLE IF NOT EXISTS `".$nuked['prefix']."_gallery_v2` (
  	`sid` int(11) NOT NULL AUTO_INCREMENT,
  	`titre` text NOT NULL,
  	`description` text NOT NULL,
  	`url` varchar(200) NOT NULL DEFAULT '',
  	`url_file` varchar(200) NOT NULL DEFAULT '',
  	`cat` int(11) NOT NULL DEFAULT '0',
  	`date` varchar(12) NOT NULL DEFAULT '',
  	`count` int(10) NOT NULL DEFAULT '0',
  	`count_dl` int(10) NOT NULL DEFAULT '0',
  	`autor` text NOT NULL,
  	`level` int(1) NOT NULL DEFAULT '0',
  	`type` varchar(50) NOT NULL,
  	`statut` int(1) NOT NULL DEFAULT '0',
  	`taille` varchar(6) NOT NULL,
  	`mot_cle` text NOT NULL,
  	`actif` int(1) NOT NULL DEFAULT '0',
  	PRIMARY KEY (`sid`),
  	KEY `cat` (`cat`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
	$req = mysql_query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `".$nuked['prefix']."_gallery_v2_cat` (
  	`cid` int(11) NOT NULL AUTO_INCREMENT,
  	`parentid` int(11) NOT NULL DEFAULT '0',
  	`titre` varchar(50) NOT NULL DEFAULT '',
  	`image` varchar(50) NOT NULL,
  	`description` text NOT NULL,
  	`position` int(2) unsigned NOT NULL DEFAULT '0',
  	`level` int(1) NOT NULL DEFAULT '0',
  	PRIMARY KEY (`cid`),
  	KEY `parentid` (`parentid`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
	$req = mysql_query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `".$nuked['prefix']."_gallery_v2_config` (
  	`name` varchar(255) NOT NULL,
  	`value` text NOT NULL,
  	PRIMARY KEY (`name`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
	$req = mysql_query($sql);

	$sql_insert = mysql_query("INSERT INTO `".$nuked['prefix']."_gallery_v2_config` (`name`, `value`) VALUES
	('mess_admin_page', '20'),
	('color_player', '5D6953'),
	('max_cat', '9'),
	('rep_img', 'upload/Gallery_v2/'),
	('mess_guest_page', '6'),
	('aff_prev_next', 'off'),
	('title', ''),
	('dl_lvl', '1'),
	('dl_ok', 'on'),
	('suggest', '1'),
	('max_size', '5'),
	('lvl_suggest', '1'),
	('dl_zip', '0'),
	('lvl_dl_zip', '9'),
	('make_thumb', '0'),
	('no_resize', '1'),
	('block_type', '1');");
        $req = mysql_query($sql_insert);

	$sql = mysql_query("INSERT INTO ". $nuked['prefix'] ."_modules (`id`, `nom`, `niveau`, `admin`) VALUES ('', 'Gallery_v2', '0', '9');");
        $sql = mysql_query("INSERT INTO ". $nuked['prefix'] ."_stats (`nom`, `type`, `count`) VALUES ('Gallery_v2', 'pages', '0');");
        $sql = mysql_query("INSERT INTO ". $nuked['prefix'] ."_comment_mod (`id`, `module`, `active`) VALUES ('', 'gallery_v2', '1');");

        top();
        echo '<div class="tab-content" id="tab2" style="width:700px!important;margin:auto;">'
        . "<br /><br /><div class=\"notification success png_bg\"><div>Le module Galerie V2 a été installé correctement.<br />
        N'oublier pas d'ajouter le module dans le menu<br />
        Redirection en cours vers l'administration ...</div></div>";

	//Supression automatique du fichier install.php
	if(@!unlink("install.php")) echo "<br /><br /><div class=\"notification error png_bg\"><div>Penser à supprimer le fichier install.php de votre FTP .</div></div>";

        echo '</div></body></html>';
	redirect("index.php?file=Admin", 2);
}

function nan() {

	top();
        echo '<div class="tab-content" id="tab2" style="width:700px!important;margin:auto;">'
	. "<br /><br /><div class=\"notification error png_bg\"><div>Installation annulé .</div></div>";

	if(@!unlink("install.php")) echo "<br /><br /><div class=\"notification error png_bg\"><div>Penser à supprimer le fichier install.php de votre FTP .</div></div>";

        echo '</div></body></html>';

    	redirect("index.php", 2);
}

switch($_GET['op']) {
	case"index":
	index();
	break;

	case"update":
	update();
	break;

	case"nan":
	nan();
	break;

	default:
	index();
	break;
}

?>