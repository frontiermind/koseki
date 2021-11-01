<?php
/**
 * HTML_inputCheckクラス
 *
 * フォーム入力値のチェックを行う
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
	
	/** デフォルトチェック配列 */
	var $_checker = array();
	
	/** カレントパラメータ */
	var $_param   = NULL;
	
	/** カレントキー */
	var $_key     = NULL;
	
	/** エラー配列 */
	var $_err     = NULL;
	
	/** チェック対象入力配列 */
	var $_values  = NULL;
	
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * コンストラクタ
	 *
	 * @param			array			$checker			チェック配列
	 *
	 * ※チェック配列仕様※
	 * // 入力キー(各input/select/textareaのname属性)をキーとする連想配列です。
	 * array(
	 *   キー => array(
	 *     // 入力値の長さが1Byte以上であるかどうかをチェックします。要素は何でもOK。
	 *     "empty"     => NULL,
	 *
	 *     // 入力文字列の長さが<min>以上<max>以下であるかどうかをチェックします。
	 *     // <min>, <max>の片方を省略可能。
	 *     // 省略された場合はチェックを行いません。
	 *     "length"    => array(0 => <min>, 1 => <max>),
	 *
	 *     // 入力値が数値であり、<min>以上<max>以下であるかどうかをチェックします。
	 *     // <min>, <max>の片方、または双方を省略可能。
	 *     // 省略された場合は最低値・最大値チェックを行いません。
	 *     "numeric"   => array(0 => <min>, 1 => <max>),
	 *
	 *     // 入力値が整数値であり、<min>以上<max>以下であるかどうかをチェックします。
	 *     // <min>, <max>の片方、または双方を省略可能。
	 *     // 省略された場合は最低値・最大値チェックを行いません。
	 *     "integer"   => array(0 => <min>, 1 => <max>),
	 *
	 *     // 入力値にHTMLタグが含まれていないかどうかをチェックします。
	 *     // ただし要素の配列に含まれるタグは許可されます。
	 *     "notHTML"   => array("a", "b", "span", ...)
	 *
	 *     // 入力値がHTMLブラウザが識別可能な色コード・色名であるかどうかをチェックします。
	 *     // 要素はなんでもOK。
	 *     "color"     => NULL,
	 *
	 *     // 入力値が要素で指定された正規表現にマッチするかどうかをチェックします。
	 *     "regex"     => '<preg関数タイプ正規表現>',
	 *
	 *     // 入力値が<x>である時に、<チェック配列>に基づいて新たな入力値チェックを行います。
	 *     // 入力値にマッチするキーがない場合は、なにも行いません。
	 *     "switch"    => array(<x> => <チェック配列>, ...),
	 *
	 *     // 入力値が空の時に<チェック配列A>に基づいて新たな入力値チェックを行い、
	 *     // 空でない時には<チェック配列B>に基づいて新たな入力値チェックを行います。
	 *     // どちらか一方を省略することが可能です。
	 *     // 省略された場合は新たな入力値チェックは行いません。
	 *     "ifnull"    => array(TRUE => <チェック配列A>, FALSE => <チェック配列B>)
	 *
	 *     // 入力値がPHPのstrtotimeで識別可能な日時文字列であるかどうかをチェックします。
	 *     "timestamp" => NULL,
	 *
	 *     // 入力値が正しいメールアドレスであるかどうかをチェックします。
	 *     "email"     => NULL,
	 *
	 *     // 入力値が正しいURL(HTTP)であるかどうかをチェックします。
	 *     "url"       => NULL,
	 *
	 *     // 入力値が指定したキーの値と同じかどうかをチェックします。
	 *     "equal"     => '<チェック対象キー>',
	 *     
	 *     // 入力値が指定したDBクラスで接続されたDB内の特定のテーブル.カラムにおいて
	 *     // ユニークであるかどうかをチェックします。
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
	 * 入力値チェックの実行
	 *
	 * @access		public
	 * @param			array			$values				チェック対象の値
	 * @return		array			チェック結果配列 array("キー" => array("empty" => TRUE, "timestamp" => TRUE, ...), ...)
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
		// 携帯のメアドは"."含むことが許可されているので、仕方なく対応
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
