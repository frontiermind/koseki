<?php
/**
 *
 * Loach ���󥱡��ȥ����ƥ� - �����������ץ�
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: rc.php,v 1.13 2004/08/24 05:43:25 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

// {{{ *** ������ ***

define("LOACH_ENQUETE_INPUT_ENCODING",    "SJIS");
define("LOACH_ENQUETE_INNER_ENCODING",  "EUC-JP");
define("LOACH_ENQUETE_OUTPUT_ENCODING",   "SJIS");

// }}}


// {{{ *** PHP�����ѹ� ***

$include_path = array("./bin", ".");

if (preg_match('/^WIN/i', PHP_OS)) {
	// Windows
	$include_path = ini_set('include_path', implode(";", $include_path));
} else {
	// Other
	$include_path = ini_set('include_path', implode(":", $include_path));
}

// }}}

// {{{ SQLite�⥸�塼���ɤ߹���
if (!function_exists("sqlite_open")) {
	dl("sqlite.so");
}
// }}}

session_cache_limiter("none");
require_once "SPEAR.php";
mb_detect_order(SPEAR_MB_AUTO);

// {{{ �����������ɤ߹���

require "config/admin.php";

// }}}

?>