insert into cliente (nombre) values ('prueba1');
insert into cliente (nombre) values ('prueba2');

insert into trabajo(nombre,cliente) values ('trab1',1);
insert into trabajo(nombre,cliente) values ('trab2',1);
insert into trabajo(nombre,cliente) values ('trab3',2);
insert into trabajo(nombre,cliente) values ('trab4',2);

insert into grupo (nombre) values ('grupo1');
insert into grupo (nombre) values ('administrador');

insert into usuario (login,password) values ('cli1','cli1');
insert into usuario (login,password) values ('cli2','cli2');
insert into usuario (login,password,administrador) values ('admin','admin','S');

insert into usuario_grupo (usuario,grupo) values (1,1);
insert into usuario_grupo (usuario,grupo) values (2,1);
insert into usuario_grupo (usuario,grupo) values (3,2);

insert into trabajo_grupo (trabajo,grupo) values (1,1);
insert into trabajo_grupo (trabajo,grupo) values (2,1);

insert into trabajo_grupo (trabajo,grupo) values (1,2);
insert into trabajo_grupo (trabajo,grupo) values (2,2);
