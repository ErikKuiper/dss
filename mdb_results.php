<html>
  <body>
 
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
  $destination = '';
  $captaininfo = '';
  $captainfirstname = '';
  $captainlastname = '';
  $formshipname = $_GET['shipname'];
  $formdestination = $_GET['destination'];
  $formcaptainfirstname = $_GET['captainfirstname'];
  $formcaptainlastname = $_GET['captainlastname'];
  $shipnamefilter = '';
  $destinationfilter = '';
  $captainfirstnamefilter = '';
  $captainlastnamefilter = '';
  
  /* Look through the data requested by the user and create the query. */
  
  foreach ($_GET['datareq'] as $datapoint) {
	 if ($datapoint == 'shipname') {
		 $shipname = "?a mdb:schip ?s. ?s mdb:scheepsnaam ?o.";
	 }
	 if ($datapoint == 'shiptype') {
		 $shiptype = "?a mdb:schip ?s. ?s mdb:scheepstype ?t. ?t skos:prefLabel ?q.";
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
  }

  /* Apply a filter to the shipnames or captainnames. */
  
  if ($formshipname !== '') {
     $shipnamefilter = "FILTER (str(?o) = '$formshipname')";
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
		$destination
		$captaininfo
		$captainfirstname
		$captainlastname
  $shipnamefilter
  $destinationfilter
  $captainfirstnamefilter
  $captainlastnamefilter

  }
  LIMIT 10";
  
  /* execute the query */
  $rows = $store->query($query, 'rows'); 

  
    if ($errs = $store->getErrors()) {
       echo "Query errors" ;
       print_r($errs);
    }
 
    /* display the results in an HTML table */
    echo "<table border='1'>
    <thead>
		<th>Aanmonstering</th>
        <th>Shipname</th>
		<th>Shiptype</th>
		<th>Destination</th>
		<th>Captain first name</th>
		<th>Captain last name</th>
    </thead>";

    /* loop for each returned row */
    foreach( $rows as $row ) { 
    print "<tr>".
	"<td><a href='". $row['a'] . "'>" . $row['a']."</a></td>" .
	"<td>" . $row['o']. "</td>".
	"<td>" . $row['q']. "</td>".
	"<td>" . $row['l']. "</td>".
	"<td>" . $row['vn']. "</td>".
	"<td>".	$row['an']. "</td>".
	"</tr>" ;
    }
    echo "</table>" 

  ?>
  </body>
</html>