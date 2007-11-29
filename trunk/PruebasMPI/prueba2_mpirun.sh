#!/bin/bash
#PBS -l nodes=1
echo "Nodos: "
cat $PBS_NODEFILE

mpirun -np 4 /pgruso/trunk/PruebasMPI/bin/prueba2
