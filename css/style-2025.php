<?php
ob_start("ob_gzhandler");
$expires = 60 * 60 * 24 * 365;
header("Content-type: text/css; charset: UTF-8");
header('Last-Modified: ' . date('r', filemtime($_SERVER['SCRIPT_FILENAME'])));
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
//require('../../entete.php');
require('../../fonctions_generales.php');
require('../../variables_generales.php');

define("NO_ECHO_CSS", true);

$fichiersCss = array();

if (isset($_GET['part'])) {
	if ($_GET['part'] == 'responsive') {
		$fichiersCss[] = "../commun/table-responsive.css";
		$fichiersCss[] = "../commun/mobile.css";
		$fichiersCss[] = 'responsive.css';
		$fichiersCss['colorbox.mobile'] = "../2016/colorbox.mobile.css";
	}
} else {
	$fichiersCss[] = 'color-scheme.css';

	include("../commun/commun.php"); 
	//les relicats crades ont presque tous été redéfinis dans mise-en-page.css
	
	$fichiersCss[] = '../2014/html5.css';
	$fichiersCss[] = '../2016/vote.css';
	$fichiersCss['colorbox'] = '../2016/colorbox.css';
	$fichiersCss[] = '../fontawesome/css/all.min.css';

	$fichiersCss[] = 'patch-font-awesome.css';

	$fichiersCss[] = 'layout.css';
	//TODO : Revoir la version mobile
	$fichiersCss[] = 'menu.css';
	$fichiersCss[] = 'breadcrumb.css';
	//TODO : Revoir le positionnement mobile
	$fichiersCss[] = 'menu-user.css';

	$fichiersCss[] = 'base.css';
	$fichiersCss[] = 'mise-en-page.css';
	$fichiersCss[] = 'forms-buttons.css';
	$fichiersCss[] = 'content.css';
	$fichiersCss[] = 'fluent-components.css';

	$fichiersCss[] = 'cookies.css';
	$fichiersCss[] = 'articles-news.css';
	$fichiersCss[] = 'index.css';
	$fichiersCss[] = 'pronofoot.css';
	$fichiersCss[] = 'bench.css';
	$fichiersCss[] = 'onglets-lan.css';
	$fichiersCss[] = 'patch.css';
	$fichiersCss['jquery-ui'] = '../2016/jquery-ui.min.css';
	$fichiersCss[] = 'patch-jquery.ui.css';

	$fichiersCss[] = 'tables.css';
	$fichiersCss[] = 'recherche.css';

	$fichiersCss['admin'] = null;
}


$font = "
/* === POLICES PERSONNALISÉES === */

/* Poppins */
@font-face {
  font-family: 'Poppins';
  src: url('/include/fonts/poppins/Poppins-Regular.woff2') format('woff2'),
       url('/include/fonts/poppins/Poppins-Regular.woff') format('woff');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'Poppins';
  src: url('/include/fonts/poppins/Poppins-Italic.woff2') format('woff2'),
       url('/include/fonts/poppins/Poppins-Italic.woff') format('woff');
  font-weight: 400;
  font-style: italic;
  font-display: swap;
}
@font-face {
  font-family: 'Poppins';
  src: url('/include/fonts/poppins/Poppins-SemiBold.woff2') format('woff2'),
       url('/include/fonts/poppins/Poppins-SemiBold.woff') format('woff');
  font-weight: 600;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'Poppins';
  src: url('/include/fonts/poppins/Poppins-Bold.woff2') format('woff2'),
       url('/include/fonts/poppins/Poppins-Bold.woff') format('woff');
  font-weight: 700;
  font-style: normal;
  font-display: swap;
}

/* Inter */
@font-face {
  font-family: 'Inter';
  src: url('/include/fonts/inter/Inter-Regular.woff2') format('woff2'),
       url('/include/fonts/inter/Inter-Regular.woff') format('woff');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'Inter';
  src: url('/include/fonts/inter/Inter-Italic.woff2') format('woff2'),
       url('/include/fonts/inter/Inter-Italic.woff') format('woff');
  font-weight: 400;
  font-style: italic;
  font-display: swap;
}
@font-face {
  font-family: 'Inter';
  src: url('/include/fonts/inter/Inter-SemiBold.woff2') format('woff2'),
       url('/include/fonts/inter/Inter-SemiBold.woff') format('woff');
  font-weight: 600;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'Inter';
  src: url('/include/fonts/inter/Inter-Bold.woff2') format('woff2'),
       url('/include/fonts/inter/Inter-Bold.woff') format('woff');
  font-weight: 700;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'Inter';
  src: url('/include/fonts/inter/Inter-BoldItalic.woff2') format('woff2'),
       url('/include/fonts/inter/Inter-BoldItalic.woff') format('woff');
  font-weight: 700;
  font-style: italic;
  font-display: swap;
}

/* Fira Mono (pour le code) */
@font-face {
  font-family: 'Fira Mono';
  src: url('/include/fonts/fira-mono/FiraMono-Regular.woff2') format('woff2'),
       url('/include/fonts/fira-mono/FiraMono-Regular.woff') format('woff');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
";

$css = compilierFichier($fichiersCss);

//Optimisation des font :
$css = str_replace('font-display: auto;font-family:', 'font-family:', $css);

echo compresserCss($font . $css);
