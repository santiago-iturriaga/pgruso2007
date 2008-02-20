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
	foreign key (trabajo) references trabajo(id)
);
