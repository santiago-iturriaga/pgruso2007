#!/bin/bash
#PBS -l nodes={NODOS}

cd {RUTA_EJECUTABLE}

/usr/local/mpiexec/bin/mpiexec {EJECUTABLE}
