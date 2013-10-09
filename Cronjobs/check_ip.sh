#!/bin/sh

mynewip=`wget -O - http://checkip.dyndns.com|cut -b77-92|tr -d "</body>"`

myoldip=`awk -F= '{ print $2 }' < /etc/asterisk/additional_a2billing_externaddr.conf | awk -F\: '{ print $1 }'`

if test .$myoldip != .$mynewip && !(test -z $mynewip) then
    echo "externaddr=$mynewip:5060" > /etc/asterisk/additional_a2billing_externaddr.conf
    echo `date` "$myoldip -> $mynewip" >> /var/log/asterisk/sip_externaddr.log
    /usr/local/asterisk/sbin/asterisk -rx 'sip reload'
fi
