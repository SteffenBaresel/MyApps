<?php
###############################
# May Apps Configuration File #
#                             #
# c2015 Steffen Baresel       #
###############################
#
# Database Configruation
#
# First create MySQL Database:
#
# - mysql -u root -p
# - create database MyApps;
# - create user myapps@'localhost' identified by 'myapps';
# - grant all privileges on MyApps.* to myapps@'%';
# - flush privileges;
# - exit;
#
$config['dbhost'] = '127.0.0.1';
$config['dbport'] = '3306';
$config['dbname'] = 'MyApps';
$config['dbuser'] = 'myapps';
$config['dbpass'] = 'myapps';
#
# End of File
#
?>
