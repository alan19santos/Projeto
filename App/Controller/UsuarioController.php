<?php

namespace App;

use App\Controller\Controller;
use App\Usuario;

/**
 * Classe responsavel por controlar as transações do usuário
 */
class UsuarioController extends Controller {

    public $msg;
    public $erro;
    public $model;

    public function __construct()
    {

        $this->msg = '';
        $this->erro = false;
        $this->model = new Usuario;
    }

    /**
     * Valida o usuario antes de efetuar o logue
     */
    public function login($email, $senha, $token) {

        $retornoDados =  array('msg' => 'Usuário ou Senha Inválido!');
        
        if ($this->model->dadosUsuario($email,'',$senha, $token)) {
            $retornoDados['msg'] = 'Usuário Logado com Sucesso!';
        }
        return $retornoDados;
    }


    /**
     * Cadastra o usuário executando as validações antes do cadastro
     */
    public function cadastrar(Array $dados) {
        
        $token = '';
        if (isset($dados['email']) && empty($dados['email'])) {
            $this->msg = 'Por Favor, Digite um E-mail';
            $this->erro = true;
            return array('msg'=>$this->msg, 'erro'=>$this->erro);
        }
               
        if ($this->validarEmailExiste($dados['email'])) {
            $this->msg = 'E-mail já Cadastrado!';
            $this->erro = true;
            return array('msg'=>$this->msg, 'erro'=>$this->erro);
        }
   
        if (!$this->validarCPFCNPJ($dados['cpf_cnpj'])) {
            $this->msg = 'Por Favor, Digite um CPF/CNPJ Valido ou Já Existe CPF/CNPJ Cadastrado';
            $this->erro = true;
            return array('msg'=>$this->msg, 'erro'=>$this->erro);
        }


       $retorno = $this->model->inserirUsuario($dados['nome'], $dados['email'], $dados['cpf_cnpj'], $dados['senha'], $dados['tipo']);

       if ($retorno['row'] != 0) {
           $this->msg = 'Sucesso';
           $token = $retorno['token'];
       } else {
           $this->erro = true;
           $this->msg = $retorno['msg'];
       }

       return array('msg'=>$this->msg, 'erro' => $this->erro, 'token' => $token);
    }


    /**
     * Valida se o Email já foi cadastrado
     */
    private function validarEmailExiste($emial = '', $cpf_cnpj = '') {  

        $dados = $this->model->dadosUsuario($emial, $cpf_cnpj);
      
        if ($dados != 0) {
            return true;
        }
        return false;
    }

    /**
     * Verifica se o usuário digitou um cpf Valido
     */
    private function validarSequenciaCaracter($cpf_cnpj, $tipo) {

        // Verifica se nenhuma das sequências invalidas abaixo 
        // foi digitada. Caso afirmativo, retorna falso
        if ($tipo == 'CPF') {

            if ($cpf_cnpj == '00000000000' || 
                    $cpf_cnpj == '11111111111' || 
                    $cpf_cnpj == '22222222222' || 
                    $cpf_cnpj == '33333333333' || 
                    $cpf_cnpj == '44444444444' || 
                    $cpf_cnpj == '55555555555' || 
                    $cpf_cnpj == '66666666666' || 
                    $cpf_cnpj == '77777777777' || 
                    $cpf_cnpj == '88888888888' || 
                    $cpf_cnpj == '99999999999') {
                return false;
         // Calcula os digitos verificadores para verificar se o
         // CPF é válido
         } else {   
            
            for ($t = 9; $t < 11; $t++) {
                
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf_cnpj{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf_cnpj{$c} != $d) {
                    return false;
                }
            }
    
                return true;
            }
        }
         
        
    }

/**
 * Verifica se o Usuário passou o CPF
 */
    public function validarCPFCNPJ($cpf_cnpj) {

        // Verifica se um número foi informado
        if (isset($cpf_cnpj) && empty($cpf_cnpj)) {
            return false;
        }
    
        // Elimina possivel mascara
        $cpf = preg_replace("/[^0-9]/", "", $cpf_cnpj);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        
        if ($this->model->dadosUsuario('', $cpf_cnpj) != 0) {
            return false;
        }

        // Verifica se o numero de digitos informados é igual a 11 
        if (strlen($cpf) == 11) {
            if (!$this->validarSequenciaCaracter($cpf, 'CPF')) {
                return false;
            }
        }

        $cnpj = preg_replace('/[^0-9]/', '', (string) $cpf_cnpj);

        if (strlen($cnpj) == 14) {
            if (!$this->validarCNPJ($cnpj)) {
                   return false;
            }
        }
       
        return true;
    }

    /**
     * Validar CNPJ
     */
    public function validarCNPJ($cnpj) {

        if ($cnpj == '00000000000000' || 
        $cnpj == '11111111111111' || 
        $cnpj == '22222222222222' || 
        $cnpj == '33333333333333' || 
        $cnpj == '44444444444444' || 
        $cnpj == '55555555555555' || 
        $cnpj == '66666666666666' || 
        $cnpj == '77777777777777' || 
        $cnpj == '88888888888888' || 
        $cnpj == '99999999999999') {
            return false;
        // Calcula os digitos verificadores para verificar se o
        // cnpj é válido
        }
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

	return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
}

?>