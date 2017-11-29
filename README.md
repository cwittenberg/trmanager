# Tunnel-Relay Manager

*Please follow all steps precisely to install and configure Tunnel-Relay Manager*

# Installation
```
Install Apache
Install PHP
Install libapache2-mod-php
Install AutoSSH
Install Corkscrew
```

Apache:

```bash
Enable mod proxy (a2enmod proxy)
Enable mod proxy_http (a2enmod proxy_http)
Enable mod proxy_wstunnel (a2enmod proxy_wstunnel)
Enable mod rewrite (a2enmod rewrite)
```

# Configuration

## [ Setting up TRManager ] 
 1. Make sure editor/bin/start_agent.sh is executable
	```bash
	sudo chown www-data editor/bin/start_agent.sh 
	sudo chmod +x editor/bin/start_agent.sh 
	```
	
 2.	Import trmanager.sql into database 'trmanager' using command below
    ```bash
	mysql -u <username> -p trmanager < trmanager.sql
	```
 
 3. Adjust editor/conf/config.php to reflect correct DB parameters

## [ Virtual host administration ]
 1. Make sure www-data user Owns the sites-available directory so Virtual Hosts can be operated by trmanager
	
	```bash
	sudo chown www-data /etc/apache2/sites-available
	```

 2. Ensure www-data user (Apache user) can Reload Apache 
    
	Execute:
	```bash
	sudo visudo
	```

    Add the following*:
	```bash
	Cmnd_Alias      APACHE_RELOAD = /usr/sbin/service apache2 reload	
	Cmnd_Alias      APACHE_A2ENSITE = /usr/sbin/a2ensite	
	Cmnd_Alias      APACHE_A2DISSITE = /usr/sbin/a2dissite

	www-data ALL=NOPASSWD: APACHE_RELOAD	
	www-data ALL=NOPASSWD: APACHE_A2ENSITE
	www-data ALL=NOPASSWD: APACHE_A2DISSITE
	```
	

    * Please make sure Above Paths are EXACTLY correct using 'which' command to find out binary locations of a2ensite, a2dissite and service.
	
    * Same paths must also be specified in 'Settings' section in the Tunnel-Relay manager web-UI

## [ Test ]
  Try to login to TRManager using:
	
  * Username: admin@admin.com				
  * Password: Admin123,
	
