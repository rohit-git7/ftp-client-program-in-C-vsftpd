
int passive_port_number(char *message)
{
	int i = 0;
	int count = 0;
	int port = 0;
	char *token;
	char delim[]=" ,)";

	while(message[i] != '\0' && count < 4)
	{
		if(message[i] == ',')
		{
			count++;
		}
		i++;
	}
				
	count = 0;

	token = strtok(message + i,delim);
	while(token != NULL)
	{
		if(isdigit(token[0]))
		{
			if(count == 1)
			{
				port += atoi(token);
			}
						
			if(count == 0)
			{
				port = atoi(token)*256;
				count++;
			}
						
		}
		token = strtok(NULL,delim);
	}
	return port;
	
}

int func_to_connect_passive(char *address,int port)
{
	int newsockfd;
	struct sockaddr_in new_serverAddress;
	
	newsockfd = socket(AF_INET,SOCK_STREAM,0);
	bzero(&new_serverAddress,sizeof(new_serverAddress));

	new_serverAddress.sin_family = AF_INET;
	new_serverAddress.sin_addr.s_addr = inet_addr(address);
	new_serverAddress.sin_port = htons(port);

	connect(newsockfd,(struct sockaddr *)&new_serverAddress,sizeof(new_serverAddress));
	return newsockfd;
}
