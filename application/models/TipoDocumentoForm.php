<?php
/**
 * Classe modelo de formulário de Documentos
 */
class DocumentoForm extends Zend_Form
{
    public function init()
    {
		// ID do tipo do documento
        $td_id = $this->addElement('select', 'td_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Tipo de Documento:'
        ));

		// ID do assunto do documento
		$as_id = $this->addElement('select', 'as_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Assunto:'
        ));
		
		// Complemento do assunto do documento
        $dc_compl_assunto = $this->addElement('text', 'dc_compl_assunto', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(1, 150)),
            ),
            'required'   => false,
            'label'      => 'Complemento do Assunto:'
        ));
		
		// ID da Ordem Externa do documento se houver (DEFAULT NULL)
		$oe_id = $this->addElement('select', 'oe_id', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'num'
            ),
            'required'   => true,
            'label'      => 'Ordem Externa:'
        ));
		
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