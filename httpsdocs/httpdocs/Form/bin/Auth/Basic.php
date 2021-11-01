<?php
/**
 * Auth_Basicクラス
 *
 * BASIC認証簡略化クラス
 *
 * @access		public
 * @create		2004/05/20
 * @version		$Id: Basic.php,v 1.1 2004/06/05 04:01:49 shogo Exp $
 * @package   SPEAR.Auth
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

// {{{ class Auth_Basic
class Auth_Basic
{
	// {{{ Public properties
	
	/** 認証用 ユーザー名 => パスワード 配列 */
	var $authRecords = NULL;
	
	/** 認証フォームのタイトル */
	var $title       = "Basic Authorization";
	
	// }}}
	
	// {{{ Constructor
	function Auth_Basic($authRecords = NULL, $title = NULL)
	{
		$this->authRecords = $authRecords;
		if ($title !== NULL) {
			$this->title = $title;
		}
	}
	// }}}
	
	// {{{ 認証処理
	function checkAuth()
	{
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header("WWW-Authenticate: Basic realm=\"{$this->title}\"");
			header("HTTP/1.0 401 Unauthorized");
			return FALSE;
		} else {
			foreach ($this->authRecords as $user => $pw) {
				if ($_SERVER['PHP_AUTH_USER'] == $user && $_SERVER['PHP_AUTH_PW'] == $pw) {
					return TRUE;
				}
			}
			unset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
			header("WWW-Authenticate: Basic realm=\"{$this->title}\"");
			header("HTTP/1.0 401 Unauthorized");
			return FALSE;
		}
	}
	// }}}
}
// }}}

?>
