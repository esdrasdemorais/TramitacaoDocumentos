<?php
/**
 * Classe modelo de formul�rio de TramitacaoForm
 */
class TramitacaoForm extends Zend_Form
{
    public function init()
    {
		// ID do tipo do documento
        $td_id = $this->addElement('select', 'dc_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Documento:'
        ));
		
		// Cota da tramita��o se houver (DEFAULT NULL)
        $tr_cota = $this->addElement('text', 'tr_cota', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(1, 200)),
            ),
            'required'   => false,
            'label'      => 'Cota:'
        ));
		
		// Data In�cio Tramita��o
        $tr_cota = $this->addElement('text', 'tr_data_inicio', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(10, 10))
            ),
            'required'   => false,
            'label'      => 'Cota:'
        ));
		
		// Data T�rmino Tramita��o
		$oe_id = $this->addElement('text', 'tr_data_termino', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
				array('StringLength', false, array(10, 10))
            ),
            'required'   => true,
            'label'      => 'Ordem Externa:'
        ));
	
		// ID Unidade de destino (DEFAULT NULL)
		$oe_id = $this->addElement('select', 'un_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Ordem Externa:'
        ));
		
		$oe_id = $this->addElement('select', 'un_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Ordem Externa:'
        ));
		
		
		
		// Bot�o de envio do formul�rio
        $salvar = $this->addElement('submit', 'save', array(
            'required' => true,
            'ignore'   => true,
            'label'    => 'Salvar'
        ));
        
        /**
         * Esse m�todo serve para exibir mensagens de erro no submit do formul�rio
         */
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}