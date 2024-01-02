import json
import pymysql.cursors
import re

# @dev
# compareFile = './db/articles-keys.json'
# artefactsFile = './output/artefacts.txt'
# host = '127.0.0.1'
# port = 3306
# user = 'lodki'
# pwd = 'lodki'
# dbName = 'lodki'

# @prod
compareFile = './articles-keys.json'
artefactsFile = './artefacts.txt'
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
    cursorclass=pymysql.cursors.DictCursor,
    # charset='utf8',
    # use_unicode=True
)

cursor = connection.cursor()

artefacts = []
def masterpeace(article, code):
    artefacts.append('%s >> %s' % (article, code))

def goodsByArticle(article):
    sql = '''
        SELECT * FROM `%s`.`shop_goods`
        WHERE `article` LIKE "%s"
    ''' % (dbName, article.encode('utf-8'))

    cursor.execute("SET NAMES UTF8;")
    cursor.execute(sql)
    result = cursor.fetchall()
    
    return result

def goodsByArticleAndName(article, name):
    name = re.escape(name)

    sql = '''
        SELECT * FROM `%s`.`shop_goods`
        WHERE `article` LIKE "%s"
        AND `name` LIKE "%%%s%%"
    ''' % (dbName, article, name.encode('utf-8').decode('utf-8'))

    try:
        cursor.execute("SET NAMES UTF8;")
        cursor.execute(sql)
        result = cursor.fetchall()
        return result
    except:
        masterpeace(article, 'sql | errors')
        return []

def deleteGoods(goods_id):
    sql = '''
        DELETE FROM `%s`.`shop_goods`
        WHERE (`goods_id` = '%s');
    ''' % (dbName, str(goods_id))

    cursor.execute(sql)
    connection.commit()

def updateGoodsExternalId(goods_id, external_id):
    sql = '''
        UPDATE `%s`.`shop_goods`
        SET `external_id` = '%s' 
        WHERE (`goods_id` = '%s');
    ''' % (dbName, str(external_id), str(goods_id))

    cursor.execute(sql)
    connection.commit()

def goodsCountByArticle(article):
    result = goodsByArticle(article)
    return len(result)

def resolveDoubledConflict(article, actual1Cid):
    goods = goodsByArticle(article)

    deleteItem = None
    updateItem = None
    for i in range(len(goods)):
        if goods[i]['external_id'] == actual1Cid:
            deleteItem = goods.pop(i)
            break
    
    updateItem = goods[0]
    if deleteItem is None or updateItem is None:
        masterpeace(article, 'single | article not found')
        return

    if deleteItem['external_id'] == updateItem['external_id']:
        masterpeace(article, 'single | externals are equal')
        return

    deleteGoods(deleteItem['goods_id'])
    updateGoodsExternalId(updateItem['goods_id'], actual1Cid)

def updateExternalIdByArticle(article, actual1Cid):
    goods = goodsByArticle(article)
    if len(goods) > 1:
        masterpeace(article, 'db | article not found')
        return
    
    updateGoodsExternalId(goods[0]['goods_id'], actual1Cid)

def resolveCollectionConflicts(article, collection):
    # check
    for key in collection:
        goods = goodsByArticleAndName(article, collection[key]['name'])

        if len(goods) != 1:
            masterpeace(article, 'collection | item not found by name')
            return

    for key in collection:
        goods = goodsByArticleAndName(article, collection[key]['name'])
        updateGoodsExternalId(goods[0]['goods_id'], collection[key]['id'])

data = None

with open(compareFile, 'r') as f:
    data = json.load(f)

for article in data:
    print('.')
                                     # @note python2 only!!
    if type(data[article]) == str or type(data[article]) == unicode:
        count = goodsCountByArticle(article)
        if count == 1:
            updateExternalIdByArticle(article, data[article])
            continue

        if count == 2:
            resolveDoubledConflict(article, data[article])
            continue

        masterpeace(article, 'single | more than 2 includes')
        continue

    if type(data[article]) != dict:
        masterpeace(article, 'collection | undefined item')
        continue

    count = goodsCountByArticle(article)

    if len(data[article]) == count:
        if count == 1:
            ids = list(data[article].keys())
            updateExternalIdByArticle(article, ids[0])
            continue

        resolveCollectionConflicts(article, data[article])
    else:
        masterpeace(article, 'collection | not equal instances')

with open(artefactsFile, 'w') as f:
    for art in artefacts:
        f.write(art.encode('utf8'))
        f.write('\n')
