<?php
/**
 * File_upload���饹
 *
 * �ե�����Υ��åץ��ɽ�����ʷ�ˤ��륯�饹
 *
 * @access		public
 * @author		Shogo Kawase <sho@drive.co.jp>
 * @create		2003/04/28
 * @version		$Id: upload.php,v 1.1 2004/06/11 05:26:35 shogo Exp $
 * @copyright	studio fly.net
 *
 **/

class File_upload
{
	/** ���Ĥ����ĥ�Ҥ�����. ��˾�ʸ���ǻ���. NULL�ξ��Ϥ��٤Ƶ��� */
	var $accept_ext = NULL;

	/** ���Ĥ���MIME�����פ�����. NULL�ξ��Ϥ��٤Ƶ��� */
	var $accept_type = NULL;

	/** ���Ĥ���ե����륵���� (Byte). NULL�ξ������¤ʤ� */
	var $accept_size = NULL;
	
	/**
	 *
	 * ���åץ��ɤ��줿�ե���������ե�����̾����¸
	 *
	 * @access		public
	 *
	 * @param			string		$file					��¸�оݥե������̾��(�ե������name°��).
	 * @param			string		$path					��¸��ѥ�(â������ĥ�Ҥ����)
	 * @param			int				$permission		��¸����ե�����Υѡ��ߥå����
	 * @param			bool			$useOrgName		���ꥸ�ʥ�Υե�����̾��ѥ����ղä���ʤ�TRUE
	 * @param			bool			$extToLower		��ĥ�Ҥ��˾�ʸ���ǽ�������ʤ�TRUE
	 *
	 * @return		string		��¸�����ե�����̾. ���Ԥ�������NULL.
	 *
	 */
	function save($file, $path, $permission = 0655, $useOrgName = FALSE, $extToLower = FALSE)
	{
		if (empty($_FILES[$file])) {
			return NULL;
		}
		$file = &$_FILES[$file];

		// ��ĥ�Ҥ����
		preg_match('/^(.+?)\.([^.]+)$/i', $file['name'], $match);
		$pre = $match[1];
		$ext = $match[2];
		if ($extToLower) {
			$ext = strtolower($ext);
		}
		
		// ���顼�����å�
		if ($file['error']) {
			return NULL;
		}
		
		// ���¥����å�
		if (($this->accept_ext !== NULL) && !in_array(strtolower($ext), $this->accept_ext)) {
			return NULL;
		}
		if (($this->accept_type !== NULL) && !in_array($file['type'], $this->accept_type)) {
			return NULL;
		}
		if (($this->accept_size !== NULL) && $this->accept_size < $file['size']) {
			return NULL;
		}
		
		if ($usrOrgName) {
			$dist_file = "{$path}/{$pre}.{$ext}";
		} else {
			$dist_file = "{$path}.{$ext}";
		}

		// �ե�����Υ��ԡ�
		if (!@copy($file['tmp_name'], $dist_file)) {
			return NULL;
		}

		// �ѡ��ߥå�����ѹ�
		if (!@chmod($dist_file, $permission)) {
			return NULL;
		}

		return $dist_file;
	}

}

?>
