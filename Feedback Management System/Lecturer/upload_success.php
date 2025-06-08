<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Success</title>
    <style>
        .message {
            padding: 20px;
            border-radius: 5px;
            margin: 50px auto;
            max-width: 500px;
            text-align: center;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .failure-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="message success-message">
        <h2>success!</h2>
        <p>File uploaded and processed successfully.<p>
        <p>Redirecting...</p>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = 'lecturer_list.php';
        }, 3000); 
    </script>
</body>

</html>