<?php 

namespace App\Conexao\DataBase;
use Credenciais;
use PDO;
use PDOException;

 class DataBase extends PDO {

    protected $server;
    protected $user;
    protected $dbname;
    protected $port;
    protected $password;

    public function __construct()
    {
        $credenciais = new Credenciais();

        $this->server = $credenciais->server();
        $this->user = $credenciais->user();
        $this->dbname = $credenciais->dbname();
        $this->port = $credenciais->port();
        $this->password = $credenciais->password();
    }

    public function conexao() {
        try {
         
            $conn = new PDO('pgsql:host=localhost;port=' . $this->port . ';dbname=' . $this->dbname, $this->user, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $ex) {
            return 'Problema de conexão: ' . $ex;
        }
        return $conn;
    }
}

?>