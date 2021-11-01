<?php
/**
 *
 * Coolie アンケートシステム - 管理画面スクリプト
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: admin.php,v 1.4 2004/08/12 09:34:22 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

// 設定初期化
require_once 'config/rc.php';
define("COOLIE_ADMIN", 1);

require_once "Coolie/controller.php";
//require_once "Coolie/result.php";
require_once "Coolie/request.php";
require_once "template/html.php";

$request    = &new Coolie_request();
//$result     = &new Coolie_result();
$view       = &new template_html();
$controller = &new Coolie_controller(&$request, &$view);

?>