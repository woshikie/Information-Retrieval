
import os
import json

#Step 1, get list of files in current directory
files = os.listdir('Dumps/')
str_combined = '['
for file in files:
    if file.endswith('.json'):
        thisFile = open('Dumps/'+file)
        str_combined = str_combined + (thisFile.read().rstrip() + ',')
        thisFile.close()
str_combined = str_combined[:-1] + ']'
parsed = json.loads(str_combined)
CombinedFile = open('Combined.json', 'w+')
CombinedFile.write(json.dumps(parsed, indent=4))
#CombinedFile.write(str_combined)
CombinedFile.close()

print('Done!')