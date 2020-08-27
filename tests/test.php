<html>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script>
$.ajax({
    // change the url here
    url: "http://localhost/api/v0/users",
    type: "GET",
    // Add the Authorization header if needed
    headers: {'Authorization' : 'Bearer 12345'},
    cache: false,
    // contentType: 'application/json',
    dataType: 'json'
}).done(function (data) {
    alert('Successfully');
    console.log(data);
}).fail(function (xhr) {
    var message = 'Server error';
    if (xhr.responseJSON && xhr.responseJSON.error.message) {
       message = xhr.responseJSON.error.message;
    }
    alert(message);
});
</script>
</html>
