<?php
/**
 *
 * Coolie アンケートシステム - 管理者設定画面 入力チェック配列
 * 
 * @access		public
 * @create		2004/08/14
 * @version		$Id: admin.php,v 1.1 2004/08/14 06:51:29 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Tsukasa Koizumi <tsukasa@virtuawave.jp>
 * @copyright	VirtuaWave Inc.
 *
 **/

return array(
	"COOLIE_ADMIN_USER" => array("empty" => NULL, "regex" => "/^[-_0-9a-zA-Z]+$/"),
	"COOLIE_ADMIN_PW"   => array("empty" => NULL, "regex" => "/^[-_0-9a-zA-Z]+$/"),
	"COOLIE_ADMIN_MAIL" => array("empty" => NULL, "email" => NULL),
);

?>