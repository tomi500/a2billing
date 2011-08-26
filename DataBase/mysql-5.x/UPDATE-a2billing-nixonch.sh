#!/bin/bash

echo ""
echo "Update A2Billing DataBase by nixonch"
echo "------------------------------------"
echo ""

echo "Enter Database Name : "
read dbname

echo "Enter Hostname : "
read hostname

echo "Enter UserName : "
read username

echo "Enter Password : "
read password

echo mysql -f --user=$username --password=$password --host=$hostname $dbname

cat UPDATE-a2billing-nixonch.sql| mysql -f --user=$username --password=$password --host=$hostname $dbname 

# All done, exit ok
exit 0
