<?php
class Tramitacao extends Zend_Db_Table 
{
	protected $_name 	= 'tb_tramitacao';
	protected $_primary = 'tr_id';
	
	public function getTramitacoes($userUnitId, $arrTrId = null, $dcNumero = null, $unId = null, $dataInicial = null, $dataFinal = null)
	{
		Zend_Loader::loadClass('Documento');
		Zend_Loader::loadClass('Unidade');
		Zend_Loader::loadClass('Usuario');
		Zend_Loader::loadClass('OrgaoExterno');
		
		//Exemplo chamada SP no Zend
		//$result = $db->query("CALL sp_tramitacoes()");
		//$result = $result->fetchAll();
		
		$objTramitacao = new Tramitacao();
		$objDocumento  = new Documento();
		$objUnidade	   = new Unidade();
		$objUsuario	   = new Usuario();
		$objOrgaoExterno = new OrgaoExterno();
		
		$arrTramitacoes = $arrNewTramitacoes = array();
		
		// Filtra pelos ids de tramitações se foram passados no array $arrTrId
		$sqlAnd = (count($arrTrId) > 0) ? ' AND tr_id IN('.implode(", ", $arrTrId).')' : '';
		
		// Filtra pelo id do documento se foi passado o número do documento, variável $dcNumero
		$sqlAnd.= (strlen(trim($dcNumero))) ? ' AND dc_id = '.$objDocumento->fetchRow("dc_numero = '".$dcNumero."'")->dc_id : '';
		
		// Filtra pelo id do documento se foi passado o número do documento, variável $dcNumero caso contrário filtra pelo id da unidade do usuário logado
		$sqlAnd.= ($unId > 0) ? ' AND un_id = '.$unId : ''; //' AND un_id = '.$userUnitId;
		
		// Filtra pela data inicial da tramitação se foi passada a variável $dataInicial
		$sqlAnd.= (strlen(trim($dataInicial))) ? " AND tr_data_inicio >= '".$dataInicial."'" : '';
		
		// Filtra pela data inicial da tramitação se foi passada a variável $dataFinal
		$sqlAnd.= (strlen(trim($dataFinal))) ? " AND tr_data_inicio <= '".$dataFinal."'" : '';
		
		$whereTramitacao  	= 'tr_excluido IS NULL'.$sqlAnd;
		$orderByTramitacao 	= array('tr_data_inicio DESC', 'dc_id DESC');
		$arrTramitacoes 	= $objTramitacao->fetchAll($whereTramitacao, $orderByTramitacao);
		$arrTramitacoes 	= $arrTramitacoes->toArray();
		foreach($arrTramitacoes as $tramitacao)
		{
			// === Retorna o Número do Documento (Provisório)
			$_whereDocumento  			= "dc_id = ".$tramitacao['dc_id'];
			$resDocumento 				= $objDocumento->fetchRow($_whereDocumento);
			$tramitacao['dc_numero']	= $resDocumento->dc_numero;
			// === Retorna o Número do Documento (Provisório)
			
			// === Retorna o nome do Orgão de Origem do Documento Unidade se interno e Orgão Externo se externo (Provisório)
			if($resDocumento->oe_id > 0)
			{
				$_whereOrgaoExterno 		= "oe_id = ".$resDocumento['oe_id'];
				$resOrgaoExterno			= $objOrgaoExterno->fetchRow($_whereOrgaoExterno);
				$tramitacao['orgao_origem'] = $resOrgaoExterno->oe_descricao;
				unset($resOrgaoExternoo, $_whereOrgaoExterno);
			}
			else
			{
				$tramitacao['orgao_origem'] = utf8_encode($objUsuario->getUnidadeUsuario($tramitacao['us_id'])->un_descricao);
			}
			// === Retorna o nome do Orgão de Origem do Documento Unidade se interno e Orgão Externo se externo (Provisório)
			unset($resDocumento, $_whereDocumento);
			
			
			// === Retorna o nome da Unidade de Destino (Provisório)
			$_whereUnidade  			= "un_id = ".$tramitacao['un_id'];
			$resUnidade 				= $objUnidade->fetchRow($_whereUnidade);
			$tramitacao['un_descricao']	= utf8_encode($resUnidade->un_descricao);
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome da Unidade de Destino (Provisório)
			
			// === Retorna o nome do Usuário (Provisório)
			$_whereUsuario  		= "us_id = ".$tramitacao['us_id'];
			$resUsuario 			= $objUsuario->fetchRow($_whereUsuario);
			$tramitacao['us_nome']	= utf8_encode($resUsuario->us_nome);
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome do Usuário (Provisório)
			
			// Caso a tramitação foi cadastrada pela unidade do usuário logado libera edição somente se a guia não foi impressa
			$tramitacao['libera_edicao_desativacao'] = ($tramitacao['tr_data_termino'] == '' && 
														$tramitacao['tr_guia_impressa'] == 0  &&
														$resUsuario['un_id'] == $userUnitId) ? 1 : 0;
														
			$arrNewTramitacoes[] = $tramitacao;
		}
		unset($arrTramitacoes, $objDocumento, $objUnidade, $objUsuario);
		
		return $arrNewTramitacoes;
		unset($arrNewTramitacoes);
	}
	
	public function getCountTramitacoesPendentes($userUnitId)
	{
		$select = $this->select();
        $select->from($this->_name, 'COUNT(tr_id) AS num');
        $select->where('tr_excluido IS NULL AND un_id = '.$userUnitId.' AND tr_data_termino IS NULL AND tr_guia_impressa IS NOT NULL AND tr_numero_guia IS NOT NULL');

        return $this->fetchRow($select)->num;
	}
	
	public function getTramitacoesPendentes($userUnitId=null, $userTipoId=null, $sqlFiltro=null)
	{
		Zend_Loader::loadClass('Unidade');
		Zend_Loader::loadClass('Documento');
		Zend_Loader::loadClass('Usuario');
		Zend_Loader::loadClass('OrgaoExterno');
		
		$objTramitacao   = new Tramitacao();
		$objDocumento    = new Documento();
		$objUnidade	   	 = new Unidade();
		$objUsuario		 = new Usuario();
		$objOrgaoExterno = new OrgaoExterno();
		$arrTramitacoes  = $arrNewTramitacoes = array();		
		
		$select = $objTramitacao->select();
		$select->from($objTramitacao, array('tr_numero_guia'));
		
		$sqlAnd = ' AND tr_data_termino IS NULL';
		$sqlAnd.= ' AND tr_guia_impressa IS NOT NULL';
		$sqlAnd.= ' AND tr_numero_guia IS NOT NULL';
		$sqlAnd.= ' AND YEAR(tr_data_inicio) = YEAR(GETDATE())';
		//$sqlAnd.= $sqlFiltro;
		$sqlAnd.= ((int) $userUnitId > 0) ? ' AND un_id='.$userUnitId : '';
		
		$select->where('tr_excluido IS NULL'.$sqlAnd);
		$select->group(array('tr_numero_guia'));
		$select->order(array('tr_numero_guia DESC'));

		/** Retorna os Documentos Tramitados Pendentes da Unidade do Usuário Logado */
		$arrTramitacoes = $objTramitacao->fetchAll($select);
		$arrTramitacoes = $arrTramitacoes->toArray();
		
		/*echo "<pre>";
		print_r($arrTramitacoes);
		echo "</pre>";
		exit;*/
		
		foreach($arrTramitacoes as $tramitacao)
		{
			$trIds = array();
			
			/************************************* Provisório ******************************************/
			$selectTramitacao = $objTramitacao->select();
			$selectTramitacao->from(array('TB_TRA' => 'tb_tramitacao'));
			
			$andTramitacao = " AND tr_data_termino IS NULL";
			$andTramitacao.= " AND tr_guia_impressa IS NOT NULL";
			$andTramitacao.= " AND tr_numero_guia IS NOT NULL";
			$andTramitacao.= " AND YEAR(tr_data_inicio) = YEAR(GETDATE())";
			//$andTramitacao.= $sqlFiltro;
			$andTramitacao.= ((int) $userUnitId > 0) ? ' AND un_id='.$userUnitId : '';
			$andTramitacao.= " AND tr_excluido IS NULL";
			
			$selectTramitacao->where("tr_numero_guia = ".$tramitacao['tr_numero_guia'].$andTramitacao);
			
			$tramitacoes = $this->fetchAll($selectTramitacao)->toArray();
			
			foreach($tramitacoes as $tramitacao)
				$trIds[] = $tramitacao['tr_id'];
				
			$tramitacao['tr_id'] = $trIds; 
				
			// === Retorna o nome da Unidade de Destino (Provisório)
			$_whereUnidade  			= "un_id = ".$tramitacao['un_id'];
			$resUnidade 				= $objUnidade->fetchRow($_whereUnidade);
			$tramitacao['un_descricao']	= utf8_encode($resUnidade->un_descricao);
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome da Unidade (Provisório)
			
			// === Retorna o nome do Usuário que iniciou a tramitação (Provisório)
			$_whereUsuario  		= "us_id = ".$tramitacao['us_id'];
			$resUsuario 			= $objUsuario->fetchRow($_whereUsuario);
			$tramitacao['us_nome']	= $resUsuario->us_nome;
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome do Usuário (Provisório)
			
			// Caso for o gestor da unidade que cadastrou a tramitação libera desativação e a guia não foi impressa
			$tramitacao['libera_desativacao'] = ($resUsuario['un_id'] == $userUnitId &&
												 $userTipoId == 1 &&
												 !isset($tramitacao['tr_guia_impressa'])) ? 1 : 0;
			/************************************* Provisório ******************************************/
												 
			// Retorna os documentos pertencentes a guia da tramitação
			$selectDocumento = $objDocumento->select();
			// Removing the integrity check on Zend_Db_Table_Select to allow JOINed rows 
		    $selectDocumento->setIntegrityCheck(false);
			$selectDocumento->from(array('TB_DOC' => 'tb_documento'));
			$selectDocumento->join(array('TB_TRA' => 'tb_tramitacao'), 'TB_DOC.dc_id = TB_TRA.dc_id', array());
			if($tramitacao['tr_numero_guia'] > 0)
			{
				$selectDocumento->where('"TB_TRA".tr_numero_guia = ?', $tramitacao['tr_numero_guia']);
				$selectDocumento->where('YEAR("TB_TRA".tr_data_inicio) = YEAR(GETDATE())');
			}
			else
			{
				$selectDocumento->where('"TB_DOC.dc_id = ?', $tramitacao['dc_id']);
			}
			$selectDocumento->where("tr_data_termino IS NULL");
			$selectDocumento->where("tr_guia_impressa IS NOT NULL");
			$selectDocumento->where("tr_numero_guia IS NOT NULL");
			$selectDocumento->where("YEAR(tr_data_inicio) = YEAR(GETDATE())".$sqlFiltro);
			/*if(strlen($sqlFiltro))
				$selectDocumento->where($sqlFiltro);*/
			if((int) $userUnitId > 0)
				$selectDocumento->where('un_id='.$userUnitId);
			$selectDocumento->where("tr_excluido IS NULL");
			
			$arrDocumentos = $objDocumento->fetchAll($selectDocumento);
			$arrDocumentos = $arrDocumentos->toArray();
			foreach($arrDocumentos as $documento)
			{
				// === Retorna o nome do Orgão de Origem do Documento, Unidade se interno e Orgão Externo se externo (Provisório)
				if($documento['oe_id'] > 0)
				{
					$_whereOrgaoExterno 		= "oe_id = ".$documento['oe_id'];
					$resOrgaoExterno			= $objOrgaoExterno->fetchRow($_whereOrgaoExterno);
					$documento['orgao_origem']  = $resOrgaoExterno->oe_descricao;
					
					unset($resOrgaoExternoo, $_whereOrgaoExterno);
				}
				else
				{
					$documento['orgao_origem'] = utf8_encode($objUsuario->getUnidadeUsuario($documento['us_id'])->un_descricao);
				}
				// === Retorna o nome do Orgão de Origem do Documento Unidade se interno e Orgão Externo se externo (Provisório)
				
				// === Retorna o nome do Usuário que cadastrou o documento (Provisório)
				$_whereDocumento  		= "us_id = ".$documento['us_id'];
				$resUsuario 			= $objUsuario->fetchRow($_whereDocumento);
				$documento['us_nome']	= $resUsuario->us_nome;
				unset($resUnidade, $_whereUnidade);
				// === Retorna o nome do Usuário que cadastrou o documento (Provisório)
				
				$documentos[] = $documento;
				
				unset($documento);
			}
			
			$tramitacao['documentos'] = $documentos; 
			$arrNewTramitacoes[] = $tramitacao;
			
			/*
			echo "<pre>";
			print_r($arrNewTramitacoes);
			echo "</pre>";
			exit;*/
			
			unset($documentos);
		}
		unset($arrTramitacoes, $objDocumento, $objUnidade, $objUsuario);
		
		/*echo "<pre>";
		print_r($arrNewTramitacoes);
		echo "</pre>";
		exit;*/
		
		return $arrNewTramitacoes;
		unset($arrNewTramitacoes);
	}
	
	public function getNextId()
	{
		$objRegistry = Zend_Registry::getInstance();
		$db	 		 = $objRegistry->db;

		// talvez deva ser mudada pois pode funcionar somente no SQL-Server
		$sql = 'SELECT MAX(tr_numero_guia) FROM tb_tramitacao WHERE YEAR(tr_data_inicio) = YEAR(GETDATE())';
		$maxguia = $db->fetchRow($sql);
		
		foreach($maxguia as $nmax)
		{
			return ++$nmax;
		}
	}
	
	public function getTramitacoesBatch($userId)
	{
		$select = $this->select();
		$select->where("us_id = ?", $userId);
		$select->where("tr_excluido IS NULL");
		$select->where("tr_guia_impressa IS NULL");
		$select->where("tr_numero_guia IS NULL");

		return $this->fetchAll($select);
	}

	public function getUnidadesTramitacoesBatch($userId)
	{
		$select = $this->select();
		$select->from($this->_name, array('un_id', 'COUNT(tr_id) AS qtd_tramites'));
		$select->where("us_id = ?", $userId);
		$select->where("tr_excluido IS NULL");
		$select->where("tr_guia_impressa IS NULL");
		$select->where("tr_numero_guia IS NULL");
		$select->group("un_id");

		return $this->fetchAll($select);
	}
	
	public function getTramitacoesBatchUnidade($userId, $unitId)
	{
		$select = $this->select();
		$select->where("us_id = ?", $userId);
		$select->where("tr_excluido IS NULL");
		$select->where("tr_guia_impressa IS NULL");
		$select->where("tr_numero_guia IS NULL");
		$select->where("un_id IN (".$unitId.")");
		
		return $this->fetchAll($select);
	}
}