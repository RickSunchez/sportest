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

def trimGoodsArticle(goods):
    sql = '''
        UPDATE `%s`.`shop_goods`
        SET `article` = '%s' 
        WHERE (`goods_id` = '%s');
    ''' % (dbName, goods['article'].strip(), str(goods['goods_id']))

    cursor.execute(sql)
    connection.commit()

sql = '''
    SELECT * FROM `%s`.`shop_goods`
''' % dbName

cursor.execute(sql)
result = cursor.fetchall()

for row in result:
    trimGoodsArticle(row)