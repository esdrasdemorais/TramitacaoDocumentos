<?php
/**
 * Controlador de Tramitação de Documento
 * @author Esdras
 * @copyright FGSL 2009
 * @license New SDB
 * @package application
 * @subpackage controllers
 * @filesource 
 */
class TramitacaoController extends Zend_Controller_Action 
{
	/**
	 * Inits
	 * 
	 */
	public function init()
	{
		/** Obtendo o objeto view registrado no bootstrap index.php **/
		$objRegistry = Zend_Registry::getInstance();
		$this->db	 = $objRegistry->db;
		//$this->view  = $objRegistry->view;
		$this->view->setEncoding('UTF-8');												/** Configura a codificação das páginas */
		$this->view->setEscape('htmlentities');											/** Escapar entradas HTML */
		$this->view->setBasePath(APPLICATION_PATH.'/application/views/');				/** Define o diretório onde estarão as visões */
		$this->view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper'); 	/** Adiciona a lib js Dojo ao objeto View */

		$this->initView();
		$this->view->baseUrl 		= $this->_request->getBaseUrl();
		$this->view->actionName 	= $this->getRequest()->getActionName();
		$this->view->controllerName	= $this->getRequest()->getControllerName();
		if(Zend_Auth::getInstance() && Zend_Auth::getInstance()->hasIdentity() && Zend_Auth::getInstance()->hasIdentity())
		{
			$this->view->userLogin	= @Zend_Auth::getInstance()->getIdentity()->us_login;
			$this->view->userUnitId	= @Zend_Auth::getInstance()->getIdentity()->un_id;
			$this->view->userUnit 	= @Zend_Auth::getInstance()->getIdentity()->un_descricao;
			$this->view->userId 	= @Zend_Auth::getInstance()->getIdentity()->us_id;
			$this->view->userTipoId = @Zend_Auth::getInstance()->getIdentity()->tu_id;
		}		
		
		$objRegistry->view = $this->view;
		
		Zend_Loader::loadClass('Tramitacao');
		Zend_Loader::loadClass('Documento');
		Zend_Loader::loadClass('TramitacaoForm');
		Zend_Loader::loadClass('TramitacaoSearchForm');
		Zend_Loader::loadClass('TramitacoesPendentesSearchForm');
		Zend_Loader::loadClass('TramitacaoDocumentosForm1'); // Form Selecionar Documentos
		Zend_Loader::loadClass('TramitacaoDocumentosForm2'); // Form Dados Complementares da Tramitação
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		Zend_Loader::loadClass('Zend_Form_Element_MultiCheckbox');
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		Zend_Loader::loadClass('Zend_Date');
		Zend_Loader::loadClass('Zend_Locale');
		Zend_loader::loadClass('Zend_Paginator');
	}
	
	/**
     * Esse método é chamado antes das actions (indexAction)
     * 
     */
    /*public function preDispatch()
    {
        if(Zend_Auth::getInstance()->hasIdentity()) 
        {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if($this->getRequest()->getActionName() != 'add' || $this->getRequest()->getActionName() != 'edit') 
            {
				echo $this->getRequest()->getActionName()."Aqui é index.";
            	#$this->_helper->redirector('index', 'index');
            }
        }
    }*/
	
	public function getDocumentoSearchForm()
	{
		Zend_Loader::loadClass('DocumentoSearchForm');
		
		return new DocumentoSearchForm(
        	array(
            	'action' => $this->view->baseUrl . '/tramitacao/index',
            	'method' => 'post'
        	)
        );
	}
	
	/**
	 * Método default
	 *
	 */
	public function indexAction()
	{
		// Segundo Passo do Cadastro da(s) Tramitação(ões) se foi feita requisição do método POST e for passado o array de documentos (dc_id) a tramitar
		if($this->_request->getPost('dc_id')) //strtolower($_SERVER['REQUEST_METHOD']) == 'post' && is_array($this->_request->getPost('dc_id'))
		{
			Zend_Loader::loadClass('Zend_Form_Element_Hidden');
			Zend_Loader::loadClass('Zend_Form_Element_Submit');
			
			$request = $this->getRequest();
			
			// Check if we have a POST request
			if(!$request->isPost())
			{
				return $this->_helper->redirector($this->baseUrl.'/index');
			}
			
			$has_dc_id = $this->_request->getPost('dc_id') > 0 ? 1 : 0;
			// Redirecionar para Selecionar Documento
			if($has_dc_id === 0)
			{
				return $this->_helper->redirector($this->baseUrl);
			}
			
			// validate form
			/*if(!$objForm->isValid($request->getPost()))
			{
				// Invalid entries
				$this->view->form = $objForm;
				return $this->render('index'); // re-render the tramitacao form
			}*/
			
			$this->view->title = $this->view->escape("Cadastro Tramitação de Documentos - Informações Complementares");
			
			$objForm = $this->getForm('TramitacaoDocumentosForm2');	
			$objForm->setAction($this->view->baseUrl.'/'.$this->view->controllerName.'/add');
			
			$objFilter 	= new Zend_Filter_StripTags();
			foreach($this->_request->getPost('dc_id') as $dc_id)
				$arrDcId[] = (int) trim($objFilter->filter($dc_id));
			
			$strDescription = "O(s) seguinte(s) documento(s) foi(ram) selecionado(s):";
			
			$objDocumento 	= new Documento();
			$arrDocumentos 	= $objDocumento->getDocumentos($this->view->userId,$arrDcId);
			
			/*
			// Provisório
			$strDescription.= "<table>\n";
			$strDescription.= "<tr>\n";
			$strDescription.= " <th>Nº Documento</th>\n";
			$strDescription.= " <th>Tipo Documento</th>\n";
			$strDescription.= " <th>Usuário</th>\n";
			$strDescription.= " <th>Data Elaboração</th>\n";
			$strDescription.= " <th>Data Cadastro</th>\n";
			$strDescription.= " <th>Assunto</th>\n";
			$strDescription.= "	<th>Complemento Assunto</th>\n";
			$strDescription.= "	<th>Orgão Origem</th>\n";
			$strDescription.= "</tr>\n";
			foreach($arrDocumentos as $documento)
			{
				$documento['us_nome'] 		   = ($documento['us_id'] == $this->view->userId) ? "<strong>".$this->view->escape($documento['us_nome'])."</strong>" : $this->view->escape($documento['us_nome']);
				$documento['dc_compl_assunto'] = (strlen($documento['dc_compl_assunto'])) ? $this->view->escape(trim($documento['dc_compl_assunto'])) : '&nbsp;';
				$documento['orgao_origem'] 	   = (strlen($documento['orgao_origem'])) ? $this->view->escape(trim($documento['orgao_origem'])) : '&nbsp;';
				
				$strDescription.= "<tr>\n";
				$strDescription.= "	<td>".$documento['dc_numero']."</td>\n";
				$strDescription.= "	<td>".$this->view->escape($documento['td_descricao'])."</td>\n";
				$strDescription.= " <td>".$documento['us_nome']."</td>\n";
				$strDescription.= " <td>".$this->view->escape($documento['dc_data_elaboracao'])."</td>\n";
				$strDescription.= " <td>".$this->view->escape($documento['dc_data_cadastro'])."</td>\n";
				$strDescription.= " <td>".$this->view->escape(trim($documento['as_descricao']))."</td>\n";
				$strDescription.= " <td>".$documento['dc_compl_assunto']."</td>\n";
				$strDescription.= " <td>".$documento['orgao_origem']."</td>\n";
				$strDescription.= "</tr>\n";
			}
			$strDescription.= "</table>\n";
			$strDescription.= "<br />\n";
			$strDescription.= "<strong>Antes de concluir a tramitação confirme se os documentos selecionados estão corretos. Não haverá como retroceder esta ação.</strong>";
			$strDescription.= "<br /><hr />\n";
			#$objForm->setDescription($strDescription);
			*/
			#echo $objForm->renderHtmlTag('wrap this content');
			
			$this->view->descriptionHtml = $strDescription;
			
			#$dc_ids = '';
			#foreach($arrDcId as $key=>$dc_id)
			#	$dc_ids .= ($key+1 < count($arrDcId)) ? $dc_id.';' : $dc_id;
			$dc_ids = implode(';', $arrDcId);
			
			$dcIdHidden = new Zend_Form_Element_Hidden('dc_id');
			$dcIdHidden->setName('dc_id');
			$dcIdHidden->setValue($dc_ids); /** Adiociona Array de Ids de Documentos ao Campo Hidden */
			$dcIdHidden->removeDecorator('label');
			$dcIdHidden->removeDecorator('HtmlTag');
			$objForm->addElement($dcIdHidden);
			unset($dcIdHidden);
			
			$this->view->form 		= $objForm;
			$this->view->documentos = $arrDocumentos;
			
			$this->render();
		}
		// Primeiro Passo do Cadastro da(s) Tramitação(ões)
		else
		{
			$this->view->title = "Cadastro Nova Tramitação";
			
			// Filtro de documentos
			$this->view->documentoSearchForm = $this->getDocumentoSearchForm();
			
			// tramitacao id should be $params['id']
			$id = (int) $this->_request->getParam('id',0);
		
			$objDocumento 	= new Documento();
			$arrDocumentos 	= $objDocumento->getDocumentosUnidade($this->view->userUnitId);
			
			/* ------------------------------------------------- Versão Antiga ------------------------------------------------------------------------------ */
			/*	$objForm = $this->getForm('TramitacaoDocumentosForm1');
				
				$dcId = new Zend_Form_Element_MultiCheckbox('dc_id');
				#$dcId->setLabel('Documentos');
				$dcId->setRequired(true);
				
				$objDocumento 	= new Documento();
				$arrDocumentos 	= $objDocumento->getDocumentosUnidade($this->view->userUnitId);
				
				/** Check Checkbox Item * /
				foreach($arrDocumentos as $documento)
				{
					$dcId->addMultiOption($documento['dc_id'], $documento['dc_numero'].' - '.$documento['td_descricao'].' - '.$documento['dc_data_elaboracao']);
					#$dcId->setCheckedValue($documento['dc_id'])
					#->setUncheckedValue('0')
					#->setId('dc_id'.$documento['dc_id']);
				}

				$dcId->setValue($id);
				
				$objForm->addElement($dcId);
				
				// Botão de envio do formulário
				$objForm->addElement('submit', 'save', array(
					'required' => true,
					'ignore'   => true,
					'label'    => 'Avançar >'
				));
				
				/**
				 * Esse método serve para decorar o formulário tramitação
				 * /
				$objForm->setDecorators(array(
					'FormElements',
					array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form_tramitacao')),
					array('Description', array('placement' => 'prepend')),
					'Form'
				));
				
				$this->view->form 		= $objForm;
			*/
			/* ------------------------------------------------- Versão Antiga ------------------------------------------------------------------------------ */
			
			$this->view->id			= $id;
			$this->view->documentos = $arrDocumentos;
			
			if($this->_request->getParam('pagina', 1) > 0)
			{
				Zend_loader::loadClass('Zend_Paginator');
				Zend_loader::loadClass('Zend_View_Helper_PaginationControl');
				
				$paginator = Zend_Paginator::factory($this->view->documentos);
				$paginator->setCurrentPageNumber($this->_request->getParam('pagina', 1));
				$paginator->setItemCountPerPage(100); //número de registros por página
				
				/*Zend_Paginator::setDefaultScrollingStyle('Sliding');
				/*Zend_View_Helper_PaginationControl::setDefaultViewPartial(
					'pagination/partial.phtml'
				);
				$paginator->setView($this->view);*/
				
				// additional view fields required by form
				$page = new Zend_Form_Element_Hidden('pagina');
				$page->setValue($this->_request->getParam('pagina', 1));
				
				$this->view->documentoSearchForm->addElement($page);
				
				$this->view->pages	   = $paginator->getPages();
				$this->view->paginator = $paginator;
			}
			
			$this->render();
		}
	}
	
	public function addAction()
	{
		Zend_Loader::loadClass('Documento');
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		Zend_Loader::loadClass('TramitacaoDocumentosForm2');
		Zend_Loader::loadClass('ImprimirTramitacaoDocumentosForm');
		
		$this->view->title = "Salvar Nova Tramitação";
		$request = $this->getRequest();
	
        // Check if we have a POST request
        if(!$request->isPost())
		{
            return $this->_helper->redirector($this->baseUrl.'/index');
        }
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			$objFilter 	= new Zend_Filter_StripTags();
			$arrDcId 	= explode(";",trim($objFilter->filter($this->_request->getPost('dc_id')))); /** Transforma o literal de ids de documentos separados por ; para array */
			$trCota 	= (string) trim($objFilter->filter($this->_request->getPost('tr_cota')));
			$unId 		= (int) trim($objFilter->filter($this->_request->getPost('un_id')));
			
			// Get our form and validate it
			$form = $this->getForm('TramitacaoDocumentosForm2');
			if(!$form->isValid($request->getPost()))
			{
				$dcIdHidden = new Zend_Form_Element_Hidden('dc_id');
				$dcIdHidden->setName('dc_id');
				$dcIdHidden->setValue(implode(';',$arrDcId)); /** Adiociona Array de Ids de Documentos ao Campo Hidden */
				$dcIdHidden->removeDecorator('label');
				$dcIdHidden->removeDecorator('HtmlTag');
				$form->addElement($dcIdHidden);
				unset($dcIdHidden);
				
				// Invalid entries
				$this->view->form = $form;
				return $this->render('index'); // re-render the tramitacao form
			}
			
			if(count($arrDcId) && $unId > 0)
			{
				$trCota = (strlen($trCota)) ? $trCota : null;
				
				$this->db->beginTransaction();
				try
				{
					$objTramitacao = new Tramitacao();
								
					// Data Início Tramitação
					#$trDataInicio = new Zend_Db_Expr('NOW()');				
					$objDate 		= new Zend_Date();
					$objLocale  	= new Zend_Locale('pt_BR');
					Zend_Date::setOptions(array('format_type' => 'php'));
					#echo $objDate->get(); /** Output of the desired Timestamp date */
					
					$trDataInicio = $objDate->toString('m-d-Y H:i:s'); /** pt_BR format */
					
					/**
					 * @Date: 21/12/2009
					 * @Author:	Esdras
					 * @Describe: Alterado retorno do último número da guia para inserir um único número de guia para todos os documentos da tramitação
					 */
					$trNumeroGuia = $objTramitacao->getNextId();
					
					foreach($arrDcId as $dcId)
					{
						// ===== Retorna o ID do usuário autenticado (Provisório)
						#$objConfig = new Zend_Config_Ini('./application/config.ini','database');
						#$db = Zend_Db::factory($objConfig->db->adapter, $objConfig->db->config->toArray());
						
						#$sqlUsuario = "SELECT us_id FROM tb_usuario WHERE us_login = '".Zend_Auth::getInstance()->getIdentity()."'";
						#$usId 	    = $db->fetchOne($sqlUsuario);
						#echo "----------------<pre>" . var_dump($resUsuario) . "</pre><br>";
						// ==== Retorna o ID do usuário autenticado (Provisório)
						$usId = Zend_Auth::getInstance()->getIdentity()->us_id;
						
						// === Retorna o ID do tipo do documento (Provisório)
						$objDocumento 		= new Documento();
						$_whereDocumento  	= "dc_id = ".$dcId;
						$resDocumento 		= $objDocumento->fetchRow($_whereDocumento);
						$tdId				= $resDocumento->td_id;
						// === Retorna o ID do tipo do documento (Provisório)
						
						$arrData = array(
						'dc_id' 		 => $dcId,
						'td_id' 		 => $tdId,
						'tr_data_inicio' => $trDataInicio,
						'tr_cota' 		 => $trCota,
						'us_id' 		 => $usId,
						'un_id' 		 => $unId,
						'tr_numero_guia' => $trNumeroGuia
						);
						
						$arrTrId[] = $objTramitacao->insert($arrData);		
					}
					// Comita a transação caso tenham sido cadastradas as tramitações de todos os documentos selecionados
					if(count($arrDcId) == count($arrTrId))
						$this->db->commit();
					else
					{
						$this->db->rollBack();
						$this->err = "Nem todos os documentos foram tramitados.\n" . $e->getMessage();
					}
				}
				catch(Exception $e)
				{
					$this->db->rollBack();
					$this->err = $e->getMessage();
				}
				
				// Impressão
				$this->view->title = 'Imprimir Guia da Tramitação';
				
				// Obtém os dados complementares das tramitações salvas de um documento selecionado
				$arrTramitacao 	= $objTramitacao->getTramitacoes($this->view->userUnitId,$arrTrId);
				$tramitacao 	= $arrTramitacao[0];
				
				$strDescription = "<strong>Guia da Tramitação</strong>\n";
				$strDescription.= "<br /><hr /><br />\n";
				$strDescription.= " <strong>Cota:</strong> ".$tramitacao['tr_cota']."<br />\n";
				$strDescription.= " <strong>Unidade de Destino:</strong> ".$tramitacao['un_descricao']."<br />\n";
				$strDescription.= " <strong>Usuário:</strong> ".$tramitacao['us_nome']."<br />\n";
				$strDescription.= "<br /><hr /><br />\n";
				
				$strDescription.= "<table border=\"1\">\n";
				$strDescription.= "<tr>\n";
				$strDescription.= " <th>Nº Documento</th>\n";
				$strDescription.= " <th>Tipo de Documento</th>\n";
				$strDescription.= " <th>Data Elaboração</th>\n";
				$strDescription.= " <th>Data Cadastro</th>\n";
				$strDescription.= " <th>Assunto</th>\n";
				$strDescription.= "	<th>Complemento Assunto</th>\n";
				$strDescription.= "	<th>Orgão Origem</th>\n";
				$strDescription.= "</tr>\n";
				
				$objDocumento 	= new Documento();
				$arrDocumentos 	= $objDocumento->getDocumentos($this->view->userId,$arrDcId);
				foreach($arrDocumentos as $key=>$documento)
				{
					$strComplAssunto = (strlen($documento['dc_compl_assunto'])) ? $this->view->escape(trim($documento['dc_compl_assunto'])) : '&nbsp;';
					$strOrgaoExterno = (strlen($documento['orgao_origem'])) ? $this->view->escape(trim($documento['orgao_origem'])) : '&nbsp;';
					
					$strDescription.= "<tr>\n";
					$strDescription.= "	<td>".$documento['dc_numero']."</td>\n";
					$strDescription.= "	<td>".$this->view->escape($documento['td_descricao'])."</td>\n";
					$strDescription.= " <td>".$this->view->escape($documento['dc_data_elaboracao'])."</td>\n";
					$strDescription.= " <td>".$this->view->escape($documento['dc_data_cadastro'])."</td>\n";
					$strDescription.= " <td>".$this->view->escape(trim($documento['as_descricao']))."</td>\n";
					$strDescription.= " <td>".$strComplAssunto."</td>\n";
					$strDescription.= " <td>".$strOrgaoExterno."</td>\n";
					$strDescription.= "</tr>\n";
				}
				
				$strDescription.= "</table>\n";
				$strDescription.= "<br />\n";
				#$strDescription.= "<strong><input type=\"button\" onclick=\"window.print();\" id=\"btn_imprimir\" value=\"Imprimir\"></strong>";
				#$objForm->setDescription($strDescription);
				#echo $objForm->renderHtmlTag('wrap this content');
				$this->view->descriptionHtml = $strDescription;

				$objForm = $this->getForm('ImprimirTramitacaoDocumentosForm');
				
				$tr_ids 	= implode(';', $arrTrId);
				$trIdHidden = new Zend_Form_Element_Hidden('tr_id');
				$trIdHidden->setName('tr_id');
				$trIdHidden->setValue($tr_ids); /** Adiociona Array de Ids de Tramitacoes ao Campo Hidden */
				$trIdHidden->removeDecorator('label');
				$trIdHidden->removeDecorator('HtmlTag');
				$objForm->addElement($trIdHidden);
				unset($trIdHidden);
				
				$this->view->form = $objForm;
				$this->render();
				#$this->_redirect('add');
				#return;
			}
		}
	}
	
	public function editAction()
	{
		Zend_Loader::loadClass('Documento');
		Zend_Loader::loadClass('TramitacaoDocumentosForm2');
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		
		$this->view->title = "Alterar Tramitação de Documentos";
		$objTramitacao 	   = new Tramitacao();
		
		// Get our form
		$form = $this->getForm('TramitacaoDocumentosForm2');
		$form->setAction($this->view->baseUrl.'/'.$this->view->controllerName.'/edit');
		#$form->setAttrib('accept-charset', 'UTF-8'); /** Set Form Charset  */
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			
			$request = $this->getRequest();
			// Check if we have a POST request
			if(!$request->isPost())
			{
				return $this->_helper->redirector('index');
			}
			
			$filter  = new Zend_Filter_StripTags();
			$trId = explode(';', $this->_request->getPost('tr_id'));
			$trCota	 = (string) trim($filter->filter($this->_request->getPost('tr_cota')));
			$unId	 = (int) $this->_request->getPost('un_id');


			if(count($trId) > 0 && $unId > 0) //&& $dcId > 0
			{
				// Validate form
				if(!$form->isValid($request->getPost()))
				{
					// Invalid entries
					$this->view->form = $form;
					return $this->render('index'); // re-render the login form
				}
				
				$this->db->beginTransaction();			
				try
				{				
					$trCota = (strlen($trCota)) ? $trCota : null;
					
					// === Retorna o ID do tipo do documento (Provisório)
					/*$objDocumento 		= new Documento();
					$_whereDocumento  	= "dc_id = ".$dcId;
					$resDocumento 		= $objDocumento->fetchRow($_whereDocumento);
					$tdId				= $resDocumento->td_id;*/
					// === Retorna o ID do tipo do documento (Provisório)
					
					$arrData = array(
					#'dc_id' => $dcId,
					#'td_id' => $tdId,
					'tr_cota' => $trCota,
					'un_id' => $unId
					);
					
					#$where = 'tr_id = '.$trId;
					$where = 'tr_id IN('.implode(", ", $trId).')';
					
					$rowsAfected = $objTramitacao->update($arrData, $where);
					if($rowsAfected)
						$this->db->commit();
					else
					{
						$this->db->rollBack();
						$this->view->err = "A transação da edição da tramitação não foi completada!";
					}
				}
				catch(Exception $e)
				{
					$this->db->rollBack();
					$this->view->err = $e;
				}
				
				return $this->_helper->redirector('edit');
			}
			else
			{
				// Obtém os dados complementares das tramitações salvas de um documento selecionado
				$this->view->tramitacao = $objTramitacao->fetchRow('tr_id = '.$arrTrId[0]);
			}
		}
		else
		{
			// usuario id should be $params['id']
			$trId = (int) $this->_request->getParam('id',0);
			if($trId > 0)
			{
				$this->view->tramitacao = $objTramitacao->fetchRow('tr_id = '.$trId);
			}
		}
		
		if($this->view->tramitacao)
		{
			// additional view fields required by form
			$form->setDefault('tr_cota', $this->view->tramitacao->tr_cota);
			$form->setDefault('un_id', $this->view->tramitacao->un_id);
			
			$trIdHidden = new Zend_Form_Element_Hidden('tr_id');
			$trIdHidden->setName('tr_id');
			$trIdHidden->setValue($trId); /** Adiociona Array de Ids de Tramitacoes ao Campo Hidden */
			$trIdHidden->removeDecorator('label');
			$trIdHidden->removeDecorator('HtmlTag');
			$form->addElement($trIdHidden);
			unset($trIdHidden);
		}
		
		$this->view->form = $form;
		$this->render(); // render tipo documento form
	}
	
	public function deleteAction()
	{
		$this->view->title = "Excluir Tramitação";
		
		$objTramitacao = new Tramitacao();
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			
			$filter = new Zend_Filter_Alpha();
			$id 	= (int) $this->_request->getPost('id');
			$delete = $filter->filter($this->_request->getPost('delete'));
			
			if($id > 0 && $delete == 'Sim')
			{
				$arrData = array(
				'tr_excluido' => '1'
				);
				
				$where = 'tr_id = ' . $id;
				
				$objTramitacao->update($arrData, $where);
				#$rowsAffected = $objTramitacao->delete($_where);
			}
		}
		else
		{
			$id = (int) $this->_request->getParam('id');
			if($id > 0)
			{
				$this->view->tramitacao = $objTramitacao->fetchRow('tr_id='.$id);
				if($this->view->tramitacao->tr_id > 0)
				{
					Zend_Loader::loadClass('DeleteConfirmationForm');
					
					$this->view->form = new DeleteConfirmationForm(
						array(
							'action' => $this->view->baseUrl.'/tramitacao/delete',
							'method' => 'post'
						)
					);
					
					// additional view fields required by form
					$trId = new Zend_Form_Element_Hidden('id');
					$trId->setValue($this->view->tramitacao->tr_id);
					$this->view->form->addElement($trId);
					
					return $this->render();
				}
			}
		}
		
		// redireciona à listagem novamente
		return $this->_helper->redirector('list');
		#$this->_redirect('list');
	}
	
	public function batchAction()
	{
		// Redireciona para impressao da guia dos documentos salvos em lote com unidade de destino informada
		#$this->_helper->viewRenderer->setNoRender();
		#echo $this->view->controllerName.'/printguia/'.$this->view->actionName.'/1';exit;
		$this->_redirect($this->view->controllerName.'/printguia/'.$this->view->actionName.'/1');
	}
	
	public function listAction()
	{
		$this->view->title = 'Listagem de Tramitações';
		
		$this->view->tramitacaoSearchForm = $this->getTramitacaoSearchForm();
		
		$dcNumero = $unId = $dataInicial = $dataFinal = null;
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			
			$request = $this->getRequest();
			// Check if we have a POST request
			if(!$request->isPost())
			{
				return $this->_helper->redirector('list');
			}
			
			$filter		 			 	  = new Zend_Filter_StripTags();
			$this->view->urlBotaoImprimir = '';
			
			$dcNumero    			 	  = (string) trim($filter->filter($this->_request->getPost('dc_numero')));			
			$this->view->urlBotaoImprimir = (strlen(trim($dcNumero))) ? '/dc_numero/'.$dcNumero : '';
			
			$unId	  	 				  = (int) $this->_request->getPost('un_id');
			$this->view->urlBotaoImprimir.= ($unId > 0) ? '/un_id/'.$unId : '';

			// Formata a data inicial no formato do MSSQL 2000 m-d-Y
			if(strlen(trim($filter->filter($this->_request->getPost('data_inicial')))) && $this->_request->getPost('data_inicial') != 'dd/mm/aaaa')
			{
				$dataInicial 			 	  = (string) trim($filter->filter($this->_request->getPost('data_inicial')));
				$dataInicial 				  = explode("-", str_replace("/", "-", $dataInicial));
				$dataInicial 				  = $dataInicial[1]."-".$dataInicial[0]."-".$dataInicial[2];
				$this->view->urlBotaoImprimir.= (strlen(trim($dataInicial))) ? '/data_inicial/'.trim($filter->filter($this->_request->getPost('data_inicial'))) : '';
			}
			
			// Formata a data final no formato do MSSQL 2000 m-d-Y
			if(strlen(trim($filter->filter($this->_request->getPost('data_final')))) && $this->_request->getPost('data_final') != 'dd/mm/aaaa')
			{
				$dataFinal  			 	  = (string) trim($filter->filter($this->_request->getPost('data_final')));
				$dataFinal 				  	  = explode("-", str_replace("/", "-", $dataFinal));
				$dataFinal 				  	  = $dataFinal[1]."-".$dataFinal[0]."-".$dataFinal[2];
				$this->view->urlBotaoImprimir.= (strlen(trim($dataFinal))) ? '/data_final/'.trim($filter->filter($this->_request->getPost('data_final'))) : '';
			}
			
			// additional view fields required by form
			$this->view->tramitacaoSearchForm->setDefault('dc_numero', $dcNumero);
			$this->view->tramitacaoSearchForm->setDefault('un_id', $unId);
			$this->view->tramitacaoSearchForm->setDefault('data_inicial', trim($filter->filter($this->_request->getPost('data_inicial'))));
			$this->view->tramitacaoSearchForm->setDefault('data_final', trim($filter->filter($this->_request->getPost('data_final'))));
		}
		
		$objTramitacao 			 = new Tramitacao();
		$this->view->tramitacoes = $objTramitacao->getTramitacoes($this->view->userUnitId, null, $dcNumero, $unId, $dataInicial, $dataFinal);
		
		if($this->_request->getParam('pagina', 1) > 0)
		{
			Zend_loader::loadClass('Zend_Paginator');
			Zend_loader::loadClass('Zend_View_Helper_PaginationControl');
			
			$paginator = Zend_Paginator::factory($this->view->tramitacoes);
			$paginator->setCurrentPageNumber($this->_request->getParam('pagina', 1));
			$paginator->setItemCountPerPage(10); //número de registros por página
			
			/*Zend_Paginator::setDefaultScrollingStyle('Sliding');
			/*Zend_View_Helper_PaginationControl::setDefaultViewPartial(
			    'pagination/partial.phtml'
			);
			$paginator->setView($this->view);*/
			
			// additional view fields required by form
			$page = new Zend_Form_Element_Hidden('pagina');
			$page->setValue($this->_request->getParam('pagina', 1));
			
			$this->view->tramitacaoSearchForm->addElement($page);
			
			$this->view->pages	   = $paginator->getPages();
			$this->view->paginator = $paginator;
		}
		
		$this->render();		
	}
	
	public function listpendenciesAction()
	{
		$this->view->title = 'Listagem de Documentos Tramitados Pendentes';
		$sqlFiltro	 	   = '';
		
		$this->view->tramitacoesPendentesSearchForm = $this->getTramitacoesPendentesSearchForm();
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			$objDocumento = new Documento();
			
			$filter = new Zend_Filter_StripTags();
			
			$dcNumero    			 	  = (string) trim($filter->filter($this->_request->getPost('dc_numero')));						
			$this->view->urlBotaoImprimir = (strlen(trim($dcNumero))) ? '/dc_numero/'.$dcNumero : '';
			// Filtra pelo id do documento se foi passado o número do documento, variável $dcNumero
			$sqlFiltro.= (strlen(trim($dcNumero))) ? ' AND "TB_DOC".dc_id = '.$objDocumento->fetchRow("dc_numero = '".$dcNumero."'")->dc_id : '';
		
			$trNumeroGuia = (string) trim($filter->filter($this->_request->getPost('tr_numero_guia')));
			$trNumeroGuia = explode('/', $trNumeroGuia);
			$sqlFiltro.= ((int) $trNumeroGuia[0] > 0) ? " AND tr_numero_guia = ".$trNumeroGuia[0] : ''; 
			
			// Formata a data inicial no formato do MSSQL 2000 m-d-Y
			if(strlen(trim($filter->filter($this->_request->getPost('data_inicial')))) && $this->_request->getPost('data_inicial') != 'dd/mm/aaaa')
			{
				$dataInicial 			 	  = (string) trim($filter->filter($this->_request->getPost('data_inicial')));
				$dataInicial 				  = explode("-", str_replace("/", "-", $dataInicial));
				$dataInicial 				  = $dataInicial[1]."-".$dataInicial[0]."-".$dataInicial[2];
				
				// Filtra pela data inicial da tramitação se foi passada a variável $dataInicial
				$sqlFiltro.= (strlen(trim($dataInicial))) ? " AND tr_data_inicio >= '".$dataInicial."'" : '';
				
				$this->view->urlBotaoImprimir.= (strlen(trim($dataInicial))) ? '/data_inicial/'.trim($filter->filter($this->_request->getPost('data_inicial'))) : '';
			}
			
			// Formata a data final no formato do MSSQL 2000 m-d-Y
			if(strlen(trim($filter->filter($this->_request->getPost('data_final')))) && $this->_request->getPost('data_final') != 'dd/mm/aaaa')
			{
				$dataFinal  			 	  = (string) trim($filter->filter($this->_request->getPost('data_final')));
				$dataFinal 				  	  = explode("-", str_replace("/", "-", $dataFinal));
				$dataFinal 				  	  = $dataFinal[1]."-".$dataFinal[0]."-".$dataFinal[2];
				
				// Filtra pela data inicial da tramitaçãoo se foi passada a variável $dataFinal
				$sqlFiltro.= (strlen(trim($dataFinal))) ? " AND tr_data_inicio <= '".$dataFinal."'" : '';
		
				$this->view->urlBotaoImprimir.= (strlen(trim($dataFinal))) ? '/data_final/'.trim($filter->filter($this->_request->getPost('data_final'))) : '';
			}
		}
			
		$objTramitacao = new Tramitacao();
		$this->view->tramitacoes = $objTramitacao->getTramitacoesPendentes($this->view->userUnitId, $this->view->userTipoId, $sqlFiltro);
		
		if($this->_request->getParam('pagina', 1) > 0)
		{
			Zend_loader::loadClass('Zend_Paginator');
			Zend_loader::loadClass('Zend_View_Helper_PaginationControl');
			
			$paginator = Zend_Paginator::factory($this->view->tramitacoes);
			$paginator->setCurrentPageNumber($this->_request->getParam('pagina', 1));
			$paginator->setItemCountPerPage(10); //número de registros por página
			
			/*Zend_Paginator::setDefaultScrollingStyle('Sliding');
			/*Zend_View_Helper_PaginationControl::setDefaultViewPartial(
			    'pagination/partial.phtml'
			);
			$paginator->setView($this->view);*/
			
			// additional view fields required by form
			$page = new Zend_Form_Element_Hidden('pagina');
			$page->setValue($this->_request->getParam('pagina', 1));
			
			$this->view->tramitacoesPendentesSearchForm->addElement($page);
			
			$this->view->pages	   = $paginator->getPages();
			$this->view->paginator = $paginator;
		}
		
		$this->render();
	}
	
	public function finallyAction()
	{
		$this->view->title = "Finalizar Tramitação?";
		
		$objTramitacao = new Tramitacao();
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			
			$filter = new Zend_Filter_Alpha();
			$ids 	= is_array($this->_request->getPost('ids')) ? $this->_request->getPost('ids') : explode(";", $this->_request->getPost('ids'));
			$edit 	= $filter->filter($this->_request->getPost('edit'));

			// Finaliza documentos tramitados
			if(count($ids) > 0 && $edit == 'Sim')
			{
				$this->db->beginTransaction();			
				try
				{
					foreach($ids as $id)
					{
						$id 		= (int) $id; 
						$tramitacao = $objTramitacao->fetchRow('tr_id='.$id);
					
						/**  Caso o usuário logado for da unidade de destino do documento finaliza a tramitação */
						if($this->view->userUnitId == $tramitacao->un_id)
						{
							// Data Término Tramitação
							#$trDataTermino = new Zend_Db_Expr('NOW()');
							$objDate 		= new Zend_Date();
							$objLocale  	= new Zend_Locale('pt_BR');
							Zend_Date::setOptions(array('format_type' => 'php'));
							#echo $objDate->get(); /** Output of the desired Timestamp date */
							$trDataTermino = $objDate->toString('m-d-Y H:i:s'); /** pt_BR format */
							
							$arrData = array(
							'tr_data_termino' => $trDataTermino
							);
							
							//$where = 'tr_id = ' . $id;
							$where = $objTramitacao->getAdapter()->quoteInto('tr_id = ?', $id);
							
							$rowsAffected[] = $objTramitacao->update($arrData, $where);
							#$rowsAffected = $objTramitacao->delete($_where);
						}
					}
					
					if(count($rowsAffected) == count($ids))
					{
						$this->db->commit();
					}
					else
					{
						$this->db->rollBack();
						$this->view->err = 'Erro na transação de recebimento das tramitações em lote.';
					}
				}
				catch(Exception $e)
				{
					$this->db->rollBack();
					$this->view->err = $e;
				}
					
				return $this->_helper->redirector('listpendencies');
			}
			// Retorna a listagem de documentos pendentes na unidade
			elseif($edit == 'Não')
			{
				return $this->_helper->redirector('listpendencies');
			}
			// Confirma finalização (recebimento) dos documentos tramitados
			else
			{
				Zend_Loader::loadClass('EditConfirmationForm');
				
				/* **************** Une Ids das Tramitações num único array */
				//$ids = count($this->_request->getPost('ids')) ? $this->_request->getPost('ids') : array($this->_request->getPost('id'));
				if(is_array($this->_request->getPost('ids')))
					$ids = $this->_request->getPost('ids');
				else
				 	$ids = explode(";", $this->_request->getPost('ids'));
				
				$arrIds = array();
				foreach($ids as $id)
					$arrIds = array_merge($arrIds, explode(";", $id));
				$ids = $arrIds;
				/* **************** Une Ids das Tramitações num único array */
				
				if(count($ids) > 0)
				{
					foreach($ids as $key=>$id)
					{
						$ids[$key] = (int) $id;
					}
						
					$this->view->form = new EditConfirmationForm(
						array(
							'action' => $this->view->baseUrl.'/tramitacao/finally',
							'method' => 'post'
						)
					);
					
					// additional view fields required by form
					$trId = new Zend_Form_Element_Hidden('ids');
					$trId->setValue(implode(";", $ids));
					$this->view->form->addElement($trId);
					
					$this->view->ids = $trId->getValue();
									
					return $this->render();
				}
			}
		}
		
		// redireciona listagem novamente
		return $this->_helper->redirector('listpendencies', 'listpendencies');
		#$this->_redirect('list');
	}
	
	public function printguiaAction()
	{
		$objTramitacao = new Tramitacao();
		
		// Imprime a Guia de documentos a tramitar de para uma única unidade de destino
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			
			$request = $this->getRequest();
		
			// Check if we have a POST request
			if(!$request->isPost())
			{
				return $this->_helper->redirector('index');
			}
			
			
			/*// Validate form
			if(!$form->isValid($request->getPost()))
			{
				// Invalid entries
				$this->view->form = $form;
				return $this->render('index'); // re-render the login form
			}*/
			
			$filter 		= new Zend_Filter_StripTags();
			$trId 		 	= explode(';', $filter->filter($this->_request->getPost('tr_id')));			
		}
		// Imprime a Guia de documentos a tramitar em lote agrupados pela unidade de destino
		elseif((int) $this->_request->getParam('batch') > 0)
		{
			$unIds = $objTramitacao->getUnidadesTramitacoesBatch($this->view->userId)->toArray();

			if(!count($unIds))
			{
				$this->view->msg = 'Não há documentos em lote a imprimir guia de tramitação.';
				return $this->render();
			}
			else
			{
				try
				{
					foreach($unIds as $key=>$un)
					{
						$this->db->beginTransaction();
						
						$rowsAfected = array();
						$trId = array();
						
						// Data Início Tramitação
						#$trDataInicio = new Zend_Db_Expr('NOW()');
						Zend_Date::setOptions(array('format_type' => 'php'));				
						$objDate 	  = new Zend_Date();
						$objLocale    = new Zend_Locale('pt_BR');
						$trDataInicio = $objDate->toString('m-d-Y H:i:s'); /** pt_BR format */
		
						// Retorna o próximo número de guia
						$trNumeroGuia = $objTramitacao->getNextId();			
						
						$arrData = array(
						'tr_data_inicio' => $trDataInicio,
						'tr_numero_guia' => $trNumeroGuia
						);
						
						$tramitacoesBatchUnidade = $objTramitacao->getTramitacoesBatchUnidade($this->view->userId, $un['un_id'])->toArray();
						foreach($tramitacoesBatchUnidade as $tramitacaoBatchUnidade)
						{
							$trId[] = $tramitacaoBatchUnidade['tr_id'];
						}
						
						$where = 'tr_id IN ('.implode(", ", $trId).')';
						
						$rowsAffected[] = $objTramitacao->update($arrData, $where);
						
						if($un->qtd_tramites == count($rowsAfected))				
							$this->db->commit();
						else
						{
							$this->db->rollBack();
							$this->view->err = "A transação de impressão da guia da tramitação não foi completada!";
						}
						
						// Adiciona o array de ids de tramitações a matriz de unidades de destino
						$un['tr_id'] = $trId;
						$unIds[$key] = $un;
					}
				}
				catch(Exception $e)
				{
					$this->db->rollBack();
					$this->view->err = "Erro na transação ao atualizar tramitação de documentos em lote.";
				}
			}
		}
		
		// Caso não tenha sido impressa a guia em lote (vindo do cadastro do documento) adiciona o array de ids de tramitações a matriz $unIds
		$unIds = (isset($unIds) && is_array($unIds) && count($unIds)) ? $unIds : array(array('tr_id'=>$trId));
		
		#echo "<pre>";
		#print_r($unIds);
		#echo "</pre>";
		#exit;
		
		//$this->getHelper('viewRenderer')->setNoRender();		
			
		// Load Zend_Pdf class
		Zend_Loader::loadClass('Zend_Pdf'); 
		
		// Create new PDF 
		$pdf = new Zend_Pdf();
		
		// Monta as páginas da guia por unidade de destino 
		foreach($unIds as $un)
		{
			$trId = $un['tr_id'];
				
			// Caso existam ids de tramitações gera PDF da guia de tramitação
			if(count($trId) > 0)
			{
				$this->db->beginTransaction();
				try
				{
					$arrData = array(
					'tr_guia_impressa' => '1'
					);
					
					#$where = 'tr_id = '.$trId;
					$where = 'tr_id IN('.implode(", ", $trId).')';
					
					$rowsAfected = $objTramitacao->update($arrData, $where);
					if($rowsAfected)
						$this->db->commit();
					else
					{
						$this->db->rollBack();
						$this->view->err = "A transação da impressão da guia da tramitação não foi completada!";
					}
				}
				catch(Exception $e)
				{
					$this->db->rollBack();
					$this->view->err = $e;
					
					return $this->_helper->redirector('edit');
				}
			
				// Add new page to the document 
				$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
				$pdf->pages[] = $page;
				
				$pageHeight = $page->getHeight();
				$pageWidth  = $page->getWidth();
	
				$topPos 	= $pageHeight - 36;
				$leftPos 	= 45;
				$bottomPos 	= 36; 
				$rightPos 	= $pageWidth - 90;
				
				$style = new Zend_Pdf_Style();
				$style->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
				$style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
				$style->setLineWidth(3);
				$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8); 
	
				$page->setStyle($style);
				
				$objTramitacao = new Tramitacao();
				$tramitacoes   = $objTramitacao->getTramitacoes($this->view->userUnitId, $trId);
				//Título
				//-----------------------------------------------------------------------------------
				$linha = 15;
				
				$objDate 		= new Zend_Date($tramitacoes['tr_data_inicio']);
				$objLocale  	= new Zend_Locale('pt_BR');
				Zend_Date::setOptions(array('format_type' => 'php'));
				$anotramit = $objDate->toString('Y'); /** pt_BR format */
				
				$page->drawText('Guia para remessa de processo administrativo '. $tramitacoes[0]['tr_numero_guia'].'/'.$anotramit.' - Data: '.$tramitacoes[0]['tr_data_inicio'], $leftPos, $topPos - $linha,'UTF-8');
				$linha += 10;
				$page->drawText('Origem: '.$tramitacoes[0]['orgao_origem'], $leftPos, $topPos - $linha,'UTF-8');
				$linha += 10;
				$page->drawText('Destino: '.$tramitacoes[0]['un_descricao'], $leftPos, $topPos - $linha,'UTF-8');
				//-----------------------------------------------------------------------------------
				
				$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
				$page->setStyle($style);
				
				//Divisor
				//-----------------------------------------------------------------------------------
				$linha = 42;
				for($traco=0; $traco <= $rightPos; $traco++)
				{
					$page->drawText('-', $leftPos + $traco, $topPos - $linha);
				}
				//------------------------------------------------------------------------------------
				
				//Cabeçalho
				//--------------------------------------------------------------------------------------
				$linha = 50;
				
				$page->drawText('Nº Documento', $leftPos, $topPos - $linha, 'UTF-8');
				$page->drawText('Unidade de Destino', $leftPos + 70, $topPos - $linha);
				$page->drawText('Data da Tramitação', $leftPos + 185, $topPos - $linha, 'UTF-8');
				//$page->drawText('Cota', $leftPos + 270, $topPos - $linha);
				$page->drawText('Orgão de Origem', $leftPos + 270, $topPos - $linha, 'UTF-8');
				//$page->drawText('Assunto', $leftPos + 580, $topPos - $linha);
				$page->drawText('Usuário', $leftPos + 450, $topPos - $linha, 'UTF-8');
				//--------------------------------------------------------------------------------------
				
				//Divisor
				//----------------------------------------------------------------------------------------------------
				$linha = 57;
				for ($traco=0; $traco <= $rightPos; $traco++)
				{
					$page->drawText('-', $leftPos + $traco, $topPos - $linha);
				}
				//-----------------------------------------------------------------------------------------------------
				
				//Corpo do relatório
				//-----------------------------------------------------------------------------------------------------
				
				#echo '<pre>'; print_r ($tramitacoes); exit;
				
				$linha = 75;
				foreach($tramitacoes as $row)
				{
					$registro = strip_tags($row['dc_numero']);
					$registro = wordwrap($registro , 30, '\n');
					$headlineArray1 = explode('\n', $registro );
					
					$registro = strip_tags($row['un_descricao']);
					$registro = wordwrap($registro , 30, '\n');
					$headlineArray2 = explode('\n', $registro );
					
					$registro = strip_tags($row['tr_data_inicio']);
					$registro = wordwrap($registro , 25, '\n');
					$headlineArray3 = explode('\n', $registro );
					
					$registro = strip_tags($row['orgao_origem']);
					$registro = wordwrap($registro , 50, '\n');
					$headlineArray4 = explode('\n', $registro );
					
					$registro = strip_tags($row['us_nome']);
					$registro = wordwrap($registro , 30, '\n');
					$headlineArray5 = explode('\n', $registro );
					
					
					if ($topPos - ((sizeof($headlineArray1) * 15) + $linha) <= $bottomPos || $topPos - ((sizeof($headlineArray2) * 15) + $linha) <= $bottomPos
					|| $topPos - ((sizeof($headlineArray3) * 15) + $linha) <= $bottomPos || $topPos - ((sizeof($headlineArray4) * 15) + $linha) <= $bottomPos )
					{
						// Add new page 
						$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
						$pdf->pages[] = $page;
						
						$style = new Zend_Pdf_Style();
						$style->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
						$style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
						$style->setLineWidth(3);
						$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8); 
						
						$page->setStyle($style);
						
						//Divisor
						//-----------------------------------------------------------------------------------
						$linha = 25;
						for ($traco=0; $traco <= $rightPos; $traco++)
						{
							$page->drawText('-', $leftPos + $traco, $topPos - $linha);
						}
						//------------------------------------------------------------------------------------
						
						//Cabeçalho
						//--------------------------------------------------------------------------------------
						$linha = 40;
						$page->drawText('Nº Documento', $leftPos, $topPos - $linha, 'UTF-8');
						$page->drawText('Unidade de Destino', $leftPos + 70, $topPos - $linha);
						$page->drawText('Data da Tramitação', $leftPos + 185, $topPos - $linha,'UTF-8');
						//$page->drawText('Cota', $leftPos + 270, $topPos - $linha);
						$page->drawText('Orgão Origem', $leftPos + 270, $topPos - $linha, 'UTF-8');
						//$page->drawText('Assunto', $leftPos + 580, $topPos - $linha);
						$page->drawText('Usuário', $leftPos + 450, $topPos - $linha, 'UTF-8');
						//--------------------------------------------------------------------------------------
						
						//Divisor
						//----------------------------------------------------------------------------------------------------
						$linha = 55;
						for ($traco=0; $traco <= $rightPos; $traco++)
						{
							$page->drawText('-', $leftPos + $traco, $topPos - $linha);
						}
						//-----------------------------------------------------------------------------------------------------
						$linha = 87;
					}
					
					
					//$repline = true;
					//$contline = 0;
					
					
					foreach ($headlineArray1 as $line)
					{
						$line = ltrim($line);
						$page->drawText($line, $leftPos, $topPos - $linha,'UTF-8');
						$linha+= 15;
					}	
					
					$linha = $linha - (15 * sizeof($headlineArray1));
	
					$regmaior = sizeof($headlineArray1);
					
					foreach ($headlineArray2 as $line)
					{
						$line = ltrim($line);
						$page->drawText($line, $leftPos + 70, $topPos - $linha,'UTF-8');
						$linha+= 15;
					}	
					
					$linha = $linha - (15 * sizeof($headlineArray2));
					
					if ($regmaior < sizeof($headlineArray2))
					{
						$regmaior = sizeof($headlineArray2);
					}
					
					foreach ($headlineArray3 as $line)
					{
						$line = ltrim($line);
						$page->drawText($line, $leftPos + 185, $topPos - $linha,'UTF-8');
						$linha+= 15;
					}	
					
					$linha = $linha - (15 * sizeof($headlineArray3));
					
					if ($regmaior < sizeof($headlineArray3))
					{
						$regmaior = sizeof($headlineArray3);
					}
					
					foreach ($headlineArray4 as $line)
					{
						$line = ltrim($line);
						$page->drawText($line, $leftPos + 270, $topPos - $linha,'UTF-8');
						$linha+= 15;
					}	
					
					$linha = $linha - (15 * sizeof($headlineArray4));
					
					if ($regmaior < sizeof($headlineArray4))
					{
						$regmaior = sizeof($headlineArray4);
					}
					
					foreach ($headlineArray5 as $line)
					{
						$line = ltrim($line);
						$page->drawText($line, $leftPos + 450, $topPos - $linha,'UTF-8');
						$linha+= 15;
					}
					
					if ($regmaior < sizeof($headlineArray5))
					{
						$regmaior = sizeof($headlineArray5);
					}
					
					$linha = $linha - (15 * sizeof($headlineArray5));
					$linha+= ($regmaior * 15);
				}
				
				$linha = 750;
				$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 6);
				$page->setStyle($style);
				
				$page->drawText('Recebi em ___/___/___ às ___:___ os documentos relacionados acima', $leftPos, $topPos - $linha,'UTF-8');
				$linha += 15;
				$page->drawText('__________________________', $leftPos, $topPos - $linha,'UTF-8');
				$linha += 8;
				$page->drawText('              Nome Legível', $leftPos, $topPos - $linha,'UTF-8');
				//------------------------------------------------------------------------------------------------------
				
				//$pdf->pages[0] = ($page);
			}
		}
		//------------------------------------------------------------------------------------------------------
		
		$this->view->pdf = $pdf->render();
		
		$this->render();
	}
	
	public function printAction()
	{
		/* ------------------------------- Filtros se Passados p/ o método listAction e na URL no botão no menu imprimir ----------------------------------------- */
		$dcNumero = $unId = $dataInicial = $dataFinal = null;
		
		$filter		 			 	  = new Zend_Filter_StripTags();
		$this->view->urlBotaoImprimir = '';
		
		$dcNumero    			 	  = (string) trim($filter->filter($this->_request->getPost('dc_numero')));			
		$this->view->urlBotaoImprimir = (strlen(trim($dcNumero))) ? '/dc_numero/'.$dcNumero : '';
		
		$unId	  	 				  = (int) $this->_request->getPost('un_id');
		$this->view->urlBotaoImprimir.= ($unId > 0) ? '/un_id/'.$unId : '';

		// Formata a data inicial no formato do MSSQL 2000 m-d-Y
		if(strlen(trim($filter->filter($this->_request->getPost('data_inicial')))) && $this->_request->getPost('data_inicial') != 'dd/mm/aaaa')
		{
			$dataInicial 			 	  = (string) trim($filter->filter($this->_request->getPost('data_inicial')));
			$dataInicial 				  = explode("-", str_replace("/", "-", $dataInicial));
			$dataInicial 				  = $dataInicial[1]."-".$dataInicial[0]."-".$dataInicial[2];
			$this->view->urlBotaoImprimir.= (strlen(trim($dataInicial))) ? '/data_inicial/'.trim($filter->filter($this->_request->getPost('data_inicial'))) : '';
		}
		
		// Formata a data final no formato do MSSQL 2000 m-d-Y
		if(strlen(trim($filter->filter($this->_request->getPost('data_final')))) && $this->_request->getPost('data_final') != 'dd/mm/aaaa')
		{
			$dataFinal  			 	  = (string) trim($filter->filter($this->_request->getPost('data_final')));
			$dataFinal 				  	  = explode("-", str_replace("/", "-", $dataFinal));
			$dataFinal 				  	  = $dataFinal[1]."-".$dataFinal[0]."-".$dataFinal[2];
			$this->view->urlBotaoImprimir.= (strlen(trim($dataFinal))) ? '/data_final/'.trim($filter->filter($this->_request->getPost('data_final'))) : '';
		}
		/* ------------------------------- Filtros se Passados p/ o método listAction e na URL no botão no menu imprimir ----------------------------------------- */
		
		$objTramitacao 			 = new Tramitacao();
		$tramitacoes = $objTramitacao->getTramitacoes($this->view->userUnitId, null, $dcNumero, $unId, $dataInicial, $dataFinal);
		
		if ($dcNumero == null)
		{
			// Load Zend_Pdf class
			Zend_Loader::loadClass('Zend_Pdf'); 
			
			$this->getHelper('viewRenderer')->setNoRender();
			
			// Create new PDF 
			$pdf = new Zend_Pdf();
			
			// Add new page to the document 
			$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
			$pdf->pages[] = $page;
			
			$pageHeight = $page->getHeight();
			$pageWidth  = $page->getWidth();

			$topPos 	= $pageHeight - 36;
			$leftPos 	= 45;
			$bottomPos 	= 36; 
			$rightPos 	= $pageWidth - 90;
			
			$style = new Zend_Pdf_Style();
			$style->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
			$style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
			$style->setLineWidth(3);
			$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12); 

			$page->setStyle($style);
			
			//Título
			//-----------------------------------------------------------------------------------
			$linha = 15;
			$page->drawText('Relatório de tramitação de documentos', $leftPos + 140, $topPos - $linha,'UTF-8');
			//-----------------------------------------------------------------------------------
			
			$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
			$page->setStyle($style);
			
			//Divisor
			//-----------------------------------------------------------------------------------
			$linha = 25;
			for($traco=0; $traco <= $rightPos; $traco++)
			{
				$page->drawText('-', $leftPos + $traco, $topPos - $linha);
			}
			//------------------------------------------------------------------------------------
			
			//Cabeçalho
			//--------------------------------------------------------------------------------------
			$linha = 40;
			
			
			$page->drawText('Nº Documento', $leftPos, $topPos - $linha, 'UTF-8');
			#$page->drawText('Unidade de Origem', $leftPos, $topPos - $linha,'UTF-8');
			$page->drawText('Unidade de Destino', $leftPos + 70, $topPos - $linha);
			$page->drawText('Data da Tramitação', $leftPos + 185, $topPos - $linha,'UTF-8');
			$page->drawText('Cota', $leftPos + 270, $topPos - $linha);
			#$page->drawText('Assunto', $leftPos + 580, $topPos - $linha);
			$page->drawText('Usuário', $leftPos + 450, $topPos - $linha, 'UTF-8');
			//--------------------------------------------------------------------------------------
			
			//Divisor
			//----------------------------------------------------------------------------------------------------
			$linha = 55;
			for ($traco=0; $traco <= $rightPos; $traco++)
			{
				$page->drawText('-', $leftPos + $traco, $topPos - $linha);
			}
			//-----------------------------------------------------------------------------------------------------
			
			//Corpo do relatório
			//-----------------------------------------------------------------------------------------------------
		}
		else
		{
			// Load Zend_Pdf class
			Zend_Loader::loadClass('Zend_Pdf'); 
			
			$this->getHelper('viewRenderer')->setNoRender();
			
			// Create new PDF 
			$pdf = new Zend_Pdf();
			
			// Add new page to the document 
			$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
			$pdf->pages[] = $page;
			
			$pageHeight = $page->getHeight();
			$pageWidth  = $page->getWidth();

			$topPos 	= $pageHeight - 36;
			$leftPos 	= 45;
			$bottomPos 	= 36; 
			$rightPos 	= $pageWidth - 90;
			
			$style = new Zend_Pdf_Style();
			$style->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
			$style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
			$style->setLineWidth(3);
			$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14); 

			$page->setStyle($style);
			
			//Título
			//-----------------------------------------------------------------------------------
			$linha = 15;
			$page->drawText('Relatório de tramitação de documentos', $leftPos + 250, $topPos - $linha,'UTF-8');
			//-----------------------------------------------------------------------------------
			
			$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
			$page->setStyle($style);
			
			//Divisor
			//-----------------------------------------------------------------------------------
			$linha = 25;
			for($traco=0; $traco <= $rightPos; $traco++)
			{
				$page->drawText('-', $leftPos + $traco, $topPos - $linha);
			}
			//------------------------------------------------------------------------------------
			$objdocumento = new Documento();
			$documentos = $objdocumento->getDocumentos(null,array($tramitacoes[0]['dc_id']));
			$documentos = $documentos[0];
			
			$linha = 35;
			$page->drawText('Documento: '.$documentos['dc_numero'] , $leftPos, $topPos - $linha,'UTF-8');
			$linha+= 15;
			$page->drawText('Orgão de origem: '.$documentos['orgao_origem'], $leftPos, $topPos - $linha,'UTF-8');
			$linha+= 15;
			$page->drawText('Data de elaboração: '.$documentos['dc_data_elaboracao'], $leftPos, $topPos - $linha,'UTF-8');
			$linha+= 15;
			$page->drawText('Assunto: ' .$documentos['as_descricao'], $leftPos, $topPos - $linha,'UTF-8');
			$linha+= 15;
			$page->drawText('Compl. Assunto: '.$documentos['dc_compl_assunto'], $leftPos, $topPos - $linha,'UTF-8');
			
			//Divisor
			//-----------------------------------------------------------------------------------
			$linha = 105;
			for($traco=0; $traco <= $rightPos; $traco++)
			{
				$page->drawText('-', $leftPos + $traco, $topPos - $linha);
			}
			//------------------------------------------------------------------------------------
			
			//Cabeçalho
			//--------------------------------------------------------------------------------------
			$linha = 115;
			
			
			$page->drawText('Nº Documento', $leftPos, $topPos - $linha, 'UTF-8');
			#$page->drawText('Unidade de Origem', $leftPos, $topPos - $linha,'UTF-8');
			$page->drawText('Unidade de Destino', $leftPos + 70, $topPos - $linha);
			$page->drawText('Data da Tramitação', $leftPos + 290, $topPos - $linha,'UTF-8');
			$page->drawText('Usuário', $leftPos + 400, $topPos - $linha,'UTF-8');
			#$page->drawText('Assunto', $leftPos + 580, $topPos - $linha);
			$page->drawText('Cota', $leftPos + 500, $topPos - $linha, 'UTF-8');
			//--------------------------------------------------------------------------------------
			
			//Divisor
			//----------------------------------------------------------------------------------------------------
			$linha = 125;
			for ($traco=0; $traco <= $rightPos; $traco++)
			{
				$page->drawText('-', $leftPos + $traco, $topPos - $linha);
			}
			//-----------------------------------------------------------------------------------------------------
			
			//Corpo do relatório
			//-----------------------------------------------------------------------------------------------------
		}
		
		if ($dcNumero == null)
		{
			$linha = 87;
			foreach($tramitacoes as $row)
			{
				$registro = strip_tags($row['dc_numero']);
				$registro = wordwrap($registro , 30, '\n');
				$headlineArray1 = explode('\n', $registro );
				
				$registro = strip_tags($row['un_descricao']);
				$registro = wordwrap($registro , 30, '\n');
				$headlineArray2 = explode('\n', $registro );
				
				$objDate 		= new Zend_Date($row['tr_data_inicio']);
				$objLocale  	= new Zend_Locale('pt_BR');
				Zend_Date::setOptions(array('format_type' => 'php'));
				$row['tr_data_inicio'] = $objDate->toString('d/m/Y H:i'); /** pt_BR format */
				
				$registro = strip_tags($row['tr_data_inicio']);
				$registro = wordwrap($registro , 25, '\n');
				$headlineArray3 = explode('\n', $registro );
				
				$registro = strip_tags($row['tr_cota']);
				$registro = wordwrap($registro , 50, '\n');
				$headlineArray4 = explode('\n', $registro );
				
				$registro = strip_tags($row['us_nome']);
				$registro = wordwrap($registro , 30, '\n');
				$headlineArray5 = explode('\n', $registro );
				
				
				if ($topPos - ((sizeof($headlineArray1) * 15) + $linha) <= $bottomPos || $topPos - ((sizeof($headlineArray2) * 15) + $linha) <= $bottomPos
				|| $topPos - ((sizeof($headlineArray3) * 15) + $linha) <= $bottomPos || $topPos - ((sizeof($headlineArray4) * 15) + $linha) <= $bottomPos )
				{
					// Add new page 
					$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
					$pdf->pages[] = $page;
					
					$style = new Zend_Pdf_Style();
					$style->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
					$style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
					$style->setLineWidth(3);
					$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8); 

					$page->setStyle($style);
										
					//Divisor
					//-----------------------------------------------------------------------------------
					$linha = 25;
					for ($traco=0; $traco <= $rightPos; $traco++)
					{
						$page->drawText('-', $leftPos + $traco, $topPos - $linha);
					}
					//------------------------------------------------------------------------------------
					
					//Cabeçalho
					//--------------------------------------------------------------------------------------
					$linha = 40;
					$page->drawText('Nº Documento', $leftPos, $topPos - $linha, 'UTF-8');
					$page->drawText('Unidade de Destino', $leftPos + 70, $topPos - $linha);
					$page->drawText('Data da Tramitação', $leftPos + 185, $topPos - $linha,'UTF-8');
					$page->drawText('Cota', $leftPos + 270, $topPos - $linha);
					$page->drawText('Usuário', $leftPos + 450, $topPos - $linha, 'UTF-8');
					//--------------------------------------------------------------------------------------
					
					//Divisor
					//----------------------------------------------------------------------------------------------------
					$linha = 55;
					for ($traco=0; $traco <= $rightPos; $traco++)
					{
						$page->drawText('-', $leftPos + $traco, $topPos - $linha);
					}
					//-----------------------------------------------------------------------------------------------------
					$linha = 87;
				}
				
				
				//$repline = true;
				//$contline = 0;
				
				
				foreach ($headlineArray1 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}	
				
				$linha = $linha - (15 * sizeof($headlineArray1));

				$regmaior = sizeof($headlineArray1);
				
				foreach ($headlineArray2 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos + 70, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}	
				
				$linha = $linha - (15 * sizeof($headlineArray2));
				
				if ($regmaior < sizeof($headlineArray2))
				{
					$regmaior = sizeof($headlineArray2);
				}
				
				foreach ($headlineArray3 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos + 185, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}	
				
				$linha = $linha - (15 * sizeof($headlineArray3));
				
				if ($regmaior < sizeof($headlineArray3))
				{
					$regmaior = sizeof($headlineArray3);
				}
				
				foreach ($headlineArray4 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos + 270, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}	
				
				$linha = $linha - (15 * sizeof($headlineArray4));
				
				if ($regmaior < sizeof($headlineArray4))
				{
					$regmaior = sizeof($headlineArray4);
				}
				
				foreach ($headlineArray5 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos + 450, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}
				
				if ($regmaior < sizeof($headlineArray5))
				{
					$regmaior = sizeof($headlineArray5);
				}
				
				$linha = $linha - (15 * sizeof($headlineArray5));
				$linha+= ($regmaior * 15);
			}
			
		}
		else
		{
			$linha = 140;
			foreach($tramitacoes as $row)
			{
				$registro = strip_tags($row['dc_numero']);
				$registro = wordwrap($registro , 30, '\n');
				$headlineArray1 = explode('\n', $registro );
				
				$registro = strip_tags($row['un_descricao']);
				$registro = wordwrap($registro , 70, '\n');
				$headlineArray2 = explode('\n', $registro );
				
				$objDate 		= new Zend_Date($row['tr_data_inicio']);
				$objLocale  	= new Zend_Locale('pt_BR');
				Zend_Date::setOptions(array('format_type' => 'php'));
				$row['tr_data_inicio'] = $objDate->toString('d/m/Y H:i'); /** pt_BR format */
				
				$registro = strip_tags($row['tr_data_inicio']);
				$registro = wordwrap($registro , 25, '\n');
				$headlineArray3 = explode('\n', $registro );
				
				$registro = strip_tags($row['us_nome']);
				$registro = wordwrap($registro , 50, '\n');
				$headlineArray4 = explode('\n', $registro );
				
				$registro = strip_tags($row['tr_cota']);
				$registro = wordwrap($registro , 55, '\n');
				$headlineArray5 = explode('\n', $registro );
				
				
				if ($topPos - ((sizeof($headlineArray1) * 15) + $linha) <= $bottomPos || $topPos - ((sizeof($headlineArray2) * 15) + $linha) <= $bottomPos
				|| $topPos - ((sizeof($headlineArray3) * 15) + $linha) <= $bottomPos || $topPos - ((sizeof($headlineArray4) * 15) + $linha) <= $bottomPos )
				{
					// Add new page 
					$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
					$pdf->pages[] = $page;
					
					$style = new Zend_Pdf_Style();
					$style->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
					$style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
					$style->setLineWidth(3);
					$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10); 

					$page->setStyle($style);
										
					//Divisor
					//-----------------------------------------------------------------------------------
					$linha = 25;
					for ($traco=0; $traco <= $rightPos; $traco++)
					{
						$page->drawText('-', $leftPos + $traco, $topPos - $linha);
					}
					//------------------------------------------------------------------------------------
					
					//Cabeçalho
					//--------------------------------------------------------------------------------------
					$linha = 40;
					$page->drawText('Nº Documento', $leftPos, $topPos - $linha, 'UTF-8');
					$page->drawText('Unidade de Destino', $leftPos + 70, $topPos - $linha);
					$page->drawText('Data da Tramitação', $leftPos + 290, $topPos - $linha,'UTF-8');
					$page->drawText('Usuário', $leftPos + 400, $topPos - $linha);
					$page->drawText('Cota', $leftPos + 500, $topPos - $linha, 'UTF-8');
					//--------------------------------------------------------------------------------------
					
					//Divisor
					//----------------------------------------------------------------------------------------------------
					$linha = 55;
					for ($traco=0; $traco <= $rightPos; $traco++)
					{
						$page->drawText('-', $leftPos + $traco, $topPos - $linha);
					}
					//-----------------------------------------------------------------------------------------------------
					$linha = 87;
				}
				
				
				//$repline = true;
				//$contline = 0;
				
				
				foreach ($headlineArray1 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}	
				
				$linha = $linha - (15 * sizeof($headlineArray1));

				$regmaior = sizeof($headlineArray1);
				
				foreach ($headlineArray2 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos + 70, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}	
				
				$linha = $linha - (15 * sizeof($headlineArray2));
				
				if ($regmaior < sizeof($headlineArray2))
				{
					$regmaior = sizeof($headlineArray2);
				}
				
				foreach ($headlineArray3 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos + 290, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}	
				
				$linha = $linha - (15 * sizeof($headlineArray3));
				
				if ($regmaior < sizeof($headlineArray3))
				{
					$regmaior = sizeof($headlineArray3);
				}
				
				foreach ($headlineArray4 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos + 400, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}	
				
				$linha = $linha - (15 * sizeof($headlineArray4));
				
				if ($regmaior < sizeof($headlineArray4))
				{
					$regmaior = sizeof($headlineArray4);
				}
				
				foreach ($headlineArray5 as $line)
				{
					$line = ltrim($line);
					$page->drawText($line, $leftPos + 500, $topPos - $linha,'UTF-8');
					$linha+= 15;
				}
				
				if ($regmaior < sizeof($headlineArray5))
				{
					$regmaior = sizeof($headlineArray5);
				}
				
				$linha = $linha - (15 * sizeof($headlineArray5));
				$linha+= ($regmaior * 15);
			}
		}
		//------------------------------------------------------------------------------------------------------
		
		//$pdf->pages[0] = ($page);
		
		$this->view->pdf = $pdf->render();
		
		$this->render();
	}
	
	/**
     * Outros métodos
     * 
     */    
    public function getForm($formClassName='TramitacaoForm')
    {
		// Formulário de seleção de documentos a tramitar
		if($formClassName == 'TramitacaoDocumentosForm1')
		{
			return new TramitacaoDocumentosForm1(
				array(
					'action' => $this->view->baseUrl . '/tramitacao/index',
					'method' => 'post'
				)
			);
		}
		// Formulário de dados complementares da tramitação
		elseif($formClassName == 'TramitacaoDocumentosForm2')
		{	
			return new TramitacaoDocumentosForm2(
				array(
					'action' => $this->view->baseUrl . '/tramitacao/add',
					'method' => 'post',
				)
			);
		}
		// Formulário de impressão da guia da tramitação
		elseif($formClassName == 'ImprimirTramitacaoDocumentosForm')
		{
			return new ImprimirTramitacaoDocumentosForm(
				array(
					'action' => $this->view->baseUrl . '/tramitacao/print',
					'method' => 'post'
				)
			);
		}
		// Formulário de Adição de um único documento
		else
		{
			return new TramitacaoForm(
				array(
					'action' => $this->view->baseUrl . '/tramitacao/add',
					'method' => 'post'
				)
			);
		}
    }
	
	public function getTramitacaoSearchForm()
    {
        return new TramitacaoSearchForm(
        	array(
            	'action' => $this->view->baseUrl.'/'.$this->view->controllerName.'/list',
            	'method' => 'post'
        	)
        );
    }
    
    public function getTramitacoesPendentesSearchForm()
    {
    	return new TramitacoesPendentesSearchForm(
    		array(
    			'action' => $this->view->baseUrl.'/'.$this->view->controllerName.'/listpendencies',
				'method' => 'post'
    		)
    	);
    }
}
