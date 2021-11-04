<?php

namespace App;
use App\Model;
use PDOException;
use PDO;



class Transferencia extends Model {

    protected $conexao = null;

    public function __construct(){
        $conexao = new Model();
        $this->conexao  = $conexao->getConexao();
    }


    /**
     * Efetua Deposito na conta
     */
    public function deposito($email, $valor){
      
        $idUsuario = (is_int($email) ? $email : $this->usuario($email));
        $retorno = array();
        $this->conexao->beginTransaction();
        try {
            $sql = $this->conexao->prepare('insert into valor_usuario (valor, id_usuario)
                 values (:valor, :id)');
            $sql->execute(array(':valor'=>$valor, 
                            ':id'=>$idUsuario));
           
            if ($sql->rowCount() != 0) {
                $retorno['ok']= true;
                $this->conexao->commit();
            }else {
                $retorno['ok']= false;
                $this->conexao->rollBack();
            }

        } catch (PDOException $ex) {
            $retorno['ok'] = false;
            $retorno['msg'] = $ex;
            $this->conexao->rollBack();

        }
        
        return $retorno;
    }

   

    /**
     * busca o id do usuário que ta efetuando transferencia
     */
    public function usuario($email){

        $idUsuario = array('id'=> 0);

        $consulta = "select * from usuario where email like '".$email."'";
        $sql = $this->conexao->query($consulta);
        $retorno = $sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($retorno as $dados) {
            $idUsuario['id'] = $dados['id'];
        }
        return $idUsuario['id'];
    }

    /**
     * busca os dados do usuário para validar se possui saldo
     */
    public function validarSaldo($id) {
       
        $consulta = "select * from valor_usuario where id_usuario = ".$id;
        $sql = $this->conexao->query($consulta);
        $retorno['sql'] = $sql->fetchAll(PDO::FETCH_ASSOC);
        $retorno['row'] = $sql->rowCount();
        return $retorno;
    }

    /**
     * Transfere o dinheiro para o usuário
     */
    public function transferencia($usuario, $usuario_recebidor, $valor) {
        $dataAtual = date('Y-m-d');
        $retorno = array('msg'=>'', 'ok'=>false);
        //inicia a transação
        $this->conexao->beginTransaction();
       try {
        $sql = $this->conexao->prepare('insert into transferencia_bancaria(data, id_usuario_envio, id_usuario_recebidor, valor)
                    values(:data, :usuario_envio, :usuario_recebido, :valor)');
        $sql->execute(array(':data'=>$dataAtual,
                    ':usuario_envio'=>$usuario,
                    ':usuario_recebido'=>$usuario_recebidor,
                    ':valor'=>$valor));
            
                if ($sql->rowCount() != 0) {
                    $retorno['ok'] = true;
                    $this->conexao->commit();
                } else {
                    $retorno['msg'] = 'Falha ao efetuar transação';
                    $this->conexao->rollBack();
                }
             //enviar email caso sucesso
       } catch (PDOException $ex) {
           $retorno['msg'] = 'Erro '.$ex;
           $this->conexao->rollBack();
             //enviar email da falha
       }
        return $retorno;
    }

    /**
     * atualizar Usurio de transferencia
     */
    public function atualizarUsuarioTransferencia($id, $valor_atualizado){
        
        $retorno = array('msg'=>'', 'ok'=>false);
        $this->conexao->beginTransaction();
        try {
            $sql = $this->conexao->prepare('update valor_usuario set valor = :valor where id_usuario = :id');
            $sql->execute(array(':valor'=>$valor_atualizado,
                                ':id'=>$id));
                if ($sql->rowCount() != 0) {
                    $retorno['ok'] = true;
                    $retorno['msg'] = 'Sucesso!';
                    $this->conexao->commit();
                } else {
                    $retorno['msg'] = 'Falha ao atualizar dados';
                    $this->conexao->rollBack();
                }
        } catch (PDOException $ex) {
            $retorno['msg'] = 'Error: ' .  $ex;
            $this->conexao->rollBack();
        }
        return $retorno;
    }

}


?>