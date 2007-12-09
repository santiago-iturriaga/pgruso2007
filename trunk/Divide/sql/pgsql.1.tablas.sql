create table cliente (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(12) not null,
    UNIQUE (nombre));

create table trabajo (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(12) not null,
    cliente int not null,
	foreign key (cliente) references cliente(id)
    );

CREATE TABLE grupo(
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(25) not null,
	UNIQUE (nombre));

create table usuario (
     id SERIAL PRIMARY KEY,
 	 login VARCHAR(12) not null,
	 password VARCHAR(12) not null,
	 administrador char(1),
	 email varchar(50),
	 UNIQUE (login));

CREATE TABLE usuario_grupo(
    usuario int not null,
    grupo int not null,
	foreign key (grupo) references grupo(id),
	foreign key (usuario) references usuario(id));

CREATE TABLE trabajo_grupo(
    trabajo int not null,
    grupo int not null,
	foreign key (grupo) references grupo(id),
	foreign key (trabajo) references trabajo(id));

CREATE TABLE alertas(
	id SERIAL PRIMARY KEY,
	usuario int not null,
	trabajo int not null,
	asunto VARCHAR(256) not null,
	body VARCHAR(1000) not null,
	fecha timestamp default CURRENT_TIMESTAMP,
		foreign key (trabajo) references trabajo(id),
		foreign key (usuario) references usuario(id));
