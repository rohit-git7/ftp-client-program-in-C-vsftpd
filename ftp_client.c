#include"header.h"
#include"list_client.h"
#include"passive_connect.h"
#include"list_content.h"
#include"get_content.h"
#include"put_content.h"

char *find_home_dir(char *file)
{
	struct passwd *pw;
	char *sudo_uid = getenv("SUDO_UID");
	pw = getpwuid(atoi(sudo_uid));
	
	return pw->pw_dir;

}

int validate_ip(char *ip)
{
	int value_1 = -1;
	int value_2 = -1;
	int value_3 = -1;
	int value_4 = -1;
	int count = 0;
	int i = 0;

	while(ip[i] != '\0')
	{
		if(ip[i] == '.')
			count++;
		i++;
	}
	
	if(count != 3 )
		return -1;
	else
	{
		sscanf(ip,"%d.%d.%d.%d",&value_1,&value_2,&value_3,&value_4);
		
		if(value_1 < 0 || value_2 < 0 || value_3 < 0 || value_4 < 0 || value_1 > 255 || value_2 > 255 || value_3 > 255 || value_4 > 255)
			return -1;
		else
			return 1;

	}

}

int main(int argc, char *argv[])
{
	int sockfd;//to create socket
	int no_of_bytes;
	int connect_value;
	int ip_valid;

	struct sockaddr_in serverAddress;//client will connect on this

	char message_from_server[MAXSZ];//message from server
	char user_input[MAXSZ];//input from user
	char message_to_server[MAXSZ];//message to server
	char user[MAXSZ];//user details sent to server
	char pass[MAXSZ];//password details sent to server
	char dir[MAXSZ];//directory name
	char username[MAXSZ];//username entered by the user
	char working_dir[MAXSZ];

	char *home_dir;
	char *password = malloc(MAXSZ);//password enterd by user

	if(argc != 2)
	{
		printf("Error: argument should be ip-address of server\n");
		exit(1);
	}
	
	ip_valid = validate_ip(argv[1]);
	
	if(ip_valid == -1)
	{
		printf("Error: Invalid ip-address\n");
		exit(1);
	}
	
	home_dir = find_home_dir(argv[0]);

	sockfd = socket(AF_INET,SOCK_STREAM,0);
	if(sockfd == -1)
	{
        	perror("Error:"); 
		exit(1);
	}

	bzero(&serverAddress,sizeof(serverAddress));
	serverAddress.sin_family = AF_INET;
	serverAddress.sin_addr.s_addr = inet_addr(argv[1]);
	serverAddress.sin_port = htons(PORT);

	//client  connect to server on port
	connect_value = connect(sockfd,(struct sockaddr *)&serverAddress,sizeof(serverAddress));
	if(connect_value == -1)
	{
       		perror("Error");
        	exit(1);
	}
	
	printf("Connected to %s.\n",argv[1]);
	
	no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
	message_from_server[no_of_bytes] = '\0';
	printf("%s\n",message_from_server);

	printf("Name (%s): ",argv[1]);
	scanf("%s",username);
	
	sprintf(user,"USER %s\r\n",username);
		
	send(sockfd,user,strlen(user),0);
	no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
	message_from_server[no_of_bytes] = '\0';
	printf("%s\n",message_from_server);
	
	password = getpass("Password: ");
	sprintf(pass,"PASS %s\r\n",password);
	
	send(sockfd,pass,strlen(pass),0);
	no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
	message_from_server[no_of_bytes] = '\0';
	printf("%s\n",message_from_server);
	
	if(memcmp(message_from_server,"230",3) != 0)
	{
		exit(1);
	}
	
	while(1)
	{
		printf("ftp> ");
		fflush(stdout);
	
		bzero(user_input,MAXSZ);
		bzero(message_to_server,MAXSZ);
		bzero(message_from_server,MAXSZ);
		bzero(working_dir,MAXSZ);
	
		no_of_bytes = read(STDIN_FILENO,user_input,MAXSZ);
		user_input[no_of_bytes] = '\0';		
		
		if(user_input[no_of_bytes - 1] == '\n')
			user_input[no_of_bytes - 1] = '\0';
		if(user_input[no_of_bytes - 1] == '\r')
			user_input[no_of_bytes - 1] = '\0';

		if(strcmp(user_input,"exit") == 0 || strcmp(user_input,"quit") == 0 || strcmp(user_input,"bye") == 0)
		{
			sprintf(message_to_server,"QUIT\r\n");

			send(sockfd,message_to_server,strlen(message_to_server),0);
			no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
			break;
		}

		if(memcmp(user_input,"!cd ",4) == 0 || strcmp(user_input,"!cd") == 0)
		{
			if(chdir(user_input + 4) == 0)
			{
				printf("Directory successfully changed\n\n");
			}			
			else
			{
				perror("Error");
			}
		}

		if(memcmp(user_input,"!pwd ",5) == 0 || strcmp(user_input,"!pwd") == 0)
		{
			getcwd(working_dir,MAXSZ);	
			printf("%s\n\n",working_dir);
		}	

		if(memcmp(user_input,"!ls -l ",7) == 0 || strcmp(user_input,"!ls -l") == 0)
		{
			getcwd(working_dir,MAXSZ);	
			ls_l_dir(working_dir);
			continue;
		}
			
		if(memcmp(user_input,"!ls ",4) == 0 || strcmp(user_input,"!ls") == 0)
		{
			getcwd(working_dir,MAXSZ);	
			ls_dir(working_dir);
		}
		
		if(memcmp(user_input,"cd ",3) == 0)
		{
			sprintf(dir,"CWD %s\r\n",user_input + 3);
			send(sockfd,dir,strlen(dir),0);
		
			no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
		}
	
		if(memcmp(user_input,"ls ",3)== 0 || strcmp(user_input,"ls")== 0)
		{
			list_content(argv[1],user_input,sockfd);	
		}
	
		if(strcmp(user_input,"pwd")== 0)
		{
			sprintf(message_to_server,"PWD\r\n");
			send(sockfd,message_to_server,strlen(message_to_server),0);
		
			no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
		}

		if(memcmp(user_input,"get ",4) == 0)
		{
			get_content(argv[1],user_input,sockfd,home_dir);
		}
		
		if(memcmp(user_input,"put ",4)== 0)
		{
			put_content(argv[1],user_input,sockfd);
		}
	}
        close(sockfd);
	return 0;
}
