<?php
/**
 * Controlador de Documento
 * @author Esdras
 * @copyright FGSL 2009
 * @license New SDB
 * @package application
 * @subpackage controllers
 * @filesource 
 */
class DocumentoController extends Zend_Controller_Action 
{
	/**
	 * Inits
	 * 
	 */
	public function init()
	{
		/** Obtendo o objeto view registrado no bootstrap index.php **/
		$objRegistry 	= Zend_Registry::getInstance();
		$this->db	 	= $objRegistry->db;
		$this->config   = $objRegistry->config;
		//$this->view   = $objRegistry->view;
		
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
		else
		{
			// Somente Visualiza os Documentos sem permissões de edição
			$this->view->readonly = 1;
		}
		
		$objRegistry->view = $this->view;
		
		$this->initView();
		Zend_Loader::loadClass('Flex');
		Zend_Loader::loadClass('DocumentoForm');
		Zend_Loader::loadClass('DocumentoSearchForm');
		Zend_Loader::loadClass('Documento');
		Zend_Loader::loadClass('DocumentoArquivo');
		Zend_Loader::loadClass('Zend_Registry');
		Zend_Loader::loadClass('Zend_Date');
		Zend_Loader::loadClass('Zend_Locale');
		
		//$doc = new Flex();
		
		/*Echo "<pre>";
		print_r($doc->getDoc());
		Echo "</pre>";
		
		Echo "<pre>";
		print_r($doc->Arrayflex());
		Echo "</pre>";
		
		#$doc = $doc[0];
		
		
		#$ndoc['primeiro'] = $doc['dc_id'];
		#$ndoc['segundo'] = $doc['dc_numero'];
		#$ndoc['terceiro'] = $doc['td_id'];
		#print_r($ndoc); 
		*/
	}
	
	/**
	 * Método default
	 *
	 */
	public function indexAction()
	{
		$this->view->title = "Cadastro Novo Documento";
		$this->view->form = $this->getForm();
		$this->render();
		#$this->view->welcomeMsg = "Olá " . Zend_Auth::getInstance()->getIdentity(); Zend_Auth
		
		//Zend_Loader::loadClass('DocumentoForm');
		//Zend_Loader::loadClass("Zend_Paginator");
		//Zend_Loader::loadClass("Zend_Paginator_Adapter_Array");
		
		#Zend_Loader::loadClass("Zend_Form");
    	#Zend_Loader::loadClass("SearchAlbumForm");
    	
    	
    	/*$this->view->formSearch = $this->getSearchAlbumForm();    	
    	$this->view->title = "Página de teste de álbuns";
    	
    	$album = new Album();
    	$_where = null;
    	
    	if ( $this->_request->isPost() )
    	{
    		$_where	= $this->ParsePostFilter();
    	}*/
    	
    	/*
    	echo "where = " . $_where;
    	exit();
    	*/
    	
    	//$this->view->albuns = $album->fetchAll($_where);
    	
    	//Zend_Loader::loadClass("Zend_Paginator");
    	//Zend_Loader::loadClass("Zend_Paginator_Adapter_Array");
		//$pagina = $this->_request->getParam('pagina', 1);

    	    
    	//$paginator = Zend_Paginator::factory($this->view->albuns);
    	//$paginator->setItemCountPerPage(1);
    	//$paginator->getItemsByPage(2);
    	//$paginator->setCurrentPageNumber($pagina);
    	
    	//Zend_Paginator::setDefaultScrollingStyle('Sliding');
    	//$this->view->albuns = $paginator;
    	//$this->view->paginator = $paginator;
	}

	public function addAction()
	{
		$this->view->title = "Salvar Novo Documento";
		$request = $this->getRequest();
	
        // Check if we have a POST request
        if(!$request->isPost())
		{
            return $this->_helper->redirector($this->baseUrl.'/index');
        }

        // Get our form and validate it
        $form = $this->getForm();
        if(!$form->isValid($request->getPost()))
		{
            // Invalid entries
            $this->view->form = $form;
		    return $this->render('index'); // re-render the documento form
        }
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			
			$objFilter  		= new Zend_Filter_StripTags();
			$dcNumero			= (string) trim($objFilter->filter($this->_request->getPost('dc_numero')));
			$tdId 				= (int) trim($objFilter->filter($this->_request->getPost('td_id')));
			// Formata a data no formato do MSSQL 2000 m-d-Y
			$dcDataElaboracao 	= (string) trim($objFilter->filter($this->_request->getPost('calendar')));
			$dcDataElaboracao	= explode("-", str_replace("/", "-", $dcDataElaboracao));
			$dcDataElaboracao	= $dcDataElaboracao[1]."-".$dcDataElaboracao[0]."-".$dcDataElaboracao[2];
			$unId				= (int) trim($objFilter->filter($this->_request->getPost('un_id')));
		
			// retorna um objeto do tipo Zend_File_Transfer_Adapter_Http para manipula��o do arquivo enviado
			$adapterDaArquivo = $form->getElement('da_arquivo')->getTransferAdapter();

			#print_r($adapterDaArquivo);
			
			/*echo "<pre>";
			print_r($_REQUEST);
			echo "</pre><br /><br />";*/
			
			#echo "<pre>";
			#print_r($_FILES);
			#echo "</pre>";
			
			if($adapterDaArquivo->isUploaded('da_arquivo'))
			{
				#echo "<pre>";
				#print_r($adapterDaArquivo->getFileInfo());
				#echo "</pre>";
				
				$fileInfo = $adapterDaArquivo->getFileInfo();
				
				// Obtendo os dados do arquivo em bin�rio (Provis�rio)
				#$daArquivo 		 = addslashes(fread(fopen($fileInfo['da_arquivo']['tmp_name'], "r"), $fileInfo['da_arquivo']['size']));
				#$daArquivo = file_get_contents($fileInfo['da_arquivo']['tmp_name']);
				#$daArquivo = unpack("H*hex", $daArquivo);
				#$daArquivo = '0x'.$daArquivo['hex'];
				
				#$daArquivo 		 = file_get_contents($fileInfo['da_arquivo']['tmp_name']);
				#$daArquivo 	 	 = addslashes(fread(fopen($fileInfo['da_arquivo']['tmp_name'], "r"), $fileInfo['da_arquivo']['size']));
				$daArquivoBase64 = base64_encode(fread(fopen($fileInfo['da_arquivo']['tmp_name'], "r"), $fileInfo['da_arquivo']['size']));
				$daTipo			 = $this->getTypeUploadFile($fileInfo['da_arquivo']['type']);
				$daTamanho		 = $fileInfo['da_arquivo']['size'];
				
				#echo strlen($daArquivo);
				#echo strlen($daArquivoBase64);
				
				#echo "<br><hr><br><pre>";
				#print($daArquivoBase64);
				#echo "</pre>";
				#exit;
				
				#echo "<br><hr><br><pre>";
				#print(base64_decode($daArquivoBase64));
				#echo "</pre>";
				#exit;
				
				#header("Content-type: application/pdf");
				#echo(base64_decode($daArquivoBase64));
			}
			
			$asId 				= (int) trim($objFilter->filter($this->_request->getPost('as_id')));
			$dcComplAssunto 	= (string) trim($objFilter->filter($this->_request->getPost('dc_compl_assunto')));
			$oeId 				= (int) trim($objFilter->filter($this->_request->getPost('oe_id')));
			
			if(($tdId > 0) && ($asId > 0) && strlen($dcDataElaboracao))
			{
				$dcComplAssunto 	= (strlen($dcComplAssunto)) ? $dcComplAssunto : null;
				$oeId 			  	= ($oeId > 0) ? $oeId : null;
				#$dc_data_elaboracao = new Zend_Db_Expr('NOW()');
				
				// Data Cadastro Documento
				#$trDataInicio = new Zend_Db_Expr('NOW()');				
				$objDate 		= new Zend_Date();
				$objLocale  	= new Zend_Locale('pt_BR');
				Zend_Date::setOptions(array('format_type' => 'php'));
				#echo $objDate->get(); /** Output of the desired Timestamp date */
				$dcDataCadastro = $objDate->toString('m-d-Y H:i:s'); /** pt_BR format */
				
				// ===== Retorna o ID do usuário autenticado (Provisório)
				$usId = Zend_Auth::getInstance()->getIdentity()->us_id;
				
				#$objConfig = new Zend_Config_Ini('./application/config.ini','database');
				#$db = Zend_Db::factory($objConfig->db->adapter, $objConfig->db->config->toArray());
				
				#$sqlUsuario = "SELECT us_id FROM tb_usuario WHERE us_login = '".Zend_Auth::getInstance()->getIdentity()."'";
				#$usId 	    = $db->fetchOne($sqlUsuario);
				#echo "----------------<pre>" . var_dump($resUsuario) . "</pre><br>";
				// ==== Retorna o ID do usuário autenticado (Provisório)
				
				$arrData = array(
				'td_id' 			 => $tdId,
				'dc_numero' 		 => $dcNumero,
				'as_id' 			 => $asId,
				'us_id' 			 => $usId,
				'dc_data_elaboracao' => $dcDataElaboracao,
				'dc_data_cadastro' 	 => $dcDataCadastro,
				'dc_compl_assunto' 	 => $dcComplAssunto,
				'oe_id' 			 => $oeId
				);
				
				$objDocumento = new Documento();
				$dcId = $objDocumento->insert($arrData);
				
				if($dcId && strlen($daArquivoBase64))
				{
					$objDocumentoArquivo = new DocumentoArquivo();
					
					/*
					$arrData = array(
					'dc_id' => $dcId,
					'da_arquivo' => $daArquivo
					);
					$objDocumentoArquivo->insert($arrData);
					// Não Funcionou com coluna do IMAGE no (MS SQL Server 2000) pois o método insert por default colocar o valor da variável $daArquivo (Arquivo PDF em Hexa) em apóstrofe (') no INSERT
					*/
					
					$this->db->beginTransaction();					
					try
					{
						$sql = "SET TEXTSIZE 2147483647";
						$this->db->query($sql);
						
						$sql = "exec('";
						$sql.= "INSERT INTO tb_documento_arquivo(";
						$sql.= "dc_id,";
						$sql.= "da_tipo,";
						$sql.= "da_tamanho,";
						$sql.= "da_arquivo_base64"; //$sql.= "da_arquivo";
						$sql.= ")VALUES(";
						$sql.= $dcId.",";
						$sql.= "''".$daTipo."'',";
						$sql.= $daTamanho.",";
						$sql.= "''".$daArquivoBase64."''"; //$sql.= $daArquivo;
						$sql.= ")";
						$sql.= "')";
						
						$this->db->query($sql); // 18/12/2009 - SQLSTATE[HY000]: General error: 10025 Possible network error: Write to SQL Server Failed. General network error. Check your documentation. [10025] (severity 9) []
						/***************************** PROVIS�RIO (MUDAR PARA PDO MSSQL ****************************/
						/*$conexao = mssql_connect('servdados', 'sa', '');
						$banco	 = mssql_select_db('Documentos', $conexao);
						
						$resDocumentoArquivo = mssql_query($sql, $conexao);
						
						if(!$resDocumentoArquivo) die(mssql_error());
						
						#echo "<pre>";print_r($resDocumentoArquivo);echo "</pre>";exit;
						mssql_close($conexao);*/
						/***************************** PROVISÓRIO (MUDAR PARA PDO MSSQL ****************************/
						
						$this->db->commit();
					}
					catch(Exception $e)
					{
						$this->db->rollBack();
						$this->view->err = $e->getMessage();
					}
				}
				
				// Caso a unidade de destino seja informada salva a tramitação pré-cadastrada para impressão da guia dos documentos salvos em lote 
				if($dcId && ((int) $unId > 0))
				{
					$this->db->beginTransaction();
					try
					{
						Zend_Loader::loadClass('Tramitacao');
						
						$objTramitacao = new Tramitacao();
						
						// Data Início Tramitação
						#$trDataInicio = new Zend_Db_Expr('NOW()');
						Zend_Date::setOptions(array('format_type' => 'php'));				
						$objDate 		= new Zend_Date();
						$objLocale  	= new Zend_Locale('pt_BR');
						$trDataInicio   = $objDate->toString('m-d-Y H:i:s'); /** pt_BR format */
						
						// === Retorna o ID do tipo do documento (Provisório)
						$objDocumento 		= new Documento();
						$_whereDocumento  	= "dc_id = ".$dcId;
						$resDocumento 		= $objDocumento->fetchRow($_whereDocumento);
						$tdId				= $resDocumento->td_id;
						// === Retorna o ID do tipo do documento (Provisório)
						
						// Id do Usuário logado
						$usId = Zend_Auth::getInstance()->getIdentity()->us_id;
						
						$arrTramitacaoData = array(
							'dc_id' 		 => $dcId,
							'td_id' 		 => $tdId,
							'tr_data_inicio' => $trDataInicio,
							'us_id' 		 => $usId,
							'un_id' 		 => $unId
						);
						
						$trId = $objTramitacao->insert($arrTramitacaoData);
						
						if($trId > 0)
							$this->db->commit();
					}
					catch(Exception $e)
					{
						$this->db->rollBack();
						$this->view->err = $e->getMessage();
					}
				}
				
				// Caso tenha sido informada a unidade de destino redireciona para o cadastro de um novo documento, caso contrário para a tramitação de documentos 
				if($unId > 0)
					return $this->_helper->redirector('index'); /** Redirecionar para cadastro de Documento */
				else
					return $this->_redirect('tramitacao/list'); /** Redirecionar para cadastro de Tramitação */
			}
			else
			{
				$this->view->detail = "Informe todos os campos obrigatórios.";
			}
		}
		
		// set up an "empty" documento
		$this->view->documento = new stdClass();
		$this->view->documento->td_id = null;
		$this->view->documento->as_id = null;
		$this->view->documento->dc_compl_assunto = '';
		
		// additional view fields required by form
		$this->view->action 	= 'add';
		$this->view->buttonText = 'Adicionar';
	}
	
	public function viewarquivoAction()
	{
		$objDocumentoArquivo = new DocumentoArquivo();
		$dcId = $this->_request->getParam('id');
		if($dcId > 0 && $objDocumentoArquivo->hasDocumentoArquivo($dcId))
		{
			#set_magic_quotes_runtime(0);
			
			$sql = "SET TEXTSIZE 2147483647";
			$res = $this->db->query($sql);
			
			#$whereDocumentoArquivo = 'dc_id = '.$dcId;
			#$documentoArquivo 		= $objDocumentoArquivo->fetchRow($whereDocumentoArquivo);
			$sqlDocumentoArquivo = "exec('";
			$sqlDocumentoArquivo.= "SELECT dc_id, da_tipo, da_arquivo, da_arquivo_base64 ";
			$sqlDocumentoArquivo.= "FROM tb_documento_arquivo ";
			$sqlDocumentoArquivo.= "WHERE dc_id=".$dcId;
			$sqlDocumentoArquivo.= "')";
			#$resDocumentoArquivo = $this->db->fetchRow($sqlDocumentoArquivo);
			
			/***************************** PROVIS�RIO (MUDAR PARA PDO MSSQL ****************************/
			$conexao = mssql_connect($this->config->db->config->host, $this->config->db->config->username, $this->config->db->config->password);
			$banco	 = mssql_select_db($this->config->db->config->dbname, $conexao);
			
			$resDocumentoArquivo = mssql_query($sqlDocumentoArquivo, $conexao);
			$resDocumentoArquivo = mssql_fetch_assoc($resDocumentoArquivo);
			
			#echo "<pre>";print_r($resDocumentoArquivo);echo "</pre>";exit;
			mssql_close($conexao);
			/***************************** PROVIS�RIO (MUDAR PARA PDO MSSQL ****************************/
			
			#print_r($resDocumentoArquivo);exit;
			
			// Retorna automaticamente em formato PDF o conte�do armazenado no tipo hexa na coluna image do MSSQL 2000
			$this->view->pdf = strlen(trim($resDocumentoArquivo['da_arquivo_base64'])) ? base64_decode($resDocumentoArquivo['da_arquivo_base64']) : $resDocumentoArquivo['da_arquivo'];
			
			#echo $resDocumentoArquivo['da_tipo']."<BR>". utf8_encode($this->view->pdf);exit;
			
			#$daArquivo = unpack("H*hex", $this->view->pdf);
			#$daArquivo = '0x'.$daArquivo['hex'];

			#echo "tamanho=".strlen($this->view->pdf);
			#echo "<br>tipo=".gettype($this->view->pdf);
			
			#echo($resDocumentoArquivo['da_arquivo_base64']);
			#echo(stripslashes(base64_decode($resDocumentoArquivo['da_arquivo_base64'])));
			#exit;
			
			switch($resDocumentoArquivo['da_tipo'])
			{
          		case 'pdf':
          			$contentType = 'application/pdf';
          			break;
          		case 'doc':
          			$contentType = 'application/msword';
          			break;
          		case 'odt':
          			$contentType = 'application/vnd.oasis.opendocument.text';
          			break;
          		case 'xls':
          			$contentType = 'application/vnd.ms-excel';
          			break;
			}
			
			#Provis�rio (Acertar)
			//$resDocumentoArquivo['da_tipo'] = 'pdf';
			header("Content-type: ".$contentType);
			echo($this->view->pdf);
			exit;
			
			$this->render();
		}
	}
	
	public function getTypeUploadFile($daTipo)
	{
		//$arrTiposPermitidos = array('pdf', 'doc', 'xls', 'odt');
		switch($daTipo)
		{
			case (stripos($daTipo, 'pdf') !== FALSE):
				$daTipo = 'pdf';
				break;
			case (stripos($daTipo, 'word') !== FALSE):
				$daTipo = 'doc';
				break;
			case (stripos($daTipo, 'xls') !== FALSE || stripos($daTipo, 'excel') !== FALSE):
				$daTipo = 'xls';
				break;
			case (stripos($daTipo, 'odt') !== FALSE || stripos($daTipo, 'opendocument')):
				$daTipo = 'odt';
				break;
		}
		return $daTipo;
	}
		
	public function editAction()
	{
		$this->view->title = "Atualizar Documento";
		$objDocumento = new Documento();
		
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
			$dcId 			= (int) $this->_request->getPost('dc_id');
			$dcNumero		= (string) trim($filter->filter($this->_request->getPost('dc_numero')));
			// Formata a data no formato do MSSQL 2000 m-d-Y
			$dcDataElaboracao 	= (string) trim($filter->filter($this->_request->getPost('calendar')));
			$dcDataElaboracao	= explode("-", str_replace("/", "-", $dcDataElaboracao));
			$dcDataElaboracao	= $dcDataElaboracao[1]."-".$dcDataElaboracao[0]."-".$dcDataElaboracao[2];
			// Formata a data no formato do MSSQL 2000 m-d-Y
			$tdId			= (int) $this->_request->getPost('td_id');
			$asId 			= (int) $this->_request->getPost('as_id');
			$dcComplAssunto	= (string) trim($filter->filter($this->_request->getPost('dc_compl_assunto')));
			$oeId 			= (int) $this->_request->getPost('oe_id');
			
			if($dcId > 0 && $tdId > 0 && $asId > 0 && strlen($dcNumero) && strlen($dcDataElaboracao))
			{
				$dcComplAssunto = strlen($dcComplAssunto) ? $dcComplAssunto : null;
				$oeId 			= ($oeId > 0) ? $oeId : null;
				
				$arrData = array(
				'dc_numero' 		 => $dcNumero,
				'td_id' 			 => $tdId,
				'dc_data_elaboracao' => $dcDataElaboracao,
				'as_id' 			 => $asId,
				'dc_compl_assunto'   => $dcComplAssunto,
				'oe_id' 			 => $oeId
				);
				
				$where = 'dc_id = '.$dcId;
				$objDocumento->update($arrData, $where);
			
				/*********************************************** Salva Arquivo se enviado **********************************************************/
				// retorna um objeto do tipo Zend_File_Transfer_Adapter_Http para manipula��o do arquivo enviado
				$adapterDaArquivo = $form->getElement('da_arquivo')->getTransferAdapter();

				#print_r($adapterDaArquivo);
				
				/*echo "<pre>";
				print_r($_REQUEST);
				echo "</pre><br /><br />";*/
				
				#echo "<pre>";
				#print_r($_FILES);
				#echo "</pre>";
				
				if($adapterDaArquivo->isUploaded('da_arquivo'))
				{
					#echo "<pre>";
					#print_r($adapterDaArquivo->getFileInfo());
					#echo "</pre>";
					
					$fileInfo = $adapterDaArquivo->getFileInfo();
					
					// Obtendo os dados do arquivo em bin�rio (Provis�rio)
					#$daArquivo 		 = addslashes(fread(fopen($fileInfo['da_arquivo']['tmp_name'], "r"), $fileInfo['da_arquivo']['size']));
					#$daArquivo = file_get_contents($fileInfo['da_arquivo']['tmp_name']);
					#$daArquivo = unpack("H*hex", $daArquivo);
					#$daArquivo = '0x'.$daArquivo['hex'];
					
					#$daArquivo 		 = file_get_contents($fileInfo['da_arquivo']['tmp_name']);
					#$daArquivo 	 	 = addslashes(fread(fopen($fileInfo['da_arquivo']['tmp_name'], "r"), $fileInfo['da_arquivo']['size']));
					$daArquivoBase64 = base64_encode(fread(fopen($fileInfo['da_arquivo']['tmp_name'], "r"), $fileInfo['da_arquivo']['size']));					
					$daTipo			 = $this->getTypeUploadFile($fileInfo['da_arquivo']['type']);
					$daTamanho		 = $fileInfo['da_arquivo']['size'];
					#echo strlen($daArquivo);
					#echo strlen($daArquivoBase64);
					
					#echo "<br><hr><br><pre>";
					#print($daArquivoBase64);
					#echo "</pre>";
					#exit;
					
					#echo "<br><hr><br><pre>";
					#print(base64_decode($daArquivoBase64));
					#echo "</pre>";
					#exit;
					
					#header("Content-type: application/pdf");
					#echo(base64_decode($daArquivoBase64));
					
					if($dcId && strlen($daArquivoBase64))
					{
						$objDocumentoArquivo = new DocumentoArquivo();
						
						/*
						$arrData = array(
						'dc_id' => $dcId,
						'da_arquivo' => $daArquivo
						);
						$objDocumentoArquivo->insert($arrData);
						// N�o Funcionou pois o m�todo insert por default colocar o valor da vari�vel $daArquivo (Arquivo PDF em Hexa) em ap�strofe (') no INSERT
						*/
						
						$this->db->beginTransaction();					
						try
						{
							$whereDocumentoArquivo = 'dc_id = '.$dcId;
							$objDocumentoArquivo->delete($whereDocumentoArquivo);
							
							$sql = "SET TEXTSIZE 2147483647";
							$this->db->query($sql);
							
							$sql = "exec('";
							$sql.= "INSERT INTO tb_documento_arquivo(";
							$sql.= "dc_id,";
							$sql.= "da_tipo,";
							$sql.= "da_tamanho,";
							$sql.= "da_arquivo_base64"; //$sql.= "da_arquivo";
							$sql.= ")VALUES(";
							$sql.= $dcId.",";
							$sql.= "''".$daTipo."'',";
							$sql.= $daTamanho.",";
							$sql.= "''".$daArquivoBase64."''"; //$sql.= $daArquivo;
							$sql.= ")";
							$sql.= "')";
							
							#echo "<pre>".$sql."</pre>";exit;
							
							$this->db->query($sql); // 18/12/2009 - Upload de arquivo de 7 MB - SQLSTATE[HY000]: General error: 10025 Possible network error: Write to SQL Server Failed. General network error. Check your documentation. [10025] (severity 9) []
							/***************************** PROVIS�RIO (MUDAR PARA PDO MSSQL ****************************/
							/*$conexao = mssql_connect('servdados', 'sa', '');
							$banco	 = mssql_select_db('Documentos', $conexao);
							
							$resDocumentoArquivo = mssql_query($sql, $conexao) or die("MSSQL Error. ".mssql_error());
							
							#echo "<pre>";print_r($resDocumentoArquivo);echo "</pre>";exit;
							mssql_close($conexao);*/
							/***************************** PROVIS�RIO (MUDAR PARA PDO MSSQL ****************************/
							
							$this->db->commit();
						}
						catch(Exception $e)
						{
							$this->db->rollBack();
							$this->view->err = $e->getMessage();
						}
					}
				}
				/*********************************************** Salva Arquivo se enviado **********************************************************/
			
				return $this->_helper->redirector('list');
			}
			else
			{
				$this->view->documento = $objDocumento->fetchRow('dc_id = '.$dcId);
			}
		}
		else
		{	
			// usuario id should be $params['id']
			$dcId = (int) $this->_request->getParam('id',0);
			if($dcId > 0)
			{
				$this->view->documento = $objDocumento->fetchRow('dc_id = '.$dcId);
			}
		}
		
		if($this->view->documento)
		{
			// additional view fields required by form
			$form->setDefault('dc_numero', $this->view->documento->dc_numero);
			$form->setDefault('td_id', $this->view->documento->td_id);
		
			Zend_Date::setOptions(array('format_type' => 'php'));
			$objDate = new Zend_Date($this->view->documento->dc_data_elaboracao);
			$this->view->documento->dc_data_elaboracao = $objDate->toString('d/m/Y'); /** pt_BR format */
			
			$form->setDefault('calendar', $this->view->documento->dc_data_elaboracao);
			$form->setDefault('as_id', $this->view->documento->as_id);
			$form->setDefault('dc_compl_assunto', $this->view->documento->dc_compl_assunto);
			$form->setDefault('oe_id', $this->view->documento->oe_id);
			
			$dcId = new Zend_Form_Element_Hidden('dc_id');
			$dcId->setValue($this->view->documento->dc_id);
			$form->addElement($dcId);
		}
		$this->view->form = $form;
		$this->render(); // render tipo documento form
	}
	
	public function deleteAction()
	{
		$this->view->title = "Excluir Documento";
		
		$objDocumento = new Documento();
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			
			$filter = new Zend_Filter_Alpha();
			$id 	= (int) $this->_request->getPost('id');
			$delete = $filter->filter($this->_request->getPost('delete'));
			
			if($id > 0 && $delete == 'Sim')
			{
				$arrData = array(
				'dc_excluido' => '1'
				);
				
				$where = 'dc_id = ' . $id;
				
				$objDocumento->update($arrData, $where);
				#$rowsAffected = $objDocumento->delete($_where);
			}
		}
		else
		{
			$id = (int) $this->_request->getParam('id');
			if($id > 0)
			{
				$this->view->documento = $objDocumento->fetchRow('dc_id='.$id);
				if($this->view->documento->dc_id > 0)
				{
					Zend_Loader::loadClass('DeleteConfirmationForm');
					
					$this->view->form = new DeleteConfirmationForm(
						array(
							'action' => $this->view->baseUrl.'/documento/delete',
							'method' => 'post'
						)
					);
					
					// additional view fields required by form
					$dcId = new Zend_Form_Element_Hidden('id');
					$dcId->setValue($this->view->documento->dc_id);
					$this->view->form->addElement($dcId);
					
					return $this->render();
				}
			}
		}
		
		// redireciona à listagem novamente
		return $this->_helper->redirector('list');
		#$this->_redirect('list');
	}
	
	public function initamfAction()
	{
		//renderiza a saída, devidamente formatada no protocolo AMF
		Zend_Loader::loadClass('Zend_Amf_Server');
		
		$this->getHelper('viewRenderer')->setNoRender();
		
		//Instancia o servidor PHP
		$server = new Zend_Amf_Server();
		
		//Adiciona o diretório php para que as classes sejam encontradas
		$server->addDirectory(dirname(__FILE__) ."/../models/");
		
		echo $server->handle();
		//$this->view->handle = trim($server->handle()); 
		//echo ($this->view->handle);exit;
	}
	
	public function listflexAction()
	{				
		$this->view->title = 'Listagem de Documentos';		
		$this->render();
	}
	
	public function getWhereDocumento()
	{
		$this->view->documentoSearchForm = $this->getDocumentoSearchForm();

		$this->view->urlBotaoImprimir = '';
		$whereDocumento 			  = '';	
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			
			$request = $this->getRequest();
			// Check if we have a POST request
			if(!$request->isPost())
			{
				return $this->_helper->redirector('list');
			}
			
			$filter	= new Zend_Filter_StripTags();
			
			$dcNumero = (string) trim($filter->filter($this->_request->getPost('dc_numero')));
			if(strlen(trim($dcNumero)))
			{
				$whereDocumento = " AND dc_numero = '".$dcNumero."'";
				$this->view->urlBotaoImprimir = '/dc_numero/'.$dcNumero;
			}

			$tdId = (int) $this->_request->getPost('td_id');
			if($tdId > 0)
			{
				$whereDocumento.= ' AND td_id = '.$tdId;
				$this->view->urlBotaoImprimir.= '/td_id/'.$tdId;
			}
		
			$unId = (int) $this->_request->getPost('un_id');
			if($unId > 0)
			{
				$whereDocumento.= ' AND us_id IN';
				$whereDocumento.= ' (';
				$whereDocumento.= ' SELECT us_id FROM tb_usuario WHERE un_id = '.$unId;
				$whereDocumento.= ' )';
				$this->view->urlBotaoImprimir.= '/un_id/'.$unId;
			}

			$asId = (int) $this->_request->getPost('as_id');
			if($asId > 0)
			{
				$whereDocumento.= ' AND as_id = '.$asId;
				$this->view->urlBotaoImprimir.= '/as_id/'.$asId;
			} 
			
			$oeId = (int) $this->_request->getPost('oe_id');
			if($oeId > 0)
			{
				$whereDocumento.= ' AND oe_id = '.$oeId;
				$this->view->urlBotaoImprimir.= '/oe_id/'.$oeId;
			}
			
			// Formata a data inicial no formato do MSSQL 2000 m-d-Y
			if(strlen(trim($filter->filter($this->_request->getPost('data_inicial')))) && $this->_request->getPost('data_inicial') != 'dd/mm/aaaa')
			{
				$dataInicial 			 	  = (string) trim($filter->filter($this->_request->getPost('data_inicial')));
				$dataInicial 				  = explode("-", str_replace("/", "-", $dataInicial));
				$dataInicial 				  = $dataInicial[1]."-".$dataInicial[0]."-".$dataInicial[2];

				$whereDocumento.= (strlen(trim($dataInicial))) ? " AND dc_data_cadastro >= '".$dataInicial."'" : '';

				$this->view->urlBotaoImprimir.= (strlen(trim($dataInicial))) ? '/data_inicial/'.trim($filter->filter($this->_request->getPost('data_inicial'))) : '';
			}
			
			// Formata a data final no formato do MSSQL 2000 m-d-Y
			if(strlen(trim($filter->filter($this->_request->getPost('data_final')))) && $this->_request->getPost('data_final') != 'dd/mm/aaaa')
			{
				$dataFinal  			 	  = (string) trim($filter->filter($this->_request->getPost('data_final')));
				$dataFinal 				  	  = explode("-", str_replace("/", "-", $dataFinal));
				$dataFinal 				  	  = $dataFinal[1]."-".$dataFinal[0]."-".$dataFinal[2];
				
				$whereDocumento.= (strlen(trim($dataFinal))) ? " AND dc_data_cadastro <= '".$dataFinal."'" : '';
				
				$this->view->urlBotaoImprimir.= (strlen(trim($dataFinal))) ? '/data_final/'.trim($filter->filter($this->_request->getPost('data_final'))) : '';
			}
			
			if(strlen(trim($filter->filter($this->_request->getPost('dc_compl_assunto')))))
			{
				$dcComplAssunto = (string) trim($filter->filter($this->_request->getPost('dc_compl_assunto')));
				
				$whereDocumento.= (strlen(trim($dcComplAssunto))) ? " AND UPPER(dc_compl_assunto) LIKE UPPER('%".utf8_decode($dcComplAssunto)."%')" : '';
				
				$this->view->urlBotaoImprimir.= (strlen(trim($dcComplAssunto))) ? '/dc_compl_assunto/'.trim($filter->filter($this->_request->getPost('dc_data_elaboracao'))) : '';
			}
			
			// Formata a data de elaboração no formato do MSSQL 2000 m-d-Y
			if(strlen(trim($filter->filter($this->_request->getPost('dc_data_elaboracao')))) && $this->_request->getPost('dc_data_elaboracao') != 'dd/mm/aaaa')
			{
				$dataElaboracao = (string) trim($filter->filter($this->_request->getPost('dc_data_elaboracao')));
				$dataElaboracao	= explode("-", str_replace("/", "-", $dataElaboracao));
				$dataElaboracao = $dataElaboracao[1]."-".$dataElaboracao[0]."-".$dataElaboracao[2];
				
				$whereDocumento.= (strlen(trim($dataElaboracao))) ? " AND dc_data_elaboracao = '".$dataElaboracao."'" : '';
				
				$this->view->urlBotaoImprimir.= (strlen(trim($dataElaboracao))) ? '/dc_data_elaboracao/'.trim($filter->filter($this->_request->getPost('dc_data_elaboracao'))) : '';
			}
			
			// additional view fields required by form
			$this->view->documentoSearchForm->setDefault('dc_numero', $dcNumero);
			$this->view->documentoSearchForm->setDefault('td_id', $tdId);
			//$this->view->documentoSearchForm->setDefault('un_id', $unId);
			$this->view->documentoSearchForm->setDefault('as_id', $asId);
			$this->view->documentoSearchForm->setDefault('oe_id', $oeId);
			$this->view->documentoSearchForm->setDefault('data_inicial', trim($filter->filter($this->_request->getPost('data_inicial'))));
			$this->view->documentoSearchForm->setDefault('data_final', trim($filter->filter($this->_request->getPost('data_final'))));
			$this->view->documentoSearchForm->setDefault('dc_data_elaboracao', trim($filter->filter($this->_request->getPost('dc_data_elaboracao'))));
			$this->view->documentoSearchForm->setDefault('dc_compl_assunto', trim($filter->filter($this->_request->getPost('dc_compl_assunto'))));
		}
		
		return $whereDocumento;
	}
	
	public function listAction()
	{
		Zend_Loader::loadClass('Usuario');
		
		//$this->view->title = 'Listagem de Documentos';
		$this->view->title = 'Resoluções e Documentos';
		
		// Retorna a SQL de filtro caso seja informado algum quando no form de busca $documentoSearchForm 
		$whereDocumento = $this->getWhereDocumento();
		
		$objDocumento 			= new Documento();
		$this->view->documentos = $objDocumento->getDocumentos($this->view->userId, null, $whereDocumento);
		
		if($this->_request->getParam('pagina', 1) > 0)
		{
			Zend_loader::loadClass('Zend_Paginator');
			Zend_loader::loadClass('Zend_View_Helper_PaginationControl');
			
			$paginator = Zend_Paginator::factory($this->view->documentos);
			$paginator->setCurrentPageNumber($this->_request->getParam('pagina', 1));
			$paginator->setItemCountPerPage(30); //número de registros por p�gina
			
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
	
	/**
     * Outros métodos
     * 
     */    
    public function getForm()
    {
    	$action = ($this->view->actionName == 'index') ? 'add' : $this->view->actionName; 
        return new DocumentoForm(
        	array(
            	'action' => $this->view->baseUrl . '/documento/'.$action,
            	'method' => 'post'
        	)
        );
    }
	
    public function getDocumentoSearchForm()
    {
        return new DocumentoSearchForm(
        	array(
            	'action' => $this->view->baseUrl . '/documento/list',
            	'method' => 'post'
        	)
        );
    }
    
    private function parsePostFilter()
    {
    	if ( $this->_request->isPost() )
    	{
    		return "id>3";
    		/*
    		Zend_Loader::loadClass('Zend_Filter_StripTags');
    		$filter = new Zend_Filter_StripTags();
    		
    		$id = (int)$this->_request->getPost('id');
    		$artist = $filter->filter($this->_request->getPost('artist'));
    		$artist = trim($artist);
    		$title = $filter->filter($this->_request->getPost('title'));
    		$title = trim($title);    		
    		
    		if ($id !== false) 
    		{
    		    if ( $artist != '' && $title != '' )
	    		{
	    			$data = array(
	    				'artist' => $artist,
	    				'title' => $title
	    			);
	    			
	    			$where = 'id = ' . $id;
	    			
	    			$album->update($data, $where);
	    			
	    			$this->_redirect('/index/edit/id/' . $id);
	    			return;
	    		}
	    		else 
	    		{
	    			$this->view->album = $album->fetchRow('id = ' . $id);
	    		}
    		}
    		*/
    		echo "Is post";
    	}
		else 
		{
			/*
			$id = $this->_request->getParam('id', 0);
			if ($id > 0) {
				echo "vish... funciona mesmo e o id é " . $id;
			}
			*/
			return "";
		}
    }
	
	public function printAction()
	{
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
		$page->drawText('Documentos', $leftPos, $topPos - $linha,'UTF-8');
		//-----------------------------------------------------------------------------------
		
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
		$page->drawText('Número documento', $leftPos, $topPos - $linha,'UTF-8');
		$page->drawText('Data de elaboração', $leftPos + 80, $topPos - $linha,'UTF-8');
		$page->drawText('Data de cadastro', $leftPos + 160, $topPos - $linha,'UTF-8');
		$page->drawText('Assunto', $leftPos + 240, $topPos - $linha,'UTF-8');
		$page->drawText('Tipo documento', $leftPos + 370, $topPos - $linha,'UTF-8');
		$page->drawText('Usuário', $leftPos + 450, $topPos - $linha,'UTF-8');
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
		
		$objDocumento = new Documento();
		$documentos = $objDocumento->getDocumentos();
		//echo '<pre>'; print_r ($documentos); exit;
		
		$linha = 87;
		foreach ($documentos as $row)
		{
			$registro = strip_tags($row['dc_numero']);
			$registro = wordwrap($registro , 30, '\n');
            $headlineArray1 = explode('\n', $registro );
			
			$objDate 		= new Zend_Date($row['dc_data_elaboracao']);
			$objLocale  	= new Zend_Locale('pt_BR');
			Zend_Date::setOptions(array('format_type' => 'php'));
			$row['dc_data_elaboracao'] = $objDate->toString('d/m/Y H:i'); /** pt_BR format */
			
			$registro = strip_tags($row['dc_data_elaboracao']);
            $registro = wordwrap($registro , 30, '\n');
            $headlineArray2 = explode('\n', $registro );
			
			$objDate 		= new Zend_Date($row['dc_data_cadastro']);
			$objLocale  	= new Zend_Locale('pt_BR');
			Zend_Date::setOptions(array('format_type' => 'php'));
			$row['dc_data_cadastro'] = $objDate->toString('d/m/Y H:i'); /** pt_BR format */
			
			$registro = strip_tags($row['dc_data_cadastro']);
            $registro = wordwrap($registro , 25, '\n');
            $headlineArray3 = explode('\n', $registro );
			
			$registro = strip_tags($row['as_descricao']);
            $registro = wordwrap($registro , 25, '\n');
            $headlineArray4 = explode('\n', $registro );
			
			$registro = strip_tags($row['td_descricao']);
            $registro = wordwrap($registro , 20, '\n');
            $headlineArray5 = explode('\n', $registro );
			
			$registro = strip_tags($row['us_nome']);
            $registro = wordwrap($registro , 30, '\n');
            $headlineArray6 = explode('\n', $registro );
			
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
				$page->drawText('Número documento', $leftPos, $topPos - $linha,'UTF-8');
				$page->drawText('Data de elaboração', $leftPos + 80, $topPos - $linha,'UTF-8');
				$page->drawText('Data de cadastro', $leftPos + 160, $topPos - $linha,'UTF-8');
				$page->drawText('Assunto', $leftPos + 290, $topPos - $linha,'UTF-8');
				$page->drawText('Tipo documento', $leftPos + 365, $topPos - $linha,'UTF-8');
				$page->drawText('Usuário', $leftPos + 450, $topPos - $linha,'UTF-8');
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
				$page->drawText($line, $leftPos + 80, $topPos - $linha,'UTF-8');
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
				$page->drawText($line, $leftPos + 160, $topPos - $linha,'UTF-8');
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
				$page->drawText($line, $leftPos + 240, $topPos - $linha,'UTF-8');
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
				$page->drawText($line, $leftPos + 370, $topPos - $linha,'UTF-8');
				$linha+= 15;
			}	
			
			$linha = $linha - (15 * sizeof($headlineArray5));
			
			if ($regmaior < sizeof($headlineArray5))
			{
				$regmaior = sizeof($headlineArray5);
			}
			
			foreach ($headlineArray6 as $line)
			{
				$line = ltrim($line);
				$page->drawText($line, $leftPos + 450, $topPos - $linha,'UTF-8');
				$linha+= 15;
			}	
			
			if ($regmaior < sizeof($headlineArray6))
			{
				$regmaior = sizeof($headlineArray6);
			}
			
			$linha = $linha - (15 * sizeof($headlineArray6));
			$linha+= ($regmaior * 15);
			
		}
		//------------------------------------------------------------------------------------------------------
		
		//$pdf->pages[0] = ($page);
		
		//header("Content-Disposition: inline; filename=result.pdf"); 
		
		$this->view->pdf = $pdf->render();
		
		$this->render();
	}
	
	public function printrostoAction()
	{
		$this->view->title = "Folha de rosto";
		$objDocumento = new Documento();
		
		// usuario id should be $params['id']
		$dcId = (int) $this->_request->getParam('id');
		
		if($dcId > 0)
		{
			$documento = $objDocumento->getDocumentos(null,array($dcId));
		}
	
		// Load Zend_Pdf class 
		Zend_Loader::loadClass('Zend_Pdf'); 
		
		$this->getHelper('viewRenderer')->setNoRender();
		
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
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8); 
		$page->setStyle($style);
		
		//echo $this->view->baseUrl; exit;
		//echo APPLICATION_PATH.'/public/images/brasao_prefeitura.jpg'; exit;
		
		$image = Zend_Pdf_Image::imageWithPath(APPLICATION_PATH.'/public/images/brasao_prefeitura.jpg');
		$page->drawImage($image, 200, 600, 400, 800);
		
		$linha = 300;
		
		$page->drawText('Nº', $leftPos, $topPos - $linha,'UTF-8');
		$page->drawText('Nº Documento', $leftPos + 70, $topPos - $linha,'UTF-8');
		$page->drawText('Nome do requerente', $leftPos + 200, $topPos - $linha,'UTF-8');
		$page->drawText('Data de entrada', $leftPos + 430, $topPos - $linha,'UTF-8');
		
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10); 
		$page->setStyle($style);
		
		$linha = 320;
		
		$registro = strip_tags($documento[0]['dc_id']);
        $registro = wordwrap($registro , 25, '\n');
        $headlineArray1 = explode('\n', $registro );
		
		foreach ($headlineArray1 as $line)
		{
			$line = ltrim($line);
			$page->drawText($line, $leftPos, $topPos - $linha,'UTF-8');
			$linha+= 15;
		}
				
		$linha = 320;
		
		$registro = strip_tags($documento[0]['dc_numero']);
        $registro = wordwrap($registro , 25, '\n');
        $headlineArray1 = explode('\n', $registro );
		
		foreach ($headlineArray1 as $line)
		{
			$line = ltrim($line);
			$page->drawText($line, $leftPos + 70, $topPos - $linha,'UTF-8');
			$linha+= 15;
		}
		$linha = 320;
		
		$registro = strip_tags($documento[0]['orgao_origem']);
        $registro = wordwrap($registro , 60, '\n');
        $headlineArray1 = explode('\n', $registro );
		
		foreach ($headlineArray1 as $line)
		{
			$line = ltrim($line);
			$page->drawText($line, $leftPos + 200, $topPos - $linha,'UTF-8');
			$linha+= 15;
		}
		$linha = 320;
		
		$objDate 		= new Zend_Date($documento[0]['dc_data_elaboracao']);
		$objLocale  	= new Zend_Locale('pt_BR');
		Zend_Date::setOptions(array('format_type' => 'php'));
		$documento[0]['dc_data_elaboracao'] = $objDate->toString('d/m/Y'); /** pt_BR format */
		
		$registro = strip_tags($documento[0]['dc_data_elaboracao']);
        $registro = wordwrap($registro , 20, '\n');
        $headlineArray1 = explode('\n', $registro );
		
		foreach ($headlineArray1 as $line)
		{
			$line = ltrim($line);
			$page->drawText($line, $leftPos + 430, $topPos - $linha,'UTF-8');
			$linha+= 15;
		}
		
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8); 
		$page->setStyle($style);
		
		$linha = 370;
		$page->drawText('Assunto', $leftPos, $topPos - $linha,'UTF-8');
		
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10); 
		$page->setStyle($style);
		
		$linha = 390;
		
		$registro = strip_tags($documento[0]['as_descricao']);
        $registro = wordwrap($registro , 108, '\n');
        $headlineArray1 = explode('\n', $registro );
		
		foreach ($headlineArray1 as $line)
		{
			$line = ltrim($line);
			$page->drawText($line, $leftPos, $topPos - $linha,'UTF-8');
			$linha+= 15;
		}		
		
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8); 
		$page->setStyle($style);
		
		$linha = 440;
		$page->drawText('Complemento', $leftPos, $topPos - $linha,'UTF-8');
		
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10); 
		$page->setStyle($style);
		
		$linha = 460;
		
		$registro = strip_tags($documento[0]['dc_compl_assunto']);
        $registro = wordwrap($registro , 108, '\n');
        $headlineArray1 = explode('\n', $registro );
		
		foreach ($headlineArray1 as $line)
		{
			$line = ltrim($line);
			$page->drawText($line, $leftPos, $topPos - $linha,'UTF-8');
			$linha+= 15;
		}
		
		$this->view->pdf = $pdf->render();
		$this->render();
		
		
	}
}
?>