<?php
session_start();

// Initialize attempts counter
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guess']) && is_numeric($_POST['guess'])) {
        $guess = intval($_POST['guess']);
        if ($guess >= 1 && $guess <= 100) {
            // Check if the guess matches the target number
            if (!isset($_SESSION['target'])) {
                $_SESSION['target'] = rand(1, 100);
            }

            $target = $_SESSION['target'];
            if ($guess == $target) {
                // If the guess is correct, reset the session and return success
                unset($_SESSION['target']);
                $response = array('status' => 'success', 'attempts' => $_SESSION['attempts']);
                echo json_encode($response);
                $_SESSION['attempts'] = 0; // Reset the attempts counter
                exit;
            } elseif ($guess < $target) {
                // If the guess is too low, return 'low'
                $response = array('status' => 'low');
                $_SESSION['attempts']++;
                echo json_encode($response);
                exit;
            } else {
                // If the guess is too high, return 'high'
                $response = array('status' => 'high');
                $_SESSION['attempts']++;
                echo json_encode($response);
                exit;
            }
        }
    }

    // If the guess is invalid or out of range, return an error
    $response = array('status' => 'error');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Guess the Number Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 24px;
            color: cornflowerblue;
            text-align: center;
            line-height: 0;
        }

        p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
            text-align: center;
        }

        input[type="number"] {
            padding: 8px;
            display: block;
            width: 100px;
            margin: auto;
        }

        button {
            padding: 10px 20px;
            display: block;
            width: 100px;
            margin: auto;
            margin-top: 10px;
            background-color: cornflowerblue;
            border: none;
            cursor: pointer;
        }

        nav {
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007BFF;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>Guess the Number Game</h1>
    <p>Guess a number between 1 and 100:</p>
    <input type="number" id="guessInput">
    <button onclick="checkGuess()">Submit Guess</button>
    <p class="result" id="result"></p>

    <script>
        function checkGuess() {
            var guess = document.getElementById('guessInput').value;

            // Send the guess to the server for validation
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        displayResult(response);
                    } else {
                        console.log('Error: ' + xhr.status);
                    }
                }
            };
            xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('guess=' + guess);
        }

        function displayResult(response) {
            var resultElement = document.getElementById('result');
            if (response.status === 'success') {
                resultElement.textContent = 'Congratulations! You guessed the correct number in ' + response.attempts + ' attempts.';
            } else if (response.status === 'high') {
                resultElement.textContent = 'Too high! Try again.';
            } else if (response.status === 'low') {
                resultElement.textContent = 'Too low! Try again.';
            }
        }
    </script>

    <nav>
        <a href="Games.html">Games</a>
    </nav>

</body>

</html>