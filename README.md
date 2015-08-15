#                                      FTP Client program in C and PHP
------------------------------------------------------------------------------------------------------------------
FTP client program in C and PHP to connect to FTP server with all functionalities of standard FTP.(Tested on many FTP sites).                                                                                                                     
I have also implemented [multi-threaded FTP Client][1] in C language.

Following functions are included. For example:                                                                               
`ls` : To list files in current directory on server side.                                                             
`ls -l` : Same as `ls`, with some more details.                                                                       
`pwd` : Current working directory on server side.                                                                     
`get filename` : Download file from server.(File should be present on server side).                                   
`put filename` : Upload file on server.                                                                               
`uniqput filename` : Uniquely upload file on server.                                                                 
`mkdir directory_name` : Create directory on server.                                                                  
`rmdir directory_name` : Remove directory on server.                                                                  
`rm filename` : Remove file on server.                                                                                
`cd directory_name` : Traverse file system on server side.                                                            
`rename old_filename new_filename` : Renaming file on server.                                                        
                                                                                                                       
`!ls` : To list files in current directory on client side.                                                            
`!ls -l` : Same as `!ls`, with some more details.                                                                    
`!pwd` : Current working directory on client side.                                                                    
`!cd directory_name` : Traverse file system on client side.                                                           
`!mkdir directory_name` : Create directory on client side.                                                           
`!rmdir directory_name` : Remove directory on client side.                                                                     
[1]: https://github.com/rohit-takhar/Multithreaded-ftp-client-in-C
                                                                                                                     
#To run in C language:                                                                                                     
Run command `make all` to compile program.                                                                              
To execute, run `./myftp server-ip-address`.                                                                            
Enter user credentials to login on server.                                                                                                                                                                                                                                                                                                                                                                                                                                         
#To run in PHP language:                                                                                                     
To execute, run `php ftp_client.php server-ip-address`.                                                                     
Enter user credentials to login on server.                                                                                    
