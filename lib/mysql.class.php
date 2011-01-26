<?php

//        MySQL databases connectivity

class MySQL {
        var $conn;

        function MySQL($host=_MYSQL_HOST, $db=_MYSQL_DB, $user=_MYSQL_USER, $passwd=_MYSQL_PASS){
                $this->conn = mysql_connect($host, $user, $passwd) or die ('Unable to connect to server.');
                mysql_select_db($db, $this->conn) or die ('Unable to select database.');
        }

		// close connection
        function close(){
                mysql_close($this->conn) or die ('Unable to close.');
        }

        // execute single query w/o returning results or if $ret = 'id' return index of new insertion
        function sql_query($query, $ret=""){
                mysql_query($query, $this->conn) or die("query error($query): ". mysql_error($this->conn));
                if ($ret == 'id') {
                    return mysql_insert_id($this->conn);
                }
        }

        function sql_result($query) {
                $dbR = mysql_query($query, $this->conn) or die("query error($query): ". mysql_error($this->conn));
                
								return ($dbR);
        }

				// returning one(first) row object type results from DB for pref. $query
        function sql_object($query) {
                $dbR = mysql_query($query, $this->conn) or die("object query error($query): ". mysql_error($this->conn));
                $resL = mysql_fetch_object($dbR);
                 mysql_free_result($dbR);
                return ($resL);
        }

        // returning one(first) row array type results from DB for pref. $query
        function sql_row($query) {
                $dbR = mysql_query($query, $this->conn) or die("row query error($query): ". mysql_error($this->conn));
                $resL = mysql_fetch_row($dbR);
                 mysql_free_result($dbR);
                return ($resL);
        }

        function sql_array($query) {
                $dbR = mysql_query($query, $this->conn) or die("row query error($query): ". mysql_error($this->conn));
                $resL = mysql_fetch_assoc($dbR);
                 mysql_free_result($dbR);
                return ($resL);
        }

        function sql_all_arrays($query) {
                $dbR = mysql_query($query, $this->conn) or die("query error($query): ". mysql_error($this->conn));
								while ($row = mysql_fetch_assoc($dbR)) {
										$rows[] = $row;
								}                
								return ($rows);
        }

        function sql_all_rows($query) {
                $dbR = mysql_query($query, $this->conn) or die("query error($query): ". mysql_error($this->conn));
								while ($row = mysql_fetch_row($dbR)) {
										$rows[] = $row;
								}                
								return ($rows);
        }

				function sql_all_objects($query, $type="") {
                $dbR = mysql_query($query, $this->conn) or die("all obj. error($query): ". mysql_error($this->conn));

                $i = 1;
                if (mysql_num_rows($dbR) > 0) {
                   if(empty($type)){
                    while ($resL = mysql_fetch_object($dbR)) {
                       $resArray[$i] = $resL;
                       $i++;
                    }
                   } else {
                    while ($resL = mysql_fetch_row($dbR)) {
                       $resArray[$i] = $resL;
                       $i++;
                    }
                   }
                   mysql_free_result($dbR);
                } else {
                 return (false);
                }

                return ($resArray);
        }

        // returning number of rows of selected query
        function sql_num_rows($query) {
                $dbR = mysql_query($query, $this->conn) or die("num rows obj. error($query): ". mysql_error($this->conn));
                return mysql_num_rows($dbR);
        }
}

?>