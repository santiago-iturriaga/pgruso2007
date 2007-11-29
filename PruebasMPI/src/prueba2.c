#include "mpi.h"
#include <stdio.h>

int main(int argc, char *argv[]) {
	int numtasks, rank, dest, source, rc, count, tag=1, iter;  
	int inmsg;
	
	MPI_Status Stat;

	MPI_Init(&argc,&argv);
	MPI_Comm_size(MPI_COMM_WORLD, &numtasks);
	MPI_Comm_rank(MPI_COMM_WORLD, &rank);

	if (rank == 0) {
  		dest = 1;
  		source = 1;
  		
  		for (iter = 0; iter <= 3; iter++) {
  			rc = MPI_Send(&iter, 1, MPI_INT, dest, tag, MPI_COMM_WORLD);
  			printf("<< Out %d \n\n", iter);
  			sleep(2);
  		}
  	} else if (rank == 1) {
  		dest = 0;
  		source = 0;
  		
  		for (iter = 0; iter <= 3; iter++) {
  			rc = MPI_Recv(&inmsg, 1, MPI_INT, source, tag, MPI_COMM_WORLD, &Stat);
  			printf(">> Task %d: Received int from task %d with tag %d \n", rank, Stat.MPI_SOURCE, Stat.MPI_TAG);
  			printf(">> In %d \n\n", inmsg);
  			sleep(1);
  		}
	} else {
		printf ("== Number of tasks= %d My rank= %d \n", numtasks,rank);
  	}

	MPI_Finalize();
}
