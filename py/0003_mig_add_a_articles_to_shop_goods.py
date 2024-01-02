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
        ALTER TABLE `%s`.`shop_goods` 
        ADD COLUMN `a_articles` VARCHAR(2048) NOT NULL DEFAULT '' AFTER `t_article`
    ''' % dbName
]

for query in sql:
    cursor.execute(query)

connection.commit()