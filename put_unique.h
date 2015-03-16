/*
Upload file uniquely on server.
*/
void put_unique(char *arg,char *user_input,int sockfd)
{	
	/* Temporary variables*/
	int no_of_bytes;
	int port;	
	int newsockfd;
	int fd;	
	int p;
	int total;
	int size;
	
	struct stat buff;

	char message_from_server[MAXSZ];
	char message_to_server[MAXSZ];
	char file[MAXSZ];// File name
	char file_name[MAXSZ];// File name with instruction to server
	char data[MAXSZ];// Data transfer

	/* Initialise all the character arrays */
	bzero(message_from_server,MAXSZ);
	bzero(message_to_server,MAXSZ);
	bzero(file_name,MAXSZ);
	bzero(file,MAXSZ);
	bzero(data,MAXSZ);
	
	/* Tell server to change to BINARY mode */
	send(sockfd,"TYPE I\r\n",8,0);
	
	while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
	{
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);	
		if(message_from_server[no_of_bytes-2] == '\r' && message_from_server[no_of_bytes-1] == '\n')
			break;	
	}

	/* Send request for PASSIVE connection */	
	send(sockfd,passive,strlen(passive),0);
	
	while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
	{
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);	
		if(message_from_server[no_of_bytes-2] == '\r' && message_from_server[no_of_bytes-1] == '\n')
			break;
	
	}

	/* Server accepts request and sends PORT variables */
	if(strncmp(message_from_server,"227",3)== 0)
	{
		/* Generate a PORT number using PORT variables */
		port = passive_port_number(message_from_server); 
	
		/* Connect to server using another PORT for file transfers */
		newsockfd = func_to_connect_passive(arg,port);
		
		/* Send file name to server */
		sprintf(file_name,"STOU %s\r\n",user_input + 8);	
		send(sockfd,file_name,strlen(file_name),0);
			
		while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
		{
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
			fflush(stdout);	
			if(message_from_server[no_of_bytes-2] == '\r' && message_from_server[no_of_bytes-1] == '\n')
				break;
		}
		/* Send file data to server */
		if(strncmp(message_from_server,"150",3)== 0 || strncmp(message_from_server,"125",3)== 0)
		{
			sprintf(file,"%s",user_input + 4);

			fd = open(file,O_RDONLY);
			fstat(fd,&buff);
			size = (int)buff.st_size;
			while(size > 0)
			{
				no_of_bytes = read(fd,data,MAXSZ);
				total = 0;
				while(total < no_of_bytes)
                		{
                		        p = send(newsockfd,data + total,no_of_bytes - total,0);
                       			 total += p;
				}
				size -= no_of_bytes;
			}
					
			close(newsockfd);
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
}

