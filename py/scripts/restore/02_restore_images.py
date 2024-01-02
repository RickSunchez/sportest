import os
import sys
import pymysql.cursors

# python2 02_restore_images.py dev
# python2 02_restore_images.py prod

brokenPathsFile = 'data/01_brokenPaths.txt'
restoreScriptFile = 'data/02_restoreScript.sh'
restoreFolders = [
    "/Users/nikitakiselev/Projects/libs/devilbox/data/www/sportest/htdocs",
    "/Users/nikitakiselev/Projects/libs/devilbox/data/www/sportest/backup_04322164/sportest.ru/www",
    "/Users/nikitakiselev/Projects/libs/devilbox/data/www/sportest/bak_06-2023/sportest.ru/www"
]

args = sys.argv[1:]

env = ''
if len(args) > 0:
    env = args[0]

if env == 'dev':
    projectPath = '/Users/nikitakiselev/Projects/libs/devilbox/data/www/sportest/htdocs'
    restoreScripttemplate = 'cp %s %s'
elif env == 'prod':
    projectPath = '/home/u1616/sportest.ru/www'
    restoreScripttemplate = 'scp -o ConnectTimeout=5 %s u1616@91.201.52.186:%s'
else:
    print('dev or prod')
    exit()

brokenPaths = []
with open(brokenPathsFile, 'r') as f:
    data = f.read()
    brokenPaths = data.split('\n')

if len(brokenPaths) == 0:
    print('empty')
    exit()

def isFileExists(path):
    return os.path.isfile(path)

def fileIsEmpty(path):
    return os.stat(path).st_size == 0

def checkPathInFolders(path):
    global restoreFolders

    for projectPath in restoreFolders:
        filePath = projectPath + path

        if isFileExists(filePath) and (not fileIsEmpty(filePath)):
            return filePath

    return None

logScriptTemplate = 'echo -e "%s\\n" >> scp_log.txt \n%s 2>>scp_log.txt'
scripts = []
for bPath in brokenPaths:
    restoredPath = checkPathInFolders(bPath)
    if restoredPath is None:
        continue

    _from = restoredPath
    _to = projectPath + bPath
    script = restoreScripttemplate % (_from, _to)

    logScript = logScriptTemplate % (bPath, script)
    scripts.append(logScript)

with open(restoreScriptFile, 'w') as f:
    f.write('\n'.join(scripts))