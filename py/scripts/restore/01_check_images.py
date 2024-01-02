import os
import sys
import pymysql.cursors

# python2 01_check_images.py dev
# python2 01_check_images.py prod

args = sys.argv[1:]

env = ''
if len(args) > 0:
    env = args[0]

if env == 'dev':
    projectPath = '/Users/nikitakiselev/Projects/libs/devilbox/data/www/sportest/htdocs'
    host = '127.0.0.1'
    port = 3306
    user = 'root'
    pwd = ''
    dbName = 'lodki'
elif env == 'prod':
    projectPath = '/home/u1616/sportest.ru/www'
    host = 'localhost'
    port = 3306
    user = 'u1616'
    pwd = 'ce9zEAyyWu342'
    dbName = 'u1616_df2'
else:
    print('dev or prod')
    exit()

brokenPathsFile = 'data/01_brokenPaths.txt'

connection = pymysql.connect(
    host=host,
    port=port,
    user=user,
    password=pwd,
    database=dbName,
    cursorclass=pymysql.cursors.DictCursor
)

cursor = connection.cursor()

sql = 'SELECT * FROM `%s`.`df_images` WHERE `target_type` = 53' % dbName
cursor.execute(sql)
images = cursor.fetchall()

def isFileExists(path):
    return os.path.isfile(path)

def fileIsEmpty(path):
    return os.stat(path).st_size == 0

brokenPaths = []

for row in images:
    tragetId = row['target_id']
    tragetType = row['target_type']
    normalPath = projectPath + row['normal']
    previewPath = projectPath + row['preview']

    normalExists = isFileExists(normalPath) and (not (fileIsEmpty(normalPath)))
    previewExists = isFileExists(previewPath) and (not (fileIsEmpty(previewPath)))

    if not normalExists:
        brokenPaths.append(row['normal'])
    if not previewExists:
        brokenPaths.append(row['preview'])

with open(brokenPathsFile, 'w') as f:
    f.write('\n'.join(brokenPaths))
