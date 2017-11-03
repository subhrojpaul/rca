<?php

function setupPDO() {
	//database connection variables, this needs to be changed to read from config files rather than hardcoded
	// guru 30Jul17 10.33 pm moving to new live
        //$host = "rca-dev.clpy2zqi6oie.us-east-1.rds.amazonaws.com";
        $host = "rca-live.cj1mucjfpe4z.ap-south-1.rds.amazonaws.com";
        $user = "rca_dev";
        $pass = "devrca!23";
	
	//$dsn = 'mysql:host='.$host;
	$dsn = 'mysql:dbname=rca_v3;host='.$host;
	//echo "Going for the DB connection";
	//echo "<br>";
	
	try {
		$dbh = new PDO($dsn, $user, $pass);
	} catch (PDOException $e) {
		//echo 'Connection failed: ' . $e->getMessage();
		throw $e;
	}
	//echo "try block done for DB connection";
	//echo "<br>";
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	
	return $dbh;
}

function runQuerySingleRow($dbh, $query, $params){
	
	//$dbh = setupPDO();
	
	//echo "Going for DB prepare";
	//echo "<br>";
	
	try{
		$sth = $dbh->prepare($query);
	} catch(PDOException $e) {
		//echo 'Prepare failed: ' . $e->getMessage();
		throw $e;
	}
	//echo "Going for DB execute";
	//echo "<br>";
	
	try{
		$sth->execute($params);
	} catch(PDOException $e) {
		//echo 'Execute failed: ' . $e->getMessage();
		throw $e;
	}
	//echo "Going for DB fetch";
	//echo "<br>";
	//$result = $sth->fetch();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	
	//echo "Going to return ";
	//echo "<br>";
	return $result;
}

function runInsert($dbh, $query, $params){

	//$dbh = setupPDO();


	//Inserting Supplier
	//$params = array('Supplier_Name' => $supp_name,'Supplier_Vat_Number' =>$supp_vat);
	
	try{
		$sth = $dbh->prepare($query);
	} catch(PDOException $e) {
		//echo 'Prepare failed: ' . $e->getMessage();
		throw $e;
	}
	
	try{
		$sth->execute(array_values($params));
	} catch(PDOException $e) {
		//echo 'Execute failed: ' . $e->getMessage();
		throw $e;
	}
	
	return $dbh->lastInsertId();
	
}

function runUpdate($dbh, $query, $params){

	//$dbh = setupPDO();


	//Inserting Supplier
	//$params = array('Supplier_Name' => $supp_name,'Supplier_Vat_Number' =>$supp_vat);
	//echo "<br>", "going to prepare stmt using query string: ", $query;

	try{
		$sth = $dbh->prepare($query);
	} catch(PDOException $e) {
		//echo 'Prepare failed: ' . $e->getMessage();
		throw $e;
	}

	try{
		$sth->execute(array_values($params));
	} catch(PDOException $e) {
		//echo 'Execute failed: ' . $e->getMessage();
		throw $e;
	}
	return $sth->rowCount();

}


function runQueryAllRows($dbh, $query, $params){

	//$dbh = setupPDO();
		
	try{
		$sth = $dbh->prepare($query);
	} catch(PDOException $e) {
		//echo 'Prepare failed: ' . $e->getMessage();
		throw $e;
	}

	try{
		$sth->execute($params);
	} catch(PDOException $e) {
		//echo 'Execute failed: ' . $e->getMessage();
		throw $e;
	}
	//$result = $sth->fetchAll();
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);

	return $result;
}
function runQueryAllRowsNonAssoc($dbh, $query, $params){

        //$dbh = setupPDO();

        try{
                $sth = $dbh->prepare($query);
        } catch(PDOException $e) {
                //echo 'Prepare failed: ' . $e->getMessage();
                throw $e;
        }

        try{
                $sth->execute($params);
        } catch(PDOException $e) {
                //echo 'Execute failed: ' . $e->getMessage();
                throw $e;
        }
        //$result = $sth->fetchAll();
        $result = $sth->fetchAll();

        return $result;
}


function AllColumns($dbh, $query, $params){

	//$dbh = setupPDO();
		
	try{
		$sth = $dbh->prepare($query);
	} catch(PDOException $e) {
		//echo 'Prepare failed: ' . $e->getMessage();
	}

	try{
		$sth->execute($params);
	} catch(PDOException $e) {
		//echo 'Execute failed: ' . $e->getMessage();
		throw $e;
	}
	$result = $sth->fetch(PDO::FETCH_ASSOC);

	return $result;
}


function hashVal($string, $salt) {
	return sha1(sha1($string).sha1('CTSalt@'.$salt));
}

function AllColumnsMeta($dbh, $query, $params){
        try{
                $sth = $dbh->prepare($query);
        } catch(PDOException $e) {
                throw $e;
        }

        try{
                $sth->execute($params);
        } catch(PDOException $e) {
                throw $e;
        }
        foreach(range(0, $sth->columnCount() - 1) as $column_index){
                $meta[] = $sth->getColumnMeta($column_index);
        }
        return $meta;

}

?>
