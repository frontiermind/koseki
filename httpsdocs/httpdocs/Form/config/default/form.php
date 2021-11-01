<?php
/**
 *
 * Coolie アンケートシステム - 新規フォーム基本値
 * 
 * @access		public
 * @create		2004/06/04
 * @version		$Id: form.php,v 1.13 2005/01/11 06:36:38 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 */

$values = array(
	// "id"                     => NULL,
	// "type"                   => NULL,
	
	"title"                  => "（名称未設定）",
	"start_stamp"            => "2004-01-01 00:00:00",
	"end_stamp"              => "2037-12-31 23:59:59",
	"name"                   => "表題を入力してください",
	"name_size"              => 12,
	"name_color"             => "#000000",
	"name_img"               => 0,
	"comment"                => "コメントを入力してください",
	"comment_size"           => 10,
	"comment_color"          => "#000000",
	"comment_position"       => "center",
	"submit"                 => 1,
	"submit_img"             => 0,
	"submit_caption"         => "送信",
	"return"                 => 0,
	"return_caption"         => "戻る",
	"return_img"             => 0,
	"return_url"             => "",
	"send_mail"              => 0,
	"mail_subject"           => "",
	"mail_body"              => "",
	"mail_from_name"         => "",
	"mail_from_address"      => "",
	"confirm"                => 1,
	
	"body_color"             => "#000000",
	"body_fontsize"          => 10,
	"body_bgcolor"           => "#ffffff",
	"body_background"        => 0,
	"body_background_fixed"  => 0,
	"query_no"               => 1,
	"query_no_size"          => 10,
	"query_no_color"         => "#696969",
	"query_no_bgcolor"       => "#d3d3d3",
	"query_no_img"           => 0,
	"query_no_pretext"       => "質問",
	"query_no_pretext_size"  => 10,
	"query_no_pretext_color" => "#000000",
	"query_size"             => 10,
	"query_color"            => "#a9a9a9",
	"query_bgcolor"          => "#000000",
	"input_size"             => 10,
	"input_color"            => "#ffffff",
	"input_bgcolor"          => "#808080",
	"precomment_size"        => 10,
	"precomment_color"       => "#ffffff",
	"appcomment_size"        => 10,
	"appcomment_color"       => "#ffffff"
	);

// SQL文生成

extract($values, EXTR_SKIP);

$sql = "INSERT INTO forms
  SELECT
      CASE WHEN MAX(id) > 0 THEN MAX(id) + 1 ELSE 1 END AS id,
      '{$type}' AS type,
      '{$title}' AS title,
      '{$start_stamp}' AS start_stamp,
      '{$end_stamp}' AS end_stamp,
      '{$name}' AS name,
      '{$name_size}' AS name_size,
      '{$name_color}' AS name_color,
      '{$name_img}' AS name_img,
      '{$comment}' AS comment,
      '{$comment_size}' AS comment_size,
      '{$comment_color}' AS comment_color,
      '{$comment_position}' AS comment_position,
      '{$submit}' AS submit,
      '{$submit_img}' AS submit_img,
      '{$submit_caption}' AS submit_caption,
      '{$return}' AS return,
      '{$return_caption}' AS return_caption,
      '{$return_img}' AS return_img,
      '{$return_url}' AS return_url,
      '{$send_mail}' AS send_mail,
      '{$mail_subject}' AS mail_subject,
      '{$mail_body}' AS mail_body,
      '{$mail_from_name}' AS mail_from_name,
      '{$mail_from_address}' AS mail_from_address,
      '{$confirm}' AS confirm,
      
      '{$body_color}' AS body_color,
      '{$body_fontsize}' AS body_fontsize,
      '{$body_bgcolor}' AS body_bgcolor,
      '{$body_background}' AS body_background,
      '{$body_background_fixed}' AS body_background_fixed,
      '{$query_no}' AS query_no,
      '{$query_no_size}' AS query_no_size,
      '{$query_no_color}' AS query_no_color,
      '{$query_no_bgcolor}' AS query_no_bgcolor,
      '{$query_no_img}' AS query_no_img,
      '{$query_no_pretext}' AS query_no_pretext,
      '{$query_no_pretext_size}' AS query_no_pretext_size,
      '{$query_no_pretext_color}' AS query_no_pretext_color,
      '{$query_size}' AS query_size,
      '{$query_color}' AS query_color,
      '{$query_bgcolor}' AS query_bgcolor,
      '{$input_size}' AS input_size,
      '{$input_color}' AS input_color,
      '{$input_bgcolor}' AS input_bgcolor,
      '{$precomment_size}' AS precomment_size,
      '{$precomment_color}' AS precomment_color,
      '{$appcomment_size}' AS appcomment_size,
      '{$appcomment_color}' AS appcomment_color
    FROM forms
;
";

return $sql;
?>
