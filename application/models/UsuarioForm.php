<?php
/**
 * Classe modelo de formulário de TramitacaoForm
 */
class UsuarioForm extends Zend_Form
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
		
		// Cota da tramitação se houver (DEFAULT NULL)
        $us_cota = $this->addElement('text', 'tr_cota', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(1, 200)),
            ),
            'required'   => false,
            'label'      => 'Cota:'
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