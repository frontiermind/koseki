<?php
/**
 * Mail_Utilitiesクラス
 *
 * Eメールに関する様々なユーティリティを提供する
 *
 * @access		public
 * @create		2003/12/17
 * @version		$Id: Utilities.php,v 1.1 2004/08/20 07:03:55 shogo Exp $
 * @package		SPEAR.Mail
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 */

require_once "SPEAR.php";

// {{{ class Mail_Utilities
class Mail_Utilities
{
	// {{{ checkAddress()
	/**
	 *
	 * $stringが正しいメールアドレスであるかどうかのチェック
	 *
	 * @access	public
	 * @param		string		$string			チェック対象文字列
	 * @return	bool			正しければTRUE
	 *
	 */
	function checkAddress($string)
	{
		return (bool)(preg_match('/^' . Mail_Utilities::_getMailAddressRegexPattern() . '$/', $string));
	}
	// }}}
	
	// {{{ pickMailAddress()
	/**
	 *
	 * From/To/Cc/Bcc ヘッダ行からメールアドレスを抽出する
	 *
	 * @access	public
	 * @param		string		$string			抽出対象文字列
	 * @return	array			抽出されたメールアドレス
	 *
	 */
	function pickMailAddress($string)
	{
		preg_match_all('/' . Mail_Utilities::_getMailAddressRegexPattern() . '/', $string, $m);
		return (array)$m[0];
	}
	// }}}

	// {{{ _getMailAddressRegexPattern
	function _getMailAddressRegexPattern()
	{
		// $a = '[^\x00-\x20"()<>\[\]\\@,.:;\x80-\xFF]';
		// 携帯のメアドは"."含むことが許可されているので、仕方なく対応
		$a = '[^\x00-\x20"()<>\[\]\\@,:;\x80-\xFF]';
		$b = '[^\n\\\x80-\xFF\x0D"]';
		$c = '[^\x80-\xFF]';
		$d = '[^\n\\\x80-\xFF\x0D"\[\]]';
		$x = "{$a}+(?!{$a})";
		$local  = "(?:{$x}|\"{$b}*(?:\\\\{$c}{$b}*)*\")(?:\.(?:{$a}+(?![{$a})|\"{$d}*(?:\\{$c}{$d}*)*\"))*";
		$domain = "(?:{$x}|\[(?:{$d}|\\\\{$c})*\])(?:\.(?:{$a}+(?!{$a})|\[(?:{$d}|\\\\{$c})*\]))*";
		
		return "{$local}@{$domain}";
	}
	// }}}
	
	// {{{ parseFromHeader()
	/**
	 *
	 * Fromヘッダ行の解析
	 *
	 * @access	public
	 * @param		string		$from				Fromヘッダの内容
	 * @return	array			(0 => name, 1 => mail, 2 => comment)
	 *
	 */
	function parseFromHeader($from)
	{
		if (Mail_Utilities::checkAddress($from)) {
			// Fromヘッダ全体がメールアドレスフォーマットにマッチした場合
			$email   = trim($from);
			$name    = NULL;
			$comment = NULL;
		} elseif (preg_match_all('/^ *|(\"[^\"]+\" *)|([^\"\<\>\(\)]+ *)|(\([^\)]+\) *)|(\<([^\>]+)\>)* *$/', $from, $m, PREG_SET_ORDER)) {
			// Fromヘッダにメールアドレス以外が含まれる場合
			$email   = "";
			$name    = "";
			$comment = "";
			for ($i = 1; $i < count($m); $i++) {
				$email   .= $m[$i][5];
				$name    .= $m[$i][1] . $m[$i][2];
				$comment .= $m[$i][3];
			}
		}
		$result = array(
			trim($name),
			trim($email),
			trim($comment)
			);
		return $result;
	}
	// }}}
	
	// {{{ mbEncodeMimeHeader()
	/**
	 *
	 * *本当に*MIMEヘッダで使用できる形に文字列をエンコード
	 *
	 * @access	public
	 * @param		string		$header			エンコード対象文字列
	 * @return	string		エンコード済み文字列
	 *
	 */
	function mbEncodeMimeHeader($header)
	{
		$val = preg_replace('/[\r\n]+$/', "", $header);
		$enc = mb_detect_encoding($val);
		if ($enc != 'ASCII') {
			//$val = mb_convert_encoding(mb_convert_kana($val, "aKV"), 'ISO-2022-JP', $enc);
			$val = mb_convert_encoding(mb_convert_kana($val, "aKV"), 'JIS', $enc);
			$val = preg_replace_callback(
				'/(\x1b\x24.[^\x1b]*\x1b\x28.)/',
				array("Mail_Utilities", "_encodeMimeHeaderOnlyMbString"),
				$val
				);
		}
		return $val;
	}
	// }}}
	
	// {{{ _encodeMimeHeaderOnlyMbString()
	function _encodeMimeHeaderOnlyMbString($m)
	{
		//return mb_encode_mimeheader($m[1], "ISO-2022-JP");
		return mb_encode_mimeheader($m[1], "JIS");
	}
	// }}}
}
// }}}
?>
