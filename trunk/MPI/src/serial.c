#include "mpi.h"
#include <stdio.h>
#include <stdlib.h>

int rank;

double f(double a) {
	return (4.0 / (1.0 + a*a));
}

double serial(long n, long inicio, long fin) {	
	long i,freq;
	double h, sum, x;
	
	h = 1.0 / (double) n;
	freq = n / 100;
		
	printf("[RANK %i] h: %14.12f\n",rank, h);
	printf("[RANK %i] freq: %ld\n",rank, freq);
	fflush(stdout);
	
	sum = 0.0;
	for (i = 1; i <= n; i++) {
		x = h * ((double)i - 0.5);
		sum += h * f(x);
		
		if ((i % freq) == 0) {
			printf("[RANK %i] sum: %14.12f\n",rank, sum);
			fflush(stdout);
		}
	}

	return sum;
}

int main(int argc, char *argv[]) {
	int numtasks, rc, count, i;
	long rango, n, inicio, fin;
	double h, mypi, temp_mypi;
	MPI_Status stat;
	
	rc = MPI_Init(&argc,&argv);
	if (rc != MPI_SUCCESS) {
 		printf ("Error starting MPI program. Terminating.\n");
 		fflush(stdout);
 		MPI_Abort(MPI_COMM_WORLD, rc);
 		return 1;
    }
	
	MPI_Comm_size(MPI_COMM_WORLD, &numtasks);
	MPI_Comm_rank(MPI_COMM_WORLD, &rank);

	n = atol(argv[1]);
	h = 1.0 / (double) n;
	printf("n: %ld\n", n);
	fflush(stdout);

	rango = n / numtasks;
	printf("[RANK %i] rango: %ld\n",rank , n);
	fflush(stdout);
	
	inicio = rank * rango;
	
	if (rank == 0) {
		inicio = 1;
	}
	
	if (rank == (numtasks - 1)) {
		fin = n;
	} else {
		fin = inicio + rango - 1;
	}
	printf("[RANK %i] incio: %ld fin: %ld\n",rank, inicio,fin);
	fflush(stdout);
	
	mypi = serial(n, inicio, fin);
	printf("[RANK %i] mypi: %14.12f\n",rank, mypi);
	fflush(stdout);
	
	if (rank == 0) {
		for (i = 1; i < numtasks; i++) {
			printf("[RANK %i] MPI_Recv: %i\n",rank, i);	
			fflush(stdout);
			//rc = MPI_Recv(&temp_mypi, 1, MPI_DOUBLE, MPI_ANY_SOURCE, 1, MPI_COMM_WORLD, &stat);
			rc = MPI_Recv(&temp_mypi, 1, MPI_DOUBLE, i, 1, MPI_COMM_WORLD, &stat);
			
			if (rc != MPI_SUCCESS) {
		 		printf ("[RANK %i] Error starting MPI program. Terminating.\n",rank);
		 		fflush(stdout);
		 		MPI_Abort(MPI_COMM_WORLD, rc);
		 		return 1;
		    }
			
			rc = MPI_Get_count(&stat, MPI_CHAR, &count);
			
			if (rc != MPI_SUCCESS) {
		 		printf ("[RANK %i] Error starting MPI program. Terminating.\n",rank);
		 		fflush(stdout);
		 		MPI_Abort(MPI_COMM_WORLD, rc);
		 		return 1;
		    }
			
			printf("[RANK %i] Received %d char(s) from task %d with tag %d \n",rank, count, stat.MPI_SOURCE, stat.MPI_TAG);
			fflush(stdout);
			
			printf("[RANK %i] Nuevo mypi: %14.12f + %14.12f\n", rank, mypi,temp_mypi);
			fflush(stdout);
			mypi += temp_mypi;			
		}
		
		printf("[RANK %i] final mypi: %14.12f\n",rank, mypi);
		fflush(stdout);
	} else {
		rc = MPI_Send(&mypi, 1, MPI_DOUBLE, 0, 1, MPI_COMM_WORLD);
		
		if (rc != MPI_SUCCESS) {
 			printf ("[RANK %i] Error starting MPI program. Terminating.\n",rank);
 			fflush(stdout);
	 		MPI_Abort(MPI_COMM_WORLD, rc);
	 		return 1;
    	}
	}
	
	MPI_Finalize();
}
