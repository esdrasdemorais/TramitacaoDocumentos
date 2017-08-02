<?php
/**
 * Classe modelo de formulário de TramitacaoForm
 */
class TramitacaoForm extends Zend_Form
{
    public function init()
    {
		Zend_Loader::loadClass('Zend_Form_Element_Hidden');
		Zend_Loader::loadClass('Zend_Form_Element_Select');
		Zend_Loader::loadClass('Documento');
		Zend_Loader::loadClass('Unidade');
		
		/*public function __construct($options = null)
		{
			parent::__construct($options);
			
			$locale = new Zend_Locale();
			$translate = new Zend_Translate('tmx', APPLICATION_DIRECTORY . '/languages/portuguese.xml', $locale);
			$this->setDefaultTranslator($translate);
		}*/
		
		/*Zend_Loader::loadClass('Zend_Date');
		Zend_Loader::loadClass('Zend_Locale');*/
		
		// ID do tipo do documento
        /*$td_id = $this->addElement('select', 'dc_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Documento:'
        ));*/
		/*$dcId = new Zend_Form_Element_Select('dc_id');
		$dcId->setLabel('Documento:')
         ->setRequired(true);
		
		$tableDocumento = new Documento();
		$dcId->addMultiOption("", "Selecione");
		foreach($tableDocumento->fetchAll('dc_excluido IS NULL') as $tD)
			$dcId->addMultiOption($tD->dc_id, $tD->dc_id." / ".$tD->td_id); /** Label com o id do documento e id do tipo do documento */
		
		#$this->addElement($dcId);
	
		// Cota da tramitação se houver (DEFAULT NULL)
        $tr_cota = $this->addElement('textarea', 'tr_cota', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'StringLength',
                array('StringLength', false, array(2, 250)),
            ),
            'required'   => false,
            'label'      => 'Cota:',
			'cols' 		 => 70,
			'rows'		 => 10,
        ));
		
		// Data Início Tramitação
        /*$tr_cota = $this->addElement('text', 'tr_data_inicio', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(10, 10))
            ),
            'required'   => true,
            'label'      => 'Data Início Tramitação:'
        ));*/
		
		// Data Término Tramitação
		/*$oe_id = $this->addElement('text', 'tr_data_termino', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
				array('StringLength', false, array(10, 10))
            ),
            'required'   => false,
            'label'      => 'Ordem Externa:'
        ));*/
	
		// ID Unidade de destino
		/*$unId = $this->addElement('select', 'un_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Unidade Destino:'
        ));*/
		$unId = new Zend_Form_Element_Select('un_id');
		$unId->setLabel('Unidade Destino:')
         ->setRequired(true);
		 
		/** Tabela da Prefeitura a integrar */
		$tableUnidade = new Unidade();
		$unId->addMultiOption("", "Selecione");
		foreach($tableUnidade->fetchAll() as $tU)
			$unId->addMultiOption($tU->un_id, utf8_encode($tU->un_descricao));
		
		$this->addElement($unId);
		
		
		// Botão de envio do formulário
        $submit = $this->createElement('submit', 'save', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Salvar'
        ));
		$submit->removeDecorator('Label');
		$submit->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
        $submit->setDecorators(
		array(
			array("decorator" => "ViewHelper"), //This one is required...
            array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($submit);
		
		// Botão de cancelamento da ação
		$cancel = $this->createElement('button', 'cancel', array(
			'required' => true,
			'ignore'   => true,
			'label'    => 'Cancelar',
			'attribs' => array(
				'onclick' => 'window.history.back()'
			)
		));
		$cancel->removeDecorator('Label');
		$cancel->removeDecorator("DtDdWrapper"); //I'm removing DtDdWrapper and it will not be wrapped with them anymore but let's also see how I can reset them...
		$cancel->setDecorators(
		array(
			array("decorator" => "ViewHelper"), //This one is required...
            array("decorator" =>"HtmlTag", "options" => 
			array('tag' => "span", "class" =>"formbutton")))
		); //Beware that I can set the attributes of the span element via options...
		$this->addElement($cancel);
		
		#$trId = new Zend_Form_Element_Hidden('tr_id');
		#if(isset($view->dados))
		#	$trId->setValue($view->dados->tr_id);
		//$this->addElements(array($dc_id, $artista, $titulo, $submit));
		#$this->addElement($trId)
          /*->addElement($artista)
          ->addElement($titulo)
          ->addElement($sub)
          ->addElement($bt_voltar)*/;
		
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