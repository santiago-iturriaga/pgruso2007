create table cliente (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(12) not null,
    usr_linux  VARCHAR(64) not null,
    UNIQUE (nombre));

create table trabajo (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(12) not null,
    cliente int not null,
	foreign key (cliente) references cliente(id),
	nodos int,
	tiempo_maximo time,
	quota int,
	cola varchar(25),
	quota int,
	fecha_fin timestamp default null
    );

CREATE TABLE grupo(
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(25) not null,
    recibe_alertas char,
    es_admin char,
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
asunto VARCHAR(256) not null,
body VARCHAR(1000) not null);

CREATE TABLE  trabajo_alerta(
alerta int not null,
trabajo int not null,
body VARCHAR(1000) not null,
foreign key (alerta) references alertas(id),
foreign key (trabajo) references trabajo(id),
UNIQUE (alerta,trabajo)
);

CREATE TABLE usuario_alerta(
alerta int not null,
usuario int not null,
trabajo int not null,
fecha timestamp default CURRENT_TIMESTAMP,
leida int not null default 0,
foreign key (alerta,trabajo) references trabajo_alerta(alerta,trabajo),
foreign key (usuario) references usuario(id));
