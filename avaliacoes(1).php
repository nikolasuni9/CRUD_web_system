<?php
include 'conexao.php';

// Cadastro Avaliação
$mensagemAvaliacaoCadastro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $aluno_id = $_POST['idAlunoAvaliacao'] ?? null;
    $disciplina_id = $_POST['idDisciplinaAvaliacao'] ?? null;
    $nota = $_POST['nota_avaliacao']?? null;
    $data_avaliacao = $_POST['data_avaliacao'] ?? null;

    if ($aluno_id && $disciplina_id && $nota && $data_avaliacao) 
    {
        $stmt = $conn->prepare("INSERT INTO avaliacoes (aluno_id, disciplina_id, nota, data_avaliacao) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $aluno_id, $disciplina_id, $nota, $data_avaliacao);

        if ($stmt->execute()) 
        {
            $mensagemAvaliacaoCadastro = "Cadastro realizado com sucesso!";
        } 
        else 
        {
            $mensagemAvaliacaoCadastro = "Erro ao cadastrar avaliação: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Pesquisa Disciplina
$resultadoAvaliacaoPesquisa = '';
$mensagemAvaliacaoPesquisa = '';

if (isset($_GET['idAlunoProcurar']) && !empty($_GET['idAlunoProcurar'])) 
{
    $aluno_id = $_GET['idAlunoProcurar'];

    $stmt = $conn->prepare("SELECT id, disciplina_id, nota, data_avaliacao FROM avaliacoes WHERE aluno_id = ?");
    $stmt->bind_param("s", $aluno_id);
    $stmt->execute();
    $stmt->bind_result($id, $disciplina_id, $nota, $data_avaliacao);

    if ($stmt->fetch()) 
    {
        $resultadoAvaliacaoPesquisa = "ID Avaliação: $id, \nID Disciplina: $disciplina_id, \nNota: $nota, \nData Avaliação: $data_avaliacao";
        $mensagemAvaliacaoPesquisa = "Cadastro encontrado!";
    }
    else 
    {
        $mensagemAvaliacaoPesquisa = "Avaliação não encontrada.";
    }
    $stmt->close();
}

// Atualizar Avaliação
$mensagemCadastroAtualizacao = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $id = $_POST['idAvaliacaoAtualizar'] ?? null;
    $nota = $_POST['notaAlunoAtualizar'] ?? null;

    if ($aluno_id && $nota)
    {
        $stmt = $conn->prepare("UPDATE avaliacoes SET nota = ? WHERE id = ?");
        $stmt->bind_param("ss", $nota, $id);

        if ($stmt->execute()) 
        {
            if ($stmt->affected_rows > 0) 
            {
                $mensagemCadastroAtualizacao = "Cadastro atualizado com sucesso!";
            } 
            else 
            {
                $mensagemCadastroAtualizacao = "Nenhuma avaliacao encontrada com esse nome.";
            }
        }
        else 
        {
            $mensagemCadastroAtualizacao = "Erro ao atualizar avaliação: " . $stmt->error;
        }
        $stmt->close();
    } 
}

// Excluir Avaliacao
// Excluir Disciplina
$mensagemAvaliacaoExclusao = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idAvaliacaoExcluir'])) 
{
    $id = $_POST['idAvaliacaoExcluir'] ?? null;
    $disciplina_id = $_POST['idDisciplinaExcluir'] ?? null;

    if ($disciplina_id) 
    {
        $stmt = $conn->prepare("DELETE FROM avaliacoes WHERE disciplina_id = ?");
        $stmt->bind_param("i", $disciplina_id);
    } 
    elseif ($id) 
    {
        $stmt = $conn->prepare("DELETE FROM avaliacoes WHERE id = ?");
        $stmt->bind_param("s", $id);
    } 
    else 
    {
        $mensagemAvaliacaoExclusao = "Erro: ID e/ou Nome são necessários para excluir.";
    }

    if ($stmt->execute()) 
    {
        if ($stmt->affected_rows > 0) 
        {
            $mensagemAvaliacaoExclusao = "Avaliação excluída com sucesso!";
        } 
        else 
        {
            $mensagemAvaliacaoExclusao = "Nenhuma avaliação encontrada com esse nome e/ou ID.";
        }
    } 
    else 
    {
        $mensagemAvaliacaoExclusao = "Erro ao excluir avaliação: " . $stmt->error;
    }

    $stmt->close();
}

fecharConexao();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações</title>
    <!-- Incluir Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Tela Avaliações</h1>
</br>
    <h2>Escolha os serviços:</h2>
</br>


    <h3>Cadastrar Avaliação:</h3>
</br>
<form method="POST">
    <label>Digite o ID do aluno:</label>
</br>
    <textarea minlength="1" maxlength="255" name="idAlunoAvaliacao"></textarea>
</br>    
    <label>Digite o ID da disciplina:</label>
</br>
    <textarea minlength="1" maxlength="255" name="idDisciplinaAvaliacao"></textarea>
</br>
    <label>Digite a nota do aluno:</label>
</br>
        <input type="number" step="0.01" name="nota_avaliacao" required>
</br>
    <label>Digite a data da avaliação:</label>
</br>
    <input type="date" name="data_avaliacao" required>
</br>
    <button type="submit" class="btn btn-success">Cadastrar</button>
</form>
</br>

<?php if (!empty($mensagemAvaliacaoCadastro)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemAvaliacaoCadastro; ?></div>
<?php endif; ?>


    <h3>Procurar Cadastro Avaliação:</h3>
<form method="GET">
    <label>Digite o ID do aluno a procurar:</label>
</br>
    <textarea minlength="1" maxlength="255" name="idAlunoProcurar"><?php echo isset($_GET['idAlunoProcurar']) ? $_GET['idAlunoProcurar'] : ''; ?></textarea>
</br>
    <button type="submit" class="btn btn-primary">Procurar</button>
</form>
</br>

<form>
    <label>Resultado Pesquisa:</label>
</br>
    <textarea name="resultadoAvaliacaoPesquisa"><?php echo $resultadoAvaliacaoPesquisa; ?></textarea>
</form>
</br>

<?php if (!empty($mensagemAvaliacaoPesquisa)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemAvaliacaoPesquisa; ?></div>
<?php endif; ?>


    <h3>Atualizar Cadastro Avaliação:</h3>
</br>
<form method="POST">
<label>Digite o ID da avaliação:</label>
</br>
    <textarea minlength="1" maxlength="255" name="idAvaliacaoAtualizar" required></textarea>
</br>
    <label>Digite a nota atualizar:</label>
</br>
    <input type="number" step="0.01" name="notaAlunoAtualizar" required>
</br>
    <button type="submit" class="btn btn-warning">Atualizar</button>
</form>
</br>

<?php if (!empty($mensagemCadastroAtualizacao)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemCadastroAtualizacao; ?></div>
<?php endif; ?>

    <!-- Formulário de Exclusão de Avaliação -->
    <h3>Excluir Avaliação:</h3>
</br>
    <form method="POST">
    <label>Digite o ID da avaliação excluir:</label>
</br>
    <textarea minlength="1" maxlength="255" name="idAvaliacaoExcluir"></textarea>
</br>
    <label>Digite o ID da disciplina</label>
</br>
    <textarea minlength="1" maxlength="255" name="idDisciplinaExcluir"></textarea>
</br>
    <button type="submit" class="btn btn-danger">Excluir</button>
</form>
</br>

<?php if (!empty($mensagemAvaliacaoExclusao)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemAvaliacaoExclusao; ?></div>
<?php endif; ?>

    <h2><a href="http://localhost/php/CRUD_sistema_web/index.php">Voltar para tela inicial</a></h2>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>