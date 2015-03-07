
void put_content(char *arg,char *user_input,int sockfd)
{	
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
	char file[MAXSZ];
	char file_name[MAXSZ];
	char data[4096];

	bzero(message_from_server,MAXSZ);
	bzero(message_to_server,MAXSZ);
	bzero(file_name,MAXSZ);
	bzero(file,MAXSZ);
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
			
		sprintf(file_name,"STOR %s\r\n",user_input + 4);	
		send(sockfd,file_name,strlen(file_name),0);
			
		no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
		message_from_server[no_of_bytes] = '\0';
		printf("%s\n",message_from_server);
				
		if(memcmp(message_from_server,"150",3)== 0 || memcmp(message_from_server,"125",3)== 0)
		{
			sprintf(file,"%s",user_input + 4);

			fd = open(file,O_RDONLY);
			fstat(fd,&buff);
			size = (int)buff.st_size;
			while(size > 0)
			{
				no_of_bytes = read(fd,data,4096);
				total = 0;
				while(total < no_of_bytes)
                		{
                		        p = send(newsockfd,data + total,no_of_bytes - total,0);
                       			 total += p;
				}
				size -= no_of_bytes;
			}
					
			close(newsockfd);
			no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
					
		}
				
	}
}

