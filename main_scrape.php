<?php
set_time_limit(0); 

include ('simple_html_dom.php');

$dbc = mysqli_connect('localhost', 'ro_usr', 'mazafaka1983_ro', 'manoperacraft_ro')
   or die('Error connecting to mysql server');

// Function that finds the occurences of a substring in a string //
//and returns an array containing the positions of occurences //
function getocurence($chaine,$rechercher)
        {
            $lastPos = 0;
            $positions = array();
            while (($lastPos = strpos($chaine, $rechercher, $lastPos))!== false)
            {
                $positions[] = $lastPos;
                $lastPos = $lastPos + strlen($rechercher);
            }
            return $positions;
        }
//////////////////////////////////////////////////////        

// Function that finds all string between two given strings returning an array of found strings
function get_string_between_loop($string, $start, $end){
        $arr = array();
while (strpos($string,$start) !== false) {
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        $return = substr($string,$ini,$len);
        $begin = strpos($string,$start) + strlen($start);
        $string = substr($string, $begin);
        array_push($arr,$return);
         }
return $arr;
}
/////////////////////////////////////////////////////

// Function that finds only the first string between tho given strings returning that string
function get_string_between($string,$start,$end,$numb) {

        $string = " ".$string;
        $ini = strpos($string,$start,$numb);
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
}
/////////////////////////////////////////////////////

// Function that 
function find_matching ($word,$matches_array) {
$matches = array_filter($matches_array, function($var) use ($word) { return preg_match("/\b$word\b/i", $var); });
$matches_str = implode($matches);
return $matches_str; 
}

function check($blu){
if ($blu == false){$blu = "";}
else {$blu = implode(" | ",get_string_between_loop($blu,"&nbsp;&bull; " , "<"));}
return $blu;
}


$csv = array();
$file = fopen('links.csv', 'r');

while (($result = fgetcsv($file)) !== false)
{
    $csv[] = $result;
}

fclose($file);

$result = call_user_func_array('array_merge', $csv);
echo "<pre>";
print_r($result);
echo "</pre>";


foreach($result as $url){
      $html = file_get_html($url);

$elem_block = $html->find('blockquote');
$fullstring_block = implode($elem_block);

$elem_p = $html->find('blockquote p');
$fullstring_p = implode($elem_p);
$elem_p_str = implode($elem_p);
$elem_p_arr = explode("<p",$elem_p_str);

$elem_tab = $html->find('blockquote table');
$fullstring_tab = implode($elem_tab);

$elem_em = $html->find('blockquote em');
$elem_em_0 = $html->find('blockquote em',0);
$elem_em_1 = $html->find('blockquote em',1);
$elem_em_2 = $html->find('blockquote em',2);
$elem_em_3 = $html->find('blockquote em',3);

$fullstring_em = implode($elem_em);

$em_ocurence = getocurence($fullstring_em,"<em>");
$bull_ocurence = getocurence($fullstring_p, "&bull; ");

///////////////////// MEMBER //////////////////////
if (strpos($fullstring_block,"NSSF MEMBER") !== false) {
     $member = "Yes";
} else {$member = "No";}
//////////////////////////////////////////////////

////////////////////// PHONE /////////////////////
       if (strpos($fullstring_block,"Phone Information") !== false && strpos($fullstring_block,"Main") !== false) {
    $main_phone = get_string_between($fullstring_p,"&bull;"," Main",$bull_ocurence[0]);
}
        else {$main_phone = "no data";}
/////////////////////////////////////////////////

////////////////////// FAX //////////////////////
if (strpos($fullstring_block,"Fax") !== false && strpos($fullstring_block,"Main") !== false) {
    $fax = get_string_between($fullstring_p,"&bull;"," Fax",$bull_ocurence[1]);
}
    elseif (strpos($fullstring_block,"Main") == false && strpos($fullstring_block,"Fax") !== false) {
    $fax = get_string_between($fullstring_p,"&bull;"," Fax",$bull_ocurence[0]);
}
        else {$fax = "no data";}
/////////////////////////////////////////////////

////////////////// MAIL /////////////////////////
if (isset($em_ocurence[2]) && strpos($elem_em_2,"@") == true) {
$mail = mysqli_real_escape_string($dbc,get_string_between($fullstring_em,'">','</a>',$em_ocurence[2]));
} elseif (isset($em_ocurence[3]) && strpos($elem_em_3,"@") == true) {
$mail = mysqli_real_escape_string($dbc,get_string_between($fullstring_em,'">','</a>',$em_ocurence[3]));
} else {$mail = "no data";}
/////////////////////////////////////////////////

////////////////// SITE /////////////////////////
if (isset($em_ocurence[2]) && strpos($elem_em_2,"http") == true) {
$site = mysqli_real_escape_string($dbc,get_string_between($fullstring_em,'">','</a>',$em_ocurence[2]));
} elseif (isset($em_ocurence[3]) && strpos($elem_em_3,"http") == true) {
$site = mysqli_real_escape_string($dbc, get_string_between($fullstring_em,'">','</a>',$em_ocurence[3]));
} else {$site = "no data";}
////////////////////////////////////////////////

$details = find_matching ('Facility Details',$elem_p_arr);
$access = find_matching('Access To Facility',$elem_p_arr);
$shooting = find_matching('Shooting Available',$elem_p_arr);
$competition = find_matching('Competition',$elem_p_arr);
$services = find_matching('Services',$elem_p_arr);
$hunting = find_matching('Hunting - Fishing',$elem_p_arr);
          
$zip_code = get_string_between($fullstring_em,"  ","<",$em_ocurence[1]);
$company = get_string_between($fullstring_block,"<h3>","</h3>");
$company = mysqli_real_escape_string($dbc, $company); 
$street_adress = get_string_between($fullstring_em,"<em>","</em>");
$street_adress = mysqli_real_escape_string($dbc, $street_adress);
$city = get_string_between($fullstring_em,"<em>",",",$em_ocurence[1]);
$city = mysqli_real_escape_string($dbc, $city);
$state = get_string_between($fullstring_em,", ","  ",$em_ocurence[1]);
$state = mysqli_real_escape_string($dbc, $state);
$details = check($details);
$details = mysqli_real_escape_string($dbc, $details);
$access = check($access);
$access = mysqli_real_escape_string($dbc, $access);
$shooting = check($shooting);
$shooting = mysqli_real_escape_string($dbc, $shooting);
$competition = check($competition);
$competition = mysqli_real_escape_string($dbc, $competition);
$services = check($services);
$services = mysqli_real_escape_string($dbc, $services);
$hunting = check($hunting);
$hunting = mysqli_real_escape_string($dbc, $hunting);




$query = "INSERT INTO data_container (Company_name,NSSF_member,State,City,Street_adress,Zip_code,Email,Website,Phone_number,Fax,Facility_Details,Access_To_Facility,Shooting_Available,Competition,Services,Hunting_Fishing) values ('$company','$member','$state','$city','$street_adress','$zip_code','$mail','$site','$main_phone','$fax','$details','$access','$shooting','$competition','$services','$hunting')";

$results = mysqli_query($dbc,$query)
   or die('Error'. $company);



}   

mysqli_close($dbc);

#$html->clear();
#unset($html);
#}
?>
