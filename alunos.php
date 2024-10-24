<?php
include 'conexao.php';

// Cadastro Aluno
$mensagemCadastro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $nome = $_POST['nomeCadastro'] ?? null;
    $email = $_POST['emailCadastro'] ?? null;

    if ($nome && $email) 
    {
        $stmt = $conn->prepare("INSERT INTO alunos (nome, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $email);

        if ($stmt->execute()) 
        {
            $mensagemCadastro = "Cadastro realizado com sucesso!";
        } 
        else 
        {
            $mensagemCadastro = "Erro ao cadastrar aluno: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Procurar Aluno
$resultadoPesquisa = '';
$mensagemPesquisa = '';

if (isset($_GET['nomeProcurar']) && !empty($_GET['nomeProcurar'])) 
{
    $nomeProcurar = $_GET['nomeProcurar'];

    $stmt = $conn->prepare("SELECT id, email FROM alunos WHERE nome = ?");
    $stmt->bind_param("s", $nomeProcurar);
    $stmt->execute();
    $stmt->bind_result($id, $email);

    if ($stmt->fetch()) 
    {
        $resultadoPesquisa = "ID: $id\nEmail: $email";
        $mensagemPesquisa = "Cadastro encontrado!";
    }
    else 
    {
        $mensagemPesquisa = "Aluno não encontrado.";
    }
    $stmt->close();
}

// Atualizar Aluno
$mensagemAtualizacao = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $nomeAtualizar = $_POST['nomeAtualizar'] ?? null;
    $emailAtualizar = $_POST['emailAtualizar'] ?? null;

    if ($nomeAtualizar && $emailAtualizar) 
    {
        $stmt = $conn->prepare("UPDATE alunos SET email = ? WHERE nome = ?");
        $stmt->bind_param("ss", $emailAtualizar, $nomeAtualizar);

        if ($stmt->execute()) 
        {
            if ($stmt->affected_rows > 0) 
            {
                $mensagemAtualizacao = "Cadastro atualizado com sucesso!";
            } 
            else 
            {
                $mensagemAtualizacao = "Nenhum aluno encontrado com esse nome.";
            }
        }
        else 
        {
            $mensagemAtualizacao = "Erro ao atualizar cadastro: " . $stmt->error;
        }
        $stmt->close();
    } 
}

// Exclusão Cadastro
$mensagemExclusao = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomeExcluir'])) 
{
    $nomeExcluir = $_POST['nomeExcluir'] ?? null;
    $idExcluir = $_POST['idExcluir'] ?? null;

    if ($idExcluir) 
    {
        $stmt = $conn->prepare("DELETE FROM alunos WHERE id = ?");
        $stmt->bind_param("i", $idExcluir);
    } 
    elseif ($nomeExcluir) 
    {
        $stmt = $conn->prepare("DELETE FROM alunos WHERE nome = ?");
        $stmt->bind_param("s", $nomeExcluir);
    } 
    else 
    {
        $mensagemExclusao = "Erro: ID ou Nome são necessários para excluir.";
    }

    if ($stmt->execute()) 
    {
        if ($stmt->affected_rows > 0) 
        {
            $mensagemExclusao = "Aluno excluído com sucesso!";
        } 
        else 
        {
            $mensagemExclusao = "Nenhum aluno encontrado com esse nome ou ID.";
        }
    } 
    else 
    {
        $mensagemExclusao = "Erro ao excluir aluno: " . $stmt->error;
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
    <title>Alunos</title>

    <!-- Incluir Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Tela Alunos</h1>
</br>
    <h2>Escolha os serviços:</h2>
</br>


    <h3>Cadastrar Aluno:</h3>
</br>
<form method="POST">
    <label>Digite o nome do aluno a cadastrar:</label>
</br>
    <textarea minlength="3" maxlength="255" name="nomeCadastro"></textarea>
</br>
    <label>Digite o email do aluno a cadastrar:</label>
</br>
    <textarea minlength="3" maxlength="255" name="emailCadastro"></textarea>
</br>
    <button type="submit" class="btn btn-success">Cadastrar</button>
</form>
</br>

<?php if (!empty($mensagemCadastro)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemCadastro; ?></div>
<?php endif; ?>


    <h3>Procurar Cadastro Aluno:</h3>
</br>
<form method="GET">
    <label>Digite apenas o nome do aluno a procurar:</label>
</br>
    <textarea minlength="3" maxlength="255" name="nomeProcurar"><?php echo isset($_GET['nomeProcurar']) ? $_GET['nomeProcurar'] : ''; ?></textarea>
</br>
    <button type="submit" class="btn btn-primary">Procurar</button>
</form>
</br>

<form>
    <label>Resultado Pesquisa:</label>
</br>
    <textarea name="resultadoPesquisa"><?php echo $resultadoPesquisa; ?></textarea>
</form>
</br>

<?php if (!empty($mensagemPesquisa)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemPesquisa; ?></div>
<?php endif; ?>


    <h3>Atualizar Cadastro Aluno:</h3>
</br>
<form method="POST">
    <label>Digite o nome do aluno:</label>
</br>
    <textarea minlength="3" maxlength="255" name="nomeAtualizar" required></textarea>
</br>
    <label>Digite o email do aluno a atualizar:</label>
</br>
    <textarea minlength="3" maxlength="255" name="emailAtualizar" required></textarea>
</br>
    <button type="submit" class="btn btn-warning">Atualizar</button>
</form>
</br>

<?php if (!empty($mensagemAtualizacao)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemAtualizacao; ?></div>
<?php endif; ?>

    <h3>Excluir Cadastro Aluno:</h3>
</br>
<form method="POST">
    <label>Digite o nome do aluno a excluir:</label>
</br>
    <textarea minlength="3" maxlength="255" name="nomeExcluir"></textarea>
</br>
    <label>Digite o ID do aluno a excluir</label>
</br>
    <textarea minlength="1" maxlength="255" name="idExcluir"></textarea>
</br>
    <button type="submit" class="btn btn-danger">Excluir</button>
</form>
</br>

<?php if (!empty($mensagemExclusao)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemExclusao; ?></div>
<?php endif; ?>

<h2><a href="http://localhost/php/CRUD_sistema_web/index.php">Voltar para tela inicial</a></h2>

<!-- Incluir Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>