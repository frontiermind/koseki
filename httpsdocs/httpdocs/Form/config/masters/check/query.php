<?php
/**
 *
 * Coolie アンケートシステム - フォーム編集画面 入力チェック配列
 * 
 * @access		public
 * @create		2004/06/04
 * @version		$Id: query.php,v 1.5 2004/08/18 13:28:28 tsukasa Exp $
 * @package 	Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 **/

return array(
	"no"									=> array("empty" => NULL, "integer" => array(1)),
	// "qtype"							 => array(),
	"answer_list" 				=> array("empty" => NULL),
	"position"						=> array("empty" => NULL, "regex" => '/^(left|center|right)$/'),
	"query" 							=> array("empty" => NULL),
	"min_limit" 					=> array("ifnull" => array(FALSE => array("min_limit" => array("integer" => array(0))))),
	"max_limit" 					=> array("ifnull" => array(FALSE => array("max_limit" => array("integer" => array(1))))),
	"selected"						=> array(),
	"input_cols"					=> array("empty" => NULL, "integer" => array(1)),
	"input_rows"					=> array("empty" => NULL, "integer" => array(1)),
	"input_type"					=> array(
		"switch" => array(
			4 => array(
				"input_regex" 				 => array("empty" => NULL)
				)
			)
		),
	"precomment"					=> array(),
	"appcomment"					=> array(),
	"required"						=> array(
		"switch" => array(
			1 => array(
				"required_text" 			=> array("empty" => NULL),
				"required_text_color" => array("empty" => NULL, "color" => NULL),
				"required_text_size"	=> array("empty" => NULL, "numeric" => array(1))
				)
			)
		),
	"send_mail_address" 	=> array()
	);

?>
