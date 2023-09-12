<!DOCTYPE html>
<html lang="pt-BR">

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
            if(isset($_GET['client']) && $_GET['client'] != "" && 
             isset($_GET['initialContribution']) && 
             isset($_GET['period']) && $_GET['period'] > 1 &&
             isset($_GET['monthlyincome']) &&  
             isset($_GET['monthlycontribution'])){
                require_once './classes/autoloader.class.php';
                
                R::setup('mysql:host=localhost;dbname=fintech', 'root', '');

                $s = R::dispense('simulation');

                $s->client = $_GET['client'];
                $s->initialContribution = $_GET['initialContribution'];
                $s->period = $_GET['period'];
                $s->monthlyIncome = $_GET['monthlyincome'];
                $s->monthlyContribution = $_GET['monthlycontribution'];

                $id = R::store($s);

                $simulation = R::load('simulation', $id);
                    echo "<ul>";

                    echo "<li>ID: {$simulation->id}</li>";
                    echo "<li>Cliente: {$simulation->client}</li>";
                    echo "<li>Contribuição Inicial R$ ". number_format($simulation->initialContribution, 2, ',' , '.') . "</li>";
                    echo "<li>Período: {$simulation->period} meses</li>";
                    echo "<li>Taxa mensal: {$simulation->monthlyIncome} %</li>";
                    echo "<li>Contribuição mensal: " . number_format($simulation->monthlyContribution, 2, ',' , '.') . "</li>";

                    echo "</ul>";

                    echo "<fieldset>
                            <legend>Result</legend>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Mês</th>
                                            <th>Valor Inicial (R$)</th>
                                            <th>Aporte (R$)</th>
                                            <th>Rendimento (R$)</th>
                                            <th>Total (R$)</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                                        
                                        for ($i = 1; $i <= $simulation->period; $i++) {
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
                    echo "          </tbody>
                                </table>
                        </fieldset>";
                R::close();
            }else{
                echo "<h2>Valores não definidos!</h2>";
            }
        ?>
    </main>
            <br><br>
            <a href="/input.html"><button>re-simulate</button></a>
    <footer>
        <p>WanRod e Edson Júnior- &copy; 2023</p>
    </footer>
</body>

</html>