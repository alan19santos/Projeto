--tabela de usuário
create table usuario(
	id serial not null,
	nome varchar(225),
	email varchar(50) not null,
	tipo int,
	senha varchar(225) not null,
	cpf_cnpj varchar(20),
	token varchar(225),
	constraint pk_usuario primary key (id),
	
);

--tabela transferencia bancaria
create table transferencia_bancaria (
	id serial not null,
	data date not null,
	id_usuario_envio int not null,
	id_usuario_recebidor int not null,
	valor float not null,
	constraint pk_id_transf primary key (id),
	constraint fk_usuario_envio foreign key (id_usuario_envio) references usuario (id),
	constraint fk_usuario_recebidor foreign key (id_usuario_recebidor) references usuario (id)
);


--valor do usuário
create table valor_usuario(
	id serial not null,
	valor double precision not null default 0.0,
	id_usuario int not null,
	constraint pk_id_valor primary key (id),
	constraint fk_id_usuario_valor foreign key (id_usuario) references usuario(id)
);