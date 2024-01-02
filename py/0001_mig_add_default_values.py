import pymysql.cursors

# @dev
# host = '127.0.0.1'
# port = 3306
# user = 'lodki'
# pwd = 'lodki'
# dbName = 'lodki'

# @prod
host = 'localhost'
port = 3306
user = 'u1616'
pwd = 'ce9zEAyyWu342'
dbName = 'u1616_df2'

connection = pymysql.connect(
    host=host,
    port=port,
    user=user,
    password=pwd,
    database=dbName,
    cursorclass=pymysql.cursors.DictCursor
)

cursor = connection.cursor()

sql = [
    '''
        ALTER TABLE `%s`.`shop_product_collection_item` 
        CHANGE COLUMN `code` `code` VARCHAR(32) NOT NULL DEFAULT '' ;
    ''' % dbName,
    '''
        ALTER TABLE `%s`.`shop_product_collection` 
        CHANGE COLUMN `prefix` `prefix` VARCHAR(50) NOT NULL DEFAULT '' ;
    ''' % dbName,
    '''
        ALTER TABLE `%s`.`shop_goods` 
        CHANGE COLUMN `short_name` `short_name` VARCHAR(400) NOT NULL DEFAULT '' ;
    ''' % dbName,
    '''
        ALTER TABLE `%s`.`shop_goods` 
        CHANGE COLUMN `model` `model` VARCHAR(400) NOT NULL DEFAULT '' ;
    ''' % dbName,
    '''
        ALTER TABLE `%s`.`shop_goods` 
        CHANGE COLUMN `brief` `brief` VARCHAR(1000) NOT NULL DEFAULT '' ;
    ''' % dbName,
    '''
        ALTER TABLE `%s`.`shop_goods` 
        CHANGE COLUMN `prefix` `prefix` VARCHAR(50) NOT NULL DEFAULT '' ;
    ''' % dbName,
    '''
        ALTER TABLE `%s`.`shop_goods` 
        CHANGE COLUMN `parent_external_id` `parent_external_id` VARCHAR(36) NOT NULL DEFAULT '' ;
    ''' % dbName,
    '''
        ALTER TABLE `%s`.`shop_goods` 
        CHANGE COLUMN `article` `article` VARCHAR(40) NOT NULL ;
    ''' % dbName,
    '''
        ALTER TABLE `%s`.`shop_category` 
        CHANGE COLUMN `goods` `goods` INT(11) NOT NULL DEFAULT 0 ;
    ''' % dbName
]

for query in sql:
    cursor.execute(query)

connection.commit()