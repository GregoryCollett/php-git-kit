#!/bin/bash

# CHROMA CODE QUALITY TOOLKIT INSTALLATION
# THIS IS THE BASIC INSTALLATION PROCESS
# php -i | grep php.ini

# IF PHP -V >= 5.5 THEN GO AND INSTALL STUFF
# ELSE PROMPT THE USER TO UPDATE TO PHP 5.5

# INSTALL COMPOSER
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
# INSTALL PHP CODE SNIFFER
composer global require "squizlabs/php_codesniffer"
# INSTALL PHP CODING STANDARDS FIXER
composer global require "fabpot/php-cs-fixer"
# INSTALL PHP MESS DETECTOR
composer global require "phpmd/phpmd"
# INSTALL PHP UNIT
composer global require "phpunit/phpunit=4.8.*"

# MAKE FILES EXECUTABLE
chmod +x bin/console
chmod +x hooks/*

# RUN THE INITIAL SETUP
bin/console init
