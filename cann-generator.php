<?php
/**
 *  A Cann Table Generator Script
 *  Requires PHP server with cUrl extension
 *  Some PHP skills to massage data through
 *
 *  optional: output has TableSorter class which is a jQuery plugin to allow sorting & multi-sorting of columns, and zebra theme.
 *
 *  If you don't know what a Cann table is, you should probably move on.
 */

// configuration: set the url here and then some marks on the page to reduce processing the entire thing.

$url    =   'the data source';
$start  =   strpos($content,"some marker in the html");
$end    =   strpos($content,"some other marker in the html");

/**
 * You'll also need to massage the data through the process  by deciding which columns of the data exist
 * and what you want to return as the points, the second column would be team name in most cases
 * and would ordinarily be in column 1 or 2
 */


/******************************************************************************/

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //Set curl to return the data instead of printing it to the browser.
            curl_setopt($ch, CURLOPT_URL, $url);
            $raw = curl_exec($ch);
            curl_close($ch);

            // tidy up html by removing new lines, blanks, spaces and other junk
            $newlines=array("\t","\n","\r","\x20\x20","\0","\x0B","<br/>","<p>","</p>","<br>");
            $content=str_replace($newlines, "", html_entity_decode($raw));
            /******************************************************************************/
            /*table-stats*/

            $table = substr($content,$start,$end-$start);
            preg_match_all("|<tr(.*)</tr>|U",$table,$rows);
            $rank=array();


            foreach ($rows[0] as $row)
            {
                if ((strpos($row,'<th')===false)) :
                    // pos, team, pld, gd, pts of a normal PL table for example.
                    preg_match_all("|<td(.*)</td>|U",$row,$cells);
                    $f0 = strip_tags($cells[0][0]); //team
                    $f1 = strip_tags($cells[0][1]); // played
                    $f12 = strip_tags($cells[0][12]); // points

                    if(isset($f1) && $f1 <>'') :
                        array_push($rank,$f12,"$f0 ($f1)");
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

            // we then sort it
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

            // make sexy table for your pleasure thank you please.

            print '<table class="tablesorter">
                    <thead>
                    <tr>
                    <th>Points</th>
                    <th>Team</th>
                    </tr>
                    </thead>
                    <tbody>';
            foreach($output as $key => $value) {
                print '<tr><td>'.$key.'</td><td>'.$output[$key].'</td></tr>';
            }
            print '</table>';

