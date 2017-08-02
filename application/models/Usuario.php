<?php
class Usuario extends Zend_Db_Table 
{
	protected $_name = 'tb_usuario';
	
	public function getUnidadeUsuario($usId)
	{
		Zend_Loader::loadClass('Unidade');
		
		$objRegistry = Zend_Registry::getInstance();
		$db	 		 = $objRegistry->db;
		
		$objUsuario = new Usuario();
		$resUsuario = $objUsuario->fetchRow('us_id='.$usId);
		$unId 		= $resUsuario->un_id;
		
		$objUnidade = new Unidade();
		$resUnidade	= $objUnidade->fetchRow('un_id='.$unId);
		
		/*$columnsUnidadeUsuario = array(
			'un_id',
			'un_descricao'
		);
		
		$selectUnidadeUsuario = $db->select();
		$selectUnidadeUsuario->from(array('UN' => 'tb_unidade'), $columnsUnidadeUsuario);
		$selectUnidadeUsuario->joinInner(array('US' => 'tb_usuario'), 'UN.un_id = US.un_id', array());
		$selectUnidadeUsuario->where('US.us_id = ?', $usId);
		$resultUnidadeUsuario = $this->fetchAll();
		
		echo $resultUnidadeUsuario;exit;*/
		
		return $resUnidade;
		unset($resUnidade, $objUsuario, $objUnidade);
	}
}