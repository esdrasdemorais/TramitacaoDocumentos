<?php
class Assunto extends Zend_Db_Table 
{
	protected $_name 	= 'tb_assunto';
	protected $_primary = 'as_id';
	
	public function getAssuntos()
	{
		Zend_Loader::loadClass('Usuario');
	
		$objAssunto = new Assunto();
		$objUsuario  = new Usuario();
		
		$arrAssuntos = $objAssunto->fetchAll('as_excluido IS NULL', array('as_descricao ASC', 'as_data_cadastro DESC'));
		$arrAssuntos = $arrAssuntos->toArray();
		foreach($arrAssuntos as $assunto)
		{
			// === Retorna o nome do Usuário (Provisório)
			$_whereUsuario  			= "us_id = ".$assunto['us_id'];
			$resUsuario 				= $objUsuario->fetchRow($_whereUsuario);
			$assunto['us_nome']			= $resUsuario->us_nome;
			unset($resUnidade, $_whereUnidade);
			// === Retorna o nome do Usuário (Provisório)
			
			$arrNewAssuntos[] = $assunto;
		}
		unset($arrAssuntos, $objAssunto, $objUsuario);
		
		return $arrNewAssuntos;
		unset($arrNewAssuntos);
	}
}