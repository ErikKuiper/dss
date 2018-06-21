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
  <a href="mdb.html">MDB</a>
  <a href="archangel.html">Archangel</a>
  <a class="active" href="elbing.html">Elbing</a>
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
  $shipdate = '';
  $homeport = '';
  $destination = '';
  $productflow = '';
  $productinout = '';
  $productname = '';
  $captainname = '';
  $captainhometown = '';
  $formshipname = $_GET['shipname'];
  $formdestination = $_GET['destination'];
  $formproductname = $_GET['productname'];
  $formcaptainname = $_GET['captainname'];
  $formcaptainhometown = $_GET['captainhometown'];
  $shipnamefilter = '';
  $destinationfilter = '';
  $productnamefilter = '';
  $captainnamefilter = '';
  $captainhometownfilter = '';

  
  /* Look through the data requested by the user and create the query. */
  
  foreach ($_GET['datareq'] as $datapoint) {
	 if ($datapoint == 'shipname') {
		 $shipname = "?myjrn ns2:hasShip ?s. ?s ns2:label ?o.";
	 }
	 if ($datapoint == 'productname') {
		 $productflow = "?myjrn ns2:hasProductflow ?flow.";
		 $productinout = "{ ?flow ns2:hasIncoming ?inc.  ?inc ns2:hasPart ?part. } UNION { ?flow ns2:hasOutgoing ?out.  ?out ns2:hasPart ?part. }";
		 $productname = "?part ns2:hasProduct ?prod. ?prod rdfs:label ?p.";
	 }
 	 if ($datapoint == 'homeport') {
		 $homeport = "Elbing";
	 }
  	 if ($datapoint == 'destination') {
		 $destination = "?myjrn ns2:hasDestination ?dest. ?dest ns2:hasAltName ?h.";
	 }
	 if ($datapoint == 'captainname') {
		 $captainname = "?myjrn ns2:hasCaptain ?cap. ?cap ns2:label ?c.";
	 }
	 if ($datapoint == 'shipdate') {
		 $shipdate = "?myjrn ns2:hasDate ?d.";
	 }
	 if ($datapoint == 'captainhometown') {
		 $captainhometown = "?myjrn ns2:hasCaptain ?cap. ?cap ns2:hasHometown ?town. ?town rdfs:label ?t.";
	 }
  }

  /* Apply a filter to the shipnames or captainnames. */
  
  if ($formshipname !== '') {
     $shipnamefilter = "FILTER (str(?o) = '$formshipname')";
  }
  if ($formdestination !== '') {
     $destinationfilter = "FILTER (str(?h) = ' $formdestination')";
  }
  if ($formproductname !== '') {
     $productnamefilter = "FILTER (str(?p) = '$formproductname')";
  }
  if ($formcaptainname !== '') {
     $captainnamefilter = "FILTER (str(?c) = '$formcaptainname')";
  }
  if ($formcaptainhometown !== '') {
     $captainhometownfilter = "FILTER (str(?t) = '$formcaptainhometown')";
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
  PREFIX ns2: <http://purl.org/collections/nl/dss/elbing/>
  
  SELECT * WHERE {
	    ?myjrn rdf:type ns2:Journey.
		$shipname
		$shipdate
		$destination
		$productflow
		$productinout
		$productname
		$captainname
		$captainhometown
  $shipnamefilter
  $destinationfilter
  $productnamefilter
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
		<th>Date</th>
		<th>Homeport</th>
		<th>Destination</th>
		<th>Product Name</th>
		<th>Captain Name</th>
		<th>Captain Hometown</th>
    </thead>";

    /* loop for each returned row */
    foreach( $rows as $row ) { 
    print "<tr>".
	"<td>" . $row['o']. "</td>".
	"<td>" . $row['d']. "</td>".
	"<td>" . $homeport. "</td>".
	"<td>" . $row['h']. "</td>".
	"<td>" . $row['p']. "</td>".
	"<td>" . $row['c']. "</td>".
	"<td>" . $row['t']. "</td>".
	"</tr>" ;
    }
    echo "</table>" 

  ?>
  </div>
  </body>
</html>