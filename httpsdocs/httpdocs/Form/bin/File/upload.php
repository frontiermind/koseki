<?php
/**
 * File_uploadクラス
 *
 * ファイルのアップロード処理を簡潔にするクラス
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
	/** 許可する拡張子の配列. 常に小文字で指定. NULLの場合はすべて許可 */
	var $accept_ext = NULL;

	/** 許可するMIMEタイプの配列. NULLの場合はすべて許可 */
	var $accept_type = NULL;

	/** 許可するファイルサイズ (Byte). NULLの場合は制限なし */
	var $accept_size = NULL;
	
	/**
	 *
	 * アップロードされたファイルを指定ファイル名で保存
	 *
	 * @access		public
	 *
	 * @param			string		$file					保存対象ファイルの名前(フォームのname属性).
	 * @param			string		$path					保存先パス(但し、拡張子を除く)
	 * @param			int				$permission		保存するファイルのパーミッション
	 * @param			bool			$useOrgName		オリジナルのファイル名をパスに付加するならTRUE
	 * @param			bool			$extToLower		拡張子を常に小文字で処理するならTRUE
	 *
	 * @return		string		保存したファイル名. 失敗した場合はNULL.
	 *
	 */
	function save($file, $path, $permission = 0655, $useOrgName = FALSE, $extToLower = FALSE)
	{
		if (empty($_FILES[$file])) {
			return NULL;
		}
		$file = &$_FILES[$file];

		// 拡張子を取得
		preg_match('/^(.+?)\.([^.]+)$/i', $file['name'], $match);
		$pre = $match[1];
		$ext = $match[2];
		if ($extToLower) {
			$ext = strtolower($ext);
		}
		
		// エラーチェック
		if ($file['error']) {
			return NULL;
		}
		
		// 制限チェック
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

		// ファイルのコピー
		if (!@copy($file['tmp_name'], $dist_file)) {
			return NULL;
		}

		// パーミッション変更
		if (!@chmod($dist_file, $permission)) {
			return NULL;
		}

		return $dist_file;
	}

}

?>
