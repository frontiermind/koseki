<?php
/**
 *
 * Coolie アンケートシステム - 管理画面用MODELクラス
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: adminModel.php,v 1.26 2004/11/16 06:55:38 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 **/

// {{{ Constants

define("COOLIE_ANALYZE_GROUP_DAY",   1);
define("COOLIE_ANALYZE_GROUP_TIME",  2);
define("COOLIE_ANALYZE_GROUP_AGENT", 3);
define("COOLIE_ANALYZE_GROUP_IP",    4);

define("COOLIE_IMAGETYPE_NONE",      0);
define("COOLIE_IMAGETYPE_GIF",       1);
define("COOLIE_IMAGETYPE_PNG",       2);
define("COOLIE_IMAGETYPE_JPG",       3);

// }}}

// {{{ class Coolie_adminModel
class Coolie_adminModel
{
	// {{{ Private properties
	
	/** リクエストクラス */
	var $_request    = NULL;
	
	/** VIEWクラス */
	var $_view       = NULL;
	
	/** 設定管理クラス */
	var $_conf       = NULL;
	
	/** ファイルアップロードクラス */
	var $_fileUpload = NULL;
	
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * コンストラクタ
	 *
	 * @param			object		$request		リクエストクラス
	 * @param			object		$view				VIEWクラス
	 *
	 */
	function Coolie_adminModel(&$request, &$view)
	{
		$this->_request = &$request;
		$this->_view    = &$view;
	}
	// }}}
	
	// {{{ getResultCVS()
	/**
	 *
	 * アンケート結果CVSのダウンロード
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function getResultCVS()
	{
		// 入力値取得
		$id  = $this->_request->get("id");
		header("Content-Disposition: attachment; filename=enquete_{$id}.csv");
		header("Content-Type: application/octet-stream; name=enquete_{$id}.csv");
		if (is_file($csv = "./dat/{$id}.csv")) {
			require_once "File/IO.php";
			echo mb_convert_encoding(File_IO::read($csv), "SJIS", "EUC-JP");
		}
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ deleteQuery()
	/**
	 *
	 * 質問の削除
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function deleteQuery()
	{
		// 入力値取得
		$id = $this->_request->get("id");
		$no = $this->_request->get("no");
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		$this->_conf->deleteQuery($id, $no);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ deleteForm()
	/**
	 *
	 * フォームの削除
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function deleteForm()
	{
		// 入力値取得
		$id = $this->_request->get("id");
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		$this->_conf->deleteForm($id);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ editQuery()
	/**
	 *
	 * 質問の編集
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function editQuery()
	{
		// 入力値取得
		$id     = $this->_request->get("id");
		$no     = $this->_request->get("no");
		$config = $this->_request->get("config");
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		if ($err = $this->_conf->editQuery($id, $no, $config)) {
			$this->_view->assign("err", $err);
			return COOLIE_ERROR;
		}
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ createQuery()
	/**
	 *
	 * 質問の新規作成
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function createQuery()
	{
		// 入力値取得
		$id    = $this->_request->get("id");
		$qtype = $this->_request->get("qtype");
		
		if (!$id || !$qtype) {
			return COOLIE_ERROR;
		}
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		$this->_conf->createQuery($id, $qtype);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ queryList()
	/**
	 *
	 * 質問一覧
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function queryList()
	{
		// 入力値取得
		$id = $this->_request->get("id");
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		$data = $this->_conf->getQueries($id);
		
		$this->_view->assign(
			array(
				"id"      => $id,
				"queries" => $data
				)
			);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ setAdminValues()
	function setAdminValues()
	{
		require "config/admin.php";
		$this->_view->assign($admin);
	}
	// }}}
	
	// {{{ editAdmin()
	/**
	 *
	 * 管理者設定の編集
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function editAdmin()
	{
		$config = $this->_request->get("config");
		require_once "HTML/inputCheck.php";
		$CHK = &new HTML_inputCheck(include("config/masters/check/admin.php"));
		if ($err = $CHK->check($config)) {
			$this->_view->assign("err", $err);
			$this->_view->assign($config);
			return COOLIE_ERROR;
		}
		
		require_once "File/IO.php";
		$data = "<?php \$admin = " . var_export($config, 1) . "?>";
		File_IO::write("config/admin.php", $data);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ editDesign()
	/**
	 *
	 * 全体デザイン設定の編集
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function editDesign()
	{
		// 入力値取得
		$id     = $this->_request->get("id");
		$config = $this->_request->get("config");
		
		// 画像チェック
		$name = include("config/masters/filename.php");
		$c = $this->_checkFileUpload("body_background", $name["img/body"] . "_{$id}");
		if ($c > 0) {
			$config["body_background"] = $c;
		} else {
			// unset($config["body_background"]);
		}
		$c = $this->_checkFileUpload("query_no_img", $name["img/queryNo"] . "_{$id}");
		if ($c > 0) {
			$config["query_no_img"] = $c;
		} else {
			// unset($config["query_no_img"]);
		}
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		if ($err = $this->_conf->editForm($id, $config)) {
			$this->_view->assign("err", $err);
			return COOLIE_ERROR;
		}
		
		$this->_view->assign("id", $id);
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ editConfig()
	/**
	 *
	 * 送信フォーム設定の編集
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function editConfig()
	{
		// 入力値取得
		$id     = $this->_request->get("id");
		$config = $this->_request->get("config");
		
		// 画像チェック
		$name = include("config/masters/filename.php");
		foreach (array("name", "submit", "return") as $key) {
			$c = $this->_checkFileUpload("{$key}_img", $name["img/{$key}"] . "_{$id}");
			if ($c > 0) {
				$config["{$key}_img"] = $c;
			} else {
				// unset($config["{$key}_img"]);
			}
		}
			
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		if ($err = $this->_conf->editForm($id, $config)) {
			$this->_view->assign("err", $err);
			return COOLIE_ERROR;
		}
		
		$this->_view->assign("id", $id);
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ setQueryDefaultValue()
	function setQueryDefaultValue()
	{
		// 入力値取得
		$id = $this->_request->get("id");
		$no = $this->_request->get("no");
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		// フォーム設定データ取得
		$data = $this->_conf->getQueries($id, $no);
		
		// assign
		$this->_view->assign(
			array(
				"id"      => $id,
				"no"      => $no,
				"default" => $data[0]
				)
			);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ setConfigInputValues()
	function setConfigInputValues()
	{
		// 入力値取得
		$id     = $this->_request->get("id");
		$config = $this->_request->get("config");
		
		// assign
		$this->_view->assign(
			array(
				"id"      => $id,
				"default" => $config
				)
			);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ setFormDefaultValue()
	/**
	 *
	 * フォーム編集画面用デフォルト値設定
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function setFormDefaultValue()
	{
		// 入力値取得
		$id = $this->_request->get("id");
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		// フォーム設定データ取得
		$data = $this->_conf->getForms($id);
		
		// assign
		$this->_view->assign(
			array(
				"id"      => $id,
				"default" => $data[0]
				)
			);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ setMasterValues()
	/**
	 *
	 * マスタ設定
	 *
	 * @access		public
	 * @param			string		$name				マスタ名
	 * @return		int				COOLIE_OK
	 *
	 */
	function setMasterValues($name)
	{
		$this->_view->assign($name, include("config/masters/{$name}.php"));
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ formEditMenu()
	/**
	 *
	 * フォーム編集メニュー
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function formEditMenu()
	{
		$this->_view->assign("id", $this->_request->get("id"));
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ createForm()
	/**
	 *
	 * フォームの新規作成
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function createForm()
	{
		// 入力値取得
		$type = $this->_request->get("type");
		
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		// フォーム設定データ格納
		$this->_conf->createForm($type);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ formList()
	/**
	 *
	 * フォーム一覧を取得し、結果をVIEWに渡す
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function formList()
	{
		// Coolie_configManagerクラス
		$this->_getConfigManager();
		
		$url  = "http://{$_SERVER['HTTP_HOST']}";
		$url .= dirname($_SERVER['REQUEST_URI']);
		if (substr($url, -1) != "/") {
			$url .= "/";
		}
		
		// assign
		$this->_view->assign(
			array(
				"url"   => $url,
				"forms" => $this->_conf->getForms()
				)
			);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ accessAnalyze()
	/**
	 *
	 * アクセスログを集計し、統計データをVIEWに渡す
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function accessAnalyze()
	{
		// 入力値取得
		$group       = $this->_request->get("group");
		$date        = $this->_request->get("date");
		$span        = $this->_request->get("span");
		
		if (!empty($group) && !empty($span)) {
			// パラメータ整形
			list($y, $m) = explode("-", $span);
			if ($date >= 1 && $date <= 31) {
				$s = "{$span}-{$date}";
			} else {
				$s = $span;
			}
			
			// アクセスログクラス
			require_once 'Loach/accessLog.php';
			$LOG = &new Loach_accessLog();
			
			// 統計データ取得
			$result = array();
			switch ($group) {
				case COOLIE_ANALYZE_GROUP_DAY:
					$slt = $LOG->getStatistics($s, "DAYOFMONTH(access_stamp)", "g ASC");
					if ($date == 0) {
						// 該当月の日数を算出
						switch ($m) {
							case 1: case 3: case 5: case 7: case 8: case 10: case 12:
								$j = 31;
								break;
							case 4: case 6: case 9: case 11:
								$j = 30;
								break;
							case 2:
								$j = ($y % 400 ? ($y % 100 ? ($y % 4 ? 28 : 29) : 28) : 29);
								break;
						}
						for ($i = 1; $i <= $j; ++$i) {
							$result[$i] = 0;
						}
					}
					break;
				case COOLIE_ANALYZE_GROUP_TIME:
					$slt = $LOG->getStatistics($s, "HOUR(access_stamp)", "g ASC");
					for ($i = 0; $i < 24; ++$i) {
						$result[$i] = 0;
					}
					break;
				case COOLIE_ANALYZE_GROUP_AGENT:
					$slt = $LOG->getStatistics($s, "user_agent", "c DESC");
					break;
				case COOLIE_ANALYZE_GROUP_IP:
					$slt = $LOG->getStatistics($s, "ip", "c DESC");
					break;
				default:
					$slt = NULL;
			}
			
			// 結果の整形
			if (!empty($slt) && is_array($slt)) {
				foreach ($slt as $row) {
					$result[$row["g"]] = $row["c"];
				}
			}
			
			// assign
			$this->_view->assign(
				array("span" => $span, "date" => $date, "group" => $group, "data" => $result)
				);
		}
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ _getConfigManager
	/**
	 *
	 * Coolie_configManagerクラスオブジェクトの取得
	 *
	 * @access		private
	 * @return		int				COOLIE_OK
	 *
	 */
	function _getConfigManager()
	{
		if ($this->_conf === NULL) {
			require_once 'Coolie/configManager.php';
			$this->_conf = &new Coolie_configManager();
		}
	}
	// }}}
	
	// {{{ _getFileUpload()
	/**
	 *
	 * File_uploadクラスオブジェクトの取得
	 *
	 * @access		private
	 * @return		int				COOLIE_OK
	 *
	 */
	function _getFileUpload()
	{
		if ($this->_fileUpload === NULL) {
			require_once 'File/upload.php';
			$this->_fileUpload = &new File_upload();
			
			// 設定
			$this->_fileUpload->accept_ext = array("gif", "png", "jpg");
		}
	}
	// }}}
	
	// {{{ _checkFileUpload()
	/**
	 *
	 * 指定カラム名のファイルアップロードチェック
	 *
	 * @access		private
	 * @param			string		$name				カラム名
	 * @param			string		$path				ファイル保存パス(拡張子を除く)
	 * @return		int				画像タイプ定数
	 *
	 */
	function _checkFileUpload($name, $path)
	{
		// オブジェクト生成
		$this->_getFileUpload();
		
		// チェック
		$slt = $this->_fileUpload->save($name, $path, 0666, FALSE, TRUE);
		
		//error_log($slt);
		
		if ($slt !== NULL) {
			preg_match('/\.(gif|png|jpg)$/i', $slt, $m);
			switch ($m[1]) {
				case "gif":
					return COOLIE_IMAGETYPE_GIF;
				case "png":
					return COOLIE_IMAGETYPE_PNG;
				case "jpg":
					return COOLIE_IMAGETYPE_JPG;
			}
		}
		
		return COOLIE_IMAGETYPE_NONE;
	}
	// }}}
}
// }}}

?>
