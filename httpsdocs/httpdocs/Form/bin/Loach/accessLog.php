<?php
/**
 *
 * Loach システム - アクセスログ集計・統計クラス
 * 
 * @access		public
 * @create		2004/06/08
 * @version		$Id: accessLog.php,v 1.3 2004/08/18 19:19:47 shogo Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 */


// {{{ class Loach_accessLog
class Loach_accessLog
{
	
	// {{{ Private properties
	/** DB接続クラス */
	var $_DB  = NULL;
	
	/** アクセスログDB */
	var $_log = "sqlite://config/accessLog.db";
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * コンストラクタ
	 *
	 * @param			string			$log			アクセスログDB
	 *
	 */
	function Loach_accessLog($log = NULL)
	{
		if ($log !== NULL) {
			$this->_log = $log;
		}
		require_once 'DB/DB.php';
		$this->_DB = &DB::connect($this->_log);
	}
	// }}}
	
	// {{{ record()
	/**
	 *
	 * アクセスログの記録
	 *
	 * @access		private
	 * @return		bool				成功すればTRUE
	 *
	 */
	function record()
	{
		// パラメータ
		$uri   = $this->_DB->escape_string($_SERVER["REQUEST_URI"]);
		$agent = $this->_DB->escape_string($_SERVER["HTTP_USER_AGENT"]);
		$ip    = $this->_DB->escape_string($_SERVER["REMOTE_ADDR"]);
		
		// SQLクエリ
		$SQL   = "INSERT INTO access_log VALUES(NOW(), '{$uri}', '{$agent}', '{$ip}');";
		
		// 実行
		return (bool)($this->_DB->insert($SQL));
	}
	// }}}
	
	// {{{ getStatistics
	function getStatistics($span, $group, $order)
	{
		return $this->_DB->select(
			"SELECT {$group} AS g, COUNT(*) AS c FROM access_log " .
			"  WHERE access_stamp LIKE '{$span}%' " .
			"  GROUP BY {$group} ORDER BY {$order} LIMIT 50"
			);
	}
	// }}}
}
// }}}

?>
