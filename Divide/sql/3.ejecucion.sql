create table ejecucion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_ini timestamp default CURRENT_TIMESTAMP,
    archivo varchar(25) not null,
    ruta varchar(250),
    parametros varchar(250),
    argumentos varchar(250),
    fecha_fin timestamp,
    id_torque INT not null
    );
