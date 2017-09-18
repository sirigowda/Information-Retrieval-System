<?php
// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');
include ("SpellCorrector.php");
ini_set('memory_limit', '4024M');
ini_set("display_errors", 0);
ini_set('MAX_EXECUTION_TIME', 3000);
$limit = 10;
$algo="";
$additionalParameters=false;
$query =isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results =false;
$fileURLMap=array();
$handle=fopen("/home/siri/Downloads/ABCNewsData/mapABCNewsDataFile.csv","r");
if ($handle!== FALSE) 
{
    while (($data = fgetcsv($handle, ",")) !== FALSE) 
	{	
        $fileURLMap[$data[0]] = $data[1];
    }
}else
{
	echo "Could not open file";
}
fclose($handle);

if($query)
{
// The Apache Solr Client library should be on the include path
// which is usually most easily accomplished by placing in the
// same directory as this script ( . or current directory is a default
// php include path entry in the php.ini)
require_once('Apache/Solr/Service.php');
// create a new solr service instance -host, port, and corename
// path (all defaults in this example)
$solr =new Apache_Solr_Service('localhost', 8983, '/solr/test/');
// if magic quotes is enabled then stripslashes will be needed
if(get_magic_quotes_gpc() ==1)
{
$query =stripslashes($query);
}
}

?>
<html>
<head>
<title>PHP Solr Client Example</title>
</head>
<body>
<form accept-charset="utf-8" method="get">
<label for="q">Search:</label>
<input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
Choose Algorithm:
<input type="radio" name="algo" value="lucene">Lucene
<input type="radio" name="algo" value="pagerank">PageRank
<input type="submit"/>
</form>
<?php
function make_links_clickable($text){
    return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $text);
}
$algo = $_GET["algo"];
if($algo=="lucene"){
echo "LUCENE";
try
{
$additionalParameters=array('fl'=>'id,title,og_url,description');
$results =$solr->search($query, 0, 10,$additionalParameters);
}
catch(Exception $e)
{
// in production you'd probably log or email this error to an admin
// and then show a special message to the user but for this example
// we're going to show the full exception 
die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
}

}else if($algo=="pagerank"){
echo "PAGERANK";
try
{
$additionalParameters=array('fl'=>'id,title,og_url,description','sort'=>'pageRankFile desc');
$results=$solr->search($query, 0, 10, $additionalParameters);
}
catch(Exception $e)
{
// in production you'd probably log or email this error to an admin
// and then show a special message to the user but for this example
// we're going to show the full exception 
die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
}
}

$forcecheck = isset($_REQUEST['forcecheck']) ? $_REQUEST['forcecheck'] : false;
echo SpellCorrector::correct($query);
	if(!empty($_GET["algo"]) && $query!=SpellCorrector::correct($query) && $forcecheck==false)
	{
		
		$algo = $_GET["algo"];
		$query_new = SpellCorrector::correct($query);
		$query_link = 'http://localhost/index2.php?q='.$query_new.'&algo='.$algo;
		$query_old = 'http://localhost/index2.php?q='.$query.'&algo='.$algo;
		if(strcmp ($_GET["algo"],"lucene")==0)
		{
			$results = $solr->search($query_new, 0, $limit);
		}
		else if(strcmp ($_GET["algo"],"solr")==0)
		{
			$results = $solr->search($query_new, 0, $limit, $additionalParameters);
		}
		echo '<font size="5">Showing results for <a href='.$query_link.'>' .$query_new. '</a></font>';
		echo '<br>Search instead for <a href="?q='.$query.'&algo='.$algo.'&forcecheck=true" id="searchagain">'.$query.'</span><br><br>';
	}
	if(!empty($_GET["algo"]) && $query!=SpellCorrector::correct($query) && $forcecheck)
	{
		
$algo = $_GET["algo"];
if($algo=="lucene"){
try
{
$additionalParameters=array('fl'=>'id,title,og_url,description');
$results =$solr->search($query, 0, 10,$additionalParameters);
}
catch(Exception $e)
{
// in production you'd probably log or email this error to an admin
// and then show a special message to the user but for this example
// we're going to show the full exception 
die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
}
}else if($algo=="pagerank"){
try
{
$additionalParameters=array('fl'=>'id,title,og_url,description','sort'=>'pageRankFile desc');
$results=$solr->search($query, 0, 10, $additionalParameters);
}
catch(Exception $e)
{
// in production you'd probably log or email this error to an admin
// and then show a special message to the user but for this example
// we're going to show the full exception 
die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
}
}
}
// display results
if($results)
{
$total =(int) $results->response->numFound;
$start =min(1, $total);
$end =min($limit, $total);
?>
<div>Results <?php echo $start; ?> -<?php echo $end;?> of <?php echo $total; ?>:</div>
<ol>
<?php
// iterate result documents
foreach($results->response->docs as $doc)
{
?>
<li>
<table style ="border: 1px solid black; text-align: left">
<?php
// iterate document fields / values
foreach($doc as $field => $value)
{
if($field=='og_url'){
?>
<tr>
<th><?php echo htmlspecialchars ($field, ENT_NOQUOTES, 'utf-8'); ?></th>
<th><a target="_blank" href="<?php echo htmlspecialchars($fileURLMap[explode("/", $doc->id)[count(explode("/", $doc->id))-1]], ENT_NOQUOTES, 'utf-8');?>"><?php echo htmlspecialchars($fileURLMap[explode("/", $doc->id)[count(explode("/", $doc->id))-1]], ENT_NOQUOTES, 'utf-8');?></a></th>
</tr>
<?php
}else if($field=='title'){
?>
<tr>
<th><?php echo htmlspecialchars ($field, ENT_NOQUOTES, 'utf-8'); ?></th>
<th><a target="_blank" href="<?php echo htmlspecialchars($fileURLMap[explode("/", $doc->id)[count(explode("/", $doc->id))-1]], ENT_NOQUOTES, 'utf-8');?>"><?php echo htmlspecialchars($doc->title, ENT_NOQUOTES, 'utf-8');?></a></th>
</tr>
<?php }else{
?>
<tr>
<th><?php echo htmlspecialchars ($field, ENT_NOQUOTES, 'utf-8'); ?></th>
<td><?php echo htmlspecialchars ($value, ENT_NOQUOTES, 'utf-8'); ?></td>
</tr>
<?php
}}
?>
</table>
</li>
<?php
}
?>
</ol>
<?php
}
?>
</body>
<link
href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
rel="stylesheet"></link>
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
	$(function() {
		var URL_PREFIX = "http://localhost:8983/solr/myexample/suggest?indent=on&q=";
		var URL_SUFFIX = "&wt=json";
		$("#searchBox").autocomplete({
			source : function(request, response) {
				var URL = URL_PREFIX + $("#searchBox").val() + URL_SUFFIX;
				$.ajax({
					url : URL,
					success : function(data) {
						var docs = JSON.stringify(data.suggest.suggest	);
						var jsonData = JSON.parse(docs);
						for(var value in jsonData){
							value=jsonData[value].suggestions;
							if ($("#searchBox").val().length > 1)
								value=value.slice(0, 7);
							response($.map(value, function(value1, key1) {
								return {
									label: value1.term
								}
							}));
						};
					},
					dataType : 'jsonp',
					jsonp : 'json.wrf'
				});
			},
			minLength : 1
		})
	});
</script>
</html>
