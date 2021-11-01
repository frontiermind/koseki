<?php
/**
 * HTML_inputCheck���饹
 *
 * �ե����������ͤΥ����å���Ԥ�
 *
 * @access		public
 * @create		2004/06/11
 * @version		$Id: inputCheck.php,v 1.4 2004/08/20 03:51:43 shogo Exp $
 * @package   SPEAR.HTML
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

// {{{ class HTML_inputCheck
class HTML_inputCheck
{
	// {{{ Private properties
	
	/** �ǥե���ȥ����å����� */
	var $_checker = array();
	
	/** �����ȥѥ�᡼�� */
	var $_param   = NULL;
	
	/** �����ȥ��� */
	var $_key     = NULL;
	
	/** ���顼���� */
	var $_err     = NULL;
	
	/** �����å��о��������� */
	var $_values  = NULL;
	
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * ���󥹥ȥ饯��
	 *
	 * @param			array			$checker			�����å�����
	 *
	 * �������å�������͢�
	 * // ���ϥ���(��input/select/textarea��name°��)�򥭡��Ȥ���Ϣ������Ǥ���
	 * array(
	 *   ���� => array(
	 *     // �����ͤ�Ĺ����1Byte�ʾ�Ǥ��뤫�ɤ���������å����ޤ������Ǥϲ��Ǥ�OK��
	 *     "empty"     => NULL,
	 *
	 *     // ����ʸ�����Ĺ����<min>�ʾ�<max>�ʲ��Ǥ��뤫�ɤ���������å����ޤ���
	 *     // <min>, <max>���������ά��ǽ��
	 *     // ��ά���줿���ϥ����å���Ԥ��ޤ���
	 *     "length"    => array(0 => <min>, 1 => <max>),
	 *
	 *     // �����ͤ����ͤǤ��ꡢ<min>�ʾ�<max>�ʲ��Ǥ��뤫�ɤ���������å����ޤ���
	 *     // <min>, <max>���������ޤ����������ά��ǽ��
	 *     // ��ά���줿���Ϻ����͡������ͥ����å���Ԥ��ޤ���
	 *     "numeric"   => array(0 => <min>, 1 => <max>),
	 *
	 *     // �����ͤ������ͤǤ��ꡢ<min>�ʾ�<max>�ʲ��Ǥ��뤫�ɤ���������å����ޤ���
	 *     // <min>, <max>���������ޤ����������ά��ǽ��
	 *     // ��ά���줿���Ϻ����͡������ͥ����å���Ԥ��ޤ���
	 *     "integer"   => array(0 => <min>, 1 => <max>),
	 *
	 *     // �����ͤ�HTML�������ޤޤ�Ƥ��ʤ����ɤ���������å����ޤ���
	 *     // ���������Ǥ�����˴ޤޤ�륿���ϵ��Ĥ���ޤ���
	 *     "notHTML"   => array("a", "b", "span", ...)
	 *
	 *     // �����ͤ�HTML�֥饦�������̲�ǽ�ʿ������ɡ���̾�Ǥ��뤫�ɤ���������å����ޤ���
	 *     // ���ǤϤʤ�Ǥ�OK��
	 *     "color"     => NULL,
	 *
	 *     // �����ͤ����Ǥǻ��ꤵ�줿����ɽ���˥ޥå����뤫�ɤ���������å����ޤ���
	 *     "regex"     => '<preg�ؿ�����������ɽ��>',
	 *
	 *     // �����ͤ�<x>�Ǥ�����ˡ�<�����å�����>�˴�Ť��ƿ����������ͥ����å���Ԥ��ޤ���
	 *     // �����ͤ˥ޥå����륭�����ʤ����ϡ��ʤˤ�Ԥ��ޤ���
	 *     "switch"    => array(<x> => <�����å�����>, ...),
	 *
	 *     // �����ͤ����λ���<�����å�����A>�˴�Ť��ƿ����������ͥ����å���Ԥ���
	 *     // ���Ǥʤ����ˤ�<�����å�����B>�˴�Ť��ƿ����������ͥ����å���Ԥ��ޤ���
	 *     // �ɤ��餫�������ά���뤳�Ȥ���ǽ�Ǥ���
	 *     // ��ά���줿���Ͽ����������ͥ����å��ϹԤ��ޤ���
	 *     "ifnull"    => array(TRUE => <�����å�����A>, FALSE => <�����å�����B>)
	 *
	 *     // �����ͤ�PHP��strtotime�Ǽ��̲�ǽ������ʸ����Ǥ��뤫�ɤ���������å����ޤ���
	 *     "timestamp" => NULL,
	 *
	 *     // �����ͤ��������᡼�륢�ɥ쥹�Ǥ��뤫�ɤ���������å����ޤ���
	 *     "email"     => NULL,
	 *
	 *     // �����ͤ�������URL(HTTP)�Ǥ��뤫�ɤ���������å����ޤ���
	 *     "url"       => NULL,
	 *
	 *     // �����ͤ����ꤷ���������ͤ�Ʊ�����ɤ���������å����ޤ���
	 *     "equal"     => '<�����å��оݥ���>',
	 *     
	 *     // �����ͤ����ꤷ��DB���饹����³���줿DB�������Υơ��֥�.�����ˤ�����
	 *     // ��ˡ����Ǥ��뤫�ɤ���������å����ޤ���
	 *     "uniqueInDB" => array(<object>, <table>, <column>),
	 *     ),
	 *   ...
	 *   );
	 */
	function HTML_inputCheck($checker)
	{
		$this->_checker = $checker;
	}
	// }}}
	
	// {{{ check()
	/**
	 *
	 * �����ͥ����å��μ¹�
	 *
	 * @access		public
	 * @param			array			$values				�����å��оݤ���
	 * @return		array			�����å�������� array("����" => array("empty" => TRUE, "timestamp" => TRUE, ...), ...)
	 *
	 */
	function check($values)
	{
		$this->_values = &$values;
		
		foreach (array_keys($values) as $key) {
			$val = &$values[$key];
			$this->_key = $key;
			if (!empty($this->_checker[$key])) {
				foreach (array_keys($this->_checker[$key]) as $func) {
					$this->_param = $this->_checker[$key][$func];
					$func         = strtolower($func);
					$method       = "_check" . ucwords($func);
					
					switch ($func) {
						case "empty":
						case "length":
						case "numeric":
						case "integer":
						case "nothtml":
						case "color":
						case "regex":
						case "switch":
						case "ifnull":
						case "timestamp":
						case "email":
						case "url":
						case "equal":
						case "uniqueindb":
							$this->$method($val);
							break;
						default:
							break;
					}
				}
			}
		}
		
		return $this->_err;
	}
	// }}}
	
	// {{{ _checkEmpty()
	function _checkEmpty($val)
	{
		if (!strlen($val)) {
			$this->_err[$this->_key]["empty"] = TRUE;
		}
		
		return NULL;
	}
	// }}}
	
	// {{{ _checkLength()
	function _checkLength($val)
	{
		$min = $this->_param[0];
		$max = $this->_param[1];
		
		if (($min > 0 && strlen($val) < $min) || ($max > 0 && strlen($val) > $max)) {
			$this->_err[$this->_key]["length"] = TRUE;
		}
		
		return NULL;
	}
	// }}}
	
	// {{{ _checkNumeric()
	function _checkNumeric($val)
	{
		$min = $this->_param[0];
		$max = $this->_param[1];
		
		if (!is_numeric($val) || ($min !== NULL && $val < $min) || ($max !== NULL && $val > $max)) {
			$this->_err[$this->_key]["numeric"] = TRUE;
		}
		
		return NULL;
	}
	// }}}
	
	// {{{ _checkInteger()
	function _checkInteger($val)
	{
		$min = @$this->_param[0];
		$max = @$this->_param[1];
		
		if ((!is_numeric($val) || (bool)((float)$val - (int)$val)) || ($min !== NULL && $val < $min) || ($max !== NULL && $val > $max)) {
			$this->_err[$this->_key]["integer"] = TRUE;
		}
		
		return NULL;
	}
	// }}}

	// {{{ _checkNothtml()
	function _checkNothtml($val)
	{
		if (preg_match_all('!</?([a-zA-Z0-9]+)(?:[^"\'>]|"[^"]*"|\'[^\']*\')*>!', $val, $m)) {
			$m = array_unique($m[1]);
			$r = array();
			foreach ($m as $t) {
				$r[$t] = TRUE;
			}
			foreach (((array)$this->_param) as $a) {
				if (isset($r[$a])) {
					unset($r[$a]);
				}
			}
			if (!empty($r)) {
				$this->_err[$this->_key]["notHTML"] = TRUE;
			}
		}
		
		return NULL;
	}
	// }}}
	
	// {{{ _checkColor()
	function _checkColor($val)
	{
		if (!preg_match('/#([0-9A-F]{3}|[0-9A-F]{6})/i', $val)) {
			$colors = array(
				"black", "navy", "darkblue", "mediumblue", "blue", "darkgreen", "green", "teal", "darkcyan", "deepskyblue", "darkturquoise", "mediumspringgreen", "lime", "springgreen", "aqua", "cyan", "midnightblue", "dodgerblue", "lightseagreen", "forestgreen", "seagreen", "darkslategray", "limegreen", "mediumseagreen", "turquoise", "royalblue", "steelblue", "darkslateblue", "mediumturquoise", "indigo", "darkolivegreen", "cadetblue", "cornflowerblue", "mediumaquamarine", "dimgray", "slateblue", "olivedrab", "slategray", "lightslategray", "mediumslateblue", "lawngreen", "chartreuse", "aquamarine", "maroon", "purple", "olive", "gray", "skyblue", "lightskyblue", "blueviolet", "darkred", "darkmagenta", "saddlebrown", "darkseagreen", "lightgreen", "mediumpurple", "darkviolet", "palegreen", "darkorchid", "yellowgreen", "sienna", "brown", "darkgray", "lightblue", "greenyellow", "paleturquoise", "lightsteelblue", "powderblue", "firebrick", "darkgoldenrod", "mediumorchid", "rosybrown", "darkkhaki", "silver", "mediumvioletred", "indianred", "peru", "chocolate", "tan", "lightgrey", "thistle", "orchid", "goldenrod", "palevioletred", "crimson", "gainsboro", "plum", "burlywood", "lightcyan", "lavender", "darksalmon", "violet", "palegoldenrod", "lightcoral", "khaki", "aliceblue", "honeydew", "azure", "sandybrown", "wheat", "beige", "whitesmoke", "mintcream", "ghostwhite", "salmon", "antiquewhite", "linen", "lightgoldenrodyellow", "oldlace", "red", "fuchsia", "magenta", "deeppink", "orangered", "tomato", "hotpink", "coral", "darkorange", "lightsalmon", "orange", "lightpink", "pink", "gold", "peachpuff", "navajowhite", "moccasin", "bisque", "mistyrose", "blanchedalmond", "papayawhip", "lavenderblush", "seashell", "cornsilk", "lemonchiffon", "floralwhite", "snow", "yellow", "lightyellow", "ivory", "white", "activeborder", "activecaption", "appworkspace", "background", "buttonface", "buttonhighlight", "buttonshadow", "buttontext", "captiontext", "graytext", "highlight", "highlighttext", "inactiveborder", "inactivecaption", "inactivecaptiontext", "infobackground", "infotext", "menu", "menutext", "scrollbar", "threeddarkshadow", "threedface", "threedhighlight", "threedlightshadow", "threedshadow", "window", "windowframe", "windowtext"
				);
			if (!in_array(strtolower($val), $colors)) {
				$this->_err[$this->_key]["color"] = TRUE;
			}
		}
		
		return NULL;
	}
	// }}}
	
	// {{{ _checkRegex()
	function _checkRegex($val)
	{
		$regex = $this->_param;
		if (!preg_match($regex, $val)) {
			$this->_err[$this->_key]["regex"] = TRUE;
		}
		
		return NULL;
	}
	// }}}
	
	// {{{ _checkSwitch()
	function _checkSwitch($val)
	{
		$case = $this->_param;
		
		foreach (array_keys($case) as $key) {
			if ($val == $key) {
				$IC  = &new HTML_inputCheck($case[$key]);
				$slt = $IC->check($this->_values);
				if (!empty($slt) && is_array($slt)) {
					$this->_err = array_merge_recursive($this->_err, $slt);
				}
			}
		}
		
		return NULL;
	}
	// }}}
	
	// {{{ _checkIfnull
	function _checkIfnull($val)
	{
		$check = $this->_param;
		$key   = (bool)(!strlen($val));
		
		if (isset($check[$key]) && count($check[$key]) > 0) {
			$IC  = &new HTML_inputCheck($check[$key]);
			$slt = $IC->check($this->_values);
			if (!empty($slt) && is_array($slt)) {
				$this->_err = array_merge_recursive($this->_err, $slt);
			}
		}
		
		return NULL;
	}
	// }}}
	
	// {{{ _checkTimestamp()
	function _checkTimestamp($val)
	{
		if (strtotime($val) < 0) {
			$this->_err[$this->_key]["timestamp"] = TRUE;
		}
		return NULL;
	}
	// }}}
	
	// {{{ _checkEmail()
	function _checkEmail($val)
	{
		$regex = $this->_getMailAddressRegexPattern();
		if (!preg_match("/^{$regex}$/", $val)) {
			$this->_err[$this->_key]["email"] = TRUE;
		}
		return NULL;
	}
	// }}}
	
	// {{{ _checkUrl
	function _checkUrl($val)
	{
		//$regex = $this->_getHttpUrlRegexPattern();		
		if (!preg_match("/http(s|):\/\/(www.|)[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+/", $val)) {
			$this->_err[$this->_key]["url"] = TRUE;
		}
		return NULL;
	}
	// }}}
	
	// {{{ _checkEqual()
	function _checkEqual($val)
	{
		if ($val != $this->_values[$this->_param]) {
			$this->_err[$this->_key]["equal"] = TRUE;
		}
		return NULL;
	}
	
	// {{{ _checkUniqueindb()
	function _checkUniqueindb($val)
	{
		$db     = &$this->_param[0];
		$table  = $this->_param[1];
		$column = $this->_param[2];
		$slt    = $db->select("SELECT COUNT(*) AS c FROM {$table} WHERE {$column} = '{$val}'");
		if ($slt[0]['c']) {
			$this->_err[$this->_key]["uniqueInDB"] = TRUE;
		}
	}
	// }}}
	
	// {{{ _getMailAddressRegexPattern
	function _getMailAddressRegexPattern()
	{
		// $a = '[^\x00-\x20"()<>\[\]\\@,.:;\x80-\xFF]';
		// ���ӤΥᥢ�ɤ�"."�ޤळ�Ȥ����Ĥ���Ƥ���Τǡ������ʤ��б�
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
		
		return "https?:\\/\\/{$host}(?::{$port})?(?:{$abs_path}(?:\\?{$query})?)?";
	}
	// }}}
}
// }}}
?>
