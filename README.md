## INFORMATION RETREIVAL SYSTEM

#### STEPS TO CREATE SEARCH ENGINE

1.	Install Ubuntu 16.04 LTS (alternatively use Windows / Mac OS X) and install Solr on it. Then download the dataset(available online) for ABCNews and place it in the solr folder.

2.	Start the Solr server using the command **bin/solr start**. Create a core with name “abc” using the command **bin/post  create -c abc**. 

3. Enabling Spell Check

  Create a field “_text_” as shown below:
   
  `<field name="_text_" type="text_en_splitting" indexed="true" stored="false" multivalued="true"/>`
   
  This field contains all the text from other fields in Solr, as we have specified the copyField source as “*”. We will be using
   this field as the source for our spell check. To enable this Spellcheck component specify the source of the terms in the                solrconfig.xml file, which is present in the conf folder of the core.
 
 `<searchComponent class="solr.SpellCheckComponent" name="spellcheck">`<br />
  &nbsp;&nbsp;`<lst name="spellchecker">`<br />
   &nbsp;&nbsp;&nbsp;`<str name="classname">solr.IndexBasedSpellChecker</str>`<br />
  &nbsp;&nbsp;&nbsp;`<str name="spellcheckIndexDir">./spellchecker</str>`<br />
  &nbsp;&nbsp;&nbsp;`<str name="field">_text_</str>`<br />
  &nbsp;&nbsp;&nbsp;`<str name="buildOnCommit">true</str>`<br />
  &nbsp;&nbsp;`</lst>`<br />
 `<searchComponent>`

  The first element defines the searchComponent to use the solr.SpellCheckComponent. The classname is the specific
  implementation of the SpellCheckComponent, in this case solr.IndexBasedSpellChecker. buildOnCommit defines whether to build the spell   check index at every commit (that is, every time new documents are added to the index). It is optional, and can be omitted if you       would rather set it to false. In the figure above, we have specified “_text_” to be the field that is the source of our spelling         suggestions.

  We then	add a spellcheck component to the requestHandler “select”. That is, for every query, spellcheck is enabled. We have           specified “spellcheck” to be “true” by default, by including it within the “defaults” list.

4.	Use Apache Tika and in built post tool to index the html files using the command **bin/post -c abc -filetypes html crawl_data_folder**. 

5.	Compute page rank for extracted files by running info.java program. URLFileMap and FileURLMap are created by parsing the mapping files using Apache Commons CSV library. JSoup library functions are used to create edgelist, by creating edges between files that have links connecting them.

6.	Run PageRank.py to load the web graph from the edgelist created and used the NetworkX library to compute the page rank. Used undirected graph for pagerank calculation. Configurations used to compute the page rank as as follows:  **pagerank(G, alpha=0.85, personalization=None, max_iter=30, tol=1e-06, nstart=None, weight='weight', dangling=None)**

7.	Rename the page rank file as “external_pageRankFile.txt” and place it in the data folder of the core. Add a search component to solrconfig.xml and make it to use the SuggestComponent. After adding the search component, a request handler must be added to solrconfig.xml. This request handler works the same as any other request handler, and allows you to configure default parameters for serving suggestion requests. The default values for the number of suggestions is set to 5, by defining the value for element “suggest.count”, the default dictionary to be used is defined by “suggest.dictionary”.

8. The core must then be reloaded. We can search using either Lucene or PageRank algorithms through the SolrUI by changing the parameters, i.e. setting “sort” parameter to “pageRankFile desc” will allow us to search using page rank algorithm. The default search algorithm used by Solr is Lucene.

9.	 Install the Apache2 web server and the solr-php-client-API client. SearchEngine.php contains a search box and two radio buttons. The search box is used to enter the query to be searched, by sending it to Solr, and the radio buttons are used to select the algorithm based on which the search must be performed. The options for the radio button is Lucene and PageRank. The PHP script then parses the result and formats and displays the results. 
 
Note: Pages have higher page rank values when they have higher number of incoming links or when they have incoming links from important websites, i.e. websites with a higher page rank.
