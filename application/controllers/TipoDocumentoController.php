<?php
/**
 * Controlador de Tipo de Documento
 * @author Esdras
 * @copyright FGSL 2009
 * @license New SDB
 * @package application
 * @subpackage controllers
 * @filesource 
 */
class TipoDocumentoController extends Zend_Controller_Action 
{
	/**
	 * Inits
	 * 
	 */
	public function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
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
		
		Zend_Loader::loadClass('TipoDocumentoForm');
		Zend_Loader::loadClass('TipoDocumento');
		Zend_Loader::loadClass('Usuario');
		Zend_Loader::loadClass('Zend_Date');
		Zend_Loader::loadClass('Zend_Locale');
	}
	
	/**
	 * Método default
	 *
	 */
	public function indexAction()
	{
		// Caso não seja gestor redireciona para listagem de tipos de documentos
		if($this->view->userTipoId != 1)
			return $this->_helper->redirector('list');
		
		#echo "indexAction";
		$this->view->title = "Cadastro Novo Tipo de Documento";
		$this->view->form = $this->getForm();
		$this->render();
	}
	
	public function addAction()
	{
		// Caso não seja gestor redireciona para listagem de tipos de documentos
		if($this->view->userTipoId != 1)
			return $this->_helper->redirector('list');
		
		$this->view->title = "Salvar Novo Tipo de Documento";
		$request = $this->getRequest();
	
        // Check if we have a POST request
        if(!$request->isPost())
		{
            return $this->_helper->redirector($this->view->baseUrl.'/index');
        }

        // Get our form and validate it
        $form = $this->getForm();
        if(!$form->isValid($request->getPost()))
		{
            // Invalid entries
            $this->view->form = $form;
		    return $this->render('index'); // re-render the tramitacao form
        }
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			
			$objFilter  		= new Zend_Filter_StripTags();
			$tdDescricao 		= (string) trim($objFilter->filter($this->_request->getPost('td_descricao')));
			
			if(strlen($tdDescricao))
			{
				// Data Início Tramitação
				#$trDataInicio = new Zend_Db_Expr('NOW()');				
				$objDate 		= new Zend_Date();
				$objLocale  	= new Zend_Locale('pt_BR');
				Zend_Date::setOptions(array('format_type' => 'php'));
				#echo $objDate->get(); /** Output of the desired Timestamp date */
				$tdDataCadastro = $objDate->toString('m-d-Y H:i:s'); /** pt_BR format */
				
				// ===== Retorna o ID do usuário autenticado (Provisório)
				$usId = Zend_Auth::getInstance()->getIdentity()->us_id;
				
				#$objConfig = new Zend_Config_Ini('./application/config.ini','database');
				#$db = Zend_Db::factory($objConfig->db->adapter, $objConfig->db->config->toArray());
				
				#$sqlUsuario = "SELECT us_id FROM tb_usuario WHERE us_login = '".Zend_Auth::getInstance()->getIdentity()."'";
				#$usId 	    = $db->fetchOne($sqlUsuario);
				#echo "----------------<pre>" . var_dump($resUsuario) . "</pre><br>";
				// ==== Retorna o ID do usuário autenticado (Provisório)
				
				$arrData = array(
				'td_descricao' 		=> $tdDescricao,
				'td_data_cadastro' 	=> $tdDataCadastro,
				'us_id' 			=> $usId
				);
				
				$objTipoDocumento = new TipoDocumento();
				
				$objTipoDocumento->insert($arrData);
				#$id = $objTipoDocumento->lastInsertId();
				
				#$this->_helper->redirector('/');
				$this->_redirect('/');
				return;
			}
			else
			{
				$this->view->detail = "Informe todos os campos obrigatórios.";
			}
		}
	}
	
	public function editAction()
	{
		// Caso não seja gestor redireciona para listagem de tipos de documentos
		if($this->view->userTipoId != 1)
			return $this->_helper->redirector('list');
		
		$this->view->title = "Alterar Tipo de Documento";
		$objTipoDocumento = new TipoDocumento();
		
		// Get our form
		$form = $this->getForm();
		$form->setAction($this->view->baseUrl.'/'.$this->view->controllerName.'/edit');
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			
			$request = $this->getRequest();
			// Check if we have a POST request
			if(!$request->isPost())
			{
				return $this->_helper->redirector('index');
			}
			
			// Validate form
			if(!$form->isValid($request->getPost()))
			{
				// Invalid entries
				$this->view->form = $form;
				return $this->render('index'); // re-render the login form
			}
			
			$filter 		= new Zend_Filter_StripTags();
			$tdId 			= (int) $this->_request->getPost('td_id');
			$tdDescricao	= (string) trim($filter->filter($this->_request->getPost('td_descricao')));
			
			if($tdId > 0 && strlen($tdDescricao))
			{
				$arrData = array(
				'td_descricao' => $tdDescricao
				);
				
				$where = 'td_id = '.$tdId;
				$objTipoDocumento->update($arrData, $where);
				
				return $this->_helper->redirector('list');
			}
			else
			{
				$this->view->tipodocumento = $objTipoDocumento->fetchRow('td_id = '.$tdId);
			}
		}
		else
		{
			// usuario id should be $params['id']
			$tdId = (int) $this->_request->getParam('id',0);
			if($tdId > 0)
			{
				$this->view->tipodocumento = $objTipoDocumento->fetchRow('td_id = '.$tdId);
			}
		}
		
		if($this->view->tipodocumento)
		{
			// additional view fields required by form
			$form->setDefault('td_descricao', $this->view->tipodocumento->td_descricao);
			
			$tdId = new Zend_Form_Element_Hidden('td_id');
			$tdId->setValue($this->view->tipodocumento->td_id);
			$form->addElement($tdId);
		}
		
		$this->view->form = $form;
		$this->render(); // render tipo documento form
	}
	
	public function deleteAction()
	{
		// Caso não seja gestor redireciona para listagem de tipos de documentos
		if($this->view->userTipoId != 1)
			return $this->_helper->redirector('list');
		
		$this->view->title = "Excluir Tipo de Documento";
		
		$objTipoDocumento = new TipoDocumento();
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			
			$filter = new Zend_Filter_Alpha();
			$id 	= (int) $this->_request->getPost('id');
			$delete = $filter->filter($this->_request->getPost('delete'));
			
			if($id > 0 && $delete == 'Sim')
			{
				$arrData = array(
				'td_excluido' => '1'
				);
				
				$where = 'td_id = ' . $id;
				
				$objTipoDocumento->update($arrData, $where);
				#$rowsAffected = $objTipoDocumento->delete($_where);
			}
		}
		else
		{
			$id = (int) $this->_request->getParam('id');
			if($id > 0)
			{
				$this->view->tipodocumento = $objTipoDocumento->fetchRow('td_id='.$id);
				if($this->view->tipodocumento->td_id > 0)
				{
					Zend_Loader::loadClass('DeleteConfirmationForm');
					
					$this->view->form = new DeleteConfirmationForm(
						array(
							'action' => $this->view->baseUrl.'/tipodocumento/delete',
							'method' => 'post'
						)
					);
					
					// additional view fields required by form
					$tdId = new Zend_Form_Element_Hidden('id');
					$tdId->setValue($this->view->tipodocumento->td_id);
					$this->view->form->addElement($tdId);	
					
					return $this->render();
				}
			}
		}
		
		// redireciona à listagem novamente
		return $this->_helper->redirector('list');
		#$this->_redirect('list');
	}
	
	public function listAction()
	{
		$this->view->title = 'Listagem de Tipos de Documentos';
	
		$objTipoDocumento 			 = new TipoDocumento();
		$this->view->tiposdocumentos = $objTipoDocumento->getTiposDocumentos();
		
		$this->render();
	}
	
	/**
     * Outros métodos
     * 
     */    
    public function getForm()
    {
        return new TipoDocumentoForm(
        	array(
            	'action' => $this->view->baseUrl . '/tipodocumento/add',
            	'method' => 'post'
        	)
        );
    }
	
	public function printAction()
	{
		// Caso não seja gestor redireciona para listagem de tipos de documentos
		if($this->view->userTipoId != 1)
			return $this->_helper->redirector('list');
		
		// Load Zend_Pdf class 
		Zend_Loader::loadClass('Zend_Pdf'); 
		
		//$this->getHelper('viewRenderer')->setNoRender();
		
		// Create new PDF 
		$pdf = new Zend_Pdf();
		
		// Add new page to the document 
		$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
		$pdf->pages[] = $page;
		
		$pageHeight = $page->getHeight();
		$pageWidth = $page->getWidth();

		$topPos = $pageHeight - 36;
		$leftPos = 45;
		$bottomPos = 36; 
		$rightPos = $pageWidth - 90;
		
		$style = new Zend_Pdf_Style();
		$style->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
		$style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
		$style->setLineWidth(3);
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 16); 

		$page->setStyle($style);
		
		//Título
		//-----------------------------------------------------------------------------------
		$linha = 15;
		$page->drawText('Tipos de documento', $leftPos, $topPos - $linha,'UTF-8');
		//-----------------------------------------------------------------------------------
		
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
		$page->drawText('Descrição', $leftPos, $topPos - $linha,'UTF-8');
		$page->drawText('Data de cadastro', $leftPos + 150, $topPos - $linha);
		$page->drawText('Usuário', $leftPos + 300, $topPos - $linha,'UTF-8');
		$page->drawText('Excluido/Ativo', $leftPos + 430, $topPos - $linha);
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
		
		$objTipoDocumento = new TipoDocumento();
		$tiposdocumentos = $objTipoDocumento->getTiposDocumentos();
		
		//echo '<pre>'; print_r ($tiposdocumentos); exit;
		
		$linha = 87;
		foreach ($tiposdocumentos as $row)
		{
			$registro = strip_tags($row['td_descricao']);
			$registro = wordwrap($registro , 30, '\n');
            $headlineArray1 = explode('\n', $registro );
			
			$registro = strip_tags(date('d/m/Y H:i',strtotime($row['td_data_cadastro'])));
            $registro = wordwrap($registro , 30, '\n');
            $headlineArray2 = explode('\n', $registro );
			
			$registro = strip_tags($row['us_nome']);
            $registro = wordwrap($registro , 25, '\n');
            $headlineArray3 = explode('\n', $registro );
			
			$registro = strip_tags(($row['td_excluido'] == 0)? 'Ativo':'Finalizado' );
            $registro = wordwrap($registro , 30, '\n');
            $headlineArray4 = explode('\n', $registro );
			
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
				$page->drawText('Descrição', $leftPos, $topPos - $linha,'UTF-8');
				$page->drawText('Data de cadastro', $leftPos + 150, $topPos - $linha);
				$page->drawText('Usuário', $leftPos + 300, $topPos - $linha,'UTF-8');
				$page->drawText('Excluido/Ativo', $leftPos + 430, $topPos - $linha);
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
				$page->drawText($line, $leftPos + 150, $topPos - $linha,'UTF-8');
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
				$page->drawText($line, $leftPos + 300, $topPos - $linha,'UTF-8');
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
				$page->drawText($line, $leftPos + 430, $topPos - $linha,'UTF-8');
				$linha+= 15;
			}	
			
			if ($regmaior < sizeof($headlineArray4))
			{
				$regmaior = sizeof($headlineArray4);
			}
			
			$linha = $linha - (15 * sizeof($headlineArray4));
			$linha+= ($regmaior * 15);
		}	
		
		$this->view->pdf = $pdf->render();
		
		$this->render();
		
	}
}