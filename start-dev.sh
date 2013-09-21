#!/bin/bash
nginx -c nginx.config
php -S *:8001 -t www/api
