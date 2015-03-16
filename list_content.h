/*
List the contents of current working directory on server (`ls` and `ls -l` linux commands).
*/
void list_content(char *arg, char *user_input, int sockfd)
{
	/* Temporary variables */
	int no_of_bytes;
	int port;	
	int newsockfd;

	char message_from_server[MAXSZ];
	char message_to_server[MAXSZ];
	
	/* Initialise character arrays */
	bzero(message_from_server,MAXSZ);
	bzero(message_to_server,MAXSZ);

	/* Request server to start BINARY mode */
	send(sockfd,"TYPE I\r\n",8,0);

	while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
	{
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);
		if(message_from_server[no_of_bytes-2] == '\r' && message_from_server[no_of_bytes-1] == '\n')
			break;
	}
		
	/* Request server to connect to PASSIVE port for file transfers */
	send(sockfd,passive,strlen(passive),0);
	while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
	{
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);
		if(message_from_server[no_of_bytes-2] == '\r' && message_from_server[no_of_bytes-1] == '\n')
			break;
	}

	/* Request acepted. Connect to PASSIVE port */
	if(strncmp(message_from_server,"227",3)== 0)
	{
		
		/* Generate PORT address */		
		port = passive_port_number(message_from_server); 

		/* Create socket for PASSIVE connection */
		newsockfd =  func_to_connect_passive(arg,port);
					
		if(strcmp(user_input,"ls -l") == 0)
			sprintf(message_to_server,"LIST -l\r\n");/* ls -l */
		else
			sprintf(message_to_server,"NLST\r\n");/* ls */
					
		send(sockfd,message_to_server,strlen(message_to_server),0);
		
		no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);
		
		/* Read data on new PASSIVE socket */		
				
		while((no_of_bytes = recv(newsockfd,message_from_server,MAXSZ,0)) > 0)
		{
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
			fflush(stdout);
			if(message_from_server[no_of_bytes-2] == '\r' && message_from_server[no_of_bytes-1] == '\n')
				break;
		
		}

		close(newsockfd);/* Close PASSIVE connection */
		
		while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
		{	
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
			fflush(stdout);	
			if(message_from_server[no_of_bytes-2] == '\r' && message_from_server[no_of_bytes-1] == '\n')
				break;
		}
	}

}	

