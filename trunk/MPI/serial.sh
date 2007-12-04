#!/bin/bash

### Cambiar de directorio
cd /pgruso/trunk/PruebasMPI

### Nombre del trabajo
#PBS -N prueba2

### Recursos
### Cantidad de nodos
### PBS -l nodes=4:ppn=2
#PBS -l nodes=1
### Cantidad de memoria usada
### PBS -l mem=213mb
### Tiempo maximo de duracion del trabajo
#PBS -l walltime=01:00:0

### Cola del trabajo
#PBS -q prueba

### Salida estandar
#PBS -o /pgruso/trunk/PruebasMPI/prueba2.out   

### Salida error
#PBS -e /pgruso/trunk/PruebasMPI/prueba2.err   

### Unifica la salida estandar con la salida de error
###PBS -j oe   

### Copia archivos al host de ejecucion antes de que inicie la ejecucion
###PBS -W stagein=file_list

### Copia archivos desde el host de ejecucion despues de finalizar la ejecucion
###PBS -W stageout=file_list   

### Envia un mail al usuario cuando inicia el trabajo
### PBS -m b   
### Envia un mail al usuario cuando termina el trabajo
### PBS -m e   
### Envia un mail al usuario cuando el trabajo es abortado
### PBS -m a   
### Se deben agrupar si se quiere mas de uno
### PBS -m bea
### Mail del usuario
### PBS -M user_email_address
	
### Indica que el trabajo no puede re-ejecutarse si falla
###PBS -r n ??

### Exporta todas las variables de entorno al trabajo
###PBS -V

### Declare the time after which the job is eligible for execution.
### If you wish the job to be immediately eligible for execution,
### comment out this directive.  If you wish to run at some time in 
### future, the date-time argument format is
###     [DD]hhmm
### If the day DD is not specified, it will default to today if the
### time hhmm is in the future, otherwise, it defaults to tomorrow.
### If the day DD is specified as in the future, it defaults to the
### current month, otherwise, it defaults to next month.
###PBS -a 2215  commented out

### Specify the priority for the job.  The priority argument must be
### an integer between -1024 and +1023 inclusive.  The default is 0.
###PBS -p 0

### Define the interval at which the job will be checkpointed,
### if checkpointing is desired, in terms of an integer number
### of minutes of CPU time.
###PBS -c c=2 ??

##########################################
#                                        #
#   Output some useful job information.  #
#                                        #
##########################################

NCPU=`wc -l < $PBS_NODEFILE`
echo ------------------------------------------------------
echo 'This job is allocated on '${NCPU}' cpu(s)'
echo 'Job is running on node(s): '
cat $PBS_NODEFILE
echo ------------------------------------------------------
echo PBS: qsub is running on $PBS_O_HOST
echo PBS: originating queue is $PBS_O_QUEUE
echo PBS: executing queue is $PBS_QUEUE
echo PBS: working directory is $PBS_O_WORKDIR
echo PBS: execution mode is $PBS_ENVIRONMENT
echo PBS: job identifier is $PBS_JOBID
echo PBS: job name is $PBS_JOBNAME
echo PBS: node file is $PBS_NODEFILE
echo PBS: current home directory is $PBS_O_HOME
echo PBS: PATH = $PBS_O_PATH
echo ------------------------------------------------------

##############################################################
#                                                            #
#   The prologue script automatically makes a directory      #
#   on the local disks for you.  The name of this directory  #
#   depends on the job id, but you need only refer to it     #
#   using ${WORKDIR}.  In the mpirun command below, the      #
#   machines are specified using the copy of the             #
#   $PBS_NODEFILE which is placed on the /scratch partition  #
#   of the local disk by the prologue script.  You could     #
#   instead use                                              #
#            -machinefile $PBS_NODEFILE                      #
#                                                            #
##############################################################

###SERVER=$PBS_O_HOST
###WORKDIR=/scratch/PBS_$PBS_JOBID
###SCP=/usr/bin/scp
###SSH=/usr/bin/ssh
###MACHINES=${WORKDIR}/NODEFILE
###LAUNCH="/opt/mpich-1.2.4/bin/mpirun -np $NCPU -machinefile $MACHINES "

(/usr/local/mpiexec/bin/mpiexec bin/serial 100000 | cat > /pgruso/trunk/PruebasMPI/salida.txt)

