                                      FTP Client program in C(vsFTPd)
------------------------------------------------------------------------------------------------------------------
FTP client prgram in C to connect to vsFTPd server with all functionalities of standard FTP.(Tested on vsFTPd version 2 and version 3).                                                                                                        

All functions are included. For example:                                                                               
`ls` : To list files in current directory on server side.                                                                
`ls -l` : Same as `ls`, with some more details.                                                                          
`pwd` : Current working directory on server side.                                                                        
`get filename` : Download file from server.(File should be present on server side).                                      
`put filename` : Upload file on server.                                                                                 
`cd directory_name` : Traverse file system on server side.                                                               

`!ls` : To list files in current directory on client side.                                                               
`!ls -l` : Same as `!ls`, with some more details.                                                                        
`!pwd` : Current working directory on client side.                                                                       
`!cd directory_name` : Traverse file system on client side.                                                              
                                                                                                                     
To run:                                                                                                           
Run command `make all` to compile program.                                                                              
To execute, run `./myftp server-ip-address`.                                                                            
Enter user credentials to login on server.                                                                                                                                           
