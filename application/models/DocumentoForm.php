<?php
/**
 * Classe modelo do formulário de Documentos
 */
class DocumentoForm extends Zend_Form
{
    public function init()
    {
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		Zend_Loader::loadClass('Zend_Form_Element_Select');
		Zend_Loader::loadClass('TipoDocumento');
		Zend_Loader::loadClass('Assunto');
		Zend_Loader::loadClass('OrgaoExterno');
		#Zend_Loader::loadClass('Zend_Date');
		#Zend_Loader::loadClass('Zend_Locale');	
		
		// Hidden ID documento
	  	/*$td_id = $this->addElement('hidden', 'dc_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Int'
            ),
            'required'   => false
        ));*/
		
		$dcId = new Zend_Form_Element_Hidden('dc_id');
		if(isset($view->dados))
			$dcId->setValue($view->dados->dc_id);
		//$this->addElements(array($dc_id, $artista, $titulo, $submit));
		$this->addElement($dcId)
          /*->addElement($artista)
          ->addElement($titulo)
          ->addElement($sub)
          ->addElement($bt_voltar)*/;

		// ID do tipo do documento
        /*$td_id = $this->addElement('select', 'td_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum'
            ),
            'required'   => true,
            'label'      => 'Tipo de Documento:'
        ));
		$objDocumentoForm = new BrowseForm();
		$objDocumentoForm->getElement('td_id')
						   ->setMultiOptions(aray(1=>'form 1',2=>'form 2'));
		if ($browseForm->isValid($this->getRequest()->getQuery())) {
		// etc
		}
		echo $browseForm; */
		$tdId = new Zend_Form_Element_Select('td_id');
		$tdId->setLabel('* Tipo Documento:')
         ->setRequired(true);
		
		$tableTipoDocumento = new TipoDocumento();
		$tdId->addMultiOption("", "Selecione");
		foreach($tableTipoDocumento->fetchAll() as $tTD)
			$tdId->addMultiOption($tTD->td_id, $tTD->td_descricao);
		
		$this->addElement($tdId);
		
		// ID do assunto do documento
		/*$as_id = $this->addElement('select', 'as_id', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum'
            ),
            'required'   => true,
            'label'      => 'Assunto:'
        ));*/
		$asId = new Zend_Form_Element_Select('as_id');
		$asId->setLabel('* Assunto:')
         ->setRequired(true);
		
		$tableAssunto = new Assunto();
		$asId->addMultiOption("", "Selecione");
		foreach($tableAssunto->fetchAll() as $tA)
			$asId->addMultiOption($tA->as_id, $tA->as_descricao);
			
		$this->addElement($asId);
		
		// Data Elaboração
		#$objDate 		= new Zend_Date();
		#$objLocale  	= new Zend_Locale('pt_BR');
		#Zend_Date::setOptions(array('format_type' => 'php'));
		#echo $objDate->get(); /** Output of the desired Timestamp date */
		
		#$dcDataElaboracao = new Zend_Form_Element_Hidden('dc_data_elaboracao');
		#$dcDataElaboracao->setValue($objDate->toString('d-m-Y H:i:s')); /** pt_BR format */
		
		#$this->addElement($dcDataElaboracao);
		
		// Complemento do assunto do documento
        $dc_compl_assunto = $this->addElement('text', 'dc_compl_assunto', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(1, 150)),
            ),
            'required'   => false,
            'label'      => 'Complemento do Assunto:'
        ));
		
		// ID da Ordem Externa do documento se houver (DEFAULT NULL)
		/*$oe_id = $this->addElement('select', 'oe_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alnum'
            ),
            'required'   => true,
            'label'      => 'Orgão Externo:'
        ));*/
		$oeId = new Zend_Form_Element_Select('oe_id');
		$oeId->setLabel('Orgão Externo:')
         ->setRequired(false);
		
		$tableOrgaoExterno = new OrgaoExterno();
		$oeId->addMultiOption("", "Selecione");
		foreach($tableOrgaoExterno->fetchAll() as $tOE)
			$oeId->addMultiOption($tOE->oe_id, $tOE->oe_descricao);
		
		$this->addElement($oeId);
		
		
		/** automatically sets form encoding on upload */
		#$this->setAttrib('enctype', 'multipart/form-data');
		// or
		#$this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
		
		// Botão de envio do formulário
        $salvar = $this->addElement('submit', 'save', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Salvar'
        ));
        
        /**
         * Esse método serve para exibir mensagens de erro no submit do formulário
         */
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}