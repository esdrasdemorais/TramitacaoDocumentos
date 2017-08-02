<?php
class Flex
{
	public function Say()
	{
		return 'Olá Flex do PHP 2';
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
	
	public function getDoc()
	{
		//include 'Documento.php';
		$Documentos = new Documento();
		
		$Retdocs = $Documentos->getDocumentos(null, null);
		
		/*foreach($Retdoc as $row)
		{
			$ndoc['primeiro'] = $row['dc_id'];
			$ndoc['segundo'] = $row['dc_numero'];
			$ndoc['terceiro'] = $row['td_id'];
			
			$docs[] = $ndoc;
		}
		*/
		Return $Retdocs;
	}
}