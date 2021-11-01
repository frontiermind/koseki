<?php
/**
 *
 * Coolie アンケートシステム - フォーム編集画面 入力チェック配列
 * 
 * @access		public
 * @create		2004/06/04
 * @version		$Id: form.php,v 1.7 2004/08/20 06:47:49 tsukasa Exp $
 * @package 	Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 **/

return array(
	"title" 								 => array("empty" => NULL, "notHTML" => NULL),
	"start_stamp" 					 => array("empty" => NULL, "timestamp" => NULL),
	"end_stamp" 						 => array("empty" => NULL, "timestamp" => NULL),
	"name"									 => array("empty" => NULL),
	"name_size" 						 => array("empty" => NULL, "numeric" => NULL),
	"name_color"						 => array("empty" => NULL, "color" => NULL),
	"name_img"							 => array("empty" => NULL, "numeric" => NULL),
	"comment" 							 => array("empty" => NULL),
	"comment_size"					 => array("empty" => NULL, "numeric" => NULL),
	"comment_color" 				 => array("empty" => NULL, "color" => NULL),
	"comment_position"			 => array("empty" => NULL, "regex" => "/^left|center|right$/"),
	"submit"								 => array("empty" => NULL, "numeric" => NULL),
	"submit_img"						 => array("empty" => NULL, "numeric" => NULL),
	"submit_caption"				 => array("empty" => NULL, "notHTML" => NULL),
	"return"								 => array(
		"empty" => NULL,
		"numeric" => NULL,
		"switch" => array(
			1 => array(
				"return_caption" => array("empty" => NULL, "notHTML" => NULL),
				"return_url"		 => array("empty" => NULL, "url" => NULL)
				),
			2 => array(
				"return_caption" => array("empty" => NULL, "notHTML" => NULL),
				"return_url"		 => array("empty" => NULL, "url" => NULL),
				"return_img"		 => array("empty" => NULL, "regex" => '/^[1-3]$/')
				)
			)
		),
	"send_mail" 						 => array(
		"empty" 	=> NULL,
		"numeric" => NULL,
		"switch"	=> array(
			1 => array(
				"mail_subject"					 => array("empty" => NULL, "notHTML" => NULL),
				"mail_body" 						 => array("empty" => NULL, "notHTML" => NULL),
				"mail_from_name"				 => array("empty" => NULL, "notHTML" => NULL),
				"mail_from_address" 		 => array("empty" => NULL, "email" => NULL)
				),
			3 => array(
				"mail_subject"					 => array("empty" => NULL, "notHTML" => NULL),
				"mail_body" 						 => array("empty" => NULL, "notHTML" => NULL),
				"mail_from_name"				 => array("empty" => NULL, "notHTML" => NULL),
				"mail_from_address" 		 => array("empty" => NULL, "email" => NULL)
				)
			)
		),
	
	"body_color"						 => array("empty" => NULL, "color" => NULL),
	"body_fontsize" 				 => array("empty" => NULL, "numeric" => NULL),
	"body_bgcolor"					 => array("empty" => NULL, "color" => NULL),
	"body_background_fixed"  => array(),
	"query_no"							 => array(),
	"query_no_size" 				 => array("empty" => NULL, "numeric" => NULL),
	"query_no_color"				 => array("empty" => NULL, "color" => NULL),
	"query_no_bgcolor"			 => array("empty" => NULL, "color" => NULL),
	"query_no_pretext"			 => array(
		"ifnull" => array(
			FALSE => array(
				"query_no_pretext_size"  => array("empty" => NULL, "numeric" => NULL),
				"query_no_pretext_color" => array("empty" => NULL, "color" => NULL)
				)
			)
		),
	"query_size"						 => array("empty" => NULL, "numeric" => NULL),
	"query_color" 					 => array("empty" => NULL, "color" => NULL),
	"query_bgcolor" 				 => array("empty" => NULL, "color" => NULL),
	"input_size"						 => array("empty" => NULL, "numeric" => NULL),
	"input_color" 					 => array("empty" => NULL, "color" => NULL),
	"input_bgcolor" 				 => array("empty" => NULL, "color" => NULL),
	"precomment_size" 			 => array("empty" => NULL, "numeric" => NULL),
	"precomment_color"			 => array("empty" => NULL, "color" => NULL),
	"appcomment_size" 			 => array("empty" => NULL, "numeric" => NULL),
	"appcomment_color"			 => array("empty" => NULL, "color" => NULL)
	);

?>