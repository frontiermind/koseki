<?php
ini_set("mbstring.internal_encoding", "EUC-JP");

/**
 *
 * Loach アンケートシステム - MODELクラス
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: model.php,v 1.14 2004/08/24 03:47:46 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 **/

// {{{ Constants

define("COOLIE_FORMTYPE_MAIL",    1);
define("COOLIE_FORMTYPE_ENQUETE", 2);

/** 入力値チェックエラー定数 */
define("COOLIE_CHECK_ERROR_EMPTY",           1);	/** エラー：入力値が空 */
define("COOLIE_CHECK_ERROR_NOT_EMAIL1",      2);	/** エラー：入力値がメールアドレスでない */
define("COOLIE_CHECK_ERROR_NOT_EMAIL2",      4);	/** エラー：入力値が携帯メールアドレスでない */
define("COOLIE_CHECK_ERROR_NOT_EMAIL3",      8);	/** エラー：入力値が非携帯メールアドレスでない */
define("COOLIE_CHECK_ERROR_NOT_ALPHABET",   16);	/** エラー：入力値が英数字でない */
define("COOLIE_CHECK_ERROR_NOT_HIRAGANA",   32);	/** エラー：入力値がひらがなでない */
define("COOLIE_CHECK_ERROR_NOT_KATAKANA",   64);	/** エラー：入力値がカタカナでない */
define("COOLIE_CHECK_ERROR_NOT_URL",       128);	/** エラー：入力値がURLでない */
define("COOLIE_CHECK_ERROR_NOT_REGEX",     256);	/** エラー：入力値が正規表現にマッチしない */
define("COOLIE_CHECK_ERROR_OVER_LIMIT",    512);	/** エラー：入力値が設定数より多い */
define("COOLIE_CHECK_ERROR_UNDER_LIMIT",  1024);	/** エラー：入力値が設定数より少ない */

// }}}

// {{{ class Coolie_model
class Coolie_model
{
	// {{{ Private properties
	
	/** リクエストクラス */
	var $_request = NULL;
	
	/** VIEWクラス */
	var $_view    = NULL;
	
	// }}}
	
	// {{{ Costructor
	/**
	 *
	 * コンストラクタ
	 *
	 * @param			object		$request		リクエストクラス
	 * @param			object		$view				VIEWクラス
	 *
	 */
	function Coolie_model(&$request, &$view)
	{
		$this->_request = &$request;
		$this->_view    = &$view;
	}
	// }}}
	
	// {{{ createQuestionnaireSheet()
	/**
	 *
	 * アンケートフォーム表示用データ生成
	 *
	 * @access		public
	 * @return		int			成功: COOLIE_OK / エラー(例外): 1xAｷ豪	 *
	 */
	function createQuestionnaireSheet()
	{
		// 入力値取得
		$id = $this->_request->get("id");
		$this->_view->assign("id", $id);
		
		// 使用するオブジェクト
		require_once "DB/DB.php";
		require_once "File/IO.php";
		
		// DB接続
		$db  = & DB::connect("sqlite://config/config.db");
		$slt = $db->select("SELECT * FROM queries WHERE id = '{$id}' ORDER BY no ASC;");
		$this->_view->assign("queries", $slt);
		$slt = $db->select("SELECT * FROM forms WHERE id = '{$id}';");
		$this->_view->assign("form", $slt[0]);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ setRequestedValues()
	/**
	 *
	 * ユーザーの入力値を結果クラスへ与える
	 *
	 * @access		public
	 * @return		int			成功: COOLIE_OK / エラー(例外): 1xAｷ豪	 *
	 */
	function setRequestedValues()
	{
		$this->_view->assign("default", $this->_request->get("answers"));
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ checkRequestedValues()
	/**
	 *
	 * ユーザーの入力値を設定に基づいてチェックする
	 *
	 * @access		public
	 * @return		mixed		成功: COOLIE_OK 
	 *                    エラー: 各入力に対するエラーの内容 array( 入力キー => エラー定数の論理和, ... )
	 *
	 */
	function checkRequestedValues()
	{
		// 入力値取得
		$id  = $this->_request->get("id");
		$ans = $this->_request->get("answers");
		
		// 使用するオブジェクト
		require_once "DB/DB.php";
		require_once "File/IO.php";
		require      "config/masters/restriction.php";
		
		// DB接続
		$db  = &DB::connect("sqlite://config/config.db");
		$slt = $db->select("SELECT * FROM queries WHERE id = '{$id}' ORDER BY no ASC;");
		$err = array();

		$regex1 = $this->_getMailAddressRegexPattern();
		$regex2 = $this->_getHttpUrlRegexPattern();
		
		foreach ($slt as $row) {
			extract($row);
			$err[$no] = 0;
			$iCheck = true;
			if ($required && (!strlen($ans[$no]) or (is_array($ans[$no]) and preg_match('/##::####::##/', "##::##" . implode("##::##", $ans[$no]) ."##::##")))) {
				$err[$no] |= COOLIE_CHECK_ERROR_EMPTY;
			} elseif (!$required && (!strlen($ans[$no]) or (is_array($ans[$no]) and preg_match('/##::####::##/', "##::##" . implode("##::##", $ans[$no]) ."##::##")))) {
				$iCheck = false;
			}
			if ($input_type == COOLIE_QUERY_RESTRICTION_EMAIL && $iCheck) {
				if (!preg_match("/{$regex1}/", $ans[$no])) {
					$err[$no] |= COOLIE_CHECK_ERROR_NOT_EMAIL1;
				}
			} elseif ($input_type == COOLIE_QUERY_RESTRICTION_EMAIL_MOBILE && $iCheck) {
				if (!preg_match("/{$regex1}/", $ans[$no])) {
					$err[$no] |= COOLIE_CHECK_ERROR_NOT_EMAIL1;
				} elseif (!preg_match('/@(docomo\.ne\.jp|ezweb\.ne\.jp|[a-z]\.vodafone\.ne\.jp|([a-z]{2}\.)?(sky.tu-ka.ne.jp|pdx.ne.jp))$/', $ans[$no])) {
					$err[$no] |= COOLIE_CHECK_ERROR_NOT_EMAIL2;
				}
			} elseif ($input_type == COOLIE_QUERY_RESTRICTION_EMAIL_NOT_MOBILE && $iCheck) {
				if (!preg_match("/{$regex1}/", $ans[$no])) {
					$err[$no] |= COOLIE_CHECK_ERROR_NOT_EMAIL1;
				} elseif (preg_match('/@(docomo\.ne\.jp|ezweb\.ne\.jp|[a-z]\.vodafone\.ne\.jp|([a-z]{2}\.)?(sky.tu-ka.ne.jp|pdx.ne.jp))$/', $ans[$no])) {
					$err[$no] |= COOLIE_CHECK_ERROR_NOT_EMAIL3;
				}
			} elseif (($input_type == COOLIE_QUERY_RESTRICTION_ALPHABET) && !preg_match('/^([a-zA-Z0-9]|\r|\n)+$/', $ans[$no]) && $iCheck) {
				$err[$no] |= COOLIE_CHECK_ERROR_NOT_ALPHABET;
			} elseif (($input_type == COOLIE_QUERY_RESTRICTION_HIRAGANA) && !preg_match('/^(\xA4[\xA1-\xF3]|\r|\n)+$/', $ans[$no]) && $iCheck) {
				$err[$no] |= COOLIE_CHECK_ERROR_NOT_HIRAGANA;
			} elseif (($input_type == COOLIE_QUERY_RESTRICTION_KATAKANA) && !preg_match('/^(\xA5[\xA1-\xF6]|\r|\n)+$/', $ans[$no]) && $iCheck) {
				$err[$no] |= COOLIE_CHECK_ERROR_NOT_KATAKANA;
			} elseif (($input_type == COOLIE_QUERY_RESTRICTION_REGEX) && !preg_match("/{$input_regex}/", $ans[$no]) && $iCheck) {
				$err[$no] |= COOLIE_CHECK_ERROR_NOT_REGEX;
			} else {
				if (is_numeric($max_limit) && count($ans[$no]) > $max_limit) {
					$err[$no] |= COOLIE_CHECK_ERROR_OVER_LIMIT;
				}
				if (is_numeric($min_limit) && count($ans[$no]) < $min_limit) {
					$err[$no] |= COOLIE_CHECK_ERROR_UNDER_LIMIT;
				}
			}
			
			if (!$err[$no]) {
				unset($err[$no]);
			}
		}
		
		if (empty($err)) {
			return COOLIE_OK;
		}
		$this->_view->assign("err", $err);
		return -1;
	}
	// }}}
	
	// {{{ putToDatabase
	/**
	 *
	 * ユーザーの入力値をファイルへ記録する or メールで送信する
	 *
	 * @access		public
	 * @return		int			成功: COOLIE_OK / エラー(例外): 1xAｷ豪	 *
	 */
	function putToDatabase()
	{
		// 入力値取得
		$id  = $this->_request->get("id");
		$ans = $this->_request->get("answers");
		
		// 設定内容の取得
		$db      = &DB::connect("sqlite://config/config.db");
		$queries = $db->select("SELECT * FROM queries WHERE id = '{$id}' ORDER BY no ASC;");
		$form    = $db->select("SELECT * FROM forms WHERE id = '{$id}';");
		$form    = $form[0];
		
		// CSV追記
		if ($form["type"] == COOLIE_FORMTYPE_ENQUETE) {
			// 使用するオブジェクト
			require_once "File/IO.php";
			require_once "config/masters/queryType.php";
			// 書き込み
			ksort($ans);
			$data = array();
			foreach ($ans as $a) {
				switch ($queries['qtype']) {
					case COOLIE_QUERYTYPE_DATE_YM:
					case COOLIE_QUERYTYPE_DATE_YMD:
					case COOLIE_QUERYTYPE_DATE_MD:
						$data[] = '"' . addslashes(is_array($a) ? implode("/", $a) : $a) . '"';
						break;
					case COOLIE_QUERYTYPE_ZIPCODE:
					case COOLIE_QUERYTYPE_TEL:
						$data[] = '"' . addslashes(is_array($a) ? implode("-", $a) : $a) . '"';
						break;
					default:
						$data[] = '"' . addslashes(is_array($a) ? implode(" / ", $a) : $a) . '"';
						break;
				}
			}
			File_IO::write("./dat/{$id}.csv", implode(",", $data) . "\n", TRUE);
		}
		
		// メールの送信
		$this->_view->assign("mail_form", $form);
		$this->_view->assign("mail_queries", $queries);
		$this->_view->assign("mail_answers", $ans);
		require "config/admin.php";
		require_once "Mail/Utilities.php";
		if ($form["send_mail"] & 1) {
			// 回答者に送信
			$toNo = NULL;
			foreach ($queries as $row) {
				if ($row["send_mail_address"]) {
					$toNo = $row["no"];
					break;
				}
			}
			if ($toNo) {
				$to    = $ans[$toNo];
				$regex = $this->_getMailAddressRegexPattern();
				if (preg_match("/^{$regex}$/", $to)) {
					// メールの送信
					$head = array(
						'From' => "From: " . Mail_Utilities::mbEncodeMimeHeader("{$form['mail_from_name']}")."<{$form['mail_from_address']}>",
						'MIME-Version' => "MIME-Version: 1.0",
						'Content-Type' => 'Content-Type: text/plain; charset=iso-2022-jp'
						);
					$body = $this->_view->fetch("mail/user.txt");
					$body = preg_replace('/\r\n|\r|\n/', "\n", $body);	// 改行整形
					$body = mb_convert_encoding($body, "JIS", mb_detect_encoding($body));
					$form["mail_subject"]='=?ISO-2022-JP?B?'.base64_encode(mb_convert_encoding($form["mail_subject"], "ISO-2022-JP","EUC-JP")).'?=';
					mail(
						Mail_Utilities::mbEncodeMimeHeader($to),
						$form["mail_subject"],
						$body,
						implode("\n", $head),
						"-f{$admin['COOLIE_ADMIN_MAIL']}"
						);
				}
			}
		}
		if ($form["send_mail"] & 2 || $form["type"] == COOLIE_FORMTYPE_MAIL) {
			// 管理者に送信
			$regex = $this->_getMailAddressRegexPattern();
			if (preg_match("/^{$regex}$/", $admin['COOLIE_ADMIN_MAIL'])) {
				// メールの送信
				$head = array(
					'From' => "From: " . Mail_Utilities::mbEncodeMimeHeader($admin['COOLIE_ADMIN_MAIL']),
					'MIME-Version' => 'MIME-Version: 1.0',
					'Content-Type' => 'Content-Type: text/plain; charset=iso-2022-jp'
					);
				$body = $this->_view->fetch("mail/admin.txt");
				$body = preg_replace('/\r\n|\r|\n/', "\n", $body);	// 改行整形
				$body = mb_convert_encoding($body, "JIS", mb_detect_encoding($body));
				$form["title"]='=?ISO-2022-JP?B?'.base64_encode(mb_convert_encoding($form["title"], "ISO-2022-JP","EUC-JP")).'?=';
				mail(
					Mail_Utilities::mbEncodeMimeHeader($admin['COOLIE_ADMIN_MAIL']),
					$form["title"],
					$body,
					implode("\n", $head),
					"-f{$admin['COOLIE_ADMIN_MAIL']}"
					);
			}
		}
		
		return NULL;
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
	
	// {{{ _getHttpUrlRegexPattern
	function _getHttpUrlRegexPattern()
	{
		$digit         = "[0-9]";
		$alpha         = "[a-zA-Z]";
		$alphanum      = "[a-zA-Z0-9]";
		$hex           = "[0-9A-Fa-f]";
		$escaped       = "%{$hex}{2}";
		$mark          = "[-_.!~*'()]";
		$unreserved    = "(?:{$alphanum}|{$mark})";
		$reserved      = "[;\\/?:@&  =+$,]";
		$uric          = "(?:[-_.!~*'()a-zA-Z0-9;\\/?:@&=+$,]|{$escaped})";
		$query         = "{$uric}*";
		$pchar         = "(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|{$escaped})";
		$param         = "{$pchar}*";
		$segment       = "{$param}(?:{$param})*";
		$path_segments = "{$segment}(?:\\/{$segment})*";
		$abs_path      = "\\/{$path_segments}";
		$port          = "{$digit}*";
		$IPv4address   = "{$digit}+\\.{$digit}+\\.{$digit}+\\.{$digit}+";
		$toplabel      = "(?:{$alpha}|{$alpha}[-a-zA-Z0-9]*{$alphanum})";
		$domainlabel   = "(?:{$alphanum}|{$alphanum}[-a-zA-Z0-9]*{$alphanum})";
		$hostname      = "(?:{$domainlabel}\\.)*{$toplabel}\\.?";
		$host          = "(?:{$hostname}|{$IPv4address})";
		
		return "http:\\/\\/{$host}(?::{$port})?(?:{$abs_path}(?:\\?{$query})?)?";
	}
	// }}}
}
// }}}

?>
