<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb588f227ac02c98ef815a3a36eaa7ef9
{
    public static $classMap = array (
        'App\\App' => __DIR__ . '/../..' . '/App/App.php',
        'App\\Conexao\\DataBase\\DataBase' => __DIR__ . '/../..' . '/App/Conexao/DataBase.php',
        'App\\Controller\\Controller' => __DIR__ . '/../..' . '/App/Controller/Controller.php',
        'App\\Controller\\Controller\\TransferenciaController' => __DIR__ . '/../..' . '/App/Controller/TransferenciaController.php',
        'App\\Model' => __DIR__ . '/../..' . '/App/Model.php',
        'App\\Transferencia' => __DIR__ . '/../..' . '/App/Transferencia.php',
        'App\\Usuario' => __DIR__ . '/../..' . '/App/Usuario.php',
        'App\\UsuarioController' => __DIR__ . '/../..' . '/App/Controller/UsuarioController.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Credenciais' => __DIR__ . '/../..' . '/App/Conexao/Credenciais.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitb588f227ac02c98ef815a3a36eaa7ef9::$classMap;

        }, null, ClassLoader::class);
    }
}