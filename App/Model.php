<?php

namespace App;
use App\Conexao\DataBase\DataBase;


class Model extends DataBase {

    protected $conexao = null;

    public function __construct()
    {
        $dataBase = new DataBase();
        $this->conexao = $dataBase->conexao();
        
    }

    public function getConexao(){
        
        return $this->conexao;
    }
}

?>