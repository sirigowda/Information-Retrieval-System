
import networkx as nx;

G = nx.read_edgelist("/home/siri/Downloads/solr-6.5.0/edgeList.txt", create_using=nx.DiGraph())
pr = nx.pagerank(G, alpha=0.85, personalization=None, max_iter=100, tol=1e-06, nstart=None, weight='weight', dangling=None)

with open("/home/siri/Downloads/solr-6.5.0/PageRankExternal2.txt", 'w') as f:
    for key, value in pr.items():
        key = "/home/siri/Downloads/solr-6.5.0/server/solr/ABCNewsData" + key
        f.write('%s=%s\n' % (key, value))