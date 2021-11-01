<?php
/**
 * File_IO���饹
 *
 * �ե�����������Ϥ��ά�����뤿��Υ��饹
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
	 * �ե�������ɤ߹���
	 *
	 * @access	public
	 * @param		string		$file					�ɤ߹����оݥե�����Υѥ�
	 * @param		int				$offset				�ɤ߹��߳��ϰ���
	 * @param		int				$length				�ɤ߹��ߥ�����
	 * @return	string		�ɤ߹�����ե����������
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
	 * �ե�������ɤ߹��ߡ�CSV�ե�����ɤ��������
	 *
	 * @access	public
	 * @param		string		$file					�ɤ߹����оݥե�����Υѥ�
	 * @param		string		$delimiter		����ʸ��
	 * @param		int				$offset				�ɤ߹��߳��Ϲ�
	 * @param		int				$limit				�ɤ߹��ߺ����
	 * @return	array			�ɤ߹����CSV�ե����������
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
	 * �ե�����ؤν���
	 *
	 * @access	public
	 * @param		string		$file					�񤭹����оݥե�����Υѥ�
	 * @param		string		$data					�񤭹�������
	 * @param		bool			$add					�ɵ��⡼�ɤǽ񤭹������TRUE
	 * @param		bool			$binary				���ԥ����ɤμ�ư�Ѵ�����Ĥ������FALSE
	 * @return	int				�񤭹����Byte�������Ԥ�������FALSE
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
	 * �����CSV�ե�����Ȥ�����¸����
	 *
	 * @access	public
	 * @param		string		$file					�񤭹����оݥե�����Υѥ�
	 * @param		array			$data					�񤭹�������
	 * @param		string		$delimiter		����ʸ��
	 * @param		bool			$add					�ɵ��⡼�ɤǽ񤭹������TRUE
	 * @return	int				�񤭹����Byte�������Ԥ�������FALSE
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
	 * �ե��������ꥵ�����˴ݤ��
	 *
	 * @access	public
	 * @param		string		$file					�ݤ��ե�����Υѥ�
	 * @param		string		$size					���ꥵ����
	 * @return	bool			���������TRUE
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