
void list_content(char *arg, char *user_input, int sockfd)
{
	int no_of_bytes;
	int port;	
	int newsockfd;

	char message_from_server[MAXSZ];
	char message_to_server[MAXSZ];
	
	bzero(message_from_server,MAXSZ);
	bzero(message_to_server,MAXSZ);

	send(sockfd,"TYPE I\r\n",8,0);
	no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
	message_from_server[no_of_bytes] = '\0';
	printf("%s\n",message_from_server);
			
	send(sockfd,passive,strlen(passive),0);
	no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
	message_from_server[no_of_bytes] = '\0';
	printf("%s\n",message_from_server);

	if(memcmp(message_from_server,"227",3)== 0)
	{
				
		port = passive_port_number(message_from_server); 
		newsockfd =  func_to_connect_passive(arg,port);
					
		if(strcmp(user_input,"ls -l") == 0)
			sprintf(message_to_server,"LIST -l\r\n");
		else
			sprintf(message_to_server,"NLST\r\n");
					
		send(sockfd,message_to_server,strlen(message_to_server),0);
		no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);
					
		while((no_of_bytes = recv(newsockfd,message_from_server,MAXSZ,0))>0)
		{
			message_from_server[no_of_bytes] = '\0';
			write(1,message_from_server,no_of_bytes);
			fflush(stdout);
		}
				
		close(newsockfd);	
		no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
		message_from_server[no_of_bytes] = '\0';
		printf("\n%s\n",message_from_server);
		fflush(stdout);	
	}

}	

