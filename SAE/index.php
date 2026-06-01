<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatório da Turma</title>

<link rel="stylesheet" href="style.css"> <!-- IMPORTANTE -->

</head>
<body>

<?php

// Função para calcular média
function calcularMedia($n1, $n2, $n3) {
    return ($n1 + $n2 + $n3) / 3;
}

// Função para situação
function verificarSituacao($media) {
    if ($media >= 7) return "Aprovado";
    elseif ($media >= 5) return "Recuperação";
    else return "Reprovado";
}

$turma = $_POST['turma'];
$nomes = $_POST['nome'];
$p1 = $_POST['p1'];
$p2 = $_POST['p2'];
$trab = $_POST['trab'];

$total = count($nomes);

$somaMedias = 0;
$maiorMedia = -1;
$menorMedia = 999;

$aprovados = 0;
$recuperacao = 0;
$reprovados = 0;

$somaNotas = 0;

echo "<h2>Relatório da Turma: $turma</h2>";

echo "<table border='1'>
<tr>
<th>Nome</th>
<th>P1</th>
<th>P2</th>
<th>Trab</th>
<th>Média</th>
<th>Raiz</th>
<th>Diferença</th>
<th>Situação</th>
</tr>";

for ($i = 0; $i < $total; $i++) {

    $media = calcularMedia($p1[$i], $p2[$i], $trab[$i]);

    $raiz = sqrt($p1[$i] + $p2[$i] + $trab[$i]);

    $maiorNota = max($p1[$i], $p2[$i], $trab[$i]);
    $menorNota = min($p1[$i], $p2[$i], $trab[$i]);
    $diferenca = abs($maiorNota - $menorNota);

    $situacao = verificarSituacao($media);

    if ($situacao == "Aprovado") $aprovados++;
    elseif ($situacao == "Recuperação") $recuperacao++;
    else $reprovados++;

    $somaMedias += $media;
    $somaNotas += ($p1[$i] + $p2[$i] + $trab[$i]);

    if ($media > $maiorMedia) $maiorMedia = $media;
    if ($media < $menorMedia) $menorMedia = $media;

    echo "<tr>
        <td>{$nomes[$i]}</td>
        <td>{$p1[$i]}</td>
        <td>{$p2[$i]}</td>
        <td>{$trab[$i]}</td>
        <td>" . number_format($media,2) . "</td>
        <td>" . number_format($raiz,2) . "</td>
        <td>" . number_format($diferenca,2) . "</td>
        <td class='$situacao'>$situacao</td>    
    </tr>";
}

echo "</table>";

// Estatísticas
$mediaTurma = $somaMedias / $total;
$percentual = ($aprovados / $total) * 100;

echo "<h3>Estatísticas da Turma</h3>";

echo "Média da turma: " . number_format($mediaTurma,2) . "<br>";
echo "Maior média: " . number_format($maiorMedia,2) . "<br>";
echo "Menor média: " . number_format($menorMedia,2) . "<br>";

echo "Aprovados: $aprovados<br>";
echo "Recuperação: $recuperacao<br>";
echo "Reprovados: $reprovados<br>";

echo "Percentual de aprovação: " . number_format($percentual,2) . "%<br>";

echo "Soma total das notas: " . number_format($somaNotas,2) . "<br>";

// Mensagem automática
if ($percentual >= 70) {
    echo "<h3 style='color:green;'>Excelente desempenho da turma!</h3>";
} elseif ($percentual >= 50) {
    echo "<h3 style='color:orange;'>Desempenho mediano.</h3>";
} else {
    echo "<h3 style='color:red;'>Desempenho abaixo do esperado.</h3>";
}

?>
</body>
</html>