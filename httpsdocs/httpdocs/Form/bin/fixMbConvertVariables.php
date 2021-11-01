<?php
// {{{ _mb_convert_variables()
function _mb_convert_variables($inner, $input, &$value)
{
	// 引数が配列なら再帰
	if(is_array($value)) {
		foreach ($value as $k => $v) {
			_mb_convert_variables($inner, $input, $value[$k]);
		}
	} else {

		$value = mb_convert_encoding($value, $inner, $input);
	}
	return NULL;
}
// }}}
?>
