<?php
class Documento extends Zend_Db_Table
{
	protected $_name 	= 'tb_documento';
	protected $_primary = 'dc_id';	
	
	/**
	 * @$userId  = id do usuário logado
	 * @$arrDcId = array com os ids de documentos a retornar
	 */
	public function getDocumentos($userId=null, $arrDcId=null, $sqlFiltro=null)
	{
		Zend_Loader::loadClass('TipoDocumento');
		Zend_Loader::loadClass('DocumentoArquivo');
		Zend_Loader::loadClass('Assunto');
		Zend_Loader::loadClass('OrgaoExterno');
		Zend_Loader::loadClass('Usuario');
		Zend_Loader::loadClass('Tramitacao');
		
		$objDocumento 	 	 = new Documento();
		$objDocumentoArquivo = new DocumentoArquivo();
		$objTipoDocumento 	= new TipoDocumento();
		$objAssunto 	 	= new Assunto();
		$objOrgaoExterno 	= new OrgaoExterno();
		$objUsuario 		= new Usuario();
		$objTramitacao		= new Tramitacao();
		
		$sqlAnd = (count($arrDcId) > 0) ? " AND dc_id IN(".implode(", ",$arrDcId).")" : '';
		$sqlAnd.= $sqlFiltro;
		
		$arrNewDocumentos = array();
		
		$selectDocumento = $this->select();
		$selectDocumento->where('dc_excluido IS NULL'.$sqlAnd);
		$selectDocumento->order(array('RIGHT(dc_numero, 4) DESC', 'LEFT(dc_numero, 3) DESC'));
		//echo $selectDocumento;exit;
		$arrDocumentos = $objDocumento->fetchAll($selectDocumento);
		$arrDocumentos = $arrDocumentos->toArray();
		foreach($arrDocumentos as $documento)
		{
			// === Retorna o Tipo do Documento (Provisório)
			$_whereTipoDocumento  		= "td_id = ".$documento['td_id'];
			$resTipoDocumento 			= $objTipoDocumento->fetchRow($_whereTipoDocumento);
			$documento['td_descricao']	= $resTipoDocumento->td_descricao;
			unset($resTipoDocumento, $_whereTipoDocumento);
			// === Retorna o Tipo do Documento (Provisório)
			
			// === Retorna o nome da Unidade (Provisório)
			$_whereAssunto 				= "as_id = ".$documento['as_id'];
			$resAssunto					= $objAssunto->fetchRow($_whereAssunto);
			$documento['as_descricao']	= $resAssunto->as_descricao;
			unset($resAssunto, $_whereAssunto);
			// === Retorna o nome da Unidade (Provisório)
			
			// === Retorna o nome do Orgão de Origem do Documento Unidade se interno e Orgão Externo se externo (Provisório)
			if($documento['oe_id'] > 0)
			{
				$_whereOrgaoExterno 		= "oe_id = ".$documento['oe_id'];
				$resOrgaoExterno			= $objOrgaoExterno->fetchRow($_whereOrgaoExterno);
				$documento['orgao_origem']	= $resOrgaoExterno->oe_descricao;
				unset($resOrgaoExternoo, $_whereOrgaoExterno);
			}
			else
			{
				$documento['orgao_origem'] = utf8_encode($objUsuario->getUnidadeUsuario($documento['us_id'])->un_descricao);
			}
			// === Retorna o nome do Orgão de Origem do Documento Unidade se interno e Orgão Externo se externo (Provisório)
			
			// === Retorna se existe tramitação desse documento
			$_whereTramitacao  		= "dc_id = ".$documento['dc_id'];
			$resTramitacao 			    = $objTramitacao->fetchRow($_whereTramitacao); 
			$documento['tr_temtram']	= (sizeof($resTramitacao) > 0)? 1 : 0;
			//$resTramitacao->tr_descricao;
			// === Retorna se existe tramitação desse documento
			
			// === Retorna o nome do Usuário (Provisório)
			$_whereUsuario  		= "us_id = ".$documento['us_id'];
			$resUsuario 			= $objUsuario->fetchRow($_whereUsuario);
			$documento['us_nome']	= $resUsuario->us_nome;			
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome do Usuário (Provisório)
			
			//==== Retorna o órgão de origem (se existe orgão externo, assumir orgão externo, se não existe, assumir o do usuário do documento)
			
			/*if ($documento['oe_id'] != null)
			{
				$documento['oo_descricao'] = $documento['oe_']
			}
			*/
			//====
			
			if($userId > 0)
			{
				// Caso for o usuário que cadastrou o documento ou a unidade dele libera editar ou desativar o documento
				$documento['libera_edicao_desativacao'] = ($userId == $documento['us_id'] || $objUsuario->getUnidadeUsuario($userId)->un_id == $resUsuario['un_id']) ? 1 : 0;
			}
			
			// Caso o documento possuir arquivo PDF cadastrado guarda no array do documento que possui arquivo
			$documento['possuiArquivo'] = $objDocumentoArquivo->hasDocumentoArquivo($documento['dc_id']);
			
			$arrNewDocumentos[] = $documento;
		}
		unset($arrDocumentos, $objTipoDocumento, $objAssunto, $objOrgaoExterno);
		
		return $arrNewDocumentos;
		unset($arrNewDocumentos);
	}
	
	/**
	 * @Somente retorna os documentos recém cadastrados na unidade que não tem tramite e os documentos que foram enviados para a unidade
	 * @$userId  = id da unidade do usuário logado
	 */
	public function getDocumentosUnidade($userUnitId)
	{
		Zend_Loader::loadClass('TipoDocumento');
		Zend_Loader::loadClass('DocumentoArquivo');
		Zend_Loader::loadClass('Assunto');
		Zend_Loader::loadClass('OrgaoExterno');
		Zend_Loader::loadClass('Usuario');
		Zend_Loader::loadClass('Tramitacao');
		
		$objDocumento 	 	 = new Documento();
		$objDocumentoArquivo = new DocumentoArquivo();
		$objTipoDocumento 	 = new TipoDocumento();
		$objAssunto 	 	 = new Assunto();
		$objOrgaoExterno 	 = new OrgaoExterno();
		$objUsuario 		 = new Usuario();
		$objTramitacao		 = new Tramitacao();
		
		
		/** Obtendo o objeto view registrado no bootstrap index.php **/
		$objRegistry = Zend_Registry::getInstance();
		$db	 		 = $objRegistry->db;
		
		$sqlDocumentosUnidade = 'SELECT DISTINCT("TB_DOC".dc_id), "TB_DOC".* ';
		$sqlDocumentosUnidade.= 'FROM "tb_documento" AS "TB_DOC" ';
		$sqlDocumentosUnidade.= 'LEFT JOIN "tb_tramitacao" AS "TB_TRA"';
		$sqlDocumentosUnidade.= '	ON "TB_DOC".dc_id = "TB_TRA".dc_id ';
		$sqlDocumentosUnidade.= 'WHERE "TB_DOC".dc_excluido IS NULL ';
		$sqlDocumentosUnidade.= 'AND "TB_TRA".tr_excluido IS NULL ';
		$sqlDocumentosUnidade.= 'AND';
		$sqlDocumentosUnidade.= '(';
		$sqlDocumentosUnidade.= '  (';
		$sqlDocumentosUnidade.= '    "TB_TRA".tr_id IN';
		$sqlDocumentosUnidade.= '    (';
		$sqlDocumentosUnidade.= '		SELECT MAX(tr_id) ';
		$sqlDocumentosUnidade.= '		FROM tb_tramitacao ';
		$sqlDocumentosUnidade.= '		WHERE dc_id = "TB_TRA".dc_id ';
		$sqlDocumentosUnidade.= '	 )';
		$sqlDocumentosUnidade.= '    AND "TB_TRA".un_id = '.$userUnitId;
		$sqlDocumentosUnidade.= '	 AND tr_data_termino IS NOT NULL';
		$sqlDocumentosUnidade.= '	 AND tr_data_termino IS NOT NULL';
		$sqlDocumentosUnidade.= '	 AND "TB_TRA".tr_guia_impressa = 1'; 
		$sqlDocumentosUnidade.= '  )';
		$sqlDocumentosUnidade.= '  OR';
		$sqlDocumentosUnidade.= '  (';
		$sqlDocumentosUnidade.= '		"TB_DOC".us_id IN';
		$sqlDocumentosUnidade.= '		(';
		$sqlDocumentosUnidade.= '			SELECT us_id FROM tb_usuario WHERE un_id = '.$userUnitId;
		$sqlDocumentosUnidade.= '		)';
		$sqlDocumentosUnidade.= '		AND "TB_TRA".tr_id IS NULL';
		$sqlDocumentosUnidade.= '  )';
		$sqlDocumentosUnidade.= ')';
		$sqlDocumentosUnidade.= 'ORDER BY "TB_DOC".dc_data_cadastro DESC, "TB_DOC".dc_id DESC';
		
		#echo $sqlDocumentosUnidade;
		#exit;
		
		#$orderDocumentosUnidade = array('dc_data_cadastro DESC', 'dc_id DESC');
		
		#$selectUnidadeUsuario->from(array('UN' => 'tb_unidade'), $columnsUnidadeUsuario);
		#$selectUnidadeUsuario->joinInner(array('US' => 'tb_usuario'), 'UN.un_id = US.un_id', array());
		#$selectUnidadeUsuario->where('US.us_id = ?', $usId);
		#$resultUnidadeUsuario = $this->fetchAll();
		
		$arrDocumentos = $db->query($sqlDocumentosUnidade);
		#$arrDocumentos = $arrDocumentos->toArray();
		foreach($arrDocumentos as $documento)
		{
			// === Retorna o Tipo do Documento (Provisório)
			$_whereTipoDocumento  		= "td_id = ".$documento['td_id'];
			$resTipoDocumento 			= $objTipoDocumento->fetchRow($_whereTipoDocumento);
			$documento['td_descricao']	= $resTipoDocumento->td_descricao;
			unset($resTipoDocumento, $_whereTipoDocumento);
			// === Retorna o Tipo do Documento (Provisório)
			
			// === Retorna o nome da Unidade (Provisório)
			$_whereAssunto 				= "as_id = ".$documento['as_id'];
			$resAssunto					= $objAssunto->fetchRow($_whereAssunto);
			$documento['as_descricao']	= $resAssunto->as_descricao;
			unset($resAssunto, $_whereAssunto);
			// === Retorna o nome da Unidade (Provisório)
			
			// === Retorna o nome do Orgão de Origem do Documento Unidade se interno e Orgão Externo se externo (Provisório)
			if($documento['oe_id'] > 0)
			{
				$_whereOrgaoExterno 		= "oe_id = ".$documento['oe_id'];
				$resOrgaoExterno			= $objOrgaoExterno->fetchRow($_whereOrgaoExterno);
				$documento['orgao_origem']	= $resOrgaoExterno->oe_descricao;
				unset($resOrgaoExternoo, $_whereOrgaoExterno);
			}
			else
			{
				$documento['orgao_origem'] = utf8_encode($objUsuario->getUnidadeUsuario($documento['us_id'])->un_descricao);
			}
			// === Retorna o nome do Orgão de Origem do Documento Unidade se interno e Orgão Externo se externo (Provisório)
			
			// === Retorna se existe tramitação desse documento
			$_whereTramitacao  		= "dc_id = ".$documento['dc_id'];
			$resTramitacao 			    = $objTramitacao->fetchRow($_whereTramitacao); 
			$documento['tr_temtram']	= (sizeof($resTramitacao) > 0)? 1 : 0;
			//$resTramitacao->tr_descricao;
			// === Retorna se existe tramitação desse documento
			
			// === Retorna o nome do Usuário (Provisório)
			$_whereUsuario  		= "us_id = ".$documento['us_id'];
			$resUsuario 			= $objUsuario->fetchRow($_whereUsuario);
			$documento['us_nome']	= $resUsuario->us_nome;			
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome do Usuário (Provisório)
			
			//==== Retorna o órgão de origem (se existe orgão externo, assumir orgão externo, se não existe, assumir o do usuário do documento)
			
			/*if ($documento['oe_id'] != null)
			{
				$documento['oo_descricao'] = $documento['oe_']
			}
			*/
			//====
			
			// Caso o documento possuir arquivo PDF cadastrado guarda no array do documento que possui arquivo
			$documento['possuiArquivo'] = $objDocumentoArquivo->hasDocumentoArquivo($documento['dc_id']);
			
			$arrNewDocumentos[] = $documento;
		}
		unset($arrDocumentos, $objTipoDocumento, $objAssunto, $objOrgaoExterno);
		
		return $arrNewDocumentos;
		unset($arrNewDocumentos);
	}
	
	public function Arrayflex()
	{
		$Ret = Array(array('primeiro' =>'valor22', 'segundo' => 'valor33', 'terceiro' => 'valor33'),
						array('primeiro' => 'arrayx' ,'segundo' => 'arrayy', 'terceiro' => 'valor33'),
						array('primeiro' => 'alsdkfalks','segundo' => 'novovalor', 'terceiro' => 'valor33'));
		//,array('valor13','valor23'));
		//,array('valor14','valor24'),array('valor15','valor25'),array('valor16','valor26'));
		
		//Foreach ($Ret as $row)
		//{
		//}
		
		//$Retu = $Ret;
		//$Retu[] = $Ret[1];
		
		Return $Ret;
		//$Ret = Array(2 => array('oito','nove','dez','onze','doze','treze','sete');
		/*$Ret = Array(3 => array('um','dois','tres','quatro','cinco','seis','sete');
		$Ret = Array(4 => array('um','dois','tres','quatro','cinco','seis','sete');
		$Ret = Array(5 => array('um','dois','tres','quatro','cinco','seis','sete');
		$Ret = Array(6 => array('um','dois','tres','quatro','cinco','seis','sete');
		$Ret = Array(7 => array('um','dois','tres','quatro','cinco','seis','sete');
		$Ret = Array(8 => array('um','dois','tres','quatro','cinco','seis','sete');*/
	}
}