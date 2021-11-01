<?php
/**
 *
 * �ե�����/�ǥ��쥯�ȥ����饹
 *
 * @access		public
 * @author		Shogo Kawase <sho@drive.co.jp>
 * @create		2003/02/28
 * @version		$Id: FileSystem.php,v 1.1 2004/08/12 09:34:22 tsukasa Exp $
 * @copyright	studio fly.net
 *
 **/

class FileSystem
{
	/**
	 *
	 * �ǥ��쥯�ȥ꤫��ե�����������������
	 *
	 * @access		public
	 *
	 * @param			string		$path				�оݥǥ��쥯�ȥ�ѥ�
	 * @param			int				$mode				0 = ���Хѥ����� / 1 = ���Хѥ�����
	 * @param			string		$filemask		�ե�����̾�ޥ���
	 * @param			bool			$reflex			���֥ǥ��쥯�ȥ��Ƶ�Ū�˸�������ʤ�TRUE
	 * @param			string		$dirmask		���֥ǥ��쥯�ȥ�̾�ޥ���
	 * @param			string		$prefix			�Ƶ��������˻���
	 *
	 * @return		array			�ե�����̾����
	 *
	 */
	function readDir($path, $mode = 0, $filemask = '', $reflex = FALSE, $dirmask = '', $prefix = '')
	{
		// ����������
		$path = preg_replace('|/$|', '', $path);
		if ($mode) {
			$prefix = "{$path}/";
		}

		// �ǥ��쥯�ȥ꤫�ɤ���������å�
		if (!is_dir($path)) {
			trigger_error("FileSystem::read_dir :Directry '{$path}' is not exists.", E_USER_ERROR);
		}

		$dp = @opendir($path);
		if (!$dp) {
			trigger_error("FileSystem::read_dir :Directry '{$path}' cannot read.", E_USER_ERROR);
		}

		$files = $dfiles = array();
		while (($file = readdir($dp)) !== FALSE) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			$tmp = "{$path}/{$file}";
			if (is_file($tmp)) {
				// is_file
				if ($filemask && !preg_match($filemask, $file)) {
					continue;
				}
				$files[] = $prefix . $file;
			} elseif ($reflex && is_dir($tmp)) {
				// is_dir
				if ($dirmask && !preg_match($dirmask, $file)) {
					continue;
				}
				if ($sub_files = FileSystem::readDir($tmp, 0, $filemask, TRUE, $dirmask, "{$file}/")) {
					foreach ($sub_files as $f) {
						$dfiles[] = $prefix . $f;
					}
				}
			}
		}
		@closedir($dp);

		if (!$files && !$dfiles) {
			return NULL;
		}

		if ($files) {
			natsort($files);
		}
		if ($dfiles) {
			natsort($dfiles);
		}

		return array_merge($files, $dfiles);
	}

	/**
	 *
	 * �ǥ��쥯�ȥ꤫�饵�֥ǥ��쥯�ȥ�������������
	 *
	 * @access		public
	 *
	 * @param			string		$path				�оݥǥ��쥯�ȥ�ѥ�
	 * @param			string		$dirmask		���֥ǥ��쥯�ȥ�̾�ޥ���
	 * @param			string		$prefix			�Ƶ��������˻���
	 *
	 * @return		array			���֥ǥ��쥯�ȥ�̾����
	 *
	 */
	function subDirs($path, $dmask = NULL, $prefix = NULL)
	{
		// ����������
		$path = preg_replace('|/$|', '', $path);

		// �ǥ��쥯�ȥ꤫�ɤ���������å�
		if (!is_dir($path)) {
			trigger_error("FileSystem::sub_dirs :Directry '{$path}' is not exists.", E_USER_ERROR);
		}

		$dp = @opendir($path);
		if (!$dp) {
			trigger_error("FileSystem::sub_dirs :Directry '{$path}' cannot read.", E_USER_ERROR);
		}

		$dirs = $sdirs = array();
		while (($dir = readdir($dp)) !== FALSE) {
			if ($dir == '.' || $dir == '..') {
				continue;
			}
			$tmp = "{$path}/{$dir}";
			if (is_dir($tmp)) {
				// is_dir
				if ($dirmask && !preg_match($dirmask, $file)) {
					continue;
				}
				$dirs[] = $prefix . $dir;
				if ($sd = FileSystem::subDirs($tmp, $dirmask, "{$dir}/")) {
					foreach ($sd as $d) {
						$sdirs[] = $prefix . $d;
					}
				}
			}
		}
		@closedir($dp);

		if (!$dirs && !$sdirs) {
			return NULL;
		}

		if ($dirs) {
			natsort($dirs);
		}
		if ($sdirs) {
			natsort($sdirs);
		}

		return array_merge($dirs, $sdirs);
	}

	/**
	 *
	 * �ե�����̾����ǥ��쥯�ȥ�̾��������ʤ����Ϻ���
	 *
	 * @access		public
	 *
	 * @param			string		$path				�оݥե�����ѥ�
	 *
	 * @return		bool			���������TRUE
	 *
	 */
	function pathCheck($path)
	{
		$dir = dirname($path);
		if (!is_dir($dir)) {
			FileSystem::pathCheck($dir);
			mkdir($dir);
			@chmod($dir, 0777);
		}
		return is_dir($dir);
	}
}
