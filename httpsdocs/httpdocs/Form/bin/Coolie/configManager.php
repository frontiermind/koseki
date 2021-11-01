<?php
/**
 *
 * Coolie アンケートシステム - 設定テーブル操作クラス
 * 
 * @access		public
 * @create		2004/06/04
 * @version		$Id: configManager.php,v 1.21 2004/08/21 09:53:30 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 */

// {{{ Constants

define("COOLIE_CONFIG_FORMTYPE_MAIL",    1);
define("COOLIE_CONFIG_FORMTYPE_ENQUETE", 2);

// }}}

// {{{ class Coolie_configManager
class Coolie_configManager
{
	
	// {{{ Private properties
	/** DB接続クラス */
	var $_DB  = NULL;
	
	/** 設定DB */
	var $_config = "sqlite://config/config.db";
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * コンストラクタ
	 *
	 * @param			string			$config		設定DB
	 *
	 */
	function Coolie_configManager($config = NULL)
	{
		if ($config !== NULL) {
			$this->_config = $config;
		}
		require_once 'DB/DB.php';
		$this->_DB = &DB::connect($this->_config);
	}
	// }}}
	
	// {{{ getForms()
	/**
	 *
	 * フォームの設定内容一覧を取得
	 *
	 * @access		public
	 * @param			int				$id				フォームID
	 * @return		mixed			NULL or array(0 => array(フォームの設定内容), 1 => ..., ...);\
	 *
	 */
	function getForms($id = NULL)
	{
		if ($id !== NULL) {
			$sql = "SELECT * FROM forms WHERE id = '{$id}';";
		} else {
			$sql = "SELECT * FROM forms ORDER BY id ASC;";
		}
		return $this->_DB->select($sql);
	}
	// }}}
	
	// {{{ getQueries()
	/**
	 *
	 * 質問詳細一覧を取得
	 *
	 * @access		public
	 * @param			int				$id				フォームID
	 * @param			int				$no				質問番号
	 * @return		mixed			NULL or array(0 => array(質問詳細), 1 => ..., ...);\
	 *
	 */
	function getQueries($id, $no = NULL)
	{
		if ($no !== NULL) {
			$sql = "SELECT * FROM queries WHERE id = '{$id}' AND no = '{$no}';";
		} else {
			$sql = "SELECT * FROM queries WHERE id = '{$id}' ORDER BY no ASC;";
		}
		return $this->_DB->select($sql);
	}
	// }}}
	
	// {{{ createForm()
	/**
	 *
	 * フォームの新規作成
	 *
	 * @access		public
	 * @param			int				$type			フォームの種類 (1 = メール, 2 = アンケート)
	 * @return		bool			成功すればTRUE
	 *
	 */
	function createForm($type)
	{
		$sql = include("config/default/form.php");
		return (bool)($this->_DB->insert($sql));
	}
	// }}}
	
	// {{{ createQuery()
	/**
	 *
	 * 質問の新規作成
	 *
	 * @access		public
	 * @param			int				$id				フォームID
	 * @param			int				$qtype		質問の種類
	 * @return		bool			成功すればTRUE
	 *
	 */
	function createQuery($id, $qtype)
	{
		// 作成
		$sql = include("config/default/query.php");
		$slt = (bool)($this->_DB->insert($sql));
		
		// DBファイル初期化
		$this->_answerFileInit($id);
		
		return $slt;
	}
	// }}}
	
	// {{{ editForm()
	/**
	 *
	 * フォーム設定の編集
	 *
	 * @access		public
	 * @param			int				$id				フォームID
	 * @param			array			$values		編集内容
	 * @return		mixed			SQL発行に成功すれば0, 失敗すると-1, 入力値エラーの場合はカラム名 => エラー値の配列
	 *
	 */
	function editForm($id, $values)
	{
		// 入力値チェック
		require_once "HTML/inputCheck.php";
		$CHK = &new HTML_inputCheck(include("config/masters/check/form.php"));
		if ($err = $CHK->check($values)) {
			return $err;
		}
		
		// SQL文生成
		$sql = array();
		foreach (array_keys($values) as $key) {
			$sql[] = "{$key} = '" . $this->_DB->escape_string($values[$key]) . "'";
		}
		$sql = "UPDATE forms SET " . implode(",", $sql) . " WHERE id = {$id};";
		
		if (!$this->_DB->update($sql)) {
			return -1;
		}
		return 0;
	}
	// }}}
	
	// {{{ editQuery()
	/**
	 *
	 * フォーム設定の編集
	 *
	 * @access		public
	 * @param			int				$id				フォームID
	 * @param			int				$no				質問番号
	 * @param			array			$values		編集内容
	 * @return		mixed			SQL発行に成功すればTRUE, 失敗するとFALSE, 入力値エラーの場合はカラム名 => エラー値の配列
	 *
	 */
	function editQuery($id, $no, $values)
	{
		// 入力値チェック
		require_once "HTML/inputCheck.php";
		$CHK = &new HTML_inputCheck(include("config/masters/check/query.php"));
		if ($err = $CHK->check($values)) {
			return $err;
		}
		
		// 質問番号変更処理
		if ($no < $values["no"]) {
			$this->_DB->update("UPDATE queries SET no = 0 WHERE id = {$id} AND no = {$no};");
			$this->_DB->update("UPDATE queries SET no = no - 1 WHERE id = {$id} AND no > {$no} AND no <= {$values['no']};");
			$no = 0;
		} elseif ($no > $values["no"]) {
			$this->_DB->update("UPDATE queries SET no = 0 WHERE id = {$id} AND no = {$no};");
			$this->_DB->update("UPDATE queries SET no = no + 1 WHERE id = {$id} AND no < {$no} AND no >= {$values['no']};");
			$no = 0;
		}
		
		// SQL文生成
		$sql = array();
		foreach (array_keys($values) as $key) {
			$sql[] = "{$key} = '" . $this->_DB->escape_string($values[$key]) . "'";
		}
		$sql = "UPDATE queries SET " . implode(",", $sql) . " WHERE id = {$id} AND no = {$no};";
		
		$slt = (bool)($this->_DB->update($sql));
		
		// DBファイル初期化
		//$this->_answerFileInit($id); // 2004.8.20 初期化しないことに決定
		
		// DBファイルに警告挿入
		$this->_putNotice($id);
		
		if (!$slt) {
			return -1;
		}
		return 0;
	}
	// }}}
	
	// {{{ deleteForm()
	/**
	 *
	 * フォーム設定の編集
	 *
	 * @access		public
	 * @param			int				$id				フォームID
	 * @return		bool			SQL発行に成功すればTRUE, 失敗するとFALSE
	 *
	 */
	function deleteForm($id)
	{
		@unlink("./dat/{$id}.csv");
		$slt = (bool)($this->_DB->delete("DELETE FROM queries WHERE id = {$id}"));
		$slt = (bool)($this->_DB->delete("DELETE FROM forms WHERE id = {$id}")) & $slt;
		return $slt;
	}
	// }}}
	
	// {{{ deleteQuery()
	/**
	 *
	 * 質問の削除
	 *
	 * @access		public
	 * @param			int				$id				フォームID
	 * @param			int				$no				質問番号
	 * @return		bool			SQL発行に成功すればTRUE, 失敗するとFALSE
	 *
	 */
	function deleteQuery($id ,$no)
	{
		$slt = (bool)($this->_DB->delete("DELETE FROM queries WHERE id = {$id} AND no = {$no}"));
		$this->_answerFileInit($id);
		return $slt;
	}
	// }}}
	
	// {{{ _answerFileInit()
	function _answerFileInit($id)
	{
		// ファイル初期化
		require_once "File/IO.php";
		File_IO::write("dat/{$id}.csv", "");
		@chmod("dat/{$id}.csv", 0777);
		
		return NULL;
	}
	// }}}

	// {{{ _putNotice()
	function _putNotice($id)
	{
		// 質問設定変更警告
		require_once "File/IO.php";
		File_IO::write("./dat/{$id}.csv", "\"*注意*\",\"" . strftime("%Y.%m.%d %T") . "\",\"質問項目の設定が変更されました。この前後でデータフォーマットに不整合が起きる場合がありますのでご注意ください。\"\n", TRUE);
		
		return NULL;
	}
	// }}}
}
// }}}

?>
