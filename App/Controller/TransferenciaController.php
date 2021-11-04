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
        $valorAtualizado = 0.0;
        //busca id do usuário para validação de saldo em conta
        $idUsuario = $this->model->usuario($email);
        if ($idUsuario != 0) {
            
            $dados = $this->model->validarSaldo($idUsuario);
         
            foreach ($dados as $valoresSomados) {
                foreach ($valoresSomados as $valores) {

                    $valorAtualizado += $valores['valor'];
                }
              
           }
               //verifica se existe valor em conta
               if ($valorAtualizado > 0.0) {
                
                        //verifica se valor na conta é menor ou igual ao que será transferido
                        if ($valorAtualizado > $valor) {

                            $retorno['status'] = true;
                            $retorno['msg'] = 'Você tem saldo!';
                            $retorno['id'] = $idUsuario;
                            $retorno['saldor_atualizado'] = $valorAtualizado - $valor;
                        }
                 
               
            }
        }

        return $retorno;
    }

    /**
     * Autoriza os dados
     */
   private function autorizacaoEnvio(){
    $url = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
    $return = curl_exec($ch);
    var_dump($return);
    var_dump(curl_error($ch));
   }

    /**
     * realiza a transferencia dos dados
     */
    public function transferencia($usuario, $usuario_recebidor, $valor, $saldo_atualizado) {
       
        //valor id do usuário que esta recebendo transação
       $idUsuario = $this->model->usuario($usuario_recebidor);
       $valorRecebidor = $valor;

       $retorno = $this->model->transferencia($usuario, $idUsuario, $valor);
            // $this->autorizacaoEnvio(); SSL certificate problem: unable to get local issuer certificate
       if ($retorno['ok']) {
           //atualiza saldo usuario enviado e usuario recebidor
           $dados = $this->model->atualizarUsuarioTransferencia($usuario, $saldo_atualizado);
           if ($dados['ok']) {
               //atualiza dados do recebidor
               $recebidor = $this->model->validarSaldo($idUsuario);
           
               if ($recebidor['row'] != 0) {

                   foreach ($recebidor['sql'] as $dadosRecebidor) {
    
                        $dadosRecebidor['valor'] += $valor;
                        $valorRecebidor = $dadosRecebidor['valor'];
                    }
                    
                    $retorno['ok'] = $dadosRecebidor['ok'];
                    $retorno['msg'] = $dadosRecebidor['msg'];
                } else {
                    
                    $retorno = $this->model->deposito($idUsuario, $valorRecebidor);
                }
           }
       }
       return $retorno;
    }


}

?>