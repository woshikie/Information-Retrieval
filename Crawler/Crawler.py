import requests
import json
QUERY = 'STAY HOME'
PREFIX = "Dumps/STAY-HOME-RAW-"
API_KEY = '944eecd753384138a859fdb058f869f5'
PAGE_NUMBER = 1
TOTAL = None
COUNT = 0

def getUrl(page, apikey, source):
    return f"https://newsapi.org/v2/everything?q={QUERY}&language=en&pageSize=100&page=1&apiKey={apikey}&sources={source}"
def getFileName():
    global PREFIX
    global COUNT
    return f"{PREFIX}{COUNT}.json"
def writeFie(filename, content):
    file = open(filename, "w+")
    file.write(content)
    file.close()
#Get Sources first
response = requests.get(f'https://newsapi.org/v2/sources?language=en&apiKey={API_KEY}')
status = response.status_code
if status != 200:
    print (response.content)
    print ('API Request for Sources Unsuccessful. Exiting...')
    exit()
data = response.json()
SOURCES = data['sources']
#SOURCES.insert(0, {'id': 'none'})

for source in SOURCES :
    print ('Dumping Source:', PAGE_NUMBER)
    response = requests.get(getUrl(PAGE_NUMBER, API_KEY, source['id']))
    status = response.status_code
    if status != 200:
        print (response.content)
        print ('API Request Unsuccessful. Exiting...')
        exit()
    data = response.json()
    if TOTAL == None:
        TOTAL = data['totalResults']
    articles = data['articles']
    for article in articles:
        COUNT = COUNT + 1
        writeFie(getFileName(), json.dumps(article, indent=4))
        print ('Dumped Article:', COUNT)
    print('\n')
    PAGE_NUMBER = PAGE_NUMBER + 1
