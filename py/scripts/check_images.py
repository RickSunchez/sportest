import os
import pymysql.cursors
from PIL import Image

# projectPath = "/Users/nikitakiselev/Projects/libs/devilbox/data/www/sportest/htdocs"
projectPath = "/home/u1616/sportest.ru/www"

bak1Path = "/Users/nikitakiselev/Projects/libs/devilbox/data/www/sportest/backup_04322164/sportest.ru/www"
bak2Path = "/Users/nikitakiselev/Projects/libs/devilbox/data/www/sportest/bak_06-2023/sportest.ru/www"

# @dev
# host = '127.0.0.1'
# port = 3306
# user = 'root'
# pwd = ''
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

sql = "SELECT * FROM `%s`.`df_images`" % dbName
cursor.execute(sql)
images = cursor.fetchall()

def isFileExists(path):
    return os.path.isfile(path)

def fileIsEmpty(path):
    return os.stat(path).st_size == 0

log = []
ids = []
typedQeury = {}
script = []
scriptTemplate = "scp %s u1616@91.201.52.186:/home/u1616/sportest.ru/www%s"

for row in images:
    tragetId = row["target_id"]
    tragetType = row["target_type"]
    normalPath = projectPath + row["normal"]
    previewPath = projectPath + row["preview"]

    isExists = isFileExists(normalPath) and isFileExists(previewPath)
    isEmpty = isExists and (fileIsEmpty(normalPath) or fileIsEmpty(previewPath))

    isBroken = False
    if isExists and not isEmpty:
        continue
    #     try:
    #         normalImg = Image.open(normalPath)
    #         normalImg.verify()
    #         normalImg.close()
    #     except:
    #         isBroken = True

    #     try:
    #         previewImg = Image.open(previewPath)
    #         previewImg.verify()
    #         previewImg.close()
    #     except:
    #         isBroken = True
    # 
    #     if not isBroken:
    #         continue

    errors = []
    if not isExists:
        errors.append("not found")
    if isEmpty:
        errors.append("empty")
    if isBroken:
        errors.append("broken")

    if tragetType not in typedQeury:
        typedQeury[tragetType] = []
    
    typedQeury[tragetType].append(str(tragetId))

    ids.append(str(tragetId))
    log.append("id: %d, type: %d, error: %s" % (
        int(tragetId),
        int(tragetType),
        " | ".join(errors)
    ))


    # bak1Normal = bak1Path + row["normal"]
    # bak1Preview = bak1Path + row["preview"]
    # isExists = isFileExists(bak1Normal) and isFileExists(bak1Preview)
    # isEmpty = isExists and (fileIsEmpty(bak1Normal) or fileIsEmpty(bak1Preview))
    
    # if isBroken or (isExists and not isEmpty):
    #     script.append( scriptTemplate % (bak1Normal, row["normal"]) )
    #     script.append( scriptTemplate % (bak1Preview, row["preview"]) )

    #     message = "exists: %d, type: %d in bak1: %s, %s" % (int(tragetId), int(tragetType), row["normal"], row["preview"])
    #     if isBroken: message = "# " + message

    #     log.append(message)
    #     continue


    # bak2Normal = bak2Path + row["normal"]
    # bak2Preview = bak2Path + row["preview"]
    # isExists = isFileExists(bak2Normal) and isFileExists(bak2Preview)
    # isEmpty = isExists and (fileIsEmpty(bak2Normal) or fileIsEmpty(bak2Preview))
    
    # if isBroken or (isExists and not isEmpty):
    #     script.append( scriptTemplate % (bak2Normal, row["normal"]) )
    #     script.append( scriptTemplate % (bak2Preview, row["preview"]) )

    #     message = "exists: %d, type: %d in bak2: %s, %s" % (int(tragetId), int(tragetType), row["normal"], row["preview"])
    #     if isBroken: message = "# " + message

    #     log.append(message)

    

with open("results.txt", "w") as f:
    data = "\n".join(log)
    f.write(data)

for key in typedQeury:
    sql = "SELECT * FROM `%s`.`shop_goods` WHERE `goods_id` IN (%s)" % (
        dbName,
        ", ".join(typedQeury[key])
    )
    with open("query_%s.sql" % key, "w") as f:
        f.write(sql)

sql = "SELECT * FROM `%s`.`shop_goods` WHERE `goods_id` IN (%s)" % (
    dbName,
    ", ".join(ids)
)
with open("query.sql", "w") as f:
    f.write(sql)

# with open("script.sh", "w") as f:
#     data = "\n".join(script)
#     f.write(data)
