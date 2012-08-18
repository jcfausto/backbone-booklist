<?php

require 'Slim/Slim.php';

//App instance
$app = new Slim();

//Routes
$app->get('/books', 'getBooks');
$app->get('/book/:id', 'getBook');

//Run the app
$app->run();

//API Methods
function getBooks(){
  $sql = "SELECT * FROM book";
  try{
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $books = $stmt->fetchObject();
    $db = null;
    echo json_encode($books);
  } catch(PDOException $e) {
  	echo '{"error": {"text":' . $e->getMessage() .'}}';
  }
}

function getBook($id){
  $sql = "SELECT * FROM book WHERE id=:id";
  try{
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $book = $stmt->fetchObject();
    $db = null;
    echo json_encode($book);
  } catch(PDOException $e) {
  	echo '{"error": {"text":' . $e->getMessage() .'}}';
  }
}

//DB
function getConnection(){
	$dbhost="127.0.0.1";
	$dbuser="root";
	$dbpass="root";
	$dbname="booklistapp";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>