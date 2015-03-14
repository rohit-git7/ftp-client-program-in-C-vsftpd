/*
Download files from server.
*/
void get_content(char *arg,char *user_input,int sockfd,char *home_dir)
{
	/* Temporary variables*/
	int no_of_bytes;
	int port;	
	int newsockfd;
	int total;
	int p;
	int fd;	

	char message_from_server[MAXSZ];
	char message_to_server[MAXSZ];
	char file[MAXSZ];// File name
	char file_name[MAXSZ];// File name with instruction to server
	char file_home_dir[MAXSZ];// Location of file i.e. User's home directory(Complete path to file).
	char data[MAXSZ];// Data transfer

	/* Initialise all the character arrays */
	bzero(message_from_server,MAXSZ);
	bzero(message_to_server,MAXSZ);
	bzero(file_name,MAXSZ);
	bzero(file,MAXSZ);
	bzero(file_home_dir,MAXSZ);
	bzero(data,MAXSZ);
	
	/* Tell server to change to BINARY mode */
	send(sockfd,"TYPE I\r\n",8,0);

	while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
	{
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);
	}
	
	/* Send request for PASSIVE connection */	
	send(sockfd,passive,strlen(passive),0);
	while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
	{
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);
	}
	
	/* Server accepts request and sends PORT variables */
	if(strncmp(message_from_server,"227",3)== 0)
	{
		/* Generate a PORT number using PORT variables */
		port = passive_port_number(message_from_server); 
	
		/* Connect to server using another PORT for file transfers */
		newsockfd = func_to_connect_passive(arg,port);
		sprintf(file,"%s",user_input + 4);

		/* Send file name */
		sprintf(file_name,"RETR %s\r\n",user_input + 4);	
		send(sockfd,file_name,strlen(file_name),0);
				
		while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
		{
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
			fflush(stdout);
		}
				
		/* Permission Denied */
		if(strncmp(message_from_server,"550",3) == 0)
		{
			close(newsockfd);
				return;
		}
					
		sprintf(file_home_dir,"%s/%s",home_dir,file);
		
		/* Create file on client system */	
		fd = open(file_home_dir,O_CREAT|O_WRONLY|O_TRUNC,0644);			
				
		while((no_of_bytes = recv(newsockfd,data,MAXSZ,0))>0)
		{
			total = 0;
			/* For partial write operations */
			while(total < no_of_bytes)
                	{
                	        p = write(fd,data + total,no_of_bytes - total);
                       		 total += p;
			}
		}
		
		/* Close PASSIVE socket and file */
		close(newsockfd);	
		close(fd);
		while((no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0)) > 0)
		{
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
			fflush(stdout);
		}
		
	}
}
		

