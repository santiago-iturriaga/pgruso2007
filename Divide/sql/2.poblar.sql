insert into cliente (nombre) values ('prueba1');
insert into cliente (nombre) values ('prueba2');

insert into trabajo(nombre,cliente) values ('trab1',1);
insert into trabajo(nombre,cliente) values ('trab2',1);
insert into trabajo(nombre,cliente) values ('trab3',2);
insert into trabajo(nombre,cliente) values ('trab4',2);

insert into usuario (login,password,cliente) values ('cli1','cli1',1);
insert into usuario (login,password,cliente) values ('cli2','cli2',2);