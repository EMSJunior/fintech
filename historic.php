<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./style.css">
  <title>Historic</title>
</head>

<body>
  <header>
    <h2>Web development</h2>
  </header>

  <main>
    <form action="historic.php" method="get">
      <fieldset>
        <legend>Search</legend>

        <label for="id">ID: </label>
        <input type="number" name="id" id="id" step="1" <?php if (isset($_GET['id'])) echo 'value="' .  $_GET['id'] . '"'; ?>>

        <input type="submit" value="Search">
      </fieldset>
    </form>

    <?php
    require_once './classes/autoloader.class.php';

    if (isset($_GET["id"]) && $_GET["id"] != "") 
    {

      R::setup('mysql:host=localhost;dbname=fintech', 'root', '');
      $simulation = R::load('simulation', $_GET["id"]);

      if ($simulation->id == 0) 
      {
        echo "<p>Id not found!</p>";
      } 
      else 
      {
        echo "<ul>";
        echo"<h3>Data:</h3>";
        echo "<li>ID: {$simulation->id}</li>";
        echo "<li>Client: {$simulation->client}</li>";
        echo "<li>Initial contribution (R$): " . number_format($simulation->initialContribution, 2, ',', '.') . "</li>";
        echo "<li>Period: {$simulation->period} meses</li>";
        echo "<li>Monthly income (%): {$simulation->monthlyIncome} %</li>";
        echo "<li>Monthly contribution: " . number_format($simulation->monthlyContribution, 2, ',', '.') . "</li>";

        echo "</ul>";

        echo "<table>
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
        echo "    </tbody>
                        </table>";
      }
    }
    R::close();

    ?>

    </fieldset>

    <br>

    <a href="index.html"><button>Back</button></a>
  </main>

  <footer>
    <p>Edson Júnior & Wanderson Rodrigues - &copy; 2023</p>
  </footer>
</body>

</html>