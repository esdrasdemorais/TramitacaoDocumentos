<?php
class TipoDocumento extends Zend_Db_Table 
{
	protected $_name 	= 'tb_tipo_documento';
	protected $_primary = 'td_id';
	
	public function getTiposDocumentos()
	{
		Zend_Loader::loadClass('Usuario');
		
		$objTipoDocumento 	= new TipoDocumento();
		$objUsuario			= new Usuario();
		
		$arrTiposDocumentos = $objTipoDocumento->fetchAll('td_excluido IS NULL', array('td_descricao ASC', 'td_data_cadastro DESC'));
		$arrTiposDocumentos = $arrTiposDocumentos->toArray();
		foreach($arrTiposDocumentos as $tipodocumento)
		{
			// === Retorna o nome do Usuário (Provisório)
			$_whereUsuario  			= "us_id = ".$tipodocumento['us_id'];
			$resUsuario 				= $objUsuario->fetchRow($_whereUsuario);
			$tipodocumento['us_nome']	= $resUsuario->us_nome;
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome do Usuário (Provisório)
			
			$arrNewTiposDocumentos[] = $tipodocumento;
		}
		unset($arrTiposDocumentos, $objTipoDocumento, $objUsuario);
		
		return $arrNewTiposDocumentos;
		unset($arrNewTiposDocumentos);
	}
}