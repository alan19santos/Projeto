<?php 

namespace App\Controller;


abstract class Controller {

    public function __construct() {

    }

    public function usuarioLogado($email) {}

    public function verificarSaldo() {}

    public function validarAcesso() {}

    public function transferencia($usuario1, $usuario2, $valor, $saldo_atualizado) {}

    public function cadastrar(Array $dados) {}

    public function validarCPFCNPJ($cpf_cnpj) {}
}

?>