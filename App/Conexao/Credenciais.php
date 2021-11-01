<?php

//Criar as constantes com as credencias de acesso ao banco de dados
// define('HOST', 'Servidor do banco de dados');
// define('USER', 'Usuario do banco de dados');
// define('PASS', 'Senha do banco de dados');
// define('DBNAME', 'Nome do banco de dados');
// define('PORT', 'Porta do banco de dados');

class Credenciais{

    public function __construct() {}    
    

    public function server($servidor=''){
        if (!empty($servidor)) {
            return $servidor;
        }
        return 'localhost';
    }

    public function user($usuario = ''){
        if (!empty($usuario)) {
            return $usuario;
        }
        return 'postgres';
    }

    public function password($senha = ''){
        if (!empty($senha)) {
            return $senha;
        }
        return '1245';
    }

    public function dbname($bancoDados = ''){
        if (!empty($bancoDados)) {
            return $bancoDados;
        }
        return 'transferencia';
    }

    public function port($porta = ''){
        if (!empty($porta)) {
            return $porta;
        }
        return '5433';
    }
}



?>