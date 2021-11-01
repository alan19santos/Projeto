<?php

namespace App\Controller\Controller;

use App\Controller\Controller;
use App\Transferencia;

class TransferenciaController extends Controller {

    public $msg;
    public $erro;
    public $model;

    public function __construct(){
        $this->msg = '';
        $this->erro = false;
        $this->model = new Transferencia;
    }

    /**
     * verifica se o usuáio esta logado para autenticar transferencias
     */
    public function usuarioLogado($email)
    {
        $retorno = array('msg'=>'necessário autenticar login!', 'ok'=>false);
        $dados = $this->model->validarLogin($email);

        foreach($dados as $dado) {
            $retorno['tipoUsuario'] = $dado['tipo'];
            if ($dado['sessaousuario']) {
                $sessao = $dado['sessaousuario'];
            }
        }

        if ($sessao)
        {
            $retorno['ok'] =  $sessao;
            $retorno['msg'] = '';
        }

       return $retorno;
    }

    public function depositar($valorDeposito, $email){

        $retorno = array('ok'=>false, 'msg'=>'Erro no deposito!');
        $deposito = $this->model->deposito($email, $valorDeposito);
      
        if ($deposito['ok']){

            $retorno['ok'] = $deposito['ok'];
            $retorno['msg'] = 'Deposito Realizado!';
        }

        return $retorno;
    }

    public function validarSaldo($email, $valor) {

        $retorno = array('msg'=>'usuário sem saldo! efetue deposito!', 'status'=>false);
       
        //busca id do usuário para validação de saldo em conta
        $idUsuario = $this->model->usuario($email);

        if ($idUsuario != 0) {
            
           $dados = $this->model->validarSaldo($idUsuario, $valor);
          
           foreach ($dados as $dado) {
               //verifica se existe valor em conta
               if ($dado['valor'] > 0.0) {
                   if ($dado['valor'] > $valor) {
                        //verifica se valor na conta é menor ou igual ao que será transferido
                        $retorno['status'] = true;
                        $retorno['msg'] = 'Você tem saldo!';
                        $retorno['id'] = $idUsuario;
                        $retorno['saldor_atualizado'] = $dado['valor'] - $valor;
                    }
                }
            }
        }

        return $retorno;
    }

    public function validarAcesso() {

    }

    /**
     * realiza a transferencia dos dados
     */
    public function transferencia($usuario, $usuario_recebidor, $valor, $saldo_atualizado) {
       
        $idUsuario = $this->model->usuario($usuario_recebidor);

       $retorno = $this->model->transferencia($usuario, $idUsuario, $valor);
       if ($retorno['ok']) {
           //atualiza saldo usuario enviado e usuario recebidor
           $dados = $this->model->atualizarUsuarioTransferencia($usuario, $saldo_atualizado);
           if ($dados['ok']) {
               //atualiza dados do recebidor
               $recebidor = $this->model->validarSaldo($idUsuario);
                foreach ($recebidor as $dadosRecebidor) {
                    $dadosRecebidor['valor'] += $valor;
                    $valorRecebidor = $dadosRecebidor['valor'];
                }
                //atualiza os dados do usuário que esta recebendo transferencia
                $dadosRecebidor = $this->model->atualizarUsuarioTransferencia($idUsuario, $valorRecebidor);
           }
       }
       return $retorno;
    }


}

?>