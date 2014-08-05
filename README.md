galeriev2
=========

Galerie multimédia pour Nuked-Klan

pour installer ce module :
-copier le contenu de ce dossier a la base de votre ftp
-Lancer install.php en entrant l'adresse suivante dans votre navigateur : http://votre-site/install.php
-suivez les indications

Si jamais vous avez un soucis : http://www.chezyann.net

Démo disponible sur http://nk.chezyann.net

-> le fichier index.php du module Comment est inclus car il faut rajouter ce bout de code pour autoriser les commentaires :
     
elseif($module == "Gallery_v2" || $module == "gallery_v2"):  // Modification Galerie V2
$WhereModule = 'gallery_v2';
$sqlverif = "gallery_v2";
$specification = "sid";


credits :

* colorpick : http://www.eyecon.ro/colorpicker/

* inconnue_team pour la fonction recherche, les bugs corigés et les suggestions ^^
http://kotshiro.free.fr

* Starkeus pour la modif catégorie privé

-> ffmpeg :
Si vous n'avez pas ffmpeg installer sous windows : http://ffmpeg.zeranoe.com/builds/
Ouvrez le fichier config.php puis dans la fonction create_from_ffmpeg modifier la variable $patch_ffmpeg
Cette fonction execute ffmpeg avec shell donc vous devez avoir cette function activer sur votre hébergeur.

-> htaccess :
Vous pouvez mettre le fichier .htaccess dans votre dossier upload/Gallery_v2 pour plus de sécurité

-> Si les vidéos ne s'affiche pas dans le lecteur rajouter cette ligne dans le .htaccess à la racinde de votre site : 
SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png|zip|gz|swf|flv|mp3|avi|mp4|mov|f4v)$ no-gzip dont-vary

Update :
05/08/2014 : modification de la génération des miniatures pour prendre en compte les images de type portrait, paysage ou carré
