# -*- coding: utf-8 -*-

import xmltodict
import pymysql.cursors
import sys
from time import time
import re
import os

# python2 py2_v1.py -h=127.0.0.1 -p=3306 -u=lodki -pwd=lodki -db=lodki -wd=./files
# @TODO add logs

# CONSTS
_ki = u'КоммерческаяИнформация'
_kl = u'Классификатор'
_pp = u'ПакетПредложений'
_kat = u'Каталог'
_id = u'Ид'
_n = u'Наименование'
_art = u'Артикул'
_kod = u'Код'
_ktg = u'Категория'
_groups = u'Группы'
_group = u'Группа'
_goods = u'Товары'
_good = u'Товар'
_offers = u'Предложения'
_offer = u'Предложение'
_prices = u'Цены'
_price = u'Цена'
_ppu = u'ЦенаЗаЕдиницу'
_amount = u'Количество'

transliterate = {
    u'а': u'a',  u'б': u'b',  u'в': u'v',   u'г': u'g', u'д': u'd', u'е': u'e',
    u'ё': u'e',  u'ж': u'zh', u'з': u'z',   u'и': u'i', u'й': u'y', u'к': u'k',
    u'л': u'l',  u'м': u'm',  u'н': u'n',   u'о': u'o', u'п': u'p', u'р': u'r',
    u'с': u's',  u'т': u't',  u'у': u'u',   u'ф': u'f', u'х': u'h', u'ц': u'ts',
    u'ч': u'ch', u'ш': u'sh', u'щ': u'sch', u'ъ': u'',  u'ы': u'y', u'ь': u'',
    u'э': u'e',  u'ю': u'yu', u'я': u'ya'
}

# SQL
def ShopCategoriesWhere(externalId):
    sql = 'SELECT * FROM `shop_category` WHERE `external_id` = "%s"' % externalId
    cursor.execute(sql)
    result = cursor.fetchall()
    return result

def ShopCategoryInsert(externalId, parentId, name):
    sql = '''
        INSERT INTO 
            `shop_category` (
                `cid`, `external_id`, `external_change`, `pid`, `type_id`,
                `name`, `header`, `url`, `text_top`, `text_below`, `children`, 
                `goods`, `pos`, `status`, `prefix`, `prefix_goods`, `show_cats`, 
                `show_all`, `date_cr`, `date_edit`, `popular`, `owner_id`)
        VALUES (
            NULL, '%s', '1', %s, '1',
            '%s', '', '%s', '', '', '0',
            '0', '0', '0', '', '', '0',
            '0', %s, '0', '0', '0');
    ''' % (
        externalId,
        str(parentId),
        escapeStr(name),
        urlify(u'%s' % name),
        nowTs()
    )

    cursor.execute(sql)
    connection.commit()

def ShopCategoryUpdate(primaryKey, parentId=None, name=None):
    queryParams = [
        ['`date_edit`', nowTs()]
    ]
    if parentId is not None:
        queryParams.append(
            ['`pid`', str(parentId)]
        )
    if name is not None:
        queryParams.extend([
            ['`name`', '"%s"' % escapeStr(name)],
            ['`url`', '"%s"' % urlify(u'%s' % name)]
        ])

    query = []
    for qp in queryParams:
        query.append(' = '.join(qp))

    sql = '''
        UPDATE
            `shop_category`
        SET %s
        WHERE
            (`cid` = %s);
    ''' % (', '.join(query), primaryKey)

    cursor.execute(sql)
    connection.commit()

def ShopGoodsWhere(externalId):
    sql = 'SELECT * FROM `shop_goods` WHERE `external_id` = "%s"' % externalId
    cursor.execute(sql)
    result = cursor.fetchall()
    return result

def ShopGoodsInsert(externalId, categoryId, name, article, value, amount, parentExternalId):
    sql = '''
        INSERT INTO
            `shop_goods` (
                `goods_id`, `external_id`, `external_change`, `cid`, `ctype`, `unit_id`,
                `vendor_id`, `provider_id`, `name`, `short_name`, `model`, `url`,
                `article`, `brief`, `code`, `value`, `value_system`, `value_old`,
                `value_of`, `amount`, `is_amount`, `rating`, `votes`, `status`, `pos`,
                `minimum`, `maximum`, `step`, `weight`, `date_cr`, `date_edit`, `prefix`,
                `update`, `moder`, `inner`, `popular`, `owner_id`, `delivery`, `parent_id`,
                `parent_external_id`, `video_id`)
        VALUES (
            NULL, '%s', '1', %s, '1', '1',
            '29', '0', '%s', '', '', '%s',
            '%s', '', 'RUB', %s, %s, '0.00',
            '0', %s, %s, '0.00', '0', '1', '0',
            '1.000', '0.000', '1.000', '0.000', %s, '0', '',
            '1', '0', '0', '755', '0', '0', '0',
            '%s', '0');
    ''' % (
        externalId,
        str(categoryId),
        escapeStr(name),
        urlify(u'%s' % name),
        article,
        str(value),
        str(value),
        str(amount),
        str(int(amount != 0)),
        nowTs(),
        parentExternalId
    )

    cursor.execute(sql)
    connection.commit()

def ShopGoodsUpdate(
    primaryKey,
    categoryId=None,
    name=None,
    article=None,
    value=None,
    amount=None,
    parentExternalId=None
):
    queryParams = [
        ['`date_edit`', nowTs()]
    ]
    if categoryId is not None:
        queryParams.append(
            ['`cid`', str(categoryId)]
        )
    if name is not None:
        queryParams.extend([
            ['`name`', '"%s"' % escapeStr(name)],
            ['`url`', '"%s"' % urlify(u'%s' % name)]
        ])
    if article is not None:
        queryParams.append(
            ['`article`', '"%s"' % article]
        )
    if value is not None:
        queryParams.extend([
            ['`value`', str(value)],
            ['`value_system`', str(value)]
        ])
    if amount is not None:
        amount = float(amount)
        queryParams.extend([
            ['`amount`', str(amount)],
            ['`is_amount`', str(int(amount != 0))]
        ])
    if parentExternalId is not None:
        queryParams.append(
            ['`parent_external_id`', '"%s"' % parentExternalId]
        )

    query = []
    for qp in queryParams:
        query.append(' = '.join(qp))

    sql = '''
        UPDATE
            `shop_goods`
        SET %s
        WHERE
            (`goods_id` = %s);
    ''' % (', '.join(query), primaryKey)

    cursor.execute(sql)
    connection.commit()

# HELPERS

def parseArgs():
    args = sys.argv[1:]
    keyDict = {
        '-h': 'host',
        '-p': 'port',
        '-u': 'user',
        '-pwd': 'password',
        '-db': 'database',
        '-wd': 'workdir'
    }
    result = {}

    for arg in args:
        key, val = arg.split('=')

        if key in keyDict:
            item = keyDict[key]
            if item == 'port': val = int(val)
            result[item] = val
    
    return result

def parseFiles(workdir):
    if workdir[-1] != '/': workdir += '/'

    importFile = workdir + 'import.xml'
    offersFile = workdir + 'offers.xml'

    importData = None
    offersData = None

    if not (os.path.isfile(importFile) and os.path.isfile(offersFile)):
        return False, False

    with open(importFile, 'r') as f:
        data = f.read()
        importData = xmltodict.parse(data)

    with open(offersFile, 'r') as f:
        data = f.read()
        offersData = xmltodict.parse(data)

    return importData, offersData

def removeFiles(workdir):
    if workdir[-1] != '/': workdir += '/'

    importFile = workdir + 'import.xml'
    offersFile = workdir + 'offers.xml'

    if not (os.path.isfile(importFile) and os.path.isfile(offersFile)):
        return

    os.remove(importFile)
    os.remove(offersFile)

def nowTs():
    ts = int(time())
    return u'%d' % ts

def urlify(word):
    ru = re.search(r'[%s-%s%s-%s]+' % (u'а', u'я', u'А', u'Я'), word)
    url = word

    if ru:
        url = ''
        word = word.lower()
        for c in word:
            if c in transliterate:
                url += transliterate[c]
            else:
                url += c

    particles = re.split(r'\s+', url)
    l = len(particles)
    for i in range(l):
        particles[i] = re.sub(r'\W+', r'', particles[i])


    url = '-'.join(particles)

    return u'%s' % url.lower()

def escapeStr(word):
    word = re.escape(word)
    # word = word.replace('\"', '\\"')
    # word = word.replace('\'', '\\\'')
    return word

def importGroups(groupItem, parentId=0):
    existsGroup = ShopCategoriesWhere(externalId=groupItem[_id])

    if len(existsGroup) == 0:
        ShopCategoryInsert(
            groupItem[_id],
            parentId,
            groupItem[_n]
        )
        existsGroup = ShopCategoriesWhere(externalId=groupItem[_id])[0]
    else:
        ShopCategoryUpdate(
            existsGroup[0]['cid'],
            parentId,
            groupItem[_n]
        )
        existsGroup = ShopCategoriesWhere(externalId=groupItem[_id])[0]

    if _groups not in groupItem: return
    if type(groupItem[_groups][_group]) == dict:
        groupItem[_groups][_group] = [
            groupItem[_groups][_group]
        ]

    for groupItemChild in groupItem[_groups][_group]:
        importGroups(groupItemChild, existsGroup['cid'])

def findGoodsOffer(good1CId):
    if type(offersData[_ki][_pp][_offers][_offer]) == dict:
        offersData[_ki][_pp][_offers][_offer] = [
            offersData[_ki][_pp][_offers][_offer]
        ]

    for offerItem in offersData[_ki][_pp][_offers][_offer]:
        ids = offerItem[_id].split('#')
        parentExternalId = ''
        itemExternalId = ''

        if len(ids) == 2:
            parentExternalId = ids[0]
            itemExternalId = ids[1]
        else:
            itemExternalId = ids[0]
        
        if good1CId in offerItem[_id]:
            return offerItem, itemExternalId, parentExternalId

    return False, False, False

def getCategoryId(externalId):
    existsGroup = ShopCategoriesWhere(externalId)

    if len(existsGroup) == 0:
        return 0
    
    return existsGroup[0]['cid']

# APP
args = parseArgs()

connection = pymysql.connect(
    host=args['host'],
    port=args['port'],
    user=args['user'],
    password=args['password'],
    database=args['database'],
    cursorclass=pymysql.cursors.DictCursor
)

cursor = connection.cursor()

importData, offersData = parseFiles(args['workdir'])
if not (importData and offersData):
    exit()

# GROUPS
if type(importData[_ki][_kl][_groups][_group]) == dict:
    importData[_ki][_kl][_groups][_group] = [
        importData[_ki][_kl][_groups][_group]
    ]

for groupItem in importData[_ki][_kl][_groups][_group]:
    importGroups(groupItem)

# GOODS
if type(importData[_ki][_kat][_goods][_good]) == dict:
    importData[_ki][_kat][_goods][_good] = [
        importData[_ki][_kat][_goods][_good]
    ]

for goodsItem in importData[_ki][_kat][_goods][_good]:
    existsItem = ShopGoodsWhere(goodsItem[_id])
    article = goodsItem[_kod] if goodsItem[_art] is None else goodsItem[_art]
    offerItem, externalId, parentExternalId = findGoodsOffer(goodsItem[_id])

    if offerItem is False: continue

    if len(existsItem) == 0:
        ShopGoodsInsert(
            externalId,
            getCategoryId(goodsItem[_ktg]),
            goodsItem[_n],
            article,
            offerItem[_prices][_price][_ppu],
            offerItem[_amount],
            parentExternalId
        )
    else:
        existsItem = existsItem[0]
        ShopGoodsUpdate(
            existsItem['goods_id'],
            getCategoryId(goodsItem[_ktg]),
            goodsItem[_n],
            article,
            offerItem[_prices][_price][_ppu],
            offerItem[_amount],
            parentExternalId
        )

removeFiles(args['workdir'])