#!/bin/sh
#
#
#CALCULATE STATISTICS DATA AND STORE IN CACHE TEMP TABLES
#
# TRACK
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_TOTAL LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CONTINENT LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_COUNTRY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_SUBDIVISION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CITY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_STATION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CONTINENT LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_COUNTRY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_SUBDIVISION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CITY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_STATION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_ARTIST LAST_7_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_TOTAL LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CONTINENT LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_COUNTRY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_SUBDIVISION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CITY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_STATION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CONTINENT LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_COUNTRY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_SUBDIVISION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CITY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_STATION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_ARTIST LAST_30_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_TOTAL LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CONTINENT LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_COUNTRY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_SUBDIVISION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CITY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_STATION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CONTINENT LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_COUNTRY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_SUBDIVISION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CITY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_STATION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_ARTIST LAST_365_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_TOTAL LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CONTINENT LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_COUNTRY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_SUBDIVISION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CITY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_STATION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CONTINENT LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_COUNTRY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_SUBDIVISION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CITY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_STATION LAST_YEAR 
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_ARTIST LAST_YEAR &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_TOTAL LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CONTINENT LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_COUNTRY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_SUBDIVISION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CITY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_STATION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CONTINENT LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_COUNTRY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_SUBDIVISION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CITY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_STATION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_ARTIST LAST_MONTH &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_TOTAL LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CONTINENT LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_COUNTRY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_SUBDIVISION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CITY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_STATION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CONTINENT LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_COUNTRY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_SUBDIVISION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CITY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_STATION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_ARTIST LAST_WEEK &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_TOTAL THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CONTINENT THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_COUNTRY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_SUBDIVISION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CITY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_STATION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CONTINENT THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_COUNTRY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_SUBDIVISION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CITY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_STATION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_ARTIST THIS_YEAR &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_TOTAL THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CONTINENT THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_COUNTRY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_SUBDIVISION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_CITY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_STATION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CONTINENT THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_COUNTRY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_SUBDIVISION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_CITY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_DAYTIME_STATION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA TRACK_PLAYED_BY_ARTIST THIS_MONTH &
#
# ARTIST
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_TOTAL LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CONTINENT LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_COUNTRY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_SUBDIVISION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CITY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_STATION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CONTINENT LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_COUNTRY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_SUBDIVISION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CITY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_STATION LAST_7_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_TOTAL LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CONTINENT LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_COUNTRY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_SUBDIVISION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CITY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_STATION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CONTINENT LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_COUNTRY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_SUBDIVISION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CITY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_STATION LAST_30_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_TOTAL LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CONTINENT LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_COUNTRY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_SUBDIVISION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CITY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_STATION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CONTINENT LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_COUNTRY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_SUBDIVISION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CITY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_STATION LAST_365_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_TOTAL LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CONTINENT LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_COUNTRY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_SUBDIVISION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CITY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_STATION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CONTINENT LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_COUNTRY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_SUBDIVISION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CITY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_STATION LAST_YEAR 
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_TOTAL LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CONTINENT LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_COUNTRY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_SUBDIVISION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CITY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_STATION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CONTINENT LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_COUNTRY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_SUBDIVISION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CITY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_STATION LAST_MONTH &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_TOTAL LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CONTINENT LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_COUNTRY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_SUBDIVISION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CITY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_STATION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CONTINENT LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_COUNTRY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_SUBDIVISION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CITY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_STATION LAST_WEEK &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_TOTAL THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CONTINENT THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_COUNTRY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_SUBDIVISION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CITY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_STATION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CONTINENT THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_COUNTRY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_SUBDIVISION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CITY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_STATION THIS_YEAR &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_TOTAL THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CONTINENT THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_COUNTRY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_SUBDIVISION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_CITY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_STATION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CONTINENT THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_COUNTRY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_SUBDIVISION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_CITY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA ARTIST_PLAYED_BY_DAYTIME_STATION THIS_MONTH &
#
# LABEL
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_TOTAL LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CONTINENT LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_COUNTRY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_SUBDIVISION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CITY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_STATION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CONTINENT LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_COUNTRY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_SUBDIVISION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CITY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_STATION LAST_7_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_TOTAL LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CONTINENT LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_COUNTRY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_SUBDIVISION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CITY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_STATION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CONTINENT LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_COUNTRY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_SUBDIVISION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CITY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_STATION LAST_30_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_TOTAL LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CONTINENT LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_COUNTRY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_SUBDIVISION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CITY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_STATION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CONTINENT LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_COUNTRY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_SUBDIVISION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CITY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_STATION LAST_365_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_TOTAL LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CONTINENT LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_COUNTRY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_SUBDIVISION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CITY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_STATION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CONTINENT LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_COUNTRY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_SUBDIVISION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CITY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_STATION LAST_YEAR 
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_TOTAL LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CONTINENT LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_COUNTRY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_SUBDIVISION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CITY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_STATION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CONTINENT LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_COUNTRY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_SUBDIVISION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CITY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_STATION LAST_MONTH &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_TOTAL LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CONTINENT LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_COUNTRY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_SUBDIVISION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CITY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_STATION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CONTINENT LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_COUNTRY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_SUBDIVISION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CITY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_STATION LAST_WEEK &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_TOTAL THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CONTINENT THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_COUNTRY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_SUBDIVISION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CITY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_STATION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CONTINENT THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_COUNTRY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_SUBDIVISION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CITY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_STATION THIS_YEAR &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_TOTAL THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CONTINENT THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_COUNTRY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_SUBDIVISION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_CITY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_STATION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CONTINENT THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_COUNTRY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_SUBDIVISION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_CITY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA LABEL_PLAYED_BY_DAYTIME_STATION THIS_MONTH &
#
# PUBLISHER
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_TOTAL LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CONTINENT LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_COUNTRY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_SUBDIVISION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CITY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_STATION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CONTINENT LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_COUNTRY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_SUBDIVISION LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CITY LAST_7_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_STATION LAST_7_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_TOTAL LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CONTINENT LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_COUNTRY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_SUBDIVISION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CITY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_STATION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CONTINENT LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_COUNTRY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_SUBDIVISION LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CITY LAST_30_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_STATION LAST_30_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_TOTAL LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CONTINENT LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_COUNTRY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_SUBDIVISION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CITY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_STATION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CONTINENT LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_COUNTRY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_SUBDIVISION LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CITY LAST_365_DAYS &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_STATION LAST_365_DAYS &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_TOTAL LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CONTINENT LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_COUNTRY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_SUBDIVISION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CITY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_STATION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CONTINENT LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_COUNTRY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_SUBDIVISION LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CITY LAST_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_STATION LAST_YEAR 
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_TOTAL LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CONTINENT LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_COUNTRY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_SUBDIVISION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CITY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_STATION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CONTINENT LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_COUNTRY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_SUBDIVISION LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CITY LAST_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_STATION LAST_MONTH &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_TOTAL LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CONTINENT LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_COUNTRY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_SUBDIVISION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CITY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_STATION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CONTINENT LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_COUNTRY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_SUBDIVISION LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CITY LAST_WEEK &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_STATION LAST_WEEK &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_TOTAL THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CONTINENT THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_COUNTRY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_SUBDIVISION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CITY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_STATION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CONTINENT THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_COUNTRY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_SUBDIVISION THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CITY THIS_YEAR &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_STATION THIS_YEAR &
#
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_TOTAL THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CONTINENT THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_COUNTRY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_SUBDIVISION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_CITY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_STATION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CONTINENT THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_COUNTRY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_SUBDIVISION THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_CITY THIS_MONTH &
php "/var/www/html/tools/cache_tables_update/main.php" CALC_STAT_DATA PUBLISHER_PLAYED_BY_DAYTIME_STATION THIS_MONTH &