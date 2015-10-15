/* A simple server in the internet domain using TCP
   The port number is passed as an argument */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/types.h> 
#include <sys/socket.h>
#include <netinet/in.h>
#include <assert.h>
#include "beauty_print.h"

void error(const char *msg)
{
	perror(msg);
	exit(1);
}
int write_str(int newsockfd, const char *str)
{
	int len = strlen(str);
	int n = write(newsockfd, str, len);
	printf("'%s' is %d len, and write %d\n", str, len, n);
	assert(n == len);
	if (n < 0) error("ERROR writing to socket");
	return n;
}

int main(int argc, char *argv[])
{
	int sockfd, newsockfd, portno;
	socklen_t clilen;
	char buffer[256];
	struct sockaddr_in serv_addr, cli_addr;
	int n;
	if (argc < 2) {
		fprintf(stderr,"ERROR, no port provided\n");
		exit(1);
	}
	sockfd = socket(AF_INET, SOCK_STREAM, 0);
	if (sockfd < 0) 
		error("ERROR opening socket");
	bzero((char *) &serv_addr, sizeof(serv_addr));
	portno = atoi(argv[1]);
	serv_addr.sin_family = AF_INET;
	serv_addr.sin_addr.s_addr = INADDR_ANY;
	serv_addr.sin_port = htons(portno);
	if (bind(sockfd, (struct sockaddr *) &serv_addr,
		sizeof(serv_addr)) < 0) 
			error("ERROR on binding");
	listen(sockfd,5);
	clilen = sizeof(cli_addr);
	newsockfd = accept(sockfd, 
				(struct sockaddr *) &cli_addr, 
				&clilen);
	if (newsockfd < 0) 
		error("ERROR on accept");
	bzero(buffer,256);
	n = read(newsockfd,buffer,255);
	if (n < 0) error("ERROR reading from socket");
	printf("Here is the message:\n");
	beauty_print(buffer);
	n = read(newsockfd,buffer,255);
	if (n < 0) error("ERROR reading from socket");
	beauty_print(buffer);
	n = read(newsockfd,buffer,255);
	if (n < 0) error("ERROR reading from socket");
	beauty_print(buffer);
	n = read(newsockfd,buffer,255);
	if (n < 0) error("ERROR reading from socket");
	beauty_print(buffer);
	n = read(newsockfd,buffer,255);
	if (n < 0) error("ERROR reading from socket");
	beauty_print(buffer);
	putchar('\n');

	n = write_str(newsockfd,"HTTP/1.1 200 OK\r\n");
	const char * content = "hello\r\n";
	n = write_str(newsockfd, "Connection: close\r\n");
	char header_line[256];
	sprintf(header_line, "Content-length: %d\r\n", strlen(content));
	n = write_str(newsockfd, header_line);
	n = write_str(newsockfd,"\r\n");
	n = write_str(newsockfd,"hello\r\n");
	
	close(newsockfd);
	close(sockfd);
	return 0; 
}
