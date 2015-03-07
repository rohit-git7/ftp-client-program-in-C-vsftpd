
void get_content(char *arg,char *user_input,int sockfd,char *home_dir)
{
	int no_of_bytes;
	int port;	
	int newsockfd;
	int total;
	int p;
	int fd;	

	char message_from_server[MAXSZ];
	char message_to_server[MAXSZ];
	char file[MAXSZ];
	char file_name[MAXSZ];
	char file_home_dir[MAXSZ];	
	char data[4096];

	bzero(message_from_server,MAXSZ);
	bzero(message_to_server,MAXSZ);
	bzero(file_name,MAXSZ);
	bzero(file,MAXSZ);
	bzero(file_home_dir,MAXSZ);
	bzero(data,4096);
	
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
	
		newsockfd = func_to_connect_passive(arg,port);
		sprintf(file,"%s",user_input + 4);

		sprintf(file_name,"RETR %s\r\n",user_input + 4);	
		send(sockfd,file_name,strlen(file_name),0);
				
		no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
				
		if(memcmp(message_from_server,"550",3) == 0)
		{
			close(newsockfd);
				return;
		}
					
		sprintf(file_home_dir,"%s/%s",home_dir,file);
					
		fd = open(file_home_dir,O_CREAT|O_WRONLY|O_TRUNC,0644);			
				
		while((no_of_bytes = recv(newsockfd,data,4096,0))>0)
		{
			total = 0;
			while(total < no_of_bytes)
                	{
                	        p = write(fd,data + total,no_of_bytes - total);
                       		 total += p;
			}
		}
				
		close(newsockfd);	
		close(fd);
		no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
		fflush(stdout);					
		
	}
}
		

