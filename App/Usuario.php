<?php 

namespace App;

use App\Model;
use PDOException;
use PDO;

/**
 * Classe responsável pelos dados do usuário que vem da base
 */

class Usuario extends Model {

    protected $conexao = null;

    public function __construct() 
    { 
        $conexao = new Model();
        $this->conexao  = $conexao->getConexao();
    }

    /**
     * busca os dados do usuário
     */
    public function dadosUsuario($email = '', $cpf_cnpj = '', $password = '', $token = '') {
        $emailUsuario = '';
        $senhaUsuario = '';
        $tokenUsuario = '';
      
        $consulta = "select * from usuario where 1=1 ";
        if (!empty($email)) {
            $consulta.= " and usuario.email like '".$email."'";
        }
        if (!empty($cpf_cnpj)) {
            $consulta.= " and usuario.cpf_cnpj like '".$cpf_cnpj."'";
        }
        
        $sql = $this->conexao->query($consulta);
        $row = $sql->rowCount();
       
        
        if ($row > 0) {
            $retorno = $sql->fetchAll(PDO::FETCH_ASSOC);

            
            if (!empty($password))
            {
                foreach($retorno as $dados) {
                    $emailUsuario = $dados['email'];
                    $senhaUsuario = $dados['senha'];
                    $tokenUsuario = $dados['token'];
                }
                
                
                $hash_senha = password_verify($password, $senhaUsuario);
                $tokens = strcmp(utf8_encode($token), utf8_encode($tokenUsuario));
                $emails = strcmp($email, $emailUsuario);
                
                if ($emails == 0 && $hash_senha && $tokens == 0) {
                    $this->armazenandoSessaoUsuario($email); //armazena sessão na base de dados
                    return true;
                }else{
                    return false;
                }
            }
            return $retorno;
        }
        return 0;
    }


    /**
     * Salva a sessão do usuário no banco
     */
    private function armazenandoSessaoUsuario($email) {
        $this->conexao->beginTransaction();
       
        try {
            $sql = $this->conexao->prepare("update usuario set sessaoUsuario = true where email like '".$email."'");
            $sql->execute();
            $this->conexao->commit();
        } catch (PDOException $ex) {
            echo 'Erro ' . $ex;
            $this->conexao->rollBack();
        }

    }

    /**
     * Busca o token do usuário
     */
    private function tokenUsuario($email) {

       $retorno = $this->dadosUsuario($email);
       $token = '';
     
       foreach($retorno as $dados) {
        $token = $dados['token'];
       }
        return $token;
    }


    /**
     * Cadastra os dados do usuário
     */
    public function inserirUsuario($nome, $email, $cpf_cnpj, $senha, $tipo){

        $retorno = array('msg'=>'Falha ao Inserir Dados', 'row'=> 0);

        $senhaUsuario = password_hash($senha, PASSWORD_DEFAULT);
        $cpfCnpj = password_hash(str_replace('.','',utf8_encode($cpf_cnpj)), PASSWORD_DEFAULT); //Gera o token a partir do cpf/cnpj
       
        try {

            $sql = $this->conexao->prepare('insert into usuario (nome, email, cpf_cnpj, senha, tipo, token) values (:nome, :email, :cpfcnpj, :senha, :tipo, :token)');
            $sql->execute(array(':nome' => $nome,
                                ':email' => $email,
                                ':cpfcnpj' => $cpf_cnpj,
                                ':senha' => $senhaUsuario,
                                ':tipo' => $tipo,
                                ':token' => $cpfCnpj));
                              
            $retorno['row'] = $sql->rowCount();
            $retorno['token'] = $this->tokenUsuario($email);
                                
        } catch (PDOException $ex) {
            $retorno['msg'] .= 'Erro ' . $ex;
        }
        
        return $retorno;
    }

    /**
     * valida se o usuário esta logado para executar transferencia
     */
    public function validarLogin($email) {
       

        $consulta = "select * from usuario where email like '".$email."'";
        $sql = $this->conexao->query($consulta);

        $retorno = $sql->fetchAll(PDO::FETCH_ASSOC);
       
        return $retorno;
    }
   
    
    
}

?>