<?php
/**
 * Mail_Utilities���饹
 *
 * E�᡼��˴ؤ����͡��ʥ桼�ƥ���ƥ����󶡤���
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
	 * $string���������᡼�륢�ɥ쥹�Ǥ��뤫�ɤ����Υ����å�
	 *
	 * @access	public
	 * @param		string		$string			�����å��о�ʸ����
	 * @return	bool			���������TRUE
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
	 * From/To/Cc/Bcc �إå��Ԥ���᡼�륢�ɥ쥹����Ф���
	 *
	 * @access	public
	 * @param		string		$string			����о�ʸ����
	 * @return	array			��Ф��줿�᡼�륢�ɥ쥹
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
		// ���ӤΥᥢ�ɤ�"."�ޤळ�Ȥ����Ĥ���Ƥ���Τǡ������ʤ��б�
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
	 * From�إå��Ԥβ���
	 *
	 * @access	public
	 * @param		string		$from				From�إå�������
	 * @return	array			(0 => name, 1 => mail, 2 => comment)
	 *
	 */
	function parseFromHeader($from)
	{
		if (Mail_Utilities::checkAddress($from)) {
			// From�إå����Τ��᡼�륢�ɥ쥹�ե����ޥåȤ˥ޥå��������
			$email   = trim($from);
			$name    = NULL;
			$comment = NULL;
		} elseif (preg_match_all('/^ *|(\"[^\"]+\" *)|([^\"\<\>\(\)]+ *)|(\([^\)]+\) *)|(\<([^\>]+)\>)* *$/', $from, $m, PREG_SET_ORDER)) {
			// From�إå��˥᡼�륢�ɥ쥹�ʳ����ޤޤ����
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
	 * *������*MIME�إå��ǻ��ѤǤ������ʸ����򥨥󥳡���
	 *
	 * @access	public
	 * @param		string		$header			���󥳡����о�ʸ����
	 * @return	string		���󥳡��ɺѤ�ʸ����
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
