<?php
/**
 * DB_sqlite���饹
 *
 * SQLite�ѥ��ͥ�����󥯥饹
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
	 * ���󥹥ȥ饯��
	 */
	function DB_sqlite()
	{
		
	}

	/**
	 *
	 * sqlite�ե�����򳫤�
	 *
	 * @access	private
	 *
	 * @param		string	$host			SQL�����С��Υ��ɥ쥹
	 * @param		string	$dbname		���Ѥ���ǡ����١���̾
	 * @param		string	$user			�桼����̾
	 * @param		string	$pass			�ѥ����
	 * @param		int			$port			�ݡ���
	 *
	 * @return	boolean	��³�����������TRUE
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
		
		// �ؿ�������
		sqlite_create_function($this->link, "now",         "_DB_sqlite_now",         0);
		sqlite_create_function($this->link, "date_format", "_DB_sqlite_date_format", 2);
		sqlite_create_function($this->link, "dayofmonth",  "_DB_sqlite_dayofmonth",  1);
		sqlite_create_function($this->link, "hour",        "_DB_sqlite_hour",        1);
		
		return TRUE;
	}

	/**
	 *
	 * SELECTʸ��ȯ�Ԥ�����̤�������֤��ޤ���
	 *
	 * @access	public
	 *
	 * @param		string	$query				�¹�ʸ
	 * @paran		int			$result_type	��̤μ�����ˡ(SQLITE_BOTH / SQLITE_NUM / SQLITE_ASSOC)
	 *
	 * @return	array		�������
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
	 * UPDATEʸ��ȯ�Ԥ���
	 *
	 * @access	public
	 *
	 * @param		string	$query				�¹�ʸ
	 * @param		bool		$get_rows			�ѹ����줿�Կ�������������TRUE(default:FALSE)
	 *
	 * @return	mixed		���������TRUE / $get_rows��TRUE�λ����ѹ����줿�Կ�
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
	 * INSERTʸ��ȯ�Ԥ���
	 *
	 * @access	public
	 *
	 * @param		string	$query				�¹�ʸ
	 * @param		bool		$get_rows			�ѹ����줿�Կ�������������TRUE(default:FALSE)
	 *
	 * @return	mixed		���������TRUE / $get_rows��TRUE�λ����������줿�Կ�
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
	 * DELETEʸ��ȯ�Ԥ���
	 *
	 * @access	public
	 *
	 * @param		string	$query				�¹�ʸ
	 * @param		bool		$get_rows			�ѹ����줿�Կ�������������TRUE(default:FALSE)
	 *
	 * @return	mixed		���������TRUE / $get_rows��TRUE�λ��Ϻ�����줿�Կ�
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
	 * @return	bool		���������TRUE
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
	 * @return	bool		���������TRUE
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
	 * @return	bool		���������TRUE
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
	 * (����)�������󥹤κ���
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			��������̾
	 * @param		int			$minvalue			�Ǿ���
	 * @param		int			$maxvalue			������
	 * @param		int			$increment		�����̡������ͤϾ���Υ������󥹡�����ͤϹ߽�Υ������󥹤����
	 * @param		int     $start				������
	 * @param		bool		$cycle				�����ͤ�Ķ�����������󥹤�Ǿ��ͤ˴����᤹����TRUE
	 *
	 * @return	bool		���������TRUE
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

		// ���η���
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
	 * (����)�������󥹤��˴�
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			��������̾
	 *
	 * @return	bool		������������TRUE
	 *
	 */
	function drop_sequence($seqname)
	{
		return $this->query("DROP TABLE {$seqname};");
	}

	/**
	 *
	 * (����)�������󥹤��鼡���ͤ����
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			��������̾
	 *
	 * @return	mixed		�������󥹤��֤����� / ���Ԥ�������FALSE
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
	 * (����)�������󥹤��顢�ץ������ľ����nextval���֤��줿�ͤ����
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			��������̾
	 *
	 * @return	int			�������󥹤��֤�����
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
	 * (����)�������󥹤��ͤ����ꤹ��
	 *
	 * @access	public
	 *
	 * @param		string	$seqname			��������̾
	 * @param		int			$val					������
	 * @param		bool		$is_called		is_called�ե饰��������
	 *
	 * @return	bool		������������TRUE
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
	 * sqlite�������ȯ��
	 *
	 * @access	public
	 *
	 * @param		string	$query					�¹�ʸ
	 * @param		bool		$through_errors	sqlite���顼��̵�뤹�����TRUE
	 *
	 * @return	mixed		sqlite_query()�������
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
	 * SQLʸ�˻Ȥ���褦��ʸ����򥨥�������
	 *
	 * @access	public
	 *
	 * @param	string	$input					�����о�ʸ����
	 *
	 * @return	string	���������פ��줿ʸ����
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