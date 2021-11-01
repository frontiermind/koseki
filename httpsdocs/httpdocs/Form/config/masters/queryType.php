<?php
/**
 *
 * Coolie アンケートシステム - 質問形態マスタ
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
	COOLIE_QUERYTYPE_RADIO_VERTICAL      => "ラジオボタン(縦)",
	COOLIE_QUERYTYPE_RADIO_HORIZONTAL    => "ラジオボタン(横)",
	COOLIE_QUERYTYPE_CHECKBOX_VERTICAL   => "チェックボックス(縦)",
	COOLIE_QUERYTYPE_CHECKBOX_HORIZONTAL => "チェックボックス(横)",
	COOLIE_QUERYTYPE_SELECT              => "リストボックス(単数選択)",
	COOLIE_QUERYTYPE_SELECT_MULTI        => "リストボックス(複数選択)",
	COOLIE_QUERYTYPE_TEXT                => "テキスト(単数行)",
	COOLIE_QUERYTYPE_TEXTAREA            => "テキスト(複数行)",
	COOLIE_QUERYTYPE_AREA                => "都道府県",
	COOLIE_QUERYTYPE_DATE_Y              => "年",
	COOLIE_QUERYTYPE_DATE_M              => "月",
	COOLIE_QUERYTYPE_DATE_YM             => "年月",
	COOLIE_QUERYTYPE_DATE_YMD            => "年月日",
	COOLIE_QUERYTYPE_DATE_MD             => "月日",
	COOLIE_QUERYTYPE_ZIPCODE             => "郵便番号",
	COOLIE_QUERYTYPE_TEL                 => "電話番号",
	COOLIE_QUERYTYPE_WEEKDAY             => "曜日",
	COOLIE_QUERYTYPE_SEX                 => "性別",
	COOLIE_QUERYTYPE_CONSTELLATION       => "星座",
	COOLIE_QUERYTYPE_BLOODTYPE           => "血液型",
	COOLIE_QUERYTYPE_EMAIL               => "メールアドレス"
	);

//}}}

?>
