#!/bin/bash
#PBS -l nodes=1
echo "Nodos: "
cat $PBS_NODEFILE

cd /pgruso/trunk/PruebasMPI
echo "Directorio: "
pwd

echo "inicio " > log.txt
mpirun -np 4 bin/prueba2
echo "fin " >> log.txt

