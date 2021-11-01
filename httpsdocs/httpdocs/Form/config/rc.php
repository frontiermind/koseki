<?php
/**
 *
 * Loach アンケートシステム - 初期化スクリプト
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: rc.php,v 1.13 2004/08/24 05:43:25 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

// {{{ *** 定数定義 ***

define("LOACH_ENQUETE_INPUT_ENCODING",    "SJIS");
define("LOACH_ENQUETE_INNER_ENCODING",  "EUC-JP");
define("LOACH_ENQUETE_OUTPUT_ENCODING",   "SJIS");

// }}}


// {{{ *** PHP設定変更 ***

$include_path = array("./bin", ".");

if (preg_match('/^WIN/i', PHP_OS)) {
	// Windows
	$include_path = ini_set('include_path', implode(";", $include_path));
} else {
	// Other
	$include_path = ini_set('include_path', implode(":", $include_path));
}

// }}}

// {{{ SQLiteモジュール読み込み
if (!function_exists("sqlite_open")) {
	dl("sqlite.so");
}
// }}}

session_cache_limiter("none");
require_once "SPEAR.php";
mb_detect_order(SPEAR_MB_AUTO);

// {{{ 管理者設定読み込み

require "config/admin.php";

// }}}

?>