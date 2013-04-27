<?php
/**
 * Classe modelo de formulário de login
 */
class LoginForm extends Zend_Form
{
    public function init()
    {
        $username = $this->addElement('text', 'us_login', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(3, 18)),
            ),
            'required'   => true,
            'label'      => 'Login:'
        ));
		
        $password = $this->addElement('password', 'us_senha', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 20)),
            ),
            'required'   => true,
            'label'      => 'Senha:'
        ));
		
        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Entar'
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