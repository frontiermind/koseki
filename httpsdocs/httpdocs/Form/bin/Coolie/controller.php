<?php
/**
 *
 * Coolie ���󥱡��ȥ����ƥ� - CONTROLLER���饹
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
	
	/** �ꥯ�����ȥ��饹 */
	var $_request = NULL;
	
	/** VIEW���饹 */
//	var $_view    = NULL;
	
	/** MODEL���饹 */
	var $_model   = NULL;
	
	// }}}
	
	// {{{ Constructor
	/**
	 *
	 * ���󥹥ȥ饯��
	 *
	 * @param			object		$request		�ꥯ�����ȥ��饹
	 * @param			object		$view				VIEW���饹
	 *
	 */
	function Coolie_controller(&$request, &$view)
	{
		$this->_request = &$request;
		$this->_view    = &$view;
		
		// View�����
		require_once "config/viewrc.php";
		
		$template       = $this->_execute();
		
		// ����
		$view->display($template);
	}
	// }}}
	
	// {{{ _execute()
	/**
	 *
	 * �����μ¹�
	 *
	 * @access		private
	 * @return		��������TRUE
	 *
	 */
	function _execute()
	{
		// �����ɬ�פʥѥ�᡼���μ���
		$mode  = (string)($this->_request->get("mode"));
		
		// Model���饹������
		if (COOLIE_ADMIN == 1) {
			require_once "Coolie/adminModel.php";
			$this->_model = &new Coolie_adminModel(&$this->_request, &$this->_view);
		} else {
			require_once "Coolie/model.php";
			$this->_model = &new Coolie_model(&$this->_request, &$this->_view);
		}
		
		// �ƥ�ץ졼�ȥե�����̾�ޥ����ɤ߹���
		$template = include("config/masters/filename.php");
		
		// �¹Ԥ�����������
		if (COOLIE_ADMIN == 1) {
			// ��������
			
			// ǧ�ڽ���
			require_once "Auth/Basic.php";
			$Auth = &new Auth_Basic(
				array($GLOBALS['admin']['COOLIE_ADMIN_USER'] => $GLOBALS['admin']['COOLIE_ADMIN_PW']),
				"Dynamic Form System Admin Page Login"
				);

			if ($Auth->checkAuth()) {
				// ǧ������
				switch ($mode) {
					case "queryDelete":
						// ����κ��
						$this->_model->deleteQuery();
						$this->_model->queryList();
						$this->_model->setMasterValues("queryType");
						return $template["admin/queryList"];
						
					case "queryEdit":
						// �����Խ�
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
						// �����Խ��ե�����
						$this->_model->setQueryDefaultValue();
						$this->_model->setMasterValues("restriction");
						return $template["admin/editQuery"];
						
					case "queryCreate":
						// ����ο�������
						$this->_model->createQuery();
					case "queryList":
						// �������
						$this->_model->queryList();
						$this->_model->setMasterValues("queryType");
						return $template["admin/queryList"];
						
					case "editDesign":
						// �ե�����ǥ�����������ѹ�
						if ($this->_model->editDesign() == COOLIE_OK) {
							return $template["admin/formEdit"];
						}
						$this->_model->setConfigInputValues();
						return $template["admin/editDesign"];
						
					case "editDesignView":
						// �ե�����ǥ�����������ѹ�����
						$this->_model->setFormDefaultValue();
						return $template["admin/editDesign"];
						
					case "editConfig":
						// �ե���������������ѹ�
						if ($this->_model->editConfig() == COOLIE_OK) {
							return $template["admin/formEdit"];
						}
						$this->_model->setConfigInputValues();
						return $template["admin/editConfig"];
						
					case "editConfigView":
						// �ե���������������ѹ�����
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
						// �ե������������
						$this->_model->formEditMenu();
						return $template["admin/formEdit"];
						
					case "formResult":
						// ��̤Υ��������
						$this->_model->getResultCVS();
						exit;
						
					case "formDelete":
						// �ե�����κ��
						$slt = $this->_model->deleteForm();
						$this->_model->formList();
						return $template["admin/formList"];
						
					case "formCreate":
						// �ե�����ο�������
						$this->_model->createForm();
						$this->_model->formList();
						return $template["admin/formList"];
						
					case "formList":
						// �ե��������
						$this->_model->formList();
						return $template["admin/formList"];
						
					case "accessAnalyze":
						// ������������
						$this->_model->accessAnalyze();
						return $template["admin/accessAnalyze"];
						
					case "showTopPage":
					default:
						// ��������TOP�ڡ���
						return $template["admin/index"];
				}
			} else {
				// ǧ�ڼ���
				return $template["admin/authFailed"];
			}
		} else {
			// ǧ�ڥե饰�򥻥å�
			$this->_view->assign("AUTHFLAG", ($_SERVER['PHP_AUTH_USER'] ==$GLOBALS['admin']['COOLIE_ADMIN_USER'] && $_SERVER['PHP_AUTH_PW'] == $GLOBALS['admin']['COOLIE_ADMIN_PW']));
			
			// ������������Ͽ
			require_once "Loach/accessLog.php";
			$LOG = &new Loach_accessLog();
			$LOG->record();
			
			// �桼����¦(���󥱡��Ȳ���)
			$this->_model->createQuestionnaireSheet();
			switch ($mode) {
				case "confirm":
					// ��ǧ����
					if (($err = $this->_model->checkRequestedValues()) != COOLIE_OK) {
						// ���顼����
						$this->_model->setRequestedValues();
						return $template["user/index"];
					}
					$this->_model->setRequestedValues();
					return $template["user/confirm"];
					
				case "edit":
					// ��������
					$this->_model->setRequestedValues();
					return $template["user/index"];
					
				case "submit":
					// ��λ����
					if (($err = $this->_model->checkRequestedValues()) != COOLIE_OK) {
						// ���顼����
						$this->_model->setRequestedValues();
						return $template["user/index"];
					}
					$this->_model->putToDatabase();
					$this->_model->setRequestedValues();
					return $template["user/thanks"];
					
				case "form":
				default:
					// �ե�����
					return $template["user/index"];
			}
		}
		return NULL;
	}
	// }}}
}
// }}}

?>
