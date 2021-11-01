<?php
/**
 * DB_sqliteクラス
 *
 * SQLite用コネクションクラス
 *
 * @access		public
 * @create		2003/02/01
 * @version		$Id: sqlite.php,v 1.7 2004/11/16 06:55:38 tsukasa Exp $
 * @package   SPEAR.DB
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

require_once 'SPEAR.php';

class DB_sqlite
{
	var $link = NULL;
	var $transaction = NULL;

	var $func = NULL;

	var $seq_currval = NULL;

	/**
	 * コンストラクタ
	 */
	function DB_sqlite()
	{
		
	}

	/**
	 *
	 * sqliteファイルを開く
	 *
	 * @access	private
	 *
	 * @param		string	$host			SQLサーバーのアドレス
	 * @param		string	$dbname		使用するデータベース名
	 * @param		string	$user			ユーザー名
	 * @param		string	$pass			パスワード
	 * @param		int			$port			ポート
	 *
	 * @return	boolean	接続に成功すればTRUE
	 *
	 */
	function connect($file)
	{
		//error_log($file);
		if (!$this->link = sqlite_open($file, 0666, $errorMsg)) {
			if (!empty($errorMsg)) {
				SPEAR::raiseError($errorMsg);
			}
			return NULL;
		}
		
		// 関数の設定
		sqlite_create_function($this->link, "now",         "_DB_sqlite_now",         0);
		sqlite_create_function($this->link, "date_format", "_DB_sqlite_date_format", 2);
		sqlite_create_function($this->link, "dayofmonth",  "_DB_sqlite_dayofmonth",  1);
		sqlite_create_function($this->link, "hour",        "_DB_sqlite_hour",        1);
		
		return TRUE;
	}

	/**
	 *
	 * SELECT文を発行し、結果を配列で返します。
	 *
	 * @access	public
	 *
	 * @param		string	$query				実行文
	 * @paran		int			$result_type	結果の取得方法(SQLITE_BOTH / SQLITE_NUM / SQLITE_ASSOC)
	 *
	 * @return	array		結果配列
	 *
	 */
	function select($query, $result_type = SQLITE_ASSOC)
	{
		$rid = $this->query($query);
		if (sqlite_num_rows($rid)) {
			while ($slt = sqlite_fetch_array($rid, $result_type)) {
				$result[] = $slt;
			}
		} else {
			$result = NULL;
		}
		return $result;
	}

	/**
	 *
	 * UPDATE文を発行する
	 *
	 * @access	public
	 *
	 * @param		string	$query				実行文
	 * @param		bool		$get_rows			変更された行数を取得する場合はTRUE(default:FALSE)
	 *
	 * @return	mixed		成功すればTRUE / $get_rowsがTRUEの時は変更された行数
	 *
	 */
	function update($query, $get_rows = FALSE)
	{
		$result = $this->query($query);
		if ($get_rows && $result) {
			$result = sqlite_affected_rows($this->link);
		}
		return $result;
	}

	/**
	 *
	 * INSERT文を発行する
	 *
	 * @access	public
	 *
	 * @param		string	$query				実行文
	 * @param		bool		$get_rows			変更された行数を取得する場合はTRUE(default:FALSE)
	 *
	 * @return	mixed		成功すればTRUE / $get_rowsがTRUEの時は挿入された行数
	 *
	 */
	function insert($query, $get_rows = FALSE)
	{
		$result = $this->query($query);
		if ($get_rows && $result) {
			$result = sqlite_affected_rows($this->link);
		}
		return $result;
	}

	/**
	 *
	 * DELETE文を発行する
	 *
	 * @access	public
	 *
	 * @param		string	$query				実行文
	 * @param		bool		$get_rows			変更された行数を取得する場合はTRUE(default:FALSE)
	 *
	 * @return	mixed		成功すればTRUE / $get_rowsがTRUEの時は削除された行数
	 *
	 */
	function delete($query, $get_rows = FALSE)
	{
		$result = $this->query($query);
		if ($get_rows && $result) {
			$result = sqlite_affected_rows($this->link);
		}
		return $result;
	}

	/**
	 * TRANSACTION - BEGIN
	 *
	 * @access	public
	 *
	 * @return	bool		成功すればTRUE
	 *
	 */
	function begin()
	{
		if (!$this->transaction) {
			return $this->transaction = $this->query("BEGIN;");
		}
		return NULL;
	}

	/**
	 * TRANSACTION - COMMIT
	 *
	 * @access	public
	 *
	 * @return	bool		成功すればTRUE
	 *
	 */
	function commit()
	{
		if ($this->transaction) {
			if ($this->query("COMMIT;")) {
				$this->transaction = FALSE;
				return TRUE;
			}
		}
		return NULL;
	}

	/**
	 * TRANSACTION - ROLLBACK
	 *
	 * @access	public
	 *
	 * @return	bool		成功すればTRUE
	 *
	 */
	function rollback()
	{
		if ($this->transaction) {
			if ($this->query("ROLLBACK;")) {
				$this->transaction = FALSE;
				return TRUE;
			}
		}
		return NULL;
	}

	/**
	 *
	 * (仮想)シーケンスの作成
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			シーケンス名
	 * @param		int			$minvalue			最小値
	 * @param		int			$maxvalue			最大値
	 * @param		int			$increment		増加量。正の値は昇順のシーケンス、負の値は降順のシーケンスを作成
	 * @param		int     $start				開始値
	 * @param		bool		$cycle				最大値を超えたシーケンスを最小値に巻き戻す場合にTRUE
	 *
	 * @return	bool		成功すればTRUE
	 *
	 */
	function create_sequence($seqname, $minvalue = NULL, $maxvalue = NULL, $increment = 1, $start = NULL, $cycle = FALSE)
	{
		// ASC or DESC
		$asc = (bool)($increment > 0);

		// Default values
		if ($minvalue === NULL) {
			$minvalue = $asc ? "1" : "-9223372036854775799";
		}
		if ($maxvalue === NULL) {
			$maxvalue = $asc ? "9223372036854775799" : "-1";
		}
		if ($start === NULL) {
			$start = $minvalue;
		}

		// Error check
		if ($mivalue >= $maxvalue) {
			SPEAR::raiseError("DefineSequence `{$seqname}': MINVALUE ({$minvalue}) can't be >= MAXVALUE ({$maxvalue})");
		} elseif ($maxvalue < $minvalue) {
			SPEAR::raiseError("DefineSequence `{$seqname}': MAXVALUE ({$maxvalue}) can't be <= MINVALUE ({$minvalue})");
		} elseif ($start < $minvalue) {
			SPEAR::raiseError("DefineSequence `{$seqname}': START ({$start}) can't be < MINVALUE ({$minvalue})");
		} elseif ($start > $maxvalue) {
			SPEAR::raiseError("DefineSequence `{$seqname}': START ({$start}) can't be > MAXVALUE ({$maxvalue})");
		}

		// 型の決定
		if ($CURUNSIGNED = ($start - $increment >= 0 && $minvalue >= 0 ? 'UNSIGNED' : '')) {
			$CURINT = $MAXINT;
		} else {
			$CURINT = ($MININT > $MAXINT ? $MININT : $MAXINT);
		}
		list($MINUNSIGNED, $MAXUNSIGNED) = $UNSIGNED;
		list($MININT, $MAXINT) = $INT;
		$CURINT = $INTs[$CURINT] . "INT";
		$MININT = $INTs[$MININT] . "INT";
		$MAXINT = $INTs[$MAXINT] . "INT";
		// SQL
		$SQL = "CREATE TABLE {$seqname} (" .
		       "last_value   INTEGER $CURUNSIGNED PRIMARY KEY NOT NULL, " .
		       "min_value    INTEGER $MINUNSIGNED NOT NULL, " .
		       "max_value    INTEGER $MAXUNSIGNED NOT NULL, " .
		       "increment_by INTEGER NOT NULL, " .
		       "is_cycle     TINYINT(1) NOT NULL, " .
		       "is_called    TINYINT(1) NOT NULL);";
		if (!$this->query($SQL, TRUE)) {
			return FALSE;
		}
		$cycle = $cycle ? '1' : '0';
		if (!$this->query($SQL = "INSERT INTO {$seqname} VALUES({$start}, {$minvalue}, {$maxvalue}, {$increment}, {$cycle}, 0);")) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 *
	 * (仮想)シーケンスの破棄
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			シーケンス名
	 *
	 * @return	bool		成功した場合にTRUE
	 *
	 */
	function drop_sequence($seqname)
	{
		return $this->query("DROP TABLE {$seqname};");
	}

	/**
	 *
	 * (仮想)シーケンスから次の値を取得
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			シーケンス名
	 *
	 * @return	mixed		シーケンスの返した値 / 失敗した場合にFALSE
	 *
	 */
	function nextval($seqname)
	{
		// Select sequence
		$slt = $this->select("SELECT * FROM {$seqname} LIMIT 1;");
		extract($slt[0]);

		if (!$is_called) {
			do {
				$slt = $this->update("UPDATE {$seqname} SET is_called = 1;", TRUE);
			} while (!$slt);
		} else {
			$NEXT = NULL;
			if ($increment_by < 0 && $last_value <= $min_value) {
				if ($is_cycle) {
					$NEXT = "max_value";
					$last_value = $max_value;
				}
			} elseif ($increment_by > 0 && $last_value >= $max_value) {
				if ($is_cycle) {
					$NEXT = "min_value";
					$last_value = $min_value;
				}
			} else {
				$NEXT = "last_value + increment_by";
				$last_value = $last_value + $increment_by;
			}
			if (!$NEXT) {
				SPEAR::raiseError("Sequence `{$seqname}` is terminated.");
			}

			// Update sequence
			do {
				$slt = $this->update("UPDATE {$seqname} SET last_value = $NEXT;", TRUE);
			} while (!$slt);
		}
		
		return $this->seq_currval[$seqname] = $last_value;
	}

	/**
	 *
	 * (仮想)シーケンスから、プロセス中で直前にnextvalで返された値を取得
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			シーケンス名
	 *
	 * @return	int			シーケンスの返した値
	 *
	 */
	function currval($seqname) {
		if (!isset($this->seq_currval[$seqname])) {
			SPEAR::raiseError("nextval({$seqname}) is not called.");
		}
		return $this->seq_currval[$seqname];
	}

	/**
	 *
	 * (仮想)シーケンスの値を設定する
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			シーケンス名
	 * @param		int			$val					設定値
	 * @param		bool		$is_called		is_calledフラグの設定値
	 *
	 * @return	bool		成功した場合にTRUE
	 *
	 */
	function setval($seqname, $val, $is_called = FALSE)
	{
		// Update sequence
		$is_called = $is_called ? 1 : 0;
		do {
			$slt = $this->update("UPDATE {$seqname} SET last_value = $val, is_called = $is_called;", TRUE);
		} while (!$slt);
		return TRUE;
	}
	
	/**
	 *
	 * sqliteクエリの発行
	 *
	 * @access	public
	 *
	 * @param		string	$query					実行文
	 * @param		bool		$through_errors	sqliteエラーを無視する場合はTRUE
	 *
	 * @return	mixed		sqlite_query()の戻り値
	 *
	 */
	function query($query, $through_errors = FALSE)
	{
		//error_log($query);
		$result = sqlite_query($query, $this->link);
		if (!$through_errors && !$result) {
			// transaction - rollback
			if ($this->transaction) {
				$this->rollback();
			}
			SPEAR::raiseError(
				sqlite_error_string(sqlite_last_error($this->link)) . " in " . nl2br($query),
				E_USER_ERROR,
				2
				);
		}
		return $result;
	}

	/**
	 *
	 * SQL文に使えるように文字列をエスケープ
	 *
	 * @access	public
	 *
	 * @param	string	$input					処理対象文字列
	 *
	 * @return	string	エスケープされた文字列
	 *
	 */
	function escape_string($input)
	{
		return sqlite_escape_string($input);
	}
}


// {{{ Public functions

// {{{ _DB_sqlite_now()
function _DB_sqlite_now()
{
	return date("Y-m-d H:i:s");
}
// }}}

// {{{ _DB_sqlite_date_format()
function _DB_sqlite_date_format($date, $format)
{
	return strftime($format, $date);
}
// }}}

// {{{ _DB_sqlite_dayofmonth
function _DB_sqlite_dayofmonth($date)
{
	return (int)date("d", strtotime($date));
}
// }}}

// {{{ _DB_sqlite_hour
function _DB_sqlite_hour($date)
{
	return (int)date("H", strtotime($date));
}
// }}}

// }}}

?>