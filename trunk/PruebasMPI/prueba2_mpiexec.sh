#!/bin/bash
#PBS -l nodes=1
echo "Nodos: "
cat $PBS_NODEFILE

cd /pgruso/trunk/PruebasMPI
echo "Directorio: "
pwd

echo "inicio " > log.txt
/usr/local/mpiexec/bin/mpiexec -n 2 -npernode 2  bin/prueba2
echo "fin " >> log.txt
