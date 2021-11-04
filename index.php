<?php 
require 'vendor/autoload.php';
use App\UsuarioController;
use App\Controller\Controller\TransferenciaController;

$cadastro = new UsuarioController();
$transferencia = new TransferenciaController();


    switch ($_POST['tipoFormulario']) {

        case 'cadastro':
            /**
             * Cadastrando usuário
             */
            $retorno = $cadastro->cadastrar($_POST);
            echo json_encode($retorno, JSON_UNESCAPED_SLASHES);
        break;

        /**
         * Transferencia de dinheiro
         */
        case 'transacao':
            $retorno = array();
            //validar se usuário esta logado
            $retorno = $cadastro->usuarioLogado($_POST['email']);
          
            if ($retorno['ok'] && $retorno['tipoUsuario'] == 1) {
                    //validar se o usuário possui saldo
                    $retorno = $transferencia->validarSaldo($_POST['email'], $_POST['valor']);

                if ($retorno['status'] == true) {
                    $retorno = $transferencia->transferencia($retorno['id'], $_POST['usuario_recebidor'],
                                                    $_POST['valor'], $retorno['saldor_atualizado']);
                } 
            } else {
                $retorno['ok'] = false;
                $retorno['msg'] = 'Seu pérfil é Lojista ou não tem Saldo!';
            }
            echo json_encode($retorno);
        break;

        /**
         * Gerando Deposito
         */
        case 'deposito':

            $validarUsuario = $cadastro->usuarioLogado($_POST['email']);
            $retorno = array('ok'=>false, 'msg'=>'Usuário não Validado');
            if ($validarUsuario['ok']) {

                $dados = $transferencia->depositar($_POST['depositoConta'], $_POST['email']);
                $retorno['ok'] = $dados['ok'];
                $retorno['msg'] = $dados['msg'];
            }

            echo json_encode($retorno);
            break;

        default:
        /**
         * Loguin no sistema
         */
        
          $login = $cadastro->login($_GET['email'], $_GET['senha'], $_GET['token']);
          echo json_encode($login);
        break;
    }
    


?>