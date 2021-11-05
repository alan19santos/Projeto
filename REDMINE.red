#php -S localhost:8080 Projeto/index.php



O sistema cadastra o usuário.

exemplo de envio
email -> email@email.com (validação basica do e-mail)
nome -> nome do usuário
cpf/cnpj -> 000.000.00 (necessário ser valido para ambos)
senha -> 1234
tipoFormulario -> cadastro
cadastro -> 1 (apenas para validar no index)
tipo ->  1 ou 2 (usuário comun ou lojista)



após cadastro, necessário logar no sistema, passando os parametros do cadasto
email, senha e token (no retorno do cadastro é exibido)


tipoFormulario -> transacao
email, valor, usuario_recebidor (email do usuário que vai receber, é necessário que ele esteja cadastrado)
vai validar se existe dinheiro na conta ou não.


tipoFormulario->deposito
depositoConta (valor), email (de quem recebe o deposito)

