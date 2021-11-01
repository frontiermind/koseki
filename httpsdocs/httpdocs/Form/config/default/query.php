<?php
/**
 *
 * Coolie アンケートシステム - 新規質問基本値
 * 
 * @access		public
 * @create		2004/06/04
 * @version		$Id: query.php,v 1.10 2005/02/03 20:25:53 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 */

$values = array(
	// "id"                  => NULL,
	// "no"                  => NULL,
	// "qtype"               => NULL,
	"answer_list"         => "",
	"position"            => "left",
	"query"               => "（質問文未設定）",
	"min_limit"           => 0,
	"max_limit"           => 1,
	"selected"            => "",
	"input_cols"          => 30,
	"input_rows"          => 1,
	"input_type"          => 0,
	"input_regex"          => "",
	"precomment"          => "",
	"appcomment"          => "",
	"required"            => 0,
	"required_text"       => "※必須",
	"required_text_color" => "#FF0000",
	"required_text_size"  => 9,
	"confirm_visible"     => 1,
	"send_mail_address"   => 0
	);

switch ($qtype) {
	case 12:
		$values["max_limit"] = 2;
		break;
	case 13:
		$values["max_limit"] = 3;
		break;
	case 14:
		$values["max_limit"] = 2;
		break;
	case 15:
		$values["max_limit"] = 2;
		break;
	case 16:
		$values["max_limit"] = 3;
		break;
}

// SQL文生成

extract($values, EXTR_SKIP);

$sql = "INSERT INTO queries
  SELECT
      {$id} AS id,
      CASE WHEN MAX(no) > 0 THEN MAX(no) + 1 ELSE 1 END AS no,
      '{$qtype}' AS qtype,
      '{$answer_list}' AS answer_list,
      '{$position}' AS position,
      '{$query}' AS query,
      '{$min_limit}' AS min_limit,
      '{$max_limit}' AS max_limit,
      '{$selected}' AS selected,
      '{$input_cols}' AS input_cols,
      '{$input_rows}' AS input_rows,
      '{$input_type}' AS input_type,
      '{$input_regex}' AS input_regex,
      '{$precomment}' AS precomment,
      '{$appcomment}' AS appcomment,
      '{$required}' AS required,
      '{$required_text}' AS required_text,
      '{$required_text_color}' AS required_text_color,
      '{$required_text_size}' AS required_text_size,
      '{$send_mail_address}' AS send_mail_address
    FROM queries
    WHERE id = {$id}
;
";

return $sql;
?>
