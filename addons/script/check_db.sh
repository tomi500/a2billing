#!/bin/bash

if [ -s "/root/.my.cnf" ]; then
    RES=5
    PWD=`/bin/cat /root/.my.cnf |/bin/grep 'password ='|/bin/tail -n 1|/bin/awk -F= '{ print $2 }'|/bin/sed 's/[ \t]*//;s/[ \t]*$//'`
    while(( $RES>0 )); do
	((RES--))
	if [ -S "/var/run/mysqld/mysql.sock" ]; then
	    /usr/local/mysql/bin/mysqlcheck --all-databases --auto-repair --password=$PWD
	    RES=$?
	fi
	if (( $RES!=0 )); then
	    /bin/sleep 5
	fi
    done
fi
