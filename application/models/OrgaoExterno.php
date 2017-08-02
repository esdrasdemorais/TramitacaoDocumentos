<?php
class OrgaoExterno extends Zend_Db_Table 
{
	protected $_name 	= 'tb_orgao_externo';
	protected $_primary = 'oe_id';
	
	public function getOrgaosExternos()
	{
		Zend_Loader::loadClass('Usuario');
		
		$objOrgaoExterno = new OrgaoExterno();
		$objUsuario 	 = new Usuario();
		
		$arrOrgaosExternos = $objOrgaoExterno->fetchAll('oe_excluido IS NULL', array('oe_descricao ASC', 'oe_data_cadastro DESC'));
		$arrOrgaosExternos = $arrOrgaosExternos->toArray();
		foreach($arrOrgaosExternos as $orgaoexterno)
		{
			// === Retorna o nome do Usuário (Provisório)
			$_whereUsuario  			= "us_id = ".$orgaoexterno['us_id'];
			$resUsuario 				= $objUsuario->fetchRow($_whereUsuario);
			$orgaoexterno['us_nome']	= $resUsuario->us_nome;
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome do Usuário (Provisório)
			
			$arrNewOrgaosExternos[] = $orgaoexterno;
		}
		unset($arrOrgaosExternos, $objOrgaoExterno, $objUsuario);
		
		return $arrNewOrgaosExternos;
		unset($arrNewOrgaosExternos);
	}
}