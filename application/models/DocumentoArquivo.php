<?php
class DocumentoArquivo extends Zend_Db_Table
{
	protected $_name 	= 'tb_documento_arquivo';
	protected $_primary = 'dc_id';
	
	public function hasDocumentoArquivo($dcId)
	{
		$objDocumentoArquivo = new DocumentoArquivo();
		
		$whereDocumentoArquivo 	= 'dc_id = '.$dcId; 
		$resDocumentoArquivo	= $objDocumentoArquivo->fetchRow($whereDocumentoArquivo);
		if(count($resDocumentoArquivo))
			return $resDocumentoArquivo->dc_id;
		else
			return 0;
	}
}