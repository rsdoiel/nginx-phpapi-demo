
nginx-phpapi-demo
=================

Thought exercise for setting of developer instances of Nginx for PHP based APIs and services.

# Goal of this demo

Show how you might setup a dev environment for serving static content talking to a PHP based API using Nginx and PHP's command line webserver. 

# PHP command line web server

As of version 5.4 PHP command line comes with a built in web server suitable for development purposes. The two command line options that are instramental in taking advantage of this are _-S_ and _-t_.  The first sets the hostname and port to listen to and the latter the web root directory (e.g. htdocs in Apache terminalogy)


The PHP website indicates the built-in webserver is appropraite for development and not production purposes. We are
following that recommendation here though Google has used it as a starting point to integrate PHP support into their
App Engine environment.

## Example


Via Mac Ports you would run this command from Terminal App.

```shell
    php54 -S localhost:8000 -t www
```

Via Ubuntu Linux this is the shell command I would use.

```shell
    php -S localhost:8000 -t www
```

Both examples run a local PHP webserver on port 8000 listening on localhost.  The URL to reach the webserver would be _http://localhost:8000_.  Anything in the _www_ folder would be available on the web.  *Don't run this command in a folder that contains private or sensitive information!*

# Nginx

Nginx is a high performance webserver suitable for tasks which have traditional be assigned to Apache as well as more specialized needs. It's configuration is straight forward with many websites featuring useful recipes that covering most needs. In the past getting Nginx setup and tuned was challenging for non-Russian speakers.  Recently this has changed with increased documentation also availabe in Engish. In this project we'll show just how easy it can be.

At its core Nginx is a software load balancer. It is design from the ground up to quickly and efficiently route traffic with a high level of concurrency (e.g. thousands of concurrent connections versus the typical Apache five to eight hundred concurrent connections on a modest Virtual Machine).  In this capacity it has gained wide spread uses across larger organizations for managing complex routing and high traffic websites.  Like Apache it has a module system that supports  PHP, Perl, Python and Ruby. It is very suitable as a front end to Tomcat as well as newer deployment environments like NodeJS.

# Preparing your system

The basic steps are the same though specific command very between platform.  Getting things going involes
the follow

1. Installing PHP 5.4 (or PHP 5.5) command line
2. Installing desired PHP modules (E.g. MongoDB, MySQLi, Curl)
3. Installing Nginx binary
4. Updating your local _hosts_ file to support the desired aliases for your development environment
5. Configuring Nginx to virtual host your static content and proxy to your PHP or NodeJS services.

Steps 4 and 5 are substantiall similar between Mac OS X 10.6 and the Ubuntu 13.04 system.  The Mac has
a couple extra command line utilities to cause the DNS to update based on your local _/etc/hosts_ file.


## Ubuntu Linux

Here commands to install PHP 5.4 allong with three example modules (MongoDB, MySQLi and Curl). This covers

```shell
    sudo apt-get install php5-cli php5-mysql php5-mongo php5-curl 
```

Siminlarly installing Nginx can be done with this command

```shell
    sudo apt-get install nginx-light
```

The command _sudo_ with cause a prompt for your password and assuming _sudo_ is setup correctly
allow you to install these packages to be available system wide (i.e. installs as if it was installed by the *root* user).

The command _apt-get_ is the basic Debian command line installer available on all Debian based systems include Ubuntu.

## Mac OS 10 via Mac Ports

Mac Ports adds many of the more traditional utilities and services to a standard Mac OS X system.  It requires that
the Mac OS X Xcode command line tools are already installed. Mac Ports itself installs as a standard Mac pkg file. See
[macports.org](http://macports.org) for instructions and more about what Mac Ports offers.



# Reference

* [PHP.net](http://php.net)
* [Nginx Configuration](http://blog.martinfjordvald.com/2010/07/nginx-primer/)
* [Nginx Primer 2](http://blog.martinfjordvald.com/2011/02/nginx-primer-2-from-apache-to-nginx/) from Apache to Nginx
* [Full Example](http://wiki.nginx.org/FullExample)
* [Config Cookbook](http://wiki.nginx.org/Configuration)
* [Dummies guide to setting up Nginx](http://michael.lustfield.net/nginx/dummies-guide-to-setting-up-nginx)
* [Guide to Nginx + SSL + SPDY](http://www.mare-system.de/guide-to-nginx-ssl-spdy-hsts/)
* [Limit Browsers](http://wiki.nginx.org/LimitBrowsers)
* [Separate Error Logging Perl Virtual Hosts](http://wiki.nginx.org/SeparateErrorLoggingPerVirtualHost)
* [Nginx JavaScript](https://github.com/kung-fu-tzu/ngx_http_js_module#readme)

