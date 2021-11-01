<?php
/**
 * DBクラス
 *
 * PEARのDBクラスもどき。多用される機能のみを実装してます。
 *
 * @access		public
 * @create		2003/01/30
 * @version		$Id: DB.php,v 1.2 2004/08/12 09:34:22 tsukasa Exp $
 * @package   SPEAR.DB
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

class DB {
	/**
	 *
	 * データベースサーバーに接続し、コネクションオブジェクトを返す
	 *
	 * @access	public
	 *
	 * @param		string	$dsn		接続情報
	 * 		format "type://user:pass@host:port/dbname" or "sqlite://" + filename(full)
	 *		expr.  "mysql://test:pass@localhost:110/test_db"
	 *
	 * @return	object	DBコネクションオブジェクト / 失敗した場合はNULL
	 *
	 */
	function &connect($dsn)
	{
		$type = $host = $user = $pass = $dbname = $port = NULL;
		list($type, $dsn) = explode('://', $dsn, 2);
		switch ($type) {
			case "sqlite":
				// Get object instance
				if (!$obj = &DB::_factory($type)) {
					return NULL;
				}
				// File open
				if (!$obj->connect($dsn)) {
					return NULL;
				}
				break;
			default:
				if (strstr($dsn, '@') !== FALSE) {
					list($user, $dsn) = explode('@', $dsn, 2);
					if (strstr($user, ':') !== FALSE) {
						list($user, $pass) = explode(':', $user, 2);
					}
				}
				list($host, $dbname) = explode('/', $dsn, 2);
				if (strstr($host, ':') !== FALSE) {
					list($host, $port) = explode(':', $host, 2);
				}
				// Get object instance
				if (!$obj = &DB::_factory($type)) {
					return NULL;
				}
				// RDBMS Connect
				if (!$obj->connect($host, $user, $pass, $dbname, $port)) {
					return NULL;
				}
				break;
		}
		// return
		return $obj;
	}

	/**
	 *
	 * 指定されたRDBMS用クラスを定義し、そのインスタンスを返す
	 *
	 * @access	private
	 *
	 * @param		string	$type			使用するRDBMS
	 *
	 * @return	object	DBコネクションオブジェクト / 失敗した場合はNULL
	 *
	 */
	function &_factory($type)
	{
		// Class define
//		@require_once "DB/{$type}.php";
		require_once "DB/{$type}.php";
		if (!class_exists($classname = "DB_{$type}")) {
			return NULL;
		}
		@$obj = &new $classname();
		return $obj;
	}
}

?>