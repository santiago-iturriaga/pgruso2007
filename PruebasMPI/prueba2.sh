#!/bin/bash
#PBS -l nodes=1
echo "Nodos: "
cat $PBS_NODEFILE

cd /pgruso/trunk/PruebasMPI
echo "Directorio: "
pwd

/usr/local/mpiexec/bin/mpiexec bin/prueba2
