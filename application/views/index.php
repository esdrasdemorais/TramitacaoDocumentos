<?php
// verifica a versуo
require('application/version.php');
// configura o caminho
require('application/path.php');
configurePath(basename(getcwd()));
// carrega classe que farс a inicializaчуo do Zend Framework
require('application/Bootstrap.php');
new Bootstrap($_SERVER['PHP_SELF']);