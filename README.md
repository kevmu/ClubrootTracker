# ClubrootTracker
ClubrootTracker: a leaflet.js map tool for tracking Plasmodiophora brassicae (Clubroot)

Clubroot Tracker Tutorial

Download Github Repository
Start by downloading the github repository using the following link or via command line.

Download Zip file:
https://github.com/kevmu/ClubrootTracker/archive/master.zip

Command line using git command:
git clone https://github.com/kevmu/ClubrootTracker.git

Installing webserver locally using AMPPS or using LAMP (Linux, Apache2, MySQL, PHP) stack:
Installation of AMPPS (Windows, MacOSX and Linux Website Stacks) for local webpage service.
Tutorials on how to install AMPPS can be found here. Installation and configuration of a website is beyond the scope of this tutorial. 
Windows:
http://www.ampps.com/wiki/Install
MacOSX:
http://www.ampps.com/wiki/Installation_on_Mac
Linux:
http://www.ampps.com/wiki/Installing_AMPPS_on_Linux

How to install a linux LAMP stack from scratch tutorial (Must have root access): 
Install Apache2:
sudo apt-get install apache2

Install Postgres SQL:
sudo service postgresql start
sudo -u postgres psql -c "ALTER USER postgres PASSWORD 'password';"
# Build Database Commands
sudo su postgres
psql
CREATE DATABASE clubroot_tracker_login;
\c clubroot_tracker_login
CREATE TABLE users (
database_id            SERIAL NOT NULL PRIMARY KEY,
user_id            varchar(255) NOT NULL,
email            varchar(255) NOT NULL,
firstname            varchar(255) NOT NULL,
lastname            varchar(255) NOT NULL,
academictitle            varchar(255) NOT NULL,
institution            varchar(255) NOT NULL,
country            varchar(255) NOT NULL,
password            varchar(255) NOT NULL,
termsandconditions            varchar(255) NOT NULL,
trn_date         timestamp NOT NULL);

Install PHP modules with Postgres SQL capability:
sudo apt-get install php libapache2-mod-php php-cli php-mysql php-gd php-imagick php-recode php-tidy php-xmlrpc php-pgsql
service apache2 restart
Install Python3.8 for generate_geojson.py
sudo pip3 install psycopg2-binary
sudo pip3 install pandas
sudo pip3 install numpy


Place public_html folder in the apache2 www folder (e.g /var/www/html)If you have access to a server that is already setup just place the contents of public_html directory in your apache2 or web server directory (e.g /var/www/html). You should see the index page using the http://localhost/index.php URL.

Structure of input GPS coordinates file:
(https://raw.githubusercontent.com/kevmu/ClubrootTracker/master/scripts/clubroot_tracker_data.csv)
Make a comma separate values (CSV) file with the following;
Column 1: Province – The province of the location (e.g Alberta
Column 2: Location – The city/town of the location (e.g Edmonton)
Column 3: Latitude – The latitude coordinates of the location to be displayed on the map. Positive values correspond to degrees east and negative values correspond to degrees west. (e.g -113.4938)
Column 4: Longitude – The longitude coordinates of the location to be displayed on the map. Positive values correspond to degrees north and negative values correspond to degrees south. (e.g 53.5461)
Column 5: Disease Name – The common name of the pathogen to be displayed (e.g Clubroot)
Column 6: Display – A message to display (e.g. Clubroot has been detected in this area. Please use extreme care to avoid spreading the pathogen and disease)

Run the generate_geojson.py python script. (https://raw.githubusercontent.com/kevmu/ClubrootTracker/master/scripts/generate_geojson.py)
python generate_geojson.py -i clubroot_tracker_data.csv -o /var/www/public_html/clubroot
clubroot_tracker_data.csv - An example input file for the generate_geojson.py python script that generates a tracker_geojson.js file. (https://raw.githubusercontent.com/kevmu/ClubrootTracker/master/scripts/clubroot_tracker_data.csv)
/var/www/public_html/clubroot – The output directory that contains the clubroot tracker script and data file.
tracker_geojson.js - This file is an example data file for the clubroot tracker application. The clubroot tracker parses this file for the locations and text to display. (https://raw.githubusercontent.com/kevmu/ClubrootTracker/master/public_html/clubroot/tracker_geojson.js) 

Displaying the clubroot tracker webpage.
http://localhost/clubroot/index.php
http://localhost/index.php




