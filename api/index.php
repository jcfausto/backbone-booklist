<?php

require 'Slim/Slim.php';

//App instance
$app = new Slim();

//Routes
$app->get('/books', 'getBooks');
$app->get('/books/:id', 'getBook');
$app->get('/books/search/:filter', 'searchBook');

$app->post('/books', 'addBook');

$app->delete('/books/:id', 'deleteBook');

$app->put('/books/:id', 'updateBook');

//Run the app
$app->run();

//API Methods
function getBooks(){
  $sql = "SELECT * FROM book";
  try{
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $books = $stmt->fetchAll();
    $db = null;
    echo '{"book": ' . json_encode($books) . '}';
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

function searchBook($filter){
  if ($filter == "") {
    echo '{"error": {"text":"you must specify a filter."}}'; 
    return; 
  }

  $sql = "SELECT id, name FROM book WHERE name like :filter";
  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $filter = "%".$filter."%";
    $stmt->bindParam("filter", $filter);
    $stmt->execute();
    $books = $stmt->fetchAll();
    $db = null;
    echo '{"book": ' . json_encode($books) . '}';
  } catch (PDOException $e) {
    echo '{"error": {"text":' . $e->getMessage() . '}}';
  }
}

function addBook(){
  error_log('addBook\n', 3, '/var/tmp/php.log');
  //Request object containg the posted content
  $request = Slim::getInstance()->request();
  //Getting the book posted from request object
  $book = json_decode($request->getBody());
  $sql = "INSERT INTO book (name) VALUES (:name)";
  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("name", $book->name);
    $stmt->execute();
    $book->id = $db->lastInsertId();
    $db = null;
    echo json_encode($book);
  } catch (PDOException $e) {
    error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
}

function deleteBook($bookId){
  error_log('deleteBook\n', 3, '/var/tmp/php.log');
  $sql = "DELETE FROM book WHERE id=:id";
  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $bookId);
    $stmt->execute();
    $db = null;
  } catch (PDOException $e) {
    error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
}

function updateBook($bookId){
  error_log('updateBook\n', 3, '/var/tmp/php.log');
  $sql = "UPDATE book SET name=:name WHERE id=:id";
  $request = Slim::getInstance()->request();
  $book = json_decode($request->getBody());
  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $bookId);
    $stmt->bindParam("name", $book->name);
    $stmt->execute();
    $db = null;
    //Return the updated list
    getBooks();
  } catch (PDOException $e) {
    error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
}

//DB
function getConnection(){
	$dbhost="127.0.0.1";
	$dbuser="root";
	$dbpass="root";
	$dbname="booklistapp";
  $dbport=8889;
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbport", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>