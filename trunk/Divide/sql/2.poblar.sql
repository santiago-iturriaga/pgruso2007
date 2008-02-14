insert into cliente (nombre) values ('prueba1');
insert into cliente (nombre) values ('prueba2');

insert into trabajo(nombre,cliente,nodos,cola,tiempo_maximo) values ('trab1',1,4,'prueba','15:00:00');
insert into trabajo(nombre,cliente,nodos,cola,tiempo_maximo) values ('trab2',1,4,'prueba','15:00:00');
insert into trabajo(nombre,cliente,nodos,cola,tiempo_maximo) values ('trab3',2,4,'prueba','15:00:00');
insert into trabajo(nombre,cliente,nodos,cola,tiempo_maximo) values ('trab4',2,4,'prueba','15:00:00');

insert into grupo (nombre,recibe_alertas) values ('grupo1','N');
insert into grupo (nombre,recibe_alertas) values ('administrador','S');

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


//INSERTAR EN ESTE ORDEN LUEGO DE CREAR LA TABLA ALERTAS
insert into alertas (asunto,body) values ('Limite Cuota','El trabajo {TRABAJO} del cliente {CLIENTE} se ha pasado en su cuota');
insert into alertas (asunto,body) values ('Limite Cuota','El trabajo {TRABAJO} se ha pasado en su cuota');
insert into alertas (asunto,body) values ('Finalización de taea','La tarea a finalizado');

insert into trabajo_alerta(alerta,trabajo,body) values(2,1,'Pasado de cuota');
insert into trabajo_alerta(alerta,trabajo,body) values(1,1,'La tarea a finalizado');

insert into usuario_alerta(usuario,alerta,trabajo) values(1,2,1);
insert into usuario_alerta(usuario,alerta,trabajo) values(1,1,1);