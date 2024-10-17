<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./stilo.css">
    <title>Caixa Eletrônico</title>
</head>
<body>

    <?php 
    
    $saque = $_REQUEST['saque'] ?? 0;
    $acao = $_REQUEST['acao'] ?? 'saque'; 
    $moeda = $_REQUEST['moeda'] ?? 'BRL'; 
    $mostrarNotas = false; 
    $valorConvertido = 0; 
    $mensagemConversao = '';


    $url = 'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoDolarPeriodo(dataInicial=@dataInicial,dataFinalCotacao=@dataFinalCotacao)?@dataInicial=\'10-05-2024\'&@dataFinalCotacao=\'10-15-2024\'&$top=1&$orderby=dataHoraCotacao%20desc&$format=json&$select=cotacaoCompra';
    $dados = json_decode(file_get_contents($url), true); 
    $cotacaoUSD = $dados["value"][0]["cotacaoCompra"] ?? 0; 

    
    
    if ($moeda == 'USD') {
        $valorConvertido = $saque / $cotacaoUSD; 
        $mensagemConversao = "O valor de R$" . number_format($saque, 2, ",", ".") . " equivale a $" . number_format($valorConvertido, 2, ",", ".") . " na cotação atual.";
    } elseif ($moeda == 'BRL' && $acao == 'deposito') {
        $valorConvertido = $saque / $cotacaoUSD; 
        $mensagemConversao = "O valor de $" . number_format($saque, 2, ",", ".") . " equivale a R$" . number_format($valorConvertido, 2, ",", ".") . " na cotação atual.";
    }

    
    if ($saque > 0 && $acao == 'saque') {
        $mostrarNotas = true;

        
        $resto = $saque;
        $tot100 = floor($resto / 100);
        $resto %= 100;

        $tot50 = floor($resto / 50);
        $resto %= 50;

        $tot10 = floor($resto / 10);
        $resto %= 10;

        $tot5 = floor($resto / 5);
        $resto %= 5;

        $tot2 = floor($resto / 2);
        $resto %= 2;
    }
    ?>

    <main>
        <header>
            <h1>Caixa Eletrônico</h1>
        </header>

        
        <form action="<?=$_SERVER['PHP_SELF']?>" method="get">
            <label for="numero_conta">Número da Conta:</label>
            <input type="text" id="numero_conta" name="numero_conta" required><br>

            <label for="saque">Qual valor você deseja sacar ou depositar? (R$) <sup>*</sup></label>
            <input type="number" name="saque" id="saque" step="5" required value="<?=$saque?>"><br>

            <label for="moeda">Moeda:</label>
            <select id="moeda" name="moeda" required>
                <option value="USD" <?= $moeda == 'USD' ? 'selected' : '' ?>>USD</option>
                <option value="BRL" <?= $moeda == 'BRL' ? 'selected' : '' ?>>BRL</option>
            </select><br>

            <label for="acao">Ação:</label>
            <select id="acao" name="acao" required>
                <option value="saque" <?= $acao == 'saque' ? 'selected' : '' ?>>Saque</option>
                <option value="deposito" <?= $acao == 'deposito' ? 'selected' : '' ?>>Depósito</option>
            </select><br>

            <p style="font-size: 0.9em;"><sup>*</sup>Notas disponíveis para saque: R$100, R$50, R$10, R$5 e R$2</p>
            <input type="submit" value="Executar">
        </form>
    </main>

    
    <?php if ($mostrarNotas): ?>
    <section>
        <h2>O saque de R$<?=number_format($saque, 2, ",", ".")?> foi realizado</h2>
        <p>O caixa vai te entregar as seguintes notas:</p>
        <ul>
            <li><img src="./moedas/100-reais.jpg" alt="nota de 100" class="nota"> x<?=$tot100?></li>
            <li><img src="./moedas/50-reais.jpg" alt="nota de 50" class="nota"> x<?=$tot50?></li>
            <li><img src="./moedas/10-reais.jpg" alt="nota de 10" class="nota"> x<?=$tot10?></li>
            <li><img src="./moedas/5-reais.jpg" alt="nota de 5" class="nota"> x<?=$tot5?></li>
            <li><img src="./moedas/2-reais.jpg" alt="nota de 2" class="nota"> x<?=$tot2?></li>
        </ul>
    </section>
    <?php endif; ?>

    
    <?php if ($valorConvertido > 0): ?>
    <section>
        <h2>Conversão de Moeda</h2>
        <p>do real para dolar</p>
        <p><?=$mensagemConversao?></p>
    </section>
    <?php endif; ?>

    <?php 
    
    echo "Hoje é dia " . date("d/M/Y");
    echo " e a hora da transação é " . date("G:i:s");
    const pais = "Brasil";
    echo "Você realizou sua operação no país: " . pais;
    ?>

</body>
</html>





