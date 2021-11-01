<?php
/**
 *
 * Coolie アンケートシステム - VIEW初期化スクリプト
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: viewrc.php,v 1.2 2004/06/11 07:52:31 shogo Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

$view->template_dir    = "config/templates";
$view->compile_dir     = "config/templates_c";
$view->inner_encoding  = "EUC-JP";
$view->output_encoding = "SJIS";

$view->assign(get_defined_constants());

?>
