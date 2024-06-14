<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="container mt-4">
        <div class="alert alert-success" role="alert">
            Test jig created successfully!
        </div>
    </div>

    <script>
        // Redirect to test_jigs.php after 3 seconds
        setTimeout(function() {
            window.location.href = 'test_jigs.php';
        }, 3000);
    </script>
</body>
</html>
