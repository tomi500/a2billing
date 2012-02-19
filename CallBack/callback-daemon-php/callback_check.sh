#!/bin/bash

USR=`cat /etc/a2billing.conf|grep user|tail -n 1|awk -F= '{ print $2 }'|awk '{ print $1 }'`
PASS=`cat /etc/a2billing.conf|grep password|tail -n 1|awk -F= '{ print $2 }'|awk '{ print $1 }'`

echo "UPDATE \`cc_callback_spool\` SET \`status\`='ERROR_TIMEOUT' where (\`status\`='PENDING' OR \`status\`='PROCESSING') AND \`entry_time\`<addtime(now(),'-00:01:00');" | /usr/bin/mysql -B billing -u$USR -p$PASS >> /tmp/out 2>>/tmp/err
