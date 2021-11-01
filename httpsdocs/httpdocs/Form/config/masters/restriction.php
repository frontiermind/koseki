<?php
/**
 *
 * Coolie ���󥱡��ȥ����ƥ� - ������֥ޥ���
 * 
 * @access		public
 * @create		2004/06/11
 * @version		$Id: restriction.php,v 1.4 2004/08/17 23:17:40 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 **/

// {{{ Constants

define("COOLIE_QUERY_RESTRICTION_NONE",     0);
define("COOLIE_QUERY_RESTRICTION_ALPHABET", 1);
define("COOLIE_QUERY_RESTRICTION_HIRAGANA", 2);
define("COOLIE_QUERY_RESTRICTION_KATAKANA", 3);
define("COOLIE_QUERY_RESTRICTION_REGEX",    4);
define("COOLIE_QUERY_RESTRICTION_EMAIL",    5);
define("COOLIE_QUERY_RESTRICTION_EMAIL_MOBILE",   6);
define("COOLIE_QUERY_RESTRICTION_EMAIL_NOT_MOBILE",   7);
// }}}

// {{{ Return master data

return array(
	COOLIE_QUERY_RESTRICTION_NONE     => "���¤ʤ�",
	COOLIE_QUERY_RESTRICTION_ALPHABET => "Ⱦ�ѱѿ���",
	COOLIE_QUERY_RESTRICTION_HIRAGANA => "�Ҥ餬��",
	COOLIE_QUERY_RESTRICTION_KATAKANA => "��������",
	COOLIE_QUERY_RESTRICTION_REGEX    => "����ɽ��",
	COOLIE_QUERY_RESTRICTION_EMAIL   => "���٤ƤΥ᡼�륢�ɥ쥹",
	COOLIE_QUERY_RESTRICTION_EMAIL_MOBILE   => "�������äΤ�",
	COOLIE_QUERY_RESTRICTION_EMAIL_NOT_MOBILE   => "�������ðʳ�"

	);

//}}}

?>
