import pymysql.cursors

# @dev
host = '127.0.0.1'
port = 3306
user = 'lodki'
pwd = 'lodki'
dbName = 'lodki'

# @prod
# host = 'localhost'
# port = 3306
# user = 'u1616'
# pwd = 'ce9zEAyyWu342'
# dbName = 'u1616_df2'

connection = pymysql.connect(
    host=host,
    port=port,
    user=user,
    password=pwd,
    database=dbName,
    cursorclass=pymysql.cursors.DictCursor
)

cursor = connection.cursor()

sql = '''
    SELECT * FROM `%s`.`shop_product_collection_item`;
''' % dbName

cursor.execute(sql)
collectionItems = cursor.fetchall()

sql = '''
    SELECT * FROM `%s`.`shop_product_collection`;
''' % dbName

cursor.execute(sql)
collections = cursor.fetchall()

itemsDublicatesRaw = dict()
for item in collectionItems:
    productId = item['product_id']

    if productId in itemsDublicatesRaw:
        itemsDublicatesRaw[productId].append(item)
    else:
        itemsDublicatesRaw[productId] = [item]

itemsDublicates = dict()
for key in itemsDublicatesRaw:
    l = len(itemsDublicatesRaw[key])
    if l > 1 and l % 2 == 0:
        itemsDublicates[key] = itemsDublicatesRaw[key]

collectionItemsIds = []
collectionIds = []
for key in itemsDublicates:
    if len(itemsDublicates[key]) > 2: continue

    item1 = itemsDublicates[key][0]
    item2 = itemsDublicates[key][1]
    if item1['name'] != item2['name']: continue

    delItem = None
    if item1['id'] > item2['id']:
        delItem = item2
    else:
        delItem = item1
    
    collectionItemsIds.append('(`id` = %s)' % str(delItem['id']))
    collectionIds.append('(`id` = %s)' % str(delItem['coll_id']))
    
sql = '''
    DELETE FROM `%s`.`shop_product_collection_item` WHERE %s;
''' % (dbName, ' OR '.join(collectionItemsIds))
cursor.execute(sql)

sql = '''
    DELETE FROM `%s`.`shop_product_collection` WHERE %s;
''' % (dbName, ' OR '.join(collectionIds))
cursor.execute(sql)
