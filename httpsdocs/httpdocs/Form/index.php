<?php
/**
 *
 * Coolie ���󥱡��ȥ����ƥ� - �����Բ��̥�����ץ�
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: index.php,v 1.4 2004/08/18 08:55:46 shogo Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

// ��������
require 'config/rc.php';
define("COOLIE_ADMIN", 0);

require_once "Coolie/controller.php";
require_once "Coolie/request.php";
require_once "template/html.php";

$request    = &new Coolie_request();
$view       = &new template_html();
$controller = &new Coolie_controller(&$request, &$view);


?>