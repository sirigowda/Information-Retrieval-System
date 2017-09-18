
import networkx as nx;

baseFilePath="/home/siri/Downloads/solr-6.5.0/"
G = nx.read_edgelist(baseFilePath + "edgeList.txt", create_using=nx.DiGraph())
pr = nx.pagerank(G, alpha=0.85, personalization=None, max_iter=100, tol=1e-06, nstart=None, weight='weight', dangling=None)

with open(baseFilePath + "PageRankExternal2.txt", 'w') as f:
    for key, value in pr.items():
        key = baseFilePath + "server/solr/ABCNewsData" + key
        f.write('%s=%s\n' % (key, value))
