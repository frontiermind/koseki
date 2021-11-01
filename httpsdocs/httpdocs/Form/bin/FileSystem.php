<?php
/**
 *
 * ファイル/ディレクトリ操作クラス
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
	 * ディレクトリからファイル一覧を取得する
	 *
	 * @access		public
	 *
	 * @param			string		$path				対象ディレクトリパス
	 * @param			int				$mode				0 = 相対パス取得 / 1 = 絶対パス取得
	 * @param			string		$filemask		ファイル名マスク
	 * @param			bool			$reflex			サブディレクトリを再帰的に検索するならTRUE
	 * @param			string		$dirmask		サブディレクトリ名マスク
	 * @param			string		$prefix			再帰処理時に使用
	 *
	 * @return		array			ファイル名一覧
	 *
	 */
	function readDir($path, $mode = 0, $filemask = '', $reflex = FALSE, $dirmask = '', $prefix = '')
	{
		// 引数の整形
		$path = preg_replace('|/$|', '', $path);
		if ($mode) {
			$prefix = "{$path}/";
		}

		// ディレクトリかどうかをチェック
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
	 * ディレクトリからサブディレクトリ一覧を取得する
	 *
	 * @access		public
	 *
	 * @param			string		$path				対象ディレクトリパス
	 * @param			string		$dirmask		サブディレクトリ名マスク
	 * @param			string		$prefix			再帰処理時に使用
	 *
	 * @return		array			サブディレクトリ名一覧
	 *
	 */
	function subDirs($path, $dmask = NULL, $prefix = NULL)
	{
		// 引数の整形
		$path = preg_replace('|/$|', '', $path);

		// ディレクトリかどうかをチェック
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
	 * ファイル名からディレクトリ名を取得、ない場合は作成
	 *
	 * @access		public
	 *
	 * @param			string		$path				対象ファイルパス
	 *
	 * @return		bool			成功すればTRUE
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
