<?php
/**
 * DB���饹
 *
 * PEAR��DB���饹��ɤ���¿�Ѥ���뵡ǽ�Τߤ�������Ƥޤ���
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
	 * �ǡ����١��������С�����³�������ͥ�����󥪥֥������Ȥ��֤�
	 *
	 * @access	public
	 *
	 * @param		string	$dsn		��³����
	 * 		format "type://user:pass@host:port/dbname" or "sqlite://" + filename(full)
	 *		expr.  "mysql://test:pass@localhost:110/test_db"
	 *
	 * @return	object	DB���ͥ�����󥪥֥������� / ���Ԥ�������NULL
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
	 * ���ꤵ�줿RDBMS�ѥ��饹������������Υ��󥹥��󥹤��֤�
	 *
	 * @access	private
	 *
	 * @param		string	$type			���Ѥ���RDBMS
	 *
	 * @return	object	DB���ͥ�����󥪥֥������� / ���Ԥ�������NULL
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