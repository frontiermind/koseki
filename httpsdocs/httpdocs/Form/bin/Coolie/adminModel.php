<?php
/**
 *
 * Coolie ���󥱡��ȥ����ƥ� - ����������MODEL���饹
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
	
	/** �ꥯ�����ȥ��饹 */
	var $_request    = NULL;
	
	/** VIEW���饹 */
	var $_view       = NULL;
	
	/** ����������饹 */
	var $_conf       = NULL;
	
	/** �ե����륢�åץ��ɥ��饹 */
	var $_fileUpload = NULL;
	
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * ���󥹥ȥ饯��
	 *
	 * @param			object		$request		�ꥯ�����ȥ��饹
	 * @param			object		$view				VIEW���饹
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
	 * ���󥱡��ȷ��CVS�Υ��������
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function getResultCVS()
	{
		// �����ͼ���
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
	 * ����κ��
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function deleteQuery()
	{
		// �����ͼ���
		$id = $this->_request->get("id");
		$no = $this->_request->get("no");
		
		// Coolie_configManager���饹
		$this->_getConfigManager();
		
		$this->_conf->deleteQuery($id, $no);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ deleteForm()
	/**
	 *
	 * �ե�����κ��
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function deleteForm()
	{
		// �����ͼ���
		$id = $this->_request->get("id");
		
		// Coolie_configManager���饹
		$this->_getConfigManager();
		
		$this->_conf->deleteForm($id);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ editQuery()
	/**
	 *
	 * ������Խ�
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function editQuery()
	{
		// �����ͼ���
		$id     = $this->_request->get("id");
		$no     = $this->_request->get("no");
		$config = $this->_request->get("config");
		
		// Coolie_configManager���饹
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
	 * ����ο�������
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function createQuery()
	{
		// �����ͼ���
		$id    = $this->_request->get("id");
		$qtype = $this->_request->get("qtype");
		
		if (!$id || !$qtype) {
			return COOLIE_ERROR;
		}
		
		// Coolie_configManager���饹
		$this->_getConfigManager();
		
		$this->_conf->createQuery($id, $qtype);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ queryList()
	/**
	 *
	 * �������
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function queryList()
	{
		// �����ͼ���
		$id = $this->_request->get("id");
		
		// Coolie_configManager���饹
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
	 * ������������Խ�
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
	 * ���Υǥ�����������Խ�
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function editDesign()
	{
		// �����ͼ���
		$id     = $this->_request->get("id");
		$config = $this->_request->get("config");
		
		// ���������å�
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
		
		// Coolie_configManager���饹
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
	 * �����ե�����������Խ�
	 *
	 * @access		public
	 * @return		int				COOLIE_OK / COOLIE_ERROR
	 *
	 */
	function editConfig()
	{
		// �����ͼ���
		$id     = $this->_request->get("id");
		$config = $this->_request->get("config");
		
		// ���������å�
		$name = include("config/masters/filename.php");
		foreach (array("name", "submit", "return") as $key) {
			$c = $this->_checkFileUpload("{$key}_img", $name["img/{$key}"] . "_{$id}");
			if ($c > 0) {
				$config["{$key}_img"] = $c;
			} else {
				// unset($config["{$key}_img"]);
			}
		}
			
		// Coolie_configManager���饹
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
		// �����ͼ���
		$id = $this->_request->get("id");
		$no = $this->_request->get("no");
		
		// Coolie_configManager���饹
		$this->_getConfigManager();
		
		// �ե���������ǡ�������
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
		// �����ͼ���
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
	 * �ե������Խ������ѥǥե����������
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function setFormDefaultValue()
	{
		// �����ͼ���
		$id = $this->_request->get("id");
		
		// Coolie_configManager���饹
		$this->_getConfigManager();
		
		// �ե���������ǡ�������
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
	 * �ޥ�������
	 *
	 * @access		public
	 * @param			string		$name				�ޥ���̾
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
	 * �ե������Խ���˥塼
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
	 * �ե�����ο�������
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function createForm()
	{
		// �����ͼ���
		$type = $this->_request->get("type");
		
		// Coolie_configManager���饹
		$this->_getConfigManager();
		
		// �ե���������ǡ�����Ǽ
		$this->_conf->createForm($type);
		
		return COOLIE_OK;
	}
	// }}}
	
	// {{{ formList()
	/**
	 *
	 * �ե�������������������̤�VIEW���Ϥ�
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function formList()
	{
		// Coolie_configManager���饹
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
	 * �����������򽸷פ������ץǡ�����VIEW���Ϥ�
	 *
	 * @access		public
	 * @return		int				COOLIE_OK
	 *
	 */
	function accessAnalyze()
	{
		// �����ͼ���
		$group       = $this->_request->get("group");
		$date        = $this->_request->get("date");
		$span        = $this->_request->get("span");
		
		if (!empty($group) && !empty($span)) {
			// �ѥ�᡼������
			list($y, $m) = explode("-", $span);
			if ($date >= 1 && $date <= 31) {
				$s = "{$span}-{$date}";
			} else {
				$s = $span;
			}
			
			// �������������饹
			require_once 'Loach/accessLog.php';
			$LOG = &new Loach_accessLog();
			
			// ���ץǡ�������
			$result = array();
			switch ($group) {
				case COOLIE_ANALYZE_GROUP_DAY:
					$slt = $LOG->getStatistics($s, "DAYOFMONTH(access_stamp)", "g ASC");
					if ($date == 0) {
						// ������������򻻽�
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
			
			// ��̤�����
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
	 * Coolie_configManager���饹���֥������Ȥμ���
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
	 * File_upload���饹���֥������Ȥμ���
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
			
			// ����
			$this->_fileUpload->accept_ext = array("gif", "png", "jpg");
		}
	}
	// }}}
	
	// {{{ _checkFileUpload()
	/**
	 *
	 * ���ꥫ���̾�Υե����륢�åץ��ɥ����å�
	 *
	 * @access		private
	 * @param			string		$name				�����̾
	 * @param			string		$path				�ե�������¸�ѥ�(��ĥ�Ҥ����)
	 * @return		int				�������������
	 *
	 */
	function _checkFileUpload($name, $path)
	{
		// ���֥�����������
		$this->_getFileUpload();
		
		// �����å�
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
