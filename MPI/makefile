all: prueba prueba2 serial

serial: src/serial.c
	mpicc src/serial.c -o bin/serial

prueba: src/prueba.c
	mpicc src/prueba.c -o bin/prueba
	
prueba2: src/prueba2.c
	mpicc src/prueba2.c -o bin/prueba2