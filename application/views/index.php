<?php
// verifica a vers�o
require('application/version.php');
// configura o caminho
require('application/path.php');
configurePath(basename(getcwd()));
// carrega classe que far� a inicializa��o do Zend Framework
require('application/Bootstrap.php');
new Bootstrap($_SERVER['PHP_SELF']);