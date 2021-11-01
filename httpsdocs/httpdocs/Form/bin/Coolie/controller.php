<?php
/**
 *
 * Coolie アンケートシステム - CONTROLLERクラス
 * 
 * @access		public
 * @create		2004/05/20
 * @version		$Id: controller.php,v 1.21 2004/10/29 10:44:45 tsukasa Exp $
 * @package   Loach.Coolie
 * @author		Shogo Kawase <shogo@studiofly.net>
 * @copyright	VirtuaWave Inc.
 *
 */

// {{{ Constants

define("COOLIE_OK",     0);
define("COOLIE_ERROR", -1);

// }}}

error_reporting(E_ALL ^ E_NOTICE);

// {{{ class Coolie_controller
class Coolie_controller
{
	// {{{ Private properties
	
	/** リクエストクラス */
	var $_request = NULL;
	
	/** VIEWクラス */
//	var $_view    = NULL;
	
	/** MODELクラス */
	var $_model   = NULL;
	
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * コンストラクタ
	 *
	 * @param			object		$request		リクエストクラス
	 * @param			object		$view				VIEWクラス
	 *
	 */
	function Coolie_controller(&$request, &$view)
	{
		$this->_request = &$request;
		$this->_view    = &$view;
		
		// View初期化
		require_once "config/viewrc.php";
		
		$template       = $this->_execute();
		
		// 出力
		$view->display($template);
	}
	// }}}
	
	// {{{ _execute()
	/**
	 *
	 * 処理の実行
	 *
	 * @access		private
	 * @return		成功時にTRUE
	 *
	 */
	function _execute()
	{
		// 制御に必要なパラメータの取得
		$mode  = (string)($this->_request->get("mode"));
		
		// Modelクラスの生成
		if (COOLIE_ADMIN == 1) {
			require_once "Coolie/adminModel.php";
			$this->_model = &new Coolie_adminModel(&$this->_request, &$this->_view);
		} else {
			require_once "Coolie/model.php";
			$this->_model = &new Coolie_model(&$this->_request, &$this->_view);
		}
		
		// テンプレートファイル名マスタ読み込み
		$template = include("config/masters/filename.php");
		
		// 実行する処理を決定
		if (COOLIE_ADMIN == 1) {
			// 管理画面
			
			// 認証処理
			require_once "Auth/Basic.php";
			$Auth = &new Auth_Basic(
				array($GLOBALS['admin']['COOLIE_ADMIN_USER'] => $GLOBALS['admin']['COOLIE_ADMIN_PW']),
				"Dynamic Form System Admin Page Login"
				);

			if ($Auth->checkAuth()) {
				// 認証成功
				switch ($mode) {
					case "queryDelete":
						// 質問の削除
						$this->_model->deleteQuery();
						$this->_model->queryList();
						$this->_model->setMasterValues("queryType");
						return $template["admin/queryList"];
						
					case "queryEdit":
						// 質問編集
						if ($this->_model->editQuery() == COOLIE_OK) {
							$this->_model->queryList();
							$this->_model->setMasterValues("queryType");
							return $template["admin/queryList"];
						}
						$this->_model->setQueryDefaultValue();
						$this->_model->setConfigInputValues();
						$this->_model->setMasterValues("restriction");
						return $template["admin/editQuery"];
						
					case "queryEditView":
						// 質問編集フォーム
						$this->_model->setQueryDefaultValue();
						$this->_model->setMasterValues("restriction");
						return $template["admin/editQuery"];
						
					case "queryCreate":
						// 質問の新規作成
						$this->_model->createQuery();
					case "queryList":
						// 質問一覧
						$this->_model->queryList();
						$this->_model->setMasterValues("queryType");
						return $template["admin/queryList"];
						
					case "editDesign":
						// フォームデザイン設定の変更
						if ($this->_model->editDesign() == COOLIE_OK) {
							return $template["admin/formEdit"];
						}
						$this->_model->setConfigInputValues();
						return $template["admin/editDesign"];
						
					case "editDesignView":
						// フォームデザイン設定の変更画面
						$this->_model->setFormDefaultValue();
						return $template["admin/editDesign"];
						
					case "editConfig":
						// フォーム全体設定の変更
						if ($this->_model->editConfig() == COOLIE_OK) {
							return $template["admin/formEdit"];
						}
						$this->_model->setConfigInputValues();
						return $template["admin/editConfig"];
						
					case "editConfigView":
						// フォーム全体設定の変更画面
						$this->_model->setFormDefaultValue();
						return $template["admin/editConfig"];
						
					case "adminEdit":
						if ($this->_model->editAdmin() == COOLIE_OK) {
							return $template["admin/index"];
						}
						$this->_model->setConfigInputValues();
						return $template["admin/adminConfig"];
						
					case "adminEditView":
						$this->_model->setAdminValues();
						return $template["admin/adminConfig"];
						
					case "formEdit":
						// フォーム設定画面
						$this->_model->formEditMenu();
						return $template["admin/formEdit"];
						
					case "formResult":
						// 結果のダウンロード
						$this->_model->getResultCVS();
						exit;
						
					case "formDelete":
						// フォームの削除
						$slt = $this->_model->deleteForm();
						$this->_model->formList();
						return $template["admin/formList"];
						
					case "formCreate":
						// フォームの新規作成
						$this->_model->createForm();
						$this->_model->formList();
						return $template["admin/formList"];
						
					case "formList":
						// フォーム一覧
						$this->_model->formList();
						return $template["admin/formList"];
						
					case "accessAnalyze":
						// アクセス統計
						$this->_model->accessAnalyze();
						return $template["admin/accessAnalyze"];
						
					case "showTopPage":
					default:
						// 管理画面TOPページ
						return $template["admin/index"];
				}
			} else {
				// 認証失敗
				return $template["admin/authFailed"];
			}
		} else {
			// 認証フラグをセット
			$this->_view->assign("AUTHFLAG", ($_SERVER['PHP_AUTH_USER'] ==$GLOBALS['admin']['COOLIE_ADMIN_USER'] && $_SERVER['PHP_AUTH_PW'] == $GLOBALS['admin']['COOLIE_ADMIN_PW']));
			
			// アクセスログ記録
			require_once "Loach/accessLog.php";
			$LOG = &new Loach_accessLog();
			$LOG->record();
			
			// ユーザー側(アンケート画面)
			$this->_model->createQuestionnaireSheet();
			switch ($mode) {
				case "confirm":
					// 確認画面
					if (($err = $this->_model->checkRequestedValues()) != COOLIE_OK) {
						// エラー処理
						$this->_model->setRequestedValues();
						return $template["user/index"];
					}
					$this->_model->setRequestedValues();
					return $template["user/confirm"];
					
				case "edit":
					// 修正画面
					$this->_model->setRequestedValues();
					return $template["user/index"];
					
				case "submit":
					// 完了画面
					if (($err = $this->_model->checkRequestedValues()) != COOLIE_OK) {
						// エラー処理
						$this->_model->setRequestedValues();
						return $template["user/index"];
					}
					$this->_model->putToDatabase();
					$this->_model->setRequestedValues();
					return $template["user/thanks"];
					
				case "form":
				default:
					// フォーム
					return $template["user/index"];
			}
		}
		return NULL;
	}
	// }}}
}
// }}}

?>
