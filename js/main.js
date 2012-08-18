// The root URL for the RESTful services
var rootURL = "http://localhost/backbone-booklist/api/books";

var currentBook;

// Retrieve wine list when application starts 
findAll();

// Register listeners
$('#btnSearch').click(function() {
	search($('#searchKey').val());
	return false;
});

// Trigger search when pressing 'Return' on search key input field
$('#searchKey').keypress(function(e){
	if(e.which == 13) {
		search($('#searchKey').val());
		e.preventDefault();
		return false;
    }
});

$('#booklist a').live('click', function() {
	findById($(this).data('identity'));
});

function search(searchKey) {
	if (searchKey == '') 
		findAll();
	else
		findByName(searchKey);
}

function findAll() {
	console.log('findAll');
	$.ajax({
		type: 'GET',
		url: rootURL,
		dataType: "json", // data type of response
		success: renderList
	});
}

function findByName(searchKey) {
	console.log('findByName: ' + searchKey);
	$.ajax({
		type: 'GET',
		url: rootURL + '/search/' + searchKey,
		dataType: "json",
		success: renderList 
	});
}

function findById(id) {
	console.log('findById: ' + id);
	$.ajax({
		type: 'GET',
		url: rootURL + '/' + id,
		dataType: "json",
		success: function(data){
			$('#btnDelete').show();
			console.log('findById success: ' + data.name);
			currentBook = data;
			renderDetails(currentBook);
		}
	});
}

function renderList(data) {
	// JAX-RS serializes an empty list as null, and a 'collection of one' as an object (not an 'array of one')
	var list = data == null ? [] : (data.book instanceof Array ? data.book : [data.book]);

	$('#booklist li').remove();

	$.each(list, function(index, book) {
		$('#booklist').append('<li><a href="#" data-identity="' + book.id + '">'+book.name+'</a></li>');
	});
}

function renderDetails(book){
	$('#bookId').val(book.id);
	$('#bookName').val(book.name);
}

// Helper function to serialize all the form fields into a JSON string
function formToJSON() {
	return JSON.stringify({
		"id": $('#bookId').val(), 
		"name": $('#bookName').val()
		});
}
