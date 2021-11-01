<?php
// {{{ _mb_convert_variables()
function _mb_convert_variables($inner, $input, &$value)
{
	// ����������ʤ�Ƶ�
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
