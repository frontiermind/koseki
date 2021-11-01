<?php
/**
 *
 * templateクラス
 *
 * テキストテンプレート処理クラス
 *
 * @access		public
 * @create		2003/11/14
 * @version		$Id: template.php,v 1.8 2005/02/03 22:01:03 tsukasa Exp $
 * @package   SPEAR.template
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 */
require_once 'FileSystem.php';

class template
{
	// {{{ Public Properties
	
	/** template files dir */
	var $template_dir = "templates";

	/** compiled template files dir */
	var $compile_dir  = "templates/_compiled";

	/** delimiters */
	var $left_delimiter  = "{";
	var $right_delimiter = "}";

	/** file type */
	var $content_type = "text/plane";

	/** compile option */
	var $trim_compile  = FALSE;
	
	/** encoding */
	var $inner_encoding  = "EUC-JP";
	var $output_encoding = "EUC-JP";
	
	// }}}
	
	// {{{ Private Properties
	
	/** assigned values */
	var $_tpl_vars = NULL;

	/** extract flag */
	var $_extract  = FALSE;

	/** compile methods & functions */
	var $compile_functions = array();
	var $_compile_methods = array(
		'_var_prefix',
		'_var_expansion',
		'_struct_expansion',
		'_default_expansion'
		);
	
	/** output methods & functions */
	var $output_functions = array();
	var $_output_methods = array();
	
	/** variable regexp */
	var $_var_regexp = '[a-zA-Z_\x7f-\xff]([a-zA-Z0-9_\->\x7f-\xff]*?(\[["\']*.*?[^\\]["\']*\])*?)*?';
	// PHP Bug #40706対策  $_var_regexp の簡易表現版
	var $_var_regexp_min = '[a-zA-Z][a-zA-Z0-9_\[\]\.\$\']*?';
	
	// }}}
	
	// {{{ assign()
	/**
	 *
	 * 変数の登録
	 *
	 */
	function assign($tpl_var, $value = NULL)
	{
		if (is_array($tpl_var)) {
			if ($this->inner_encoding != $this->output_encoding) {
				// {{{ // PHP Bug #26639対策
				//mb_convert_variables($this->output_encoding, $this->inner_encoding, $tpl_var);
				require_once "fixMbConvertVariables.php";
				_mb_convert_variables($this->output_encoding, $this->inner_encoding, $tpl_var);
				// }}}
			}
			foreach (array_keys($tpl_var) as $key) {
				$val = $tpl_var[$key];
				if (!empty($key) && isset($val)) {
					$this->_tpl_vars[$key] = $val;
				}
			}
		} else {
			if ($this->inner_encoding != $this->output_encoding) {
				// {{{ // PHP Bug #26639対策
				//mb_convert_variables($this->output_encoding, $this->inner_encoding, $value);
				require_once "fixMbConvertVariables.php";
				_mb_convert_variables($this->output_encoding, $this->inner_encoding, $value);
				// }}}
			}
			if (!empty($tpl_var) && isset($value)) {
				$this->_tpl_vars[$tpl_var] = $value;
			}
		}
		$this->_extract = TRUE;

		return NULL;
	}
	// }}}
	
	// {{{ assigned
	/**
	 *
	 * ある変数が登録済みかどうかをチェック
	 *
	 */
	function assigned($name)
	{
		return isset($this->_tpl_vars[$name]);
	}
	// }}}
	
	// {{{ fetch()
	/**
	 *
	 * 処理内容の取得
	 *
	 */
	function fetch($template)
	{
		// compile
		$this->_compile($template);
		
		// extract
		if ($this->_extract && !empty($this->_tpl_vars)) {
			extract($this->_tpl_vars, EXTR_PREFIX_ALL, "tpl");
		}
		
		// start buffering
		ob_start();
		
		// output
		include "{$this->compile_dir}/{$template}";
		
		$result = ob_get_contents();
		ob_end_clean();
		
		if (is_array($this->output_functions) && count($this->output_functions)) {
			foreach ($this->output_functions as $func) {
				$result = $func($result);
			}
		}
		
		if (is_array($this->_output_methods) && count($this->_output_methods)) {
			foreach ($this->_output_methods as $func) {
				$result = $this->$func($result);
			}
		}
		
		return $result;
	}
	// }}}
	
	// {{{ display()
	/**
	 *
	 * 処理内容の表示
	 *
	 */
	function display($template)
	{
		echo $this->fetch($template);
		return NULL;
	}
	// }}}
	
	// {{{ _compile()
	/**
	 *
	 * コンパイル
	 *
	 */
	function _compile($template)
	{
		$tfile = "{$this->template_dir}/{$template}";
		$cfile = "{$this->compile_dir}/{$template}";
		FileSystem::pathCheck($cfile);

		// get file stat
		$tmtime = (int)@filemtime($tfile);
		$cmtime = (int)@filemtime($cfile);

		if (!$tmtime) {
			// Error
			return FALSE;
		}

		// Timestamp Check
		if ($cmtime >= $tmtime) {
			return FALSE;
		}

		// Create
		$tfp = fopen($tfile, "r");
		$cfp = fopen($cfile, "w");
		while ($line = fgets($tfp, 40960)) {
			if ($this->trim_compile) {
				$line = str_replace("\n", "", trim($line));
			}
			foreach ($this->_compile_methods as $func) {
				$line = $this->$func($line);
			}
			if (is_array($this->compile_functions) && count($this->compile_functions)) {
				foreach ($this->compile_functions as $func) {
					$line = $func($line);
				}
			}
			fwrite($cfp, $line);
		}
		fclose($tfp);
		fclose($cfp);
		@chmod($cfile, 0666);
		return TRUE;
	}
	// }}}
	
	// {{ _var_prefix
	/**
	 *
	 * コンパイル用関数 - 変数名接頭辞追加
	 *
	 */
	function _var_prefix($line)
	{
		return preg_replace_callback(
			"/({$this->left_delimiter}.*?)(\\\${$this->_var_regexp}.*?)({$this->right_delimiter})/",
			array(&$this, "_var_prefix_callback"),
			$line
			);
	}
	// }}}
	
	// {{{ _var_prefix_callback
	function _var_prefix_callback($m)
	{
		// PHP Bug #40706対策  $_var_regexp の簡易表現版を使用
//		$rgx = "/(\\\${$this->_var_regexp})\\.([a-zA-Z0-9_]+)([^{$this->right_delimiter}]*)/";
		$rgx = "/(\\\${$this->_var_regexp_min})\\.([a-zA-Z0-9_]+)([^{$this->right_delimiter}]*)/";

		while (preg_match($rgx, $m[2])) {
			$m[2] = preg_replace_callback($rgx, array(&$this, "_var_prefix_callback2"), $m[2]);
		}
		return $m[1] . str_replace('$', '$tpl_', $m[2]) . $m[5];
	}
	// }}}
	
	function _var_prefix_callback2($m)
	{
		// PHP Bug #40706対策 に伴う変更
/*		if ($m[4]{0} == '$' || is_numeric($m[4])) {
			return "{$m[1]}[{$m[4]}]{$m[5]}";
		} else {
			$m[4] = str_replace("'", "\\'", $m[4]);
			return "{$m[1]}['{$m[4]}']{$m[5]}";
		}*/
		if ($m[2]{0} == '$' || is_numeric($m[2])) {
			return "{$m[1]}[{$m[2]}]{$m[3]}";
		} else {
			$m[2] = str_replace("'", "\\'", $m[2]);
			return "{$m[1]}['{$m[2]}']{$m[3]}";
		}
	}

	/**
	 *
	 * コンパイル用関数 - 変数展開
	 *
	 */
	function _var_expansion($line)
	{
		// PHP Bug #40706対策  $_var_regexp の簡易表現版を使用
/*		return preg_replace(
			"/{$this->left_delimiter}(\\\${$this->_var_regexp}){$this->right_delimiter}/",
			"<?=$1?>",
			$line
			);*/
		return preg_replace(
			"/{$this->left_delimiter}(\\\${$this->_var_regexp_min}){$this->right_delimiter}/",
			"<?=$1?>",
			$line
			);
	}

	/**
	 *
	 * コンパイル用関数 - 制御構造解釈
	 *
	 */
	function _struct_expansion($line)
	{
		$ld = $this->left_delimiter;
		$rd = $this->right_delimiter;
		$pattern = array(
			"!{$ld}php{$rd}!",
			"!{$ld}/php{$rd}!",
			"!{$ld}(if|while|for|foreach) *\\((.*?[^\\\\])\\){$rd}!",
			"!{$ld}switch *\\((.*?[^\\\\])\\) (default|case .*?[^\\\\]){$rd}!",
			"!{$ld}(elseif|else) *(\\(.*?[^\\\\]\\))?{$rd}!",
			"!{$ld}(default|case .*?[^\\\\]){$rd}!",
			"!{$ld}/(if|while|for|foreach|switch){$rd}!",
			"!{$ld}/case{$rd}!"
			);
		$replace = array(
			"<?php ",
			" ?>",
			"<?php \\1 (\\2) { ?>",
			"<?php switch (\\1) { \\2: ?>",
			"<?php } \\1 \\2 { ?>",
			"<?php \\1: ?>",
			"<?php } ?>",
			"<?php break; ?>"
			);
		$line = preg_replace($pattern, $replace, $line);
		return $line;
	}

	/**
	 *
	 * コンパイル用関数 - 独自拡張
	 *
	 */
	function _default_expansion($line)
	{
		return preg_replace(
			array(
				"/{$this->left_delimiter}sprintf *\\((.*?[^\\\\])\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}require *\\((.*?[^\\\\])\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}\\*.*?\\*{$this->right_delimiter}/"
				),
			array(
				"<?=sprintf(\$1)?>",
				"<?=\$this->fetch(\$1)?>",
				""
				),
				$line
			);
	}


	// {{{ htmlspecialchars_recursive()
	function htmlspecialchars_recursive(&$value)
	{
		// 引数が配列なら再帰
		if(is_array($value)) {
			foreach ($value as $k => $v) {
				$this->htmlspecialchars_recursive($value[$k]);
			}
		} else {
	
			$value = htmlspecialchars($value);
		}
		return NULL;
	}
	// }}}
}
?>
