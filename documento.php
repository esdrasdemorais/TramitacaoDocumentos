<?php 
/**
 * 
 * Sess�o de configura��o de paths e erros
 */

/** Configura as mensagens de erro que devem ser apresentadas para mostrar os erros apenas nos testes (precisa estar setado no PHP.ini) */
error_reporting(E_ALL|E_STRICT);

/** Seta o timezone pra s�o paulo (>=PHP 5.1) */
setlocale(LC_ALL, 'pt_BR');
date_default_timezone_set('America/Sao_Paulo');

/**
 * Configura o caminho a ser procurado em todos os includes.
 * Ir� procurar no diret�rio ../library, no application/models
 * e no caminho original do PHP.
 */
/**
 * � interessante utilizar set_include_path para definir onde se encontram
 * todos os arquivos do projeto, pois assim se evita que o mesmo c�digo
 * seja escrito v�rias vezes, gerando menos linhas e facilitando qualquer
 * altera��o de path.
 */
/**
 * Seta include path para o funcionamento correto do framework ZEND e o modelo da aplica��o (application/models)
 ***OBRIGAT�RIO***
 */
#set_include_path('.' . PATH_SEPARATOR . './library' . PATH_SEPARATOR . './application/models/' . PATH_SEPARATOR.get_include_path());
$operatingSystem = stripos($_SERVER['SERVER_SOFTWARE'],'win32')!== FALSE ? 'WINDOWS' : 'LINUX';
$bar 			= ($operatingSystem == 'WINDOWS') ? '\\' : '/';
$pathSeparator  = ($operatingSystem == 'WINDOWS') ? ';' : ':';
$documentRoot   = ($operatingSystem == 'WINDOWS') ? str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']) : $_SERVER['DOCUMENT_ROOT'];
/**
 * Seta o path separando os paths por PATH_SEPARATOR ou :
 * $path = ':'.$pathSeparator.$documentRoot.$bar.'teste'.$bar.'library';
 * $path+= ':'.$pathSeparator.$documentRoot.$bar.'teste'.$bar.'application'.$bar.'models';
 */
$path = $pathSeparator.$documentRoot.$bar.'tramitacao_documentos'.$bar.'library'.PATH_SEPARATOR.'application'.$bar.'models';
set_include_path(get_include_path().$path);

// configura o caminho
/*require('application/path.php');
configurePath(basename(getcwd()));*/

/**
 * Faz o include do componente Zend_Loader.
 * Este include � OBRIGAT�RIO.
 * Zend_Loader carrega arquivos, classes e recursos
 * dinamicamente em sua aplica��o PHP.
 * => suporta autocarregamento da SPL (Standard PHP Library)
 * => suporta include_path
 * => fornece mecanismo de falha baseado em exce��o
 */
require_once("Zend/Loader.php");

/**
 * O m�todo loadClass � respons�vel por incluir o arquivo respons�vel pela classe.
 * O acesso a n�veis dos diret�rios do framework ZEND � feito atrav�s do "_" n�o da "/".
 */

/**
* O registro (Zend_Registry) � um cont�iner para armazenar objetos e valores
* no espa�o da aplica��o. Armazenar um objeto ou valor
* no registro torna o mesmo sempre dispon�vel ao longo
* da aplica��o durante o tempo de vida da requisi��o.
* Este mecanismo � freq�entemente uma alternativa aceit�vel
* ao uso de vari�veis globais.
* => fornece armazenamento acess�vel globalmente para objetos
* e valores
* => fornece os padr�es iterator, array e indexed access
*/
Zend_Loader::loadClass('Zend_Registry');

/** Inclui o suporte a sess�es. S� � necess�rio caso seja usado. */
Zend_Loader::loadClass('Zend_Session');

/**
 * 
 * Carregando m�dulos necess�rios
 */
/* Classe de controladores */
Zend_Loader::loadClass('Zend_Controller_Front');
/* Classe das vis�es */
Zend_Loader::loadClass("Zend_View");

Zend_Loader::loadClass("Zend_Auth");
Zend_Loader::loadClass("Zend_Controller_Plugin_Abstract");
#Zend_Loader::loadClass("SecurityPlugin");
Zend_Loader::loadClass('Zend_Form');
#Zend_Loader::loadClass('LoginForm');
Zend_Loader::loadClass('DocumentoForm');

/**
* Este m�todo tenta carregar o arquivo passado como par�metro procurando no path
* definido com a fun��o set_include_path. Caso ele n�o consiga encontrar o arquivo,
* � gerada uma exce��o que indica arquivo inexistente ou sem acesso. O m�todo
* considera os underscores no nome do arquivo como subdiret�rios. Por exemplo, o
* comando Zend::loadClass('Zend_Controller_Front') faz a importa��o do arquivo
* ../zendframework/library/Zend/Controller/Front.php.
* Seguir esse padr�o facilita o entendimento da estrutura do projeto.
* Essa classe se encontra em Zend/Controller/Front.php
* Pode ser "loadado" diretamente pelo nome se preferir
* Para come�ar n�s precisamos "loadar" primeiro o front controller
* Ele faz um controle autom�tico para detectar a base URL e fazer o redirecionamento correto
*/
$controlador = Zend_Controller_Front::getInstance();

/* Mostrar exce��es (apenas para testes) */
$controlador->throwExceptions(TRUE);

$controlador->setControllerDirectory('./application/controllers'); // seta diret�rio com nossos controllers

#$controlador->registerPlugin(new SecurityPlugin());

/**
 * 
 * Inicializando o sistema
 */
$controlador->dispatch();

/**
 * 
 * N�o se p�e a tag de fechamento do PHP, para evitar mensagens de erros
 */