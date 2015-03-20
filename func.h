
void func(int newsockfd)
{
	int maxfd;
	int nready;
	fd_set fds;

	maxfd = newsockfd + 1;
	FD_ZERO(&fds);
	FD_SET(newsockfd,&fds);
	

	while(1)
	{
		nready = select(maxfd,&fds,(fd_set *)0,(fd_set *)0,(struct timeval *)0);
		if(nready < 1)
			continue;
		if(FD_ISSET(newsockfd,&fds))
			break;
	}

}
