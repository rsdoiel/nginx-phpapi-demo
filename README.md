
nginx-phpapi-demo
=================

Thought exercise for setting of developer instances of Nginx for PHP
based APIs and services.

# Goal of this demo

Show how you might setup a dev environment for serving static content 
talking to a PHP based API using Nginx and PHP's command line webserver
or via Nginx's FastCGI interface. 

# PHP command line web server

As of version 5.4 PHP command line comes with a built in web server 
suitable for development purposes. The two command line options that 
are instramental in taking advantage of this are _-S_ and _-t_.  The 
first sets the hostname and port to listen to and the latter the 
web root directory (e.g. htdocs in Apache terminalogy)


The PHP website indicates the built-in webserver is appropraite for 
development and not production purposes. We are following that 
recommendation here though Google has used it as a starting point to 
integrate PHP support into their App Engine environment. That approach
is intreaguing as a way for give the applications side the abality to
start/stop services on high Unix ports while leaving the main Nginx
control in the hands of system administrations and operations staff.


## Example


Via Mac Ports you would run this command from Terminal App.

``shell
    php54 -S localhost:8000 -t www
```

Via Ubuntu 12.10 this is the shell command I would use.

```shell
    php -S localhost:8000 -t www
```

Both examples run a local PHP webserver on port 8000 listening on
localhost.  The URL to reach the webserver would be 
_http://localhost:8000_.  Anything in the _www_ folder would be
available on the web.  *Don't run this command in a folder that 
contains private or sensitive information!*

# Nginx

Nginx is a high performance webserver suitable for tasks which have 
traditional be assigned to Apache as well as more specialized needs.
It's configuration is straight forward with many websites featuring 
useful recipes that covering most needs. In the past getting Nginx 
setup and tuned was challenging for non-Russian speakers.  Recently 
this has changed with increased documentation also availabe in Engish.
In this project we'll show just how easy it can be.

At its core Nginx is a software load balancer. It is design from the 
ground up to quickly and efficiently route traffic with a high level 
of concurrency (e.g. thousands of concurrent connections versus the 
typical Apache five to eight hundred concurrent connections on a 
modest Virtual Machine).  In this capacity it has gained wide spread 
uses across larger organizations for managing complex routing and high 
traffic websites.  Like Apache it has a module system that supports
PHP, Perl, Python and Ruby. It is very suitable as a front end to 
Tomcat as well as newer deployment environments like NodeJS.

# Preparing your system (Ubuntu)

The basic steps are the same though specific command very between 
platform.  Getting things going involes the follow

1. Installing PHP 5.4 (or PHP 5.5) command line with PHP FPM support
2. Installing desired PHP modules (E.g. PHP FPM, MongoDB, MySQLi, Curl)
3. Installing Nginx binary
4. Updating your local _hosts_ file to support the desired aliases for your development environment
5. Configuring Nginx to virtual host your static content and proxy to your PHP or NodeJS services.

Here commands to do 1-4.

```shell
    sudo apt-get install php5-cli php5-fpm php5-mysql php5-mongo php5-curl 
    sudo apt-get install nginx-light
    sudo nano /etc/hosts    
```

The command _sudo_ with cause a prompt for your password and assuming _sudo_ is setup correctly
allow you to install these packages to be system wide (i.e. installs as if it was installed by the *root* user).

The command _apt-get_ is the basic Debian command line installer available on all Debian based systems include Ubuntu.

The command _nano_ can be replaced with any text editor you like (e.g. vi, emacs, brackets).
Nano is available on most Linux systems (as is vi) and is easy to use.

I typically create a common sites folder (E.g. /sites or /var/sites) then create a local
virtual host record running on 127.0.0.* for development.  This lets me test thing without 
exposing it to the rest of the network.  Here's an example I would _add_ to my hosts file 
*experiment-001.local*

```hosts
    # localhost project alias
    127.0.0.2 experiment-001 experiment-001.local
```

There would be a corresponding site definition in 
*/etc/nginx/sites-available/experiment-001.local* which would be 
symbolically linked from */etc/nginx/sites-endable/experiment-001.local"
Restarting Nginx would then allow me to point my browser at
*http://experiment-001.local* for testing.

Here's what my PHP enabled */etc/nginx/sites-available/experiment-001.local* would
look like.

```shell
    #
    # Static web site with PHP support via 127.0.0.*
    #
    server {
        listen experiment-001.local:80;
        server_name experiment-001.local;
        root  /sites/experiment-011.local/www;
        index index.php index.html;
    
        location / {
            try_files $uri $uri/ /index.html;
        }
    
        #
        # pass the PHP scripts to FastCGI server
        #
        location ~ \.php$ {
            try_files $uri =404;
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            #fastcgi_pass 127.0.0.1:9000;
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
        }
    
        #
        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        #
        location ~ /\.ht {
            deny all;
        }
    }
```

Here are the command I'd use to edit my hosts file, then the Nginx site definition,
test the Nginx configurations for errors and then restart Nginx.

```shell
    sudo nano /etc/hosts
    sudo nano /etc/nginx/sites-available/experiment-001.local
    sudo nginx -t
    sudo /etc/init.d/nginx restart
```


## Mac OS 10 via Mac Ports

We can also implement this setup on a Mac OS X machine via Mac Ports. 
I will also assume you've been able to disable the default Apache installation
to free port 80 without disabling port 80 if the Mac firewall is turned on.
That bit could be a whole article in itself.


Here's the steps we need ith follow

1. Install XCode command line tools (if you haven't done so)
2. Install Mac Ports (if you haven't done so)
3. Install PHP 5.4 and the following PHP modules php54-mbstring, php54-curl, php54-mysql, php54-mongo
4. Configure Nginx
5. Add your site definitions
6. Restart (or start) Nginx and test with your browser


Mac Ports adds many of the more traditional utilities and services to a standard Mac OS X system.  It requires that
the Mac OS X Xcode command line tools are already installed. Mac Ports itself installs as a standard Mac pkg file. See
[macports.org](http://macports.org) for instructions and more about what Mac Ports offers.


Make sure your ports are fresh

```shell
    sudo port selfupdate
    sudo port update outdated
```

Installing PHP 5.4 and interesting modules

```shell
    sudo port install php54
    sudo port install php54-mbstring
    sudo port install php54-curl
    sudo port install php54-mysql
    sudo port install php54-mongo
```

By default Mac ships with Apache as its webserver.  Before you can replace it you have to unload it.

```shell
    sudo launchctl unload -w /System/Library/LaunchDaemons/org.apache.httpd.plist
```

If you point your web browser at your Mac it say "web page not available" indicating no webserver is running.

Now you're ready for Nginx. We'll install it then, configure and finally we will load it.

```shell
    sudo port install nginx
```

Now we need to configure things.  The configuration for Nginx under Mac ports is in */opt/local/etc/nginx*. You
need to copy the default configuration and add two lines in the server block to include our specific 
changes.

```shell
    sudo cp /opt/local/etc/nginx/nginx.conf.default /opt/local/etc/nginx/nginx.conf
    sudo nano /opt/local/etc/nginx/nginx.conf
```

Before the final closing "}" add these lines in */opt/local/etc/nginx/nginx.conf*

```
    #
    # Merge local configuration
    #
    include /opt/local/etc/nginx/sites-enabled/*;
```

We now need to create these the directory where we'll store our custom virtual hosts and services.
We will also create a directory which will house our custom site as well as our PHP API.

```shell
    sudo mkdir /opt/local/etc/nginx/sites-enabled
    sudo mkdir -p /sites/mydev.example.com/www
    sudo mkdir /sites/mydev/example.com/phpapi
```

Now let's create our virtual host for our machine. We will call the machine "mydev.example.com" for
the purposes of this example.

First open an empty configuration file named */opt/local/etc/nginx/sites-enabled/mydev.example.com.conf*.

```shell
    sudo nano /opt/local/etc/nginx/sites-enabled/mydev.example.com.conf
```

That file should look something like this.

```nginx
```

In this example we have create a website whos document root is in */sites/mydev.example.com/www*. If
We can add three pages get things working correctly-- *index.html*, *404.html* and *50x.html*. 


To make sure we haven't introduced a mistake in our configuration run the Nginx service with the "-t" option.

```shell
    sudo nginx -t
```

You should see something like this.

```
    nginx: the configuration file /opt/local/etc/nginx/nginx.conf syntax is ok
    nginx: configuration file /opt/local/etc/nginx/nginx.conf test is successful
```

Now we're ready to have Mac OS X load Nginx. We use *launchctl* tool for this.

```shell
    sudo launchctl load -w /Library/LaunchDaemons/org.macports.nginx.plist
```

If we change the configuration we'll need to reload it (i.e. first unload, then load)

```shell
    sudo launchctl unload /Library/LaunchDaemons/org.macports.nginx.plist
    sudo launchctl load /Library/LaunchDaemons/org.macports.nginx.plist
```

This is usaully very fast. It is just allot to type :-(.


## Setting up the proxy

This initial configuration doesn't run PHP.  It is very quick at serving static content.  Let's say you've working on a PHP based
API you're trying to debug that you are running on localhost port 3000.  That's easy to do. We just add another configuration document
in */opt/local/etc/nginx/sites-enabled/* and reload Nginx.

Here's an example JSON "hello world" service written in PHP. It will return a JSON object containing a content property and date property.
We're create this file in */sites/mydev.example.com/phpapi/index.php*

```php
    <?php
        // Set your correct time zone so we can use the date() function properly.
        date_default_timezone_set("America/Los_Angeles");
        // Set your content type header.
        header("Content-Type: application/json");
        // Send your encode a PHP Array literal as a JSON string and send.
        echo json_encode([
            "content" => "Hello World",
            "date" => date("c")
        ]);
    ?>
```

Now let's configure Nginx to pass things to our server.  We're doing to do this by runnning our service on port 3000 but listening
on port 80 for anything start with */demo*.

Create a file */opt/local/nginx/sites-enabled/php-demo-service.conf* containing this--

```nginx
#
# Static web site with PHP support via 127.0.0.*
#
server {
    listen experiment-001.local:80;
    server_name experiment-001.local;
    root  /sites/experiment-011.local/www;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    #
    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        #fastcgi_pass 127.0.0.1:9000;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }

    #
    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    #
    location ~ /\.ht {
        deny all;
    }
}
```

Reload Nginx

```shell
    sudo launchctl unload -w /Library/LaunchDaemons/org.macports.nginx.plist
    sudo launchctl load -w /Library/LaunchDaemons/org.macports.nginx.plist
```

Start our webservice.

```shell
    cd /sites/mydev.example.com/
    php54 -S localhost:3000 -t phpapi
```

Check to make sure this is running on localhost at 3000 (e.g. http://localhost:3000).

If everything worked correctly you should be able to point your browser at http://mydev.example.com/demo (assuming your machine
is called mydev.example.com) and see the same JSON blob you saw at localhost:3000.

If you see "Broken Gateway" something went wrong and Nginx isn't seeing your service.



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

