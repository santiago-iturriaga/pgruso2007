#!/bin/bash
#$1 usuario | $2 Grupo Fenton | $3 id_dsa.pub archivo de clave publica
#$4 Directorios de susarios
if [ $# -lt 4 ]; then
   echo "Necesitas pasar cuatro parámetros: usuario, grupo Fenton, archivo de clave publica de apache y directorio de usuarios\n"
   exit 0
 fi
 useradd $1 -d $4/$1 -G $2 -m
 mkdir $4/$1/.ssh
 cat $3 >> $4/$1/.ssh/authorized_keys2

