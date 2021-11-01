<?php
/**
 *
 * Coolie アンケートシステム - 質問形態マスタ
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
	COOLIE_QUERY_RESTRICTION_NONE     => "制限なし",
	COOLIE_QUERY_RESTRICTION_ALPHABET => "半角英数字",
	COOLIE_QUERY_RESTRICTION_HIRAGANA => "ひらがな",
	COOLIE_QUERY_RESTRICTION_KATAKANA => "カタカナ",
	COOLIE_QUERY_RESTRICTION_REGEX    => "正規表現",
	COOLIE_QUERY_RESTRICTION_EMAIL   => "すべてのメールアドレス",
	COOLIE_QUERY_RESTRICTION_EMAIL_MOBILE   => "携帯電話のみ",
	COOLIE_QUERY_RESTRICTION_EMAIL_NOT_MOBILE   => "携帯電話以外"

	);

//}}}

?>
