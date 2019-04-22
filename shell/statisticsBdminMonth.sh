#!/bin/sh
Cur_Dir=$(cd `dirname $0`; pwd)

# get now update timestamp.
year=""
if [ "$1" != "" ];then
    year=$1
else
    year=`$Cur_Dir/../../../../yii  statistics/bdminmonth/getyear`
fi

echo $year

month=""
if [ "$2" != "" ];then
    month=$2
else
    month=`$Cur_Dir/../../../../yii  statistics/bdminmonth/getmonth`
fi

echo $month

bdmin_user_id_count=`$Cur_Dir/../../../../yii  statistics/bdminmonth/getbdminuseridcount`

for (( i=1; i<=$bdmin_user_id_count; i++ ))
do
    bdmin_user_id=`$Cur_Dir/../../../../yii statistics/bdminmonth/getbdminuserid $i`
    echo "bdmin_user_id: $bdmin_user_id"
    order_page_count=`$Cur_Dir/../../../../yii  statistics/bdminmonth/getorderpagecount $bdmin_user_id $year $month`
    echo "order_page_count: $order_page_count"
    # init
    $Cur_Dir/../../../../yii  statistics/bdminmonth/initstatisticsmonthbdmin $bdmin_user_id $year $month
    # statistics order total
    for (( j=1; j<=$order_page_count; j++ ))
    do
        echo "j: $j"
        $Cur_Dir/../../../../yii  statistics/bdminmonth/statisticsmonthbdmincompleteordertotal $bdmin_user_id $year $month $j
    done
    # statistics admin refund
    refund_page_count=`$Cur_Dir/../../../../yii  statistics/bdminmonth/getrefundpagecount $bdmin_user_id $year $month`
    echo "order_page_count: $order_page_count"
    for (( j=1; j<=$refund_page_count; j++ ))
    do
        echo "j: $j"
        $Cur_Dir/../../../../yii  statistics/bdminmonth/statisticsmonthbdminrefundtotal $bdmin_user_id $year $month $j
    done
    # statistics bdmin refund
    bd_refund_page_count=`$Cur_Dir/../../../../yii  statistics/bdminmonth/getbdrefundpagecount $bdmin_user_id $year $month`
    echo "order_page_count: $order_page_count"
    for (( j=1; j<=$bd_refund_page_count; j++ ))
    do
        echo "j: $j"
        $Cur_Dir/../../../../yii  statistics/bdminmonth/bdstatisticsmonthbdminrefundtotal $bdmin_user_id $year $month $j
    done
    # statistics total
    $Cur_Dir/../../../../yii  statistics/bdminmonth/monthtotal $bdmin_user_id $year $month
    
done












