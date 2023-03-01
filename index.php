  <?php
$date_to =require_once(__DIR__ . '/Introvert/autoload.php');  
  


Introvert\Configuration::getDefaultConfiguration()->setHost('https://api.s1.yadrocrm.ru/tmp');
$date_from = htmlspecialchars($_GET['date_from']);
$date_to = htmlspecialchars($_GET['date_to']);  
 
function getClients() {
    return [ 
        [
            "id" => 1,
            "name" => "intrdev",
            "api" => "23bc075b710da43f0ffb50ff9e889aed"
        ],
        [
            "id" => 2,
            "name" => "artedegrass0",
            "api" => "",
        ],
    ];
}



function registrClients($clientsArr){
    $full_budget = 0;
 echo '<table> <tr> <th>ID клиента в Ядре</th> <th>Название клиента</th> <th>Сумма сделок клиента за период</th></tr>'; 
   foreach ($clientsArr as $key=> $client) {
    Introvert\Configuration::getDefaultConfiguration()->setApiKey('key', $client['api']); 
    $api = new Introvert\ApiClient();  
    $client_id = $client['id'];
    $name = $client['name']; 
    $full_budget += getvalidresult($api,$client_id,$name);
}  
echo '</table>'  . '<br>Сумма по всем клиентам за период:'. $full_budget . '<br>';
}

function getvalidresult($api,$client_id,$name){ 
    $crm_user_id = array();  
    $status = 142;  
    $id = array();  
    $ifmodif = ""; 
    $sucsess_budget =0;  
    try {
    $count = 200;  
    $offset = 1; 
    $res_count  = $count; 
    while((int) $res_count == (int)$count){ 
            $result = $api->lead->getAll($crm_user_id, $status, $id, $ifmodif, $count, $offset);
            foreach ($result['result'] as   $key => $value) {
                
            if ( ($value['date_close'] >= $date_from) && ($value['date_close'] <= $date_to) )  { 
                $sucsess_budget += (int)$value['price'];  
                $full_budget += (int)$value['price']; 
                                    }; 
        } 
                $offset+=  $count; 
    $res_count = $result['count'];

    } 
       echo '<tr> <td>'
       . $client_id. '</td><td>'
       . $name .'</td><td>'
       . $sucsess_budget . '</td></tr>'; 
    return  $full_budget;
} catch (Exception $e) {
        echo  '<tr> <td>' . $client_id. '</td><td>' . $name .'</td><td>'.$e->getMessage() . '<td><tr>';
    } 
  
}

 
registrClients(getClients());
   
?>
