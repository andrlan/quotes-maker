<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotes Maker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: row;
            width: 90%;
            max-width: 1200px;
        }
        .form-container {
            flex: 1;
            padding-right: 20px;
            border-right: 1px solid #ddd;
        }
        .form-container h1 {
            margin-top: 0;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="number"],
        form input[type="color"],
        form input[type="file"],
        form input[type="submit"] {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        form input[type="submit"] {
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        form input[type="submit"]:hover {
            background: #45a049;
        }
        .output-container {
            flex: 1;
            padding-left: 20px;
            text-align: center;
        }
        .output-container h2 {
            margin-top: 0;
        }
        .output-container img {
            max-width: 100%;
            max-height: 400px; /* Adjusted for smaller display */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Quotes Maker</h1>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <label for="quote">Quote:</label>
                <input type="text" id="quote" name="quote" required>
                
                <label for="watermark">Watermark:</label>
                <input type="text" id="watermark" name="watermark" required>

                <label for="fontSizeQuote">Font Size (Quote):</label>
                <input type="number" id="fontSizeQuote" name="fontSizeQuote" value="50" required>

                <label for="color">Text Color:</label>
                <input type="color" id="color" name="color" value="#ffffff" required>
                
                <label for="background">Background Image:</label>
                <input type="file" id="background" name="background" accept="image/*" required>
                
                <input type="submit" value="Create Quote">
            </form>
        </div>
        <div class="output-container">
            <?php
            if (isset($_GET['output'])) {
                echo '<h2>Result:</h2>';
                echo '<img src="' . htmlspecialchars($_GET['output']) . '" alt="Generated Quote Image">';
            }
            ?>
        </div>
    </div>
</body>
</html>
