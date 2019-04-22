
这里是执行的命令，在安装fecshop的时候，文档里面已经
写好了migrate的命令，您不需要再次执行下面的操作。

### 1. 生成迁移文件的命令：

1.1 生成mysql文件:

```
./yii migrate/create   --migrationPath=@fbbcbase/migrations/mysqldb    fec_bbc_base
```

1.2 生成mongodb文件:

```
./yii mongodb-migrate/create   --migrationPath=@fbbcbase/migrations/mongodb    fec_bbc_base
```


### 2. 迁移的命令（导入数据库表）

2.1 mysql(导入mysql的表，数据，索引):

```
./yii migrate --interactive=0 --migrationPath=@fbbcbase/migrations/mysqldb
```


2.2 mongodb(导入mongodb的表，数据，索引):

```
./yii mongodb-migrate  --interactive=0 --migrationPath=@fbbcbase/migrations/mongodb
```









