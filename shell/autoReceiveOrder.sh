#!/bin/sh
Cur_Dir=$(cd `dirname $0`; pwd)

page_count=`$Cur_Dir/../../../../yii  order/process/autoreceiveorderpagecount`

echo "page_count: $page_count"

for (( i=1; i<=$page_count; i++ ))
do
    echo "process : $i"
    $Cur_Dir/../../../../yii  order/process/autoreceiveorder $i
done












