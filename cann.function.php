
<?php
/**
*
* print $method->CannNamStyle($raw_html_table,$column_heading,$column_number);
*
*
function CannNamStyle($content,$table_key,$column) {
		$column = (int) $column;
		$newlines = array("\t","\n","\r","\x20\x20","\0","\x0B","<br/>","<p>","</p>","<br>");
		$raw = str_replace($newlines, "", html_entity_decode($content));
		$table = str_replace('class=','',$raw);
		preg_match_all("|<tr(.*)</tr>|U",$table,$rows);
		$rank=array();
		foreach ($rows[0] as $row) {  
			            if ((strpos($row,'<th')===false)) :
			                // pos, team, pld, gd, pts
				        preg_match_all("|<td(.*)</td>|U",$row,$cells);
						$f0 = strip_tags($cells[0][0]); //team
						$f1 = strip_tags($cells[0][1]); // played
						$fx = strip_tags($cells[0][$column]);
				              if(isset($f1) && $f1 <>'') :
					         array_push($rank,$fx,"$f0 ($f1)");
				               endif;
				   endif;
	     } // end foreach
		#################################################################
		$output = array();
		foreach($rank as $key => $value) {
		// we want value 1 as key, value 2 as value. modulus baby.
		  if($key % 2 > 0) { //every second item
			$index = $rank[$key-1];
		// we cannot have duplicate keys so we concatenate values to the key.
		if(array_key_exists($index,$output)) {
			$output[$index] .= ', '.$value;
		}
		else {
			$output[$index] = $value;
		}
		  }
		}
		@krsort($output);
		$new=array();
		reset($output);
		$first=key($output);
		reset($output);
		$last = key( array_slice( $output, -1, 1, TRUE ) );
		$i = $last;
		while ($i < $first):
			if (!@array_key_exists($output)):
					$new[$i]=" ";
			endif;
			$i++;
		endwhile;
		reset($output);
		$output= $output + $new;
		@krsort($output);
		#################################################################
		// make sexy
		$dollar  = "<div class='table-container-small'><table class='tablesorter'>";
		$dollar .= "<thead><tr><th>{$table_key}</th><th>Team</th></tr></thead><tbody>";
		foreach($output as $key => $value) {
				$dollar .= "<tr><td>{$key}</td><td>{$output[$key]}</td></tr>";
		}
		$dollar .= "</tbody></table></div>";
		return $dollar;
	}
