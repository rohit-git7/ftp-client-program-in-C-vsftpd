#include<stdio.h>
#include<sys/types.h>//socket
#include<sys/socket.h>//socket
#include<stdlib.h>//sizeof
#include<unistd.h>
#include<arpa/inet.h>
#include<string.h>
#include<ctype.h>//isdigit()
#include<fcntl.h>//open()
#include<dirent.h>
#include<sys/stat.h>//stat()
#include<grp.h>
#include<pwd.h>
#include<time.h>

#define PORT 21
#define MAXSZ 1024

void ls_dir(char *dir_name)
{
	DIR *fd;
	
	struct dirent *entry;
	fd = opendir(dir_name);
	if(fd == NULL)
	{
		perror("Error");
		return;
	}

	while((entry = readdir(fd))!=NULL)
	{
	    if(entry->d_name[0] != '.' && strcmp(entry->d_name,".") != 0 && strcmp(entry->d_name,"..") != 0)
		printf("%s\n",entry->d_name);
	}
	
	printf("\n");
	closedir(fd);
}

void ls_l_dir(char *dir_name)
{
	char time_buff[MAXSZ];

	int val = 0;
	int temp;

	struct group *gp;
	struct passwd *pw;
	struct tm *info;
	struct stat buff;
	struct dirent *entry;
	DIR *fd;
	fd  = opendir(dir_name);
	if(fd == NULL)
	{
		perror("Error");
		return;
	}
	
	while((entry = readdir(fd))!= NULL)
	{
		
		lstat(entry->d_name,&buff);
	   	gp = getgrgid(buff.st_gid);		
		pw = getpwuid(buff.st_uid);
		info = localtime(&(buff.st_mtime));
		strftime(time_buff,sizeof(time_buff),"%b %d %H:%M",info);
	   if(strcmp(entry->d_name,".") != 0 && strcmp(entry->d_name,"..") != 0 && entry->d_name[0]!= '.')
 		{
			if(buff.st_size % 1024 == 0)
			{
				temp = (int)buff.st_size / 1024;
				val += temp ;
			}
			else
			{
				temp = ((int)buff.st_size / 1024);
				val += (temp + (4 - (temp % 4)));
			}


			switch(buff.st_mode & S_IFMT)
			{
				case S_IFCHR:
					printf("c");
					break;
				case S_IFBLK:
					printf("b");
					break;
				case S_IFDIR:
                        		printf("d");
					break;
				case S_IFLNK:
					printf("l");
					break;
				case S_IFIFO:
					printf("p");
					break;
				case S_IFSOCK:
					printf("s");
					break;
				default:
					printf("-");
					break;
			}

		if(buff.st_mode & S_IRUSR)
			printf("r");
		else
			printf("-");

		if(buff.st_mode & S_IWUSR)
			printf("w");
		else
			printf("-");

		if(buff.st_mode & S_IXUSR)
			printf("x");
		else
			printf("-");

		if(buff.st_mode & S_IRGRP)
			printf("r");
		else
			printf("-");

		if(buff.st_mode & S_IWGRP)
			printf("w");
		else
			printf("-");

		if(buff.st_mode & S_IXGRP)
			printf("x");
		else
			printf("-");

		if(buff.st_mode & S_IROTH)
			printf("r");
		else
			printf("-");

		if(buff.st_mode & S_IWOTH)
			printf("w");
		else
			printf("-");
			
		if(buff.st_mode & S_IXOTH)
			printf("x");
		else
			printf("-");


		if(((buff.st_mode & S_IFMT)^S_IFCHR) == 0 || ((buff.st_mode & S_IFMT)^S_IFBLK) ==0)
			printf(" %6d %8s %8s %5d, %5d %13s %s\n",(int)buff.st_nlink,pw->pw_name,gp->gr_name,major(buff.st_rdev),minor(buff.st_rdev),time_buff,entry->d_name);
		else
			printf(" %6d %8s %8s %12u %13s %s\n",(int)buff.st_nlink,pw->pw_name,gp->gr_name,(unsigned int)buff.st_size,time_buff,entry->d_name);
		
		}
	}
	printf("total %d\n\n",val);
	closedir(fd);

}

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


int main(int argc, char *argv[])
{
	int sockfd;//to create socket
	int newsockfd;//socket for passive connection
	int port;

	struct sockaddr_in serverAddress;//client will connect on this
	struct stat buff;
	int no_of_bytes;
	int connect_value;
	int fd;
	int size;
	int p;
	int total;
	
	char message_from_server[MAXSZ];//message from server
	char user_input[MAXSZ];//input from user
	char message_to_server[MAXSZ];//message to server
	char user[MAXSZ];//user details sent to server
	char pass[MAXSZ];//password details sent to server
	char dir[MAXSZ];//directory name
	char data[4096];//send and get data
	char file_name[MAXSZ];//file name to be sent to server
	char file[MAXSZ];//file to be created on client
	char username[MAXSZ];//username entered by the user
	char working_dir[MAXSZ];

	char *password=malloc(MAXSZ);//password enterd by user
	char passive[]="PASV\r\n";	

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
		bzero(data,4096);
	
		no_of_bytes = read(0,user_input,MAXSZ);
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
				newsockfd =  func_to_connect_passive(argv[1],port);
					
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
	
		if(strcmp(user_input,"pwd")== 0)
		{
			sprintf(message_to_server,"PWD\r\n");
			send(sockfd,message_to_server,strlen(message_to_server),0);
		
			no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
			message_from_server[no_of_bytes] = '\0';
			printf("%s\n",message_from_server);
		}

		if(memcmp(user_input,"get ",4)== 0)
		{
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
	
				newsockfd = func_to_connect_passive(argv[1],port);
				sprintf(file,"%s",user_input + 4);

				sprintf(file_name,"RETR %s\r\n",user_input + 4);	
				send(sockfd,file_name,strlen(file_name),0);
				
				no_of_bytes = recv(sockfd,message_from_server,MAXSZ,0);
				message_from_server[no_of_bytes] = '\0';
				printf("%s\n",message_from_server);
				
				if(memcmp(message_from_server,"550",3) == 0)
				{
					close(newsockfd);
					continue;
				}
				
				fd = open(file,O_CREAT|O_WRONLY|O_TRUNC,0644);			
				
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
		
		if(memcmp(user_input,"put ",4)== 0)
		{
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
	
				newsockfd = func_to_connect_passive(argv[1],port);
			
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
	}
        close(sockfd);
	return 0;
}
