#!/bin/sh

HOST="$1"

/usr/local/mysql/bin/mysql -h $1 -u root -p < sensordb_DDL.sql 
