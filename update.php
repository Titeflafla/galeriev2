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
        	. '<div class="content-box-header"><h3>Mise à jour du Module Galerie V2</h3></div>',"\n"
        	. '<div class="tab-content" id="tab2"><table style="margin:auto;width:80%;color:black;" cellspacing="0" cellpadding="0" border="0">';

		// Vérification si le module est installé
		$test = mysql_query("SELECT id FROM ". $nuked['prefix'] ."_modules WHERE nom='Gallery_v2'");
		$req = mysql_num_rows($test);
		if($req == 0) echo '<tr><td style="text-align:center;"><span style="color:red; font-weight:bold;">Attention le module Galery V2 n\'est pas installé vous devez d\'abord l\'installer.</span></td></tr>';

		echo '<tr>
		<td>
		Vous allez mettre à jour le module <strong>Galerie V2</strong> <br /><br />
		Créé par <a href="http://www.chezyann.net" target="_blank">Kipcool</a> Pour <a href="http://www.nuked-klan.eu" target="_blank">Nuked-Klan</a><br /><br />
		Merci à <a href="http://kotshiro.free.fr" target="_blank">inconnue_team</a> pour la fonction recherche, les bugs corigés et les suggestions ^^ et naru01 pour la maj portrait, paysage et miniature et tout les autres ;)
		</td>
		</tr>
		<tr>
		<td style="text-align:center;">
		<input type="button" name="yes" onclick="document.location.href=\'update.php?op=update\';" value="MAJ" class="css3button"/>&nbsp;&nbsp;
		<input type="button" name="No" onclick="document.location.href=\'update.php?op=nan\';" value="Annuler" class="css3button"/>
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

	$sql_insert = mysql_query("INSERT INTO `".$nuked['prefix']."_gallery_v2_config` (`name`, `value`) VALUES
	('no_resize', '1');");
 	$req = mysql_query($sql_insert);

        top();
        echo '<div class="tab-content" id="tab2" style="width:700px!important;margin:auto;">'
        . "<br /><br /><div class=\"notification success png_bg\"><div>Le module Galerie V2 a été mise à jour correctement.<br />
        Redirection en cours vers l'administration ...</div></div>";

	//Supression automatique du fichier install.php
	if(@!unlink("update.php")) echo "<br /><br /><div class=\"notification error png_bg\"><div>Penser à supprimer le fichier update.php de votre FTP .</div></div>";

        echo '</div></body></html>';
	redirect("index.php?file=Admin", 2);
}

function nan() {

	top();
        echo '<div class="tab-content" id="tab2" style="width:700px!important;margin:auto;">'
	. "<br /><br /><div class=\"notification error png_bg\"><div>Installation annulé .</div></div>";

	if(@!unlink("install.php")) echo "<br /><br /><div class=\"notification error png_bg\"><div>Penser à supprimer le fichier update.php de votre FTP .</div></div>";

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