create table cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(12) not null,
	UNIQUE (nombre));

create table trabajo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(12) not null,
    cliente int not null,
	foreign key (cliente) references cliente(id)
    );
	
create table usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(12) not null,
	password VARCHAR(12) not null,
	cliente int not null,
	foreign key (cliente) references cliente(id),
	UNIQUE (login));

	