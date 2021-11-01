<?php
/**
 *
 * Coolie ���󥱡��ȥ����ƥ� - ������֥ޥ���
 * 
 * @access		public
 * @create		2004/06/11
 * @version		$Id: queryType.php,v 1.3 2004/06/20 21:12:44 shogo Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 **/

// {{{ Constants

define("COOLIE_QUERYTYPE_RADIO_VERTICAL",       1);
define("COOLIE_QUERYTYPE_RADIO_HORIZONTAL",     2);
define("COOLIE_QUERYTYPE_CHECKBOX_VERTICAL",    3);
define("COOLIE_QUERYTYPE_CHECKBOX_HORIZONTAL",  4);
define("COOLIE_QUERYTYPE_SELECT",               5);
define("COOLIE_QUERYTYPE_SELECT_MULTI",         6);
define("COOLIE_QUERYTYPE_TEXT",                 7);
define("COOLIE_QUERYTYPE_TEXTAREA",             8);
define("COOLIE_QUERYTYPE_AREA",                 9);
define("COOLIE_QUERYTYPE_DATE_Y",              10);
define("COOLIE_QUERYTYPE_DATE_M",              11);
define("COOLIE_QUERYTYPE_DATE_YM",             12);
define("COOLIE_QUERYTYPE_DATE_YMD",            13);
define("COOLIE_QUERYTYPE_DATE_MD",             14);
define("COOLIE_QUERYTYPE_ZIPCODE",             15);
define("COOLIE_QUERYTYPE_TEL",                 16);
define("COOLIE_QUERYTYPE_WEEKDAY",             17);
define("COOLIE_QUERYTYPE_SEX",                 18);
define("COOLIE_QUERYTYPE_CONSTELLATION",       19);
define("COOLIE_QUERYTYPE_BLOODTYPE",           20);
define("COOLIE_QUERYTYPE_EMAIL",               21);

// }}}


// {{{ Return master data

return array(
	COOLIE_QUERYTYPE_RADIO_VERTICAL      => "�饸���ܥ���(��)",
	COOLIE_QUERYTYPE_RADIO_HORIZONTAL    => "�饸���ܥ���(��)",
	COOLIE_QUERYTYPE_CHECKBOX_VERTICAL   => "�����å��ܥå���(��)",
	COOLIE_QUERYTYPE_CHECKBOX_HORIZONTAL => "�����å��ܥå���(��)",
	COOLIE_QUERYTYPE_SELECT              => "�ꥹ�ȥܥå���(ñ������)",
	COOLIE_QUERYTYPE_SELECT_MULTI        => "�ꥹ�ȥܥå���(ʣ������)",
	COOLIE_QUERYTYPE_TEXT                => "�ƥ�����(ñ����)",
	COOLIE_QUERYTYPE_TEXTAREA            => "�ƥ�����(ʣ����)",
	COOLIE_QUERYTYPE_AREA                => "��ƻ�ܸ�",
	COOLIE_QUERYTYPE_DATE_Y              => "ǯ",
	COOLIE_QUERYTYPE_DATE_M              => "��",
	COOLIE_QUERYTYPE_DATE_YM             => "ǯ��",
	COOLIE_QUERYTYPE_DATE_YMD            => "ǯ����",
	COOLIE_QUERYTYPE_DATE_MD             => "����",
	COOLIE_QUERYTYPE_ZIPCODE             => "͹���ֹ�",
	COOLIE_QUERYTYPE_TEL                 => "�����ֹ�",
	COOLIE_QUERYTYPE_WEEKDAY             => "����",
	COOLIE_QUERYTYPE_SEX                 => "����",
	COOLIE_QUERYTYPE_CONSTELLATION       => "����",
	COOLIE_QUERYTYPE_BLOODTYPE           => "��շ�",
	COOLIE_QUERYTYPE_EMAIL               => "�᡼�륢�ɥ쥹"
	);

//}}}

?>
