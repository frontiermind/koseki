<?php
/**
 * File_IOクラス
 *
 * ファイルの入出力を簡略化するためのクラス
 *
 * @access		public
 * @create		2003/11/26
 * @version		$Id: IO.php,v 1.1 2004/06/11 05:26:35 shogo Exp $
 * @package   SPEAR.File
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	studio fly.net
 *
 **/

// {{{ class File_IO
class File_IO
{
	// {{{ read()
	/**
	 *
	 * ファイルを読み込む
	 *
	 * @access	public
	 * @param		string		$file					読み込み対象ファイルのパス
	 * @param		int				$offset				読み込み開始位置
	 * @param		int				$length				読み込みサイズ
	 * @return	string		読み込んだファイルの内容
	 *
	 */
	function &read($file, $offset = NULL, $length = NULL)
	{
		if (function_exists("file_get_contents")) {
			$result = file_get_contents($file);
		} else {
			$result = implode('', file($file));
		}
		if ($offset !== NULL) {
			if ($length !== NULL) {
				$result = substr($result, $offset, $length);
			} else {
				$result = substr($result, $offset);
			}
		}
		return $result;
	}
	// }}}
	
	// {{{ readCsv()
	/**
	 *
	 * ファイルを読み込み、CSVフィールドを処理する
	 *
	 * @access	public
	 * @param		string		$file					読み込み対象ファイルのパス
	 * @param		string		$delimiter		区切文字
	 * @param		int				$offset				読み込み開始行
	 * @param		int				$limit				読み込み最大行
	 * @return	array			読み込んだCSVファイルの内容
	 *
	 */
	function readCsv($file, $delimiter = ',', $offset = 0, $limit = 0)
	{
		$tmp = file($file);
		$len = max(array_map("strlen", $tmp));
		$fp  = fopen($file, "r");
		if ($fp === FALSE) {
			return NULL;
		}
		$slt = array();
		$i   = 0;
		while (($row = fgetcsv($fp, $len, $delimiter)) && (!$limit || $offset + $limit > $i)) {
			if ($offset <= $i) {
				$slt[] = $row;
			}
			++$i;
		}
		fclose($fp);
		
		return $slt;
	}
	// }}}
	
	// {{{ write()
	/**
	 *
	 * ファイルへの出力
	 *
	 * @access	public
	 * @param		string		$file					書き込み対象ファイルのパス
	 * @param		string		$data					書き込む内容
	 * @param		bool			$add					追記モードで書き込む場合はTRUE
	 * @param		bool			$binary				改行コードの自動変換を許可する場合はFALSE
	 * @return	int				書き込んだByte数、失敗した場合はFALSE
	 *
	 */
	function write($file, $data, $add = FALSE, $binary = TRUE)
	{
		$fp  = fopen($file, ($add ? "a" : "w") . ($binary ? "b" : ""));
		$slt = fwrite($fp, $data);
		fclose($fp);
		return $slt;
	}
	// }}}
	
	// {{{ writeCSV
	/**
	 *
	 * 配列をCSVファイルとして保存する
	 *
	 * @access	public
	 * @param		string		$file					書き込み対象ファイルのパス
	 * @param		array			$data					書き込む内容
	 * @param		string		$delimiter		区切文字
	 * @param		bool			$add					追記モードで書き込む場合はTRUE
	 * @return	int				書き込んだByte数、失敗した場合はFALSE
	 *
	 */
	function writeCSV($file, $data, $delimiter = ',', $add = FALSE)
	{
		$result = array();
		if (!empty($data) && is_array($data)) {
			foreach ($data as $row) {
				$tmp = array();
				foreach ($row as $col) {
					if (!empty($col) && is_numeric($col)) {
						$tmp[] = $col;
					} else {
						$tmp[] = '"' . str_replace('"', '\\"', $col) . '"';
					}
				}
				$result[] = implode($delimiter, $tmp);
			}
			$result = implode("\n", $result);
		}
		return File_IO::write($file, $result, $add);
	}
	// }}}
	
	// {{{ truncate()
	/**
	 *
	 * ファイルを指定サイズに丸める
	 *
	 * @access	public
	 * @param		string		$file					丸めるファイルのパス
	 * @param		string		$size					指定サイズ
	 * @return	bool			成功すればTRUE
	 *
	 */
	function truncate($file, $size)
	{
		$fp  = fopen($file, "wb");
		$slt = ftruncate($fp, $size);
		fclose($fp);
		return $slt;
	}
	// }}}
}
// }}}

?>