insert into cliente (nombre, usr_linux) values ('Cliente1','pgccadar');
insert into cliente (nombre, usr_linux) values ('Cliente2','pgccadar');

insert into grupo (nombre,recibe_alertas,es_admin) values ('administrador','S','S');

insert into usuario (login,password,administrador) values ('admin','admin','S');

insert into usuario_grupo (usuario,grupo) values (1,1);

//INSERTAR EN ESTE ORDEN LUEGO DE CREAR LA TABLA ALERTAS
insert into alertas (asunto,body) values ('Limite Cuota','El trabajo {TRABAJO} del cliente {CLIENTE} se ha pasado en su cuota');
insert into alertas (asunto,body) values ('Limite Cuota','El trabajo {TRABAJO} se ha pasado en su cuota');
insert into alertas (asunto,body) values ('Comienzo de ejecuci&oacute;n de trabajo','El trabajo {TRABAJO} ha comenzado su ejecuci&oacute;n');
insert into alertas (asunto,body) values ('Finalizaci&oacute;n de trabajo','El trabajo {TRABAJO} ha finalizado');
insert into alertas (asunto,body) values ('Error de ejecuci&oacute;n de trabajo','El trabajo {TRABAJO} ha fallado');



Estas estan de mas
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

