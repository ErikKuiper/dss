<html>
<style>
.navigation {
	font-family: "Trebuchet MS", Arial, Helvetiva, sans-serif;
	background-color: #4C4C4C;
	overflow: hidden;
	width: 100%;
	}

.navigation a {
	float: left;
	color: #f2f2f2;
	text-align: center;
	padding: 14px 16px;
	text-decoration: none;
	font-size: 17px;
	}

.navigation a:hover{
	background-color: #ddd;
	color: black;
	}

.navigation a.active {
	background-color: #4C50AF;
	color: white;
	}

#results {
	font-family: "Trebuchet MS", Arial, Helvetiva, sans-serif;
	border-collapse: collapse;
	width: 100%;
	}

#results td, #results th {
	border: 1px solid #ddd;
	padding: 8px;
	}
	
#results tr:nth-child(even){background-color: #f2f2f2;}

#results tr:hover {background-color: #ddd;}

#results th {
	padding-top: 12px;
	padding-bottom: 12px;
	text-allign: center;
	background-color: #4C50AF;
	color: white;
	
	
</style>

  <body>
  <div class=navigation>
  <a href="tables.html">MDB</a>
  <a class="active" href="arachangel.html">Archangel</a>
  <a href="Elbing.html">Elbing</a>
  </div>

  <div class=table>

  <?php
  error_reporting(E_ERROR | E_PARSE);
  /* ARC2 static class inclusion */ 
  include_once('semsol/ARC2.php');  
 
  $dbpconfig = array(
  "remote_store_endpoint" => "http://semanticweb.cs.vu.nl/dss/sparql",
   );
 
  $store = ARC2::getRemoteStore($dbpconfig); 
  
  /* Create empty variables to use later if needed. */
  
  $shipname = '';
  $sourcetype = '';
  $homeport = '';
  $destination = '';
  $captainname = '';
  $captainhometown = '';
  $formshipname = $_GET['shipname'];
  $formsourcetype = $_GET['sourcetype'];
  $formhomeport = $_GET['homeport'];
  $formcaptainname = $_GET['captainname'];
  $formcaptainhometown = $_GET['captainhometown'];
  $shipnamefilter = '';
  $sourcetypefilter = '';
  $homeportfilter = '';
  $captainnamefilter = '';
  $captainhometownfilter = '';
  
  /* Look through the data requested by the user and create the query. */
  
  foreach ($_GET['datareq'] as $datapoint) {
	 if ($datapoint == 'shipname') {
		 $shipname = "?myvoy ns1:hasShip ?s. ?s ns1:label ?o.";
	 }
 	 if ($datapoint == 'sourcetype') {
		 $sourcetype = "?myvoy ns1:hasSourceType ?st.";
	 }
 	 if ($datapoint == 'homeport') {
		 $homeport = "?myvoy ns1:hasHarbour ?har. ?har ns1:hasHarbour1 ?hh. ?hh rdfs:label ?hp.";
	 }
  	 if ($datapoint == 'destination') {
		 $destination = "Archangel";
	 }
 	 if ($datapoint == 'captainname') {
		 $captainname = "?myvoy ns1:hasCaptain ?cap. ?cap ns1:label ?c.";
	 }
  	 if ($datapoint == 'captainhometown') {
		 $captainhometown = "?myvoy ns1:hasCaptHometown ?town. ?town rdfs:label ?h.";
	 }
  }

  /* Apply a filter to the shipnames or captainnames. */
  
  if ($formshipname !== '') {
     $shipnamefilter = "FILTER (str(?o) = '$formshipname')";
  }
  if ($formsourcetype !== '') {
     $sourcetypefilter = "FILTER (str(?st) = '$formsourcetype')";
  }
  if ($formhomeport !== '') {
     $homeportfilter = "FILTER (str(?hp) = '$formhomeport')";
  }
  if ($formcaptainname !== '') {
     $captainnamefilter = "FILTER (str(?c) = '$formcaptainname')";
  }
  if ($formcaptainhometown !== '') {
     $captainhometownfilter = "FILTER (str(?n) = '$formcaptainhometown')";
  }

  
  if ($errs = $store->getErrors()) {
     echo "<h1>getRemoteSotre error<h1>" ;
  }

  /* Create query by using the variables creates earlier. */
  
  $query = "
  PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
  PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
  PREFIX dss: <http://purl.org/collections/nl/dss/>   
  PREFIX gzm: <http://purl.org/collections/nl/dss/gzmvoc/>
  PREFIX mdb: <http://purl.org/collections/nl/dss/mdb/>
  PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
  PREFIX ns1: <http://purl.org/collections/nl/dss/archangel/>
  
  SELECT * WHERE {
	    ?myvoy rdf:type ns1:Voyage.
		$shipname
		$sourcetype
		$homeport
		$captainname
		$captainhometown
  $shipnamefilter
  $sourcetypefilter
  $homeportfilter
  $captainnamefilter
  $captainhometownfilter

  }
  LIMIT 10";
  
  /* execute the query */
  $rows = $store->query($query, 'rows'); 

  
    if ($errs = $store->getErrors()) {
       echo "Query errors" ;
       print_r($errs);
    }
 
    /* display the results in an HTML table */
    echo "<table id='results'>
    <thead>
        <th>Shipname</th>
		<th>Journey Type</th>
		<th>Homeport</th>
		<th>Destination</th>
		<th>Captain Name</th>
		<th>Captain Hometown</th>
    </thead>";

    /* loop for each returned row */
    foreach( $rows as $row ) { 
    print "<tr>".
	"<td>" . $row['o']. "</td>".
	"<td>" . $row['st']. "</td>".	
	"<td>" . $row['hp']. "</td>".
	"<td>" . $destination. "</td>".
	"<td>" . $row['c']. "</td>".
	"<td>" . $row['h']. "</td>".
	"</tr>" ;
    }
    echo "</table>" 

  ?>
  </div>
  </body>
</html>