#include "mpi.h"
#include <stdio.h>
#include <stdlib.h>

double f(double a) {
	return (4.0 / (1.0 + a*a));
}

int serial(int argc, char *argv[]) {	
	long n,i,freq;
	double mypi, h, sum, x;
	
	n = atol(argv[1]);
	h = 1.0 / (double) n;
	freq = n / 100;
		
	printf("h: %14.12f\n", h);
	printf("n: %ld\n", n);
	printf("freq: %ld\n", freq);
	
	sum = 0.0;
	for (i = 1; i <= n; i++) {
		x = h * ((double)i - 0.5);
		sum += h * f(x);
		
		if ((i % freq) == 0) {
			printf("sum: %14.12f\n", sum);
		}
	}
	mypi = sum;
	
	printf("pi: %14.12f\n", mypi);
	return 0;
}

int main(int argc, char *argv[]) {
	int numtasks, rank, dest, source, rc, count, tag=1, iter;  
	
	MPI_Status Stat;

	MPI_Init(&argc,&argv);
	MPI_Comm_size(MPI_COMM_WORLD, &numtasks);
	MPI_Comm_rank(MPI_COMM_WORLD, &rank);
	
	return serial(argc, argv);
	
	MPI_Finalize();
}
