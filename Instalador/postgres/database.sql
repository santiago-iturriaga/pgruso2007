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
idUA SERIAL PRIMARY KEY,
alerta int not null,
usuario int not null,
trabajo int not null,
fecha timestamp default CURRENT_TIMESTAMP,
leida int not null default 0,
foreign key (alerta,trabajo) references trabajo_alerta(alerta,trabajo),
foreign key (usuario) references usuario(id));

insert into cliente (nombre, usr_linux) values ('Cliente1','pgccadar');
insert into cliente (nombre, usr_linux) values ('Cliente2','pgccadar');

insert into grupo (nombre,recibe_alertas,es_admin) values ('administrador','S','S');

insert into usuario (login,password,administrador) values ('admin','admin','S');

insert into usuario_grupo (usuario,grupo) values (1,1);

insert into alertas (asunto,body) values ('Limite Cuota','El trabajo {TRABAJO} del cliente {CLIENTE} se ha pasado en su cuota');
insert into alertas (asunto,body) values ('Limite Cuota','El trabajo {TRABAJO} se ha pasado en su cuota');
insert into alertas (asunto,body) values ('Comienzo de ejecuci&oacute;n de trabajo','El trabajo {TRABAJO} ha comenzado su ejecuci&oacute;n');
insert into alertas (asunto,body) values ('Finalizaci&oacute;n de trabajo','El trabajo {TRABAJO} ha finalizado');
insert into alertas (asunto,body) values ('Cancelaci&oacute;n de trabajo','El trabajo {TRABAJO} ha sido cancelado');




insert into trabajo(nombre,cliente,nodos,cola,tiempo_maximo) values ('trab1',1,4,'prueba','15:00:00');
insert into trabajo(nombre,cliente,nodos,cola,tiempo_maximo) values ('trab2',1,4,'prueba','15:00:00');
insert into trabajo(nombre,cliente,nodos,cola,tiempo_maximo) values ('trab3',2,4,'prueba','15:00:00');
insert into trabajo(nombre,cliente,nodos,cola,tiempo_maximo) values ('trab4',2,4,'prueba','15:00:00');

insert into trabajo_grupo (trabajo,grupo) values (1,1);
insert into trabajo_grupo (trabajo,grupo) values (2,1);

insert into trabajo_grupo (trabajo,grupo) values (1,2);
insert into trabajo_grupo (trabajo,grupo) values (2,2);



insert into trabajo_alerta(alerta,trabajo,body) values(2,1,'Pasado de cuota');
insert into trabajo_alerta(alerta,trabajo,body) values(1,1,'La tarea a finalizado');

insert into usuario_alerta(usuario,alerta,trabajo) values(1,2,1);
insert into usuario_alerta(usuario,alerta,trabajo) values(1,1,1);

create table ejecucion (
	id SERIAL PRIMARY KEY,
	fecha_ini timestamp default CURRENT_TIMESTAMP,
	archivo varchar(25) not null,
	ruta varchar(250),
	parametros varchar(250),
	argumentos varchar(250),
	fecha_fin timestamp,
	fecha_ejecucion timestamp,
	id_torque int,
	trabajo int not null,
	log_torque text,
	tiempo_ejecucion timestamp,
	foreign key (trabajo) references trabajo(id)
);
