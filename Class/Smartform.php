<?php

Class Smartform {
    
    private $quotable_arr = array("text", "date", "email");
    
    /**
     * returns first argument ("NULL" if not provided) with quotes if $type is in $quotable_arr. 
     */
    private function _quoted_if_needed($type, $value = 'NULL') 
    {
        $result = "";
        $surrounder="";
        if ($value == 'NULL') 
        {
            $result .= "NULL";
        } else
        {
            if ( in_array($type, $this->quotable_arr))
            {
                $surrounder = "'";
            }
            $result .=  $surrounder . $value . $surrounder . ", ";    
        }
        return $result;
    }
    
    function __construct($content)
    {
        $this->metadata = $content["metadata"];
        $this->entries = $content["entries"];
        $this->hkey = $this->metadata["hidden_key"];
        //$this->editmode = (isset($_GET[$this->hkey]) || isset($_POST[$this->hkey])) ? true : false;
        $this->editmode = false;
        if (isset($_GET[$this->hkey])) 
        {
            $this->editmode = true;
            $this->hvalue = $_GET[$this->hkey];
        }
        if (isset($_POST[$this->hkey])) 
        {
            $this->editmode = true;
            $this->hvalue = $_POST[$this->hkey];
        }
        if ($this->editmode) {
            echo "-----------<br/>editmode<br/>----------";
        } else {
            echo "-----------<br/>createmode<br/>----------";
        }
        
        /* if 'name' not defined for an entry, 'name' set to 'dbfieldname' */
        for ($i = 0; $i< count($this->entries); $i++)
        {
            if (!array_key_exists("name", $this->entries[$i]))
            {
                $this->entries[$i]["name"] = $this->entries[$i]["dbfieldname"];
            }
        }
    }
    
    /**
     * echoes the form in the html page
     */
    function echo_form()
    {
        $action = $this->metadata["action"];
        echo "<form action='$action' method='POST'>\n";
 
        foreach($this->entries as $item)
        {
            $input_name=$item["name"];
            // $localedit = $this->editmode ? "edit" : "non edit";
            // echo $localedit . " | " . $input_name . " | " . $this->hkey . "<BR/>";
            /* let's consider item (not hidden key ) OR (hidden key and editmode) */
            if ($input_name <> $this->hkey || ($input_name == $this->hkey && $this->editmode) ) 
            {
                $label = (isset($item["label"])) ?  $item["label"] : "";
                $id=$item["id"];
                $input_type=$item["type"];
                $has_value = (isset($item["value"])) ? true : false;
                if ($label!=="") echo "\t<label for='$id'>$label</label>\n";
                /** <br/> between label and input default yes | any value --> none */
                if (!isset($item["br_after_label"])) 
                {
                    echo "<br/>\n";
                }
                
                /* input basic handling for email, text, number */
                $input_html = "\t<input ";
                
                /* type = as defined or hidden */
                if ($item["name"] == $this->hkey) 
                {
                    $input_html .= "type='hidden' ";
                   
                } else {
                    $input_html .= "type='$input_type' ";
                }


                $input_html .= "id='$id' "; 
                $input_html .= "name='$input_name' ";
                /* puts value if defined */
                if ( $has_value )
                {
                    $input_value = $item["value"]; 
                    if ($input_type == 'date')
                    {
                        $date = new DateTime($input_value);
                        $input_value = $date->format('Y-m-d');
                    }
                    
                    $input_html .= "value='$input_value'";
                } 
                
                
                /* test hidden field */
                
                $input_html .= "> \n";  


                echo $input_html;
                /* <br/> after input. default 1 | any value --> process */
                if (!isset($item["nb_br_after"])) 
                {
                    echo "<br/>\n";
                } 
                else 
                {
                    for ($i=1 ; $i <= $item["nb_br_after"]; $i++ )
                    {
                        echo "\t<br/>\n";
                    }
                }
                /** reste à traiter les listes */
                
                
                /** euh.. c'est pour quoi ce qui suit ?? */
                // switch ($input_type){

                //     case "text" :
                //         // echo "<label for='$id'>$label</label>";
                //         // echo "<input type='text' id='$id'>";
                //         break;
                //     case "date" :
                //         // echo "<label for='$id'>$label</label>";
                //         // echo "<input type='date' id='$id'>";
                //         break;
                //     case "email" :
                //         // echo "<label for='$id'>$label</label>";
                //         // echo "<input type='email' id='$id'>";
                //         break;	
                // }

                echo "</br>";
        
            }
        }
        
        $submit_value = $this->metadata["submit_value"];
        echo "<input type='submit' value='$submit_value'>";
        echo "</form>";
    }
    


    /**
     * returns a SQL string to insert into tablename values entered in the form inputs
     */
    function insertSQL()
    {
        $tablename = $this->metadata['table_name'];
        $sqlstring = "INSERT INTO $tablename (";    
        
        foreach ($this->entries as $element) 
        {
            if ($element['name'] <> $this->hkey)
            {
                $sqlstring .= $element["dbfieldname"] . ", "; 
            }
        } 
        
        
        $sqlstring = substr($sqlstring, 0, strlen($sqlstring)-2); // comma and space removed for last item
        $sqlstring .= ") VALUES ("; 
        // $quotable_arr = array("text", "date", "email");
        foreach ($this->entries as $element) 
        {
            if ($element['name'] <> $this->hkey)
            {
                $sqlstring .=  $this->_quoted_if_needed($element["type"], $element["value"]);    
            }
        }
        $sqlstring = substr($sqlstring, 0, strlen($sqlstring)-2);
        $sqlstring .= ");";
        return $sqlstring;
    }
     
     /**
     * returns a SQL string to update tablename with values where $condition is met
     */
    function updateSQL()
    {
        $tablename = $this->metadata['table_name'];
        $sqlstring = "UPDATE $tablename SET ";    
        
        foreach ($this->entries as $element)
        {
            if ($element['name'] <> $this->hkey)
            {
                $sqlstring .= $element["dbfieldname"] . " = "; 
                $sqlstring .= $this->_quoted_if_needed($element["type"], $element["value"]); 
            }
        } 

        $sqlstring = substr($sqlstring, 0, strlen($sqlstring)-2); // comma and space removed for last item
        
        
        $condition = $this->hkey . " = " . $this->hvalue;
        $sqlstring .= " WHERE $condition;";
        return $sqlstring;
    }

    /**
     * returns a SQL string to search values into tablename where TABLE_lookup = SEARCH_value
     */
    function seachSQL() // hum hum, est-ce bien utile ???
    {
        $tablename = $this->metadata['table_name'];
        $sqlstring = "SELECT * from $tablename WHERE (";    
        
        foreach ($this->entries as $element) $sqlstring .= $element["dbfieldname"] . ", "; 
        $sqlstring = substr($sqlstring, 0, strlen($sqlstring)-2); // comma and space removed for last item
        $sqlstring .= ") VALUES ("; 
        $quotable_arr = array("text", "date", "email");
        foreach ($this->entries as $element) 
        {
            $surrounder="";
            echo $element['name'] . " | " . $element['type'] . " | " .$element['value'] . " <br/>" ;
            if (!isset($element["value"])) 
            {
                $sqlstring .= "NULL";
            } else
            {
                if ( in_array($element['type'], $quotable_arr))
                {
                    $surrounder = "'";
                }
                $sqlstring .=  $surrounder . $element["value"] . $surrounder . ", ";    
            }
        }
        $sqlstring = substr($sqlstring, 0, strlen($sqlstring)-2);
        $sqlstring .= ");";
        return $sqlstring;
    }


     /** For each entry, adds a new key/value pair with $POST_arr passed as parameter */
    function Complete_with_POST ($POST_arr)
    {
        /** For each entry, we focus on the 'name' key (that can be 'name'!!) and we seach in POST the value where the name of the key is this name  */
        for ($i = 0; $i < count($this->entries); $i++)
        {
            $entry_name = $this->entries[$i]['name'];
            if ($entry_name <> $this->hkey)
            {
                $POST_value = $POST_arr[$entry_name];
                $this->entries[$i]['value'] = $POST_arr[$entry_name];
            }
        }

    }
    
    /** Same as Complete_with_POST, but taking dbfieldname as entry key */
    function Complete_with_PDO ($POST_arr)
    {
        /** For each entry, we focus on the 'name' key (that can be 'name'!!) and we seach in POST the value where the name of the key is this name  */
        for ($i = 0; $i < count($this->entries); $i++)
        {
            $entry_name = $this->entries[$i]['dbfieldname'];
            $POST_value = $POST_arr[$entry_name];
            $this->entries[$i]['value'] = $POST_arr[$entry_name];
        }

    }
}