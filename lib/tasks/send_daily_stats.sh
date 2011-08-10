#!/bin/sh
cd /var/www/vhosts/kudobots.com/rails_site
/usr/local/bin/rake send_daily_stats RAILS_ENV=production
