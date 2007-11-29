#!/bin/bash
#PBS -l nodes=1
echo "Nodos: "
cat $PBS_NODEFILE

/usr/local/mpiexec/bin/mpiexec -n 2 /pgruso/trunk/PruebasMPI/bin/prueba2
