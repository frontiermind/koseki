<?php

/** デストラクタリスト */
$_SPEAR_destructor_object_list = array();

// {{{ Define global constants
/** MB関数用エンコーディングリスト */
define("SPEAR_MB_AUTO", "ASCII,EUC-JP,SJIS,JIS,ISO-2022-JP,UTF-8");
// }}}

// {{{ class SPEAR
class SPEAR
{
	// setDestructor()
	/**
	 *
	 *
	 */
	function setDestructor(&$object)
	{
		global $_SPEAR_destructor_object_list;
		if (!@constant("SPEAR_REGISTERED_DESTRUCTOR")) {
			define("SPEAR_REGISTERED_DESTRUCTOR", TRUE);
			register_shutdown_function("_SPEAR_call_destructors");
		}
		$_SPEAR_destructor_object_list[] = &$object;
	}
	// }}}
	
	// {{{ raiseError()
	/**
	 *
	 *
	 */
	function raiseError($msg, $type = E_USER_ERROR, $depth = 1)
	{
		if (preg_match('/^4\.0/', PHP_VERSION) || version_compare(PHP_VERSION, "4.3.0", "<")) {
			trigger_error("SPEAR's each class::error : {$msg}", $type);
		} else {
			$slt = debug_backtrace();
			trigger_error(
				"{$slt[$depth]['class']}::error in {$slt[$depth]['file']} on line {$slt[$depth]['line']} : {$msg}", $type
				);
		}
	}
	// }}}
}
// }}}


// {{{ _SPEAR_call_destructors()
function _SPEAR_call_destructors()
{
	global $_SPEAR_destructor_object_list;
	if (is_array($_SPEAR_destructor_object_list)) {
		foreach ($_SPEAR_destructor_object_list as $obj) {
			if (is_object($obj)) {
				$method = "_" . get_class($obj);
				$obj->$method();
			}
		}
	}
	return 0;
}
// }}}

?>