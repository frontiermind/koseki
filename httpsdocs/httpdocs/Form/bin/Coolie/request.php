<?php
/**
 *
 * Loach ���󥱡��ȥ����ƥ� - �ꥯ�����Ƚ������饹
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: request.php,v 1.5 2004/08/24 05:41:47 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 **/

// {{{ class Coolie_request
class Coolie_request
{
	// {{{ Private Properties
	
	/** �ꥯ������������ */
	var $_values = NULL;
	
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * ���󥹥ȥ饯��
	 *
	 */
	function Coolie_request()
	{
		// �ꥯ�������ͼ���
		$this->_values = $_REQUEST;
		
		// �������󥳡��ɤ��Ѵ�
	  // {{{ PHP Bug #26639�к�
		// mb_convert_variables(
		require_once "fixMbConvertVariables.php";
		_mb_convert_variables(
		// }}}
			LOACH_ENQUETE_INNER_ENCODING,
			LOACH_ENQUETE_INPUT_ENCODING,
			$this->_values
			);
		
		// magic_quotes�б�
		if (ini_get("magic_quotes_gpc") != 0) {
			$this->_values = $this->_stripSlashes($this->_values);
		}
	}
	// }}}
	
	// {{{ get
	/**
	 *
	 * ���ꥭ�����ͤ��������
	 *
	 * @access		public
	 * @param			string		$name			����ʸ����
	 * @return		string		�ꥯ�����Ȥ��줿��, ���Ĥ���ʤ� or ���ξ���NULL
	 *
	 */
	function get($name = NULL)
	{
		if ($name !== NULL) {
			if (isset($this->_values[$name]) && !empty($this->_values[$name])) {
				return $this->_values[$name];
			}
		} else {
			return $this->_values;
		}
		return NULL;
	}
	// }}}
	
	// {{{ _stripSlashes()
	function _stripSlashes($values)
	{
		if (is_array($values)) {
			foreach (array_keys($values) as $key) {
				$values[$key] = $this->_stripSlashes($values[$key]);
			}
			return $values;
		}
		return stripslashes($values);
	}
	// }}}
}
// }}}

?>
