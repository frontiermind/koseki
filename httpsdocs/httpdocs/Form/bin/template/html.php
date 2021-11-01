<?php
/**
 * template_mobileHtmlクラス
 *
 * HTMLテンプレート処理クラス
 *
 * @access		public
 * @create		2003/11/14
 * @version		$Id: html.php,v 1.3 2005/02/03 22:01:03 tsukasa Exp $
 * @package   SPEAR.template
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

require_once 'template/template.php';

class template_html extends template
{
	function template_html()
	{
		$this->_compile_methods[] = "_parse_anyModifier";
		$this->_compile_methods[] = "_parse_htmlOptions";
	}

	function _parse_anyModifier($line)
	{
		// PHP Bug #40706対策  $_var_regexp の簡易表現版を使用
/*		return preg_replace(
			array(
				"/{$this->left_delimiter}urlencode\\((\\\${$this->_var_regexp})\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}nl2br\\((\\\${$this->_var_regexp})\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}date_format\\((\\\${$this->_var_regexp}|[0-9]+), *(\\\${$this->_var_regexp}|[\"'].+[\"'])\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}htmlspchars\\((\\\${$this->_var_regexp})\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}htmlspchars_recursive\\((\\\${$this->_var_regexp})\\){$this->right_delimiter}/"
				),
			array(
				"<?=rawurlencode($1)?>",
				"<?=nl2br(preg_replace('/^\r?\n/m','&nbsp;\n',$1))?>",
				"<?=date($4,$1)?>",
				"<?=htmlspecialchars($1)?>",
				"<?=\$this->htmlspecialchars_recursive($1)?>"
				),
			$line
			);*/
		return preg_replace(
			array(
				"/{$this->left_delimiter}urlencode\\((\\\${$this->_var_regexp_min})\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}nl2br\\((\\\${$this->_var_regexp_min})\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}date_format\\((\\\${$this->_var_regexp_min}|[0-9]+), *(\\\${$this->_var_regexp_min}|[\"'].+[\"'])\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}htmlspchars\\((\\\${$this->_var_regexp_min})\\){$this->right_delimiter}/",
				"/{$this->left_delimiter}htmlspchars_recursive\\((\\\${$this->_var_regexp_min})\\){$this->right_delimiter}/"
				),
			array(
				"<?=rawurlencode($1)?>",
				"<?=nl2br(preg_replace('/^\r?\n/m','&nbsp;\n',$1))?>",
				"<?=date($4,$1)?>",
				"<?=htmlspecialchars($1)?>",
				"<?=\$this->htmlspecialchars_recursive($1)?>"
				),
			$line
			);
	}
	
	function _parse_htmlOptions($line)
	{
		$regexp = "/{$this->left_delimiter}html_options \\((\\\${$this->_var_regexp})( selected (.*?)){0,1}\\){$this->right_delimiter}/";
		$line = preg_replace_callback(
			$regexp,
			array(&$this, "_html_options_callback"),
			$line
			);
		return $line;
	}

	function _html_options_callback($match)
	{
		$slt = "<?php foreach ((array){$match[1]} as \$key => \$val) {";
		if ($match[4]) {
			$slt .= "echo \"<option value=\\\"{\$key}\\\"\" . (\$key == {$match[5]} ? ' selected=\"selected\"' : '') . \">{\$val}</option>\";";
		} else {
			$slt .= "echo \"<option value=\\\"{\$key}\\\">{\$val}</option>\";";
		}
		$slt .= "} ?>";
		return $slt;
	}
}

?>