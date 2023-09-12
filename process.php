<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Process</title>
</head>

<body>
    <header>
        <h2>Web development</h2>
    </header>

    <main>
        <?php
        session_start();

        if (
            isset($_GET['client']) && $_GET['client'] != "" &&
            isset($_GET['initialContribution']) &&
            isset($_GET['period']) && $_GET['period'] > 0 &&
            isset($_GET['monthlyincome']) &&
            isset($_GET['monthlycontribution'])
        ) 
        {
            require_once './classes/autoloader.class.php';

            R::setup('mysql:host=localhost;dbname=fintech', 'root', '');

            $inputIdentifier = md5(serialize($_GET));

            if (!isset($_SESSION['input_identifiers'])) 
            {
                $_SESSION['input_identifiers'] = array();
            }

            if (!in_array($inputIdentifier, $_SESSION['input_identifiers'])) 
            {
                $_SESSION['input_identifiers'][] = $inputIdentifier;

                $s = R::dispense('simulation');

                $s->client = $_GET['client'];
                $s->initialContribution = $_GET['initialContribution'];
                $s->period = $_GET['period'];
                $s->monthlyIncome = $_GET['monthlyincome'];
                $s->monthlyContribution = $_GET['monthlycontribution'];

                $id = R::store($s);

                $simulation = R::load('simulation', $id);
                echo "<ul>";
                echo "<h3>Data:</h3>";
                echo "<li>ID: {$simulation->id}</li>";
                echo "<li>Client: {$simulation->client}</li>";
                echo "<li>Initial contribution: R$ " . number_format($simulation->initialContribution, 2, ',', '.') . "</li>";
                echo "<li>Period: {$simulation->period} months</li>";
                echo "<li>Monthly income: {$simulation->monthlyIncome} %</li>";
                echo "<li>Monthly contribution: " . number_format($simulation->monthlyContribution, 2, ',', '.') . "</li>";

                echo "</ul>";

                echo "
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Initial value (R$)</th>
                        <th>Contribution (R$)</th>
                        <th>Income (R$)</th>
                        <th>Total (R$)</th>
                    </tr>
                </thead>
                <tbody>";

                for ($i = 1; $i <= $simulation->period; $i++) 
                {
                    echo "<tr>";
                    echo "<td>" . $i . "</td>";
                    echo "<td>" . number_format($simulation->initialContribution, 2, ",", ".") . "</td>";
                    echo "<td>" . ($i > 1 ? number_format($simulation->monthlyContribution, 2, ",", ".") : "---") . "</td>";
                    echo "<td>" . number_format($simulation->initialContribution * $simulation->monthlyIncome / 100, 2, ",", ".") . "</td>";
                    $simulation->initialContribution = $simulation->initialContribution * (1 + ($simulation->monthlyIncome / 100));
                    echo "<td>" . number_format($simulation->initialContribution, 2, ",", ".") . "</td>";
                    $simulation->initialContribution += $simulation->monthlyContribution;
                    echo "</tr>";
                }
                echo "</tbody>
            </table>";
            } 
            else 
            {
                echo "<p>Data with the same input values ​​has already been sent.<br>
                If you want to see the results go to the <a href=\"./historic.php\">history page</a>.</p>";
            }

            R::close();
        } 
        else 
        {
            echo "<p>Values not defined!</p>";
        }
        ?>

        <br>
        <a href="input.html"><button>re-simulate</button></a>
    </main>

    <footer>
        <p>Edson Júnior & Wanderson Rodrigues - &copy; 2023</p>
    </footer>
</body>

</html>