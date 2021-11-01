<?php
/**
 *
 * Coolie ���󥱡��ȥ����ƥ� - ����ơ��֥����饹
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
	/** DB��³���饹 */
	var $_DB  = NULL;
	
	/** ����DB */
	var $_config = "sqlite://config/config.db";
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * ���󥹥ȥ饯��
	 *
	 * @param			string			$config		����DB
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
	 * �ե�������������ư��������
	 *
	 * @access		public
	 * @param			int				$id				�ե�����ID
	 * @return		mixed			NULL or array(0 => array(�ե��������������), 1 => ..., ...);\
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
	 * ����ٰܺ��������
	 *
	 * @access		public
	 * @param			int				$id				�ե�����ID
	 * @param			int				$no				�����ֹ�
	 * @return		mixed			NULL or array(0 => array(����ܺ�), 1 => ..., ...);\
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
	 * �ե�����ο�������
	 *
	 * @access		public
	 * @param			int				$type			�ե�����μ��� (1 = �᡼��, 2 = ���󥱡���)
	 * @return		bool			���������TRUE
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
	 * ����ο�������
	 *
	 * @access		public
	 * @param			int				$id				�ե�����ID
	 * @param			int				$qtype		����μ���
	 * @return		bool			���������TRUE
	 *
	 */
	function createQuery($id, $qtype)
	{
		// ����
		$sql = include("config/default/query.php");
		$slt = (bool)($this->_DB->insert($sql));
		
		// DB�ե���������
		$this->_answerFileInit($id);
		
		return $slt;
	}
	// }}}
	
	// {{{ editForm()
	/**
	 *
	 * �ե�����������Խ�
	 *
	 * @access		public
	 * @param			int				$id				�ե�����ID
	 * @param			array			$values		�Խ�����
	 * @return		mixed			SQLȯ�Ԥ����������0, ���Ԥ����-1, �����ͥ��顼�ξ��ϥ����̾ => ���顼�ͤ�����
	 *
	 */
	function editForm($id, $values)
	{
		// �����ͥ����å�
		require_once "HTML/inputCheck.php";
		$CHK = &new HTML_inputCheck(include("config/masters/check/form.php"));
		if ($err = $CHK->check($values)) {
			return $err;
		}
		
		// SQLʸ����
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
	 * �ե�����������Խ�
	 *
	 * @access		public
	 * @param			int				$id				�ե�����ID
	 * @param			int				$no				�����ֹ�
	 * @param			array			$values		�Խ�����
	 * @return		mixed			SQLȯ�Ԥ����������TRUE, ���Ԥ����FALSE, �����ͥ��顼�ξ��ϥ����̾ => ���顼�ͤ�����
	 *
	 */
	function editQuery($id, $no, $values)
	{
		// �����ͥ����å�
		require_once "HTML/inputCheck.php";
		$CHK = &new HTML_inputCheck(include("config/masters/check/query.php"));
		if ($err = $CHK->check($values)) {
			return $err;
		}
		
		// �����ֹ��ѹ�����
		if ($no < $values["no"]) {
			$this->_DB->update("UPDATE queries SET no = 0 WHERE id = {$id} AND no = {$no};");
			$this->_DB->update("UPDATE queries SET no = no - 1 WHERE id = {$id} AND no > {$no} AND no <= {$values['no']};");
			$no = 0;
		} elseif ($no > $values["no"]) {
			$this->_DB->update("UPDATE queries SET no = 0 WHERE id = {$id} AND no = {$no};");
			$this->_DB->update("UPDATE queries SET no = no + 1 WHERE id = {$id} AND no < {$no} AND no >= {$values['no']};");
			$no = 0;
		}
		
		// SQLʸ����
		$sql = array();
		foreach (array_keys($values) as $key) {
			$sql[] = "{$key} = '" . $this->_DB->escape_string($values[$key]) . "'";
		}
		$sql = "UPDATE queries SET " . implode(",", $sql) . " WHERE id = {$id} AND no = {$no};";
		
		$slt = (bool)($this->_DB->update($sql));
		
		// DB�ե���������
		//$this->_answerFileInit($id); // 2004.8.20 ��������ʤ����Ȥ˷���
		
		// DB�ե�����˷ٹ�����
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
	 * �ե�����������Խ�
	 *
	 * @access		public
	 * @param			int				$id				�ե�����ID
	 * @return		bool			SQLȯ�Ԥ����������TRUE, ���Ԥ����FALSE
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
	 * ����κ��
	 *
	 * @access		public
	 * @param			int				$id				�ե�����ID
	 * @param			int				$no				�����ֹ�
	 * @return		bool			SQLȯ�Ԥ����������TRUE, ���Ԥ����FALSE
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
		// �ե���������
		require_once "File/IO.php";
		File_IO::write("dat/{$id}.csv", "");
		@chmod("dat/{$id}.csv", 0777);
		
		return NULL;
	}
	// }}}

	// {{{ _putNotice()
	function _putNotice($id)
	{
		// ���������ѹ��ٹ�
		require_once "File/IO.php";
		File_IO::write("./dat/{$id}.csv", "\"*���*\",\"" . strftime("%Y.%m.%d %T") . "\",\"������ܤ����꤬�ѹ�����ޤ�������������ǥǡ����ե����ޥåȤ������礬�������礬����ޤ��ΤǤ���դ���������\"\n", TRUE);
		
		return NULL;
	}
	// }}}
}
// }}}

?>
