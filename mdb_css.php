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
  <a class="active" href="tables.html">MDB</a>
  <a href="arachangel.html">Archangel</a>
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
  $shiptype = '';
  $homeport = '';
  $destination = '';
  $captaininfo = '';
  $captainfirstname = '';
  $captainlastname = '';
  $captainhome = '';
  $formshipname = $_GET['shipname'];
  $formshiptype = $_GET['shiptype'];
  $formhomeport = $_GET['homeport'];
  $formdestination = $_GET['destination'];
  $formcaptainfirstname = $_GET['captainfirstname'];
  $formcaptainlastname = $_GET['captainlastname'];
  $formcaptainhome = $_GET['captainhome'];
  $shipnamefilter = '';
  $shiptypefilter = '';
  $homeportfilter = '';
  $destinationfilter = '';
  $captainfirstnamefilter = '';
  $captainlastnamefilter = '';
  $captainhomefilter = '';
  
  /* Look through the data requested by the user and create the query. */
  
  foreach ($_GET['datareq'] as $datapoint) {
	 if ($datapoint == 'shipname') {
		 $shipname = "?a mdb:schip ?s. ?s mdb:scheepsnaam ?o.";
	 }
	 if ($datapoint == 'shiptype') {
		 $shiptype = "?a mdb:schip ?s. ?s mdb:scheepstype ?t. ?t skos:prefLabel ?q.";
	 }
	 if ($datapoint == 'homeport') {
		 $homeport = "?a mdb:ligplaats ?x. ?x rdfs:label ?y.";
	 }
	 if ($datapoint == 'destination') {
		 $destination = "?a mdb:bestemming ?b. ?b rdfs:label ?l.";
	 }
	 if ($datapoint == 'captainfirstname') {
		 $captainfirstname = "?pers mdb:voornaam ?vn.";
		 $captaininfo = "?pc mdb:rang mdb:rang-schipper. ?pc mdb:persoon ?pers.";
	 }
	 if ($datapoint == 'captainlastname') {
		 $captainlastname = "?pers mdb:achternaam ?an.";
		 $captaininfo = "?pc mdb:rang mdb:rang-schipper. ?pc mdb:persoon ?pers.";
	 }
	 if ($datapoint == 'captainhome') {
		 $captainhome = "?pers mdb:woonplaats ?h. ?h rdfs:label ?z.";
		 $captaininfo = "?pc mdb:rang mdb:rang-schipper. ?pc mdb:persoon ?pers.";
	 }
  }

  /* Apply a filter to the shipnames or captainnames. */
  
  if ($formshipname !== '') {
     $shipnamefilter = "FILTER (str(?o) = '$formshipname')";
  }
  if ($formshiptype !== '') {
	  $shiptypefilter = "FILTER (str(?q) = '$formshiptype')";
  }
  if ($formhomeport !== '') {
	  $homeportfilter = "FILTER (str(?y) = '$formhomeport')";
  }
  if ($formdestination !== '') {
	  $destinationfilter = "FILTER (str(?l) = '$formdestination')";
  }
  if ($formcaptainfirstname !== '') {
	  $captainfirstnamefilter = "FILTER (str(?vn) = '$formcaptainfirstname')";
  }  
  if ($formcaptainlastname !== '') {
	  $captainlastnamefilter = "FILTER (str(?an) = '$formcaptainlastname')";
  }
  if ($formcaptainhome !== '') {
	  $captainhomefilter = "FILTER (str(?z) = '$formcaptainhome')";
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
  
  SELECT * WHERE {
	    ?pc mdb:has_aanmonstering ?a.
		$shipname
		$shiptype
		$homeport
		$destination
		$captaininfo
		$captainfirstname
		$captainlastname
		$captainhome
  $shipnamefilter
  $shiptypefilter
  $homeportfilter
  $destinationfilter
  $captainfirstnamefilter
  $captainlastnamefilter
  $captainhomefilter

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
		<th>Shiptype</th>
		<th>Homeport</th>
		<th>Destination</th>
		<th>Captain first name</th>
		<th>Captain last name</th>
		<th>Captain home</th>
    </thead>";

    /* loop for each returned row */
    foreach( $rows as $row ) { 
    print "<tr>".
	"<td>" . $row['o']. "</td>".
	"<td>" . $row['q']. "</td>".
	"<td>" . $row['y']. "</td>".
	"<td>" . $row['l']. "</td>".
	"<td>" . $row['vn']. "</td>".
	"<td>" . $row['an']. "</td>".
	"<td>" . $row['z']. "</td>".
	"</tr>" ;
    }
    echo "</table>" 

  ?>
  </div>
  </body>
</html>