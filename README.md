# Search engine
STEPS TO COMPLETE ASSIGNMENT:

1.	Installed Ubuntu 16.04 LTS and installed Solr on it. Then downloaded the dataset for ABCNews from Google Drive folder provided and      placed it in the solr folder.
2.	Started the Solr server using the command “bin/solr start”. Created a core with name “abc” using the command “bin/post  create -c abc”. 
3.	Made the required changes in the managed-schema file in the conf as mentioned in the assignment so as to add required fields.
4.	Used Apache Tika and in built post tool to index the html files using the command “bin/post -c abc -filetypes html crawl_data_folder”. 
5.	Computed page rank for extracted files by writing and running a Java program. Parsed the mapping files given to create URLFileMap and FileURLMap using Apache Commons CSV library. Created edgelist by creating edges between files that have links connecting them, using JSoup library functions.
6.	Wrote and ran a python program to load the web graph from the edgelist created and used the NetworkX library to compute the page rank. Used undirected graph for pagerank calculation. Configurations used to compute the page rank as as follows:  pagerank(G, alpha=0.85, personalization=None, max_iter=30, tol=1e-06, nstart=None, weight='weight', dangling=None) 
7.	Renamed the page rank file as “external_pageRankFile.txt” and placed it in the data folder of the core. Added fields to the managed-schema as specified in the assignment description to refer to this ranking and added listeners to the solrconfig.xml file so that the index will be able to access the page rank file. The core is then reloaded. We can then search using either Lucene or PageRank algorithms through the SolrUI by changing the parameters, i.e. setting “sort” parameter to “pageRankFile desc” will allow us to search using page rank algorithm. The default search algorithm used by Solr is Lucene.
8.	 Installed the Apache2 web server, php and the solr-php-client-API client. Created a webpage using PHP containing a search box and two radio buttons. The search box is used to enter the query to be searched by sending it to Solr and the radio buttons are used to select the algorithm based on which the search must be performed. The options for the radio button is Lucene and PageRank. The PHP script then parses the result and formats and displays the results. 
 
Note: Pages have higher page rank values when they have higher number of incoming links or when they have incoming links from important websites, i.e. websites with a higher page rank.
