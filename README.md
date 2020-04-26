# Information-Retrieval

Front end - Heroku
[Heroku Demo](https://cz4034-covidwatch.herokuapp.com/)

Back end - Solr on Free Trial VM (until 20 May)

# Files and Folder Structure
- Combined.csv (Combined data from Dumps Folder in csv)
- Combined.json (Combined data from Dumps Folder in json)
- Readme.md
- Classification
  - Files used and outputs for ML Classification
- Crawler
  - Source code of Crawler
- Dumps
  - Raw dumps from Crawler
- Hand Labelled
  - Labelled dumps
- Site
  - Front end source code (PHP)

# Runtime Instruction

## Back End
1. Setup a Solr Server
2. Start a Cloud using `./bin/solr start -c`
3. Create a collection using `./bin/solr create -c CovidWatch -s 2 -rf 2`
  - This creates a Solr Collection with 2 Shards and 2 Replica
4. Index the collection using `./bin/post -c CovidWatch /path/to/data/folder`
  - For an example, I have cloned this repository at my home folder
  - `./bin/post -c CovidWatch ~/Information-Retrieval/Classification/With general news dataset/Combined Predicted.csv`
5. Solr server is ready to be queried

## Front End
1. Point the Document Root to `Site` Folder
2. Change the IP at `index.php line 104` and `query.php line 71` to your Solr server's IP
3. Front end is ready
