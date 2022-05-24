<?php
  header('Content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: *');
  require("utility/database.php");

  $page=@$_POST["start"] ?? 0;
  $size=@$_POST["length"] ?? 10;
  $id = @$_POST["id"] ?? 0;
  $searchVal = $_POST["search"]["value"];
  $totalElements=0;
  $query="SELECT count(id) as conteggio FROM employees";
  if($result=$mysqli->query($query)){
    while($row=$result->fetch_assoc()){
      $totalElements = $row["conteggio"];
    }
  }
  $results = contaRisultati($searchVal);
  $draw = $_SESSION["counter"] + 1;
  $urlDiBase = "http://localhost:8080/index.php";
  $query="select count(id) as tot from employees";

  //-------------------------------------------------------------------------------------
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method

    if($searchVal!=""){
      /*$arrayJSON['data'] = GET_SEARCHFILTER($searchVal,$page*$size, $size );
      $arrayJSON['recordsFiltered'] = $totalElements;
      $arrayJSON['recordsTotal'] = $totalElements;*/
      $tmp=GET_SEARCHFILTER($searchVal,$page*$size, $size );
      $array = array(
        "data" => $tmp,
        "recordsFiltered" => $totalElements,
        "recordsTotal" => $totalElements
      );
      echo json_encode($arrayJSON);
    }else{
      /*$arrayJSON['data'] = GET($page*$size, $size);
      $arrayJSON['recordsFiltered'] = $totalElements;
      $arrayJSON['recordsTotal'] = $totalElements;*/
      $tmp=GET($searchVal,$page*$size, $size );
      $array = array(
        "data" => $tmp,
        "recordsFiltered" => $totalElements,
        "recordsTotal" => $totalElements
      );
      echo json_encode($arrayJSON);
    }
    echo $searchVal;

    /*$data = json_decode(file_get_contents('php://input'), true);

    $query="INSERT INTO employees (id, birth_date, first_name, last_name, gender, hire_date)
    VALUES ('0',
      ".$mysqli->real_escape_string($data['birth_date']).", 
      ".$mysqli->real_escape_string($data['first_name']).", 
      ".$mysqli->real_escape_string($data['last_name']).",
      ".$mysqli->real_escape_string($data['gender']).",
      ".$mysqli->real_escape_string($data['hire_date']).")";

    $mysqli->query($query)
    or die ("<br>Query fallita " . $mysqli->error . " ". $mysqli->error );*/

    //-------------------------------------------------------------------------------------
  }/*else if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    // aggiungere il content-type
    try{

    if(isset($_GET["page"])){
      $page=$_GET["page"];
    }

    if(isset($_GET["size"])){
      $size=$_GET["size"];
    }

    $limitA=$page*$size;
    $totalPages=ceil($totalElements/$size);

    $firstPage = "http://localhost:8080?page=".$page."&size=".$size;

    $totTmp=$totalPages-1;
    $lastPage = "http://localhost:8080?page=" . "?page=" . $totTmp . "&size=" . $size;

    $query="select * from employees ORDER BY id LIMIT ".$limitA.",".$size."";
    if($result=$mysqli->query($query)){
      $i=0;
      while($row=$result->fetch_assoc()){
        $emparray[] = array(
          $i => $row
        );
        $i=$i+1;
      }
    }
    //$employee = array("employees" => $emparray);

    $prev=$page-1;
    $next=$page+1;

    if($page==0){
      $tmpLinks=array(
        "first" => array("href" => $firstPage),
        "last" => array("href" => $lastPage),
        "next" => array("href" => "http://localhost:8080?page=" . "?page=" . $next . "&size=" . $size)
      );
    }else if($page==$totTmp){
      $tmpLinks=array(
        "first" => array("href" => $firstPage),
        "last" => array("href" => $lastPage),
        "prev" => array("href" => "http://localhost:8080?page=" . "?page=" . $prev . "&size=" . $size)
      );

    }else if($page>0 && $page<$totTmp){
      $tmpLinks=array(
        "first" => array("href" => $firstPage),
        "last" => array("href" => $lastPage),
        "next" => array("href" => "http://localhost:8080?page=" . "?page=" . $next . "&size=" . $size),
        "prev" => array("href" => "http://localhost:8080?page=" . "?page=" . $prev . "&size=" . $size)
      );
    }

    $tmp=array(
      "size" => $size,
      "total_Elements" => $totalElements,
      "total_Pages" => $totalPages,
      "number" => $page
    );

    //$emparray[]=["pages" => $tmp];

    $array = array(
      "data" => $emparray,
      "_links" => $tmpLinks,
      "page" => $tmp
    );

    $data = json_encode($array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    
    //header('Content-Type: application/json');
    echo $data;

    }catch(Exception $e){
      echo $e;
    }

    //-------------------------------------------------------------------------------------
  }else if ($_SERVER['REQUEST_METHOD'] === '\DELETE'){

    if(!empty($_GET["id"])){
    $id=$mysqli->real_escape_string($_GET["id"]);

    $query="DELETE FROM employees WHERE employees.id = ".$id."";

    $mysqli->query($query)
    or die ("<br>Query fallita " . $mysqli->error . " ". $mysqli->error );
    }

    //-------------------------------------------------------------------------------------
  }else if ($_SERVER['REQUEST_METHOD'] === 'PUT'){
    
    $data = json_decode(file_get_contents('php://input'), true);

    $query="UPDATE employees
    SET birth_date = ".$mysqli->real_escape_string($data['birth_date']).",
      first_name = ".$mysqli->real_escape_string($data['first_name']).", 
      last_name = ".$mysqli->real_escape_string($data['last_name']).", 
      gender = ".$mysqli->real_escape_string($data['gender']).", 
      hire_date = ".$mysqli->real_escape_string($data['hire_date'])."
    WHERE employees.id = ".$mysqli->real_escape_string($data['id'])."";

    $mysqli->query($query)
    or die ("<br>Query fallita " . $mysqli->error . " ". $mysqli->error );
      
  }

  $mysqli->close()
  or die ("<br>Chiusura connessione fallita " . $mysqli->error . " ". $mysqli->errno);

  //docker run --name some-mysql -v /home/informatica/mysqldata:/var/lib/mysql -v /home/lai2/dump:/dump -e MYSQL_ROOT_PASSWORD=my-secret-pw -d mysql:latest
  //docker exec -it "nome"*/
function GET_SEARCHFILTER($searchValue, $page, $lenght){
  require("utility/database.php");
  $query = "SELECT * FROM employees
  WHERE id like '%$searchValue%'
  OR first_name like '%$searchValue%'
  OR birth_date like '%$searchValue%'
  OR last_name like '%$searchValue%'
  OR hire_date like '%$searchValue%'
  OR gender like '%$searchValue%'
  ORDER BY id LIMIT $page, $lenght";

  $rows = array();

  if($result = $mysqli-> query($query)){
    while($row = $result-> fetch_assoc()){
      $rows[] = $row;
    }
  }

  $mysqli->close();
  return $rows;
}

function GET($page, $lenght){
  require("utility/database.php");
  $query = "SELECT * FROM employees ORDER BY id LIMIT $page, $lenght";
  $rows = array();

  if($result = $mysqli-> query($query)){
    while($row = $result-> fetch_assoc()){
      $rows[] = $row;
    }
  }

  $mysqli->close();
  return $rows;
}
?>