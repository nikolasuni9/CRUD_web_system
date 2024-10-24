<?php
include 'conexao.php';

// Cadastro Disciplina
$mensagemDisciplinaCadastro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $nome = $_POST['nomeDisciplinaCadastro'] ?? null;
    $carga_horaria = $_POST['carga_horariaDisciplinaCadastro'] ?? null;

    if ($nome && $carga_horaria) 
    {
        $stmt = $conn->prepare("INSERT INTO disciplinas (nome, carga_horaria) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $carga_horaria);

        if ($stmt->execute()) 
        {
            $mensagemDisciplinaCadastro = "Cadastro realizado com sucesso!";
        } 
        else 
        {
            $mensagemDisciplinaCadastro = "Erro ao cadastrar disciplina: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Pesquisa Disciplina
$resultadoDisciplinaPesquisa = '';
$mensagemDisciplinaPesquisa = '';

if (isset($_GET['nomeDisciplinaProcurar']) && !empty($_GET['nomeDisciplinaProcurar'])) 
{
    $nomeDisciplinaProcurar = $_GET['nomeDisciplinaProcurar'];

    $stmt = $conn->prepare("SELECT id, carga_horaria FROM disciplinas WHERE nome = ?");
    $stmt->bind_param("s", $nomeDisciplinaProcurar);
    $stmt->execute();
    $stmt->bind_result($id, $carga_horaria);

    if ($stmt->fetch()) 
    {
        $resultadoDisciplinaPesquisa = "ID: $id\nCarga Horária: $carga_horaria";
        $mensagemDisciplinaPesquisa = "Cadastro encontrado!";
    }
    else 
    {
        $mensagemDisciplinaPesquisa = "Disciplina não encontrada.";
    }
    $stmt->close();
}

// Atualizar Disciplina
$mensagemDisciplinaAtualizacao = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $nomeDisciplinaAtualizar = $_POST['nomeDisciplinaAtualizar'] ?? null;
    $carga_horariaDisciplinaAtualizar = $_POST['carga_horariaDisciplinaAtualizar'] ?? null;

    if ($nomeDisciplinaAtualizar && $carga_horariaDisciplinaAtualizar) 
    {
        $stmt = $conn->prepare("UPDATE disciplinas SET carga_horaria = ? WHERE nome = ?");
        $stmt->bind_param("ss", $carga_horariaDisciplinaAtualizar, $nomeDisciplinaAtualizar);

        if ($stmt->execute()) 
        {
            if ($stmt->affected_rows > 0) 
            {
                $mensagemDisciplinaAtualizacao = "Cadastro atualizado com sucesso!";
            } 
            else 
            {
                $mensagemDisciplinaAtualizacao = "Nenhuma disciplina encontrada com esse nome.";
            }
        }
        else 
        {
            $mensagemDisciplinaAtualizacao = "Erro ao atualizar cadastro: " . $stmt->error;
        }
        $stmt->close();
    } 
}

// Excluir Disciplina
$mensagemDisciplinaExclusao = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomeDisciplinaExcluir'])) 
{
    $nomeDisciplinaExcluir = $_POST['nomeDisciplinaExcluir'] ?? null;
    $idDisciplinaExcluir = $_POST['idDisciplinaExcluir'] ?? null;

    if ($idDisciplinaExcluir) 
    {
        $stmt = $conn->prepare("DELETE FROM disciplinas WHERE id = ?");
        $stmt->bind_param("i", $idDisciplinaExcluir);
    } 
    elseif ($nomeDisciplinaExcluir) 
    {
        $stmt = $conn->prepare("DELETE FROM disciplinas WHERE nome = ?");
        $stmt->bind_param("s", $nomeDisciplinaExcluir);
    } 
    else 
    {
        $mensagemDisciplinaExclusao = "Erro: ID e/ou Nome são necessários para excluir.";
    }

    if ($stmt->execute()) 
    {
        if ($stmt->affected_rows > 0) 
        {
            $mensagemDisciplinaExclusao = "Disciplina excluída com sucesso!";
        } 
        else 
        {
            $mensagemDisciplinaExclusao = "Nenhuma disciplina encontrada com esse nome e/ou ID.";
        }
    } 
    else 
    {
        $mensagemDisciplinaExclusao = "Erro ao excluir disciplina: " . $stmt->error;
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
    <title>Disciplinas</title>

    <!-- Incluir Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Tela Disciplinas</h1>
</br>
    <h2>Escolha os serviços:</h2>
</br>


    <h3>Cadastrar Disciplina:</h3>
</br>
<form method="POST">
    <label>Digite o nome da disciplina a cadastrar:</label>
</br>
    <textarea minlength="3" maxlength="255" name="nomeDisciplinaCadastro"></textarea>
</br>
    <label>Digite a carga horária da disciplina a cadastrar:</label>
</br>
    <textarea minlength="3" maxlength="255" name="carga_horariaDisciplinaCadastro"></textarea>
</br>
    <button type="submit" class="btn btn-success">Cadastrar</button>
</form>
</br>

<?php if (!empty($mensagemDisciplinaCadastro)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemDisciplinaCadastro; ?></div>
<?php endif; ?>


    <h3>Procurar Cadastro Disciplina:</h3>
</br>
<form method="GET">
    <label>Digite apenas o nome da disciplina a procurar:</label>
</br>
    <textarea minlength="3" maxlength="255" name="nomeDisciplinaProcurar"><?php echo isset($_GET['nomeDisciplinaProcurar']) ? $_GET['nomeDisciplinaProcurar'] : ''; ?></textarea>
</br>
    <button type="submit" class="btn btn-primary">Procurar</button>
</form>
</br>

<form>
    <label>Resultado Pesquisa:</label>
</br>
    <textarea name="resultadoDisciplinaPesquisa"><?php echo $resultadoDisciplinaPesquisa; ?></textarea>
</form>
</br>

<?php if (!empty($mensagemDisciplinaPesquisa)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemDisciplinaPesquisa; ?></div>
<?php endif; ?>


    <h3>Atualizar Cadastro Disciplina:</h3>
</br>
<form method="POST">
    <label>Digite o nome da disciplina:</label>
</br>
    <textarea minlength="3" maxlength="255" name="nomeDisciplinaAtualizar" required></textarea>
</br>
    <label>Digite a carga horária da disciplina a atualizar:</label>
</br>
    <textarea minlength="3" maxlength="255" name="carga_horariaDisciplinaAtualizar" required></textarea>
</br>
    <button type="submit" class="btn btn-warning">Atualizar</button>
</form>
</br>

<?php if (!empty($mensagemDisciplinaAtualizacao)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemDisciplinaAtualizacao; ?></div>
<?php endif; ?>

    <h3>Excluir Cadastro Disciplina:</h3>
</br>
<form method="POST">
    <label>Digite o nome da disciplina a excluir:</label>
</br>
    <textarea minlength="3" maxlength="255" name="nomeDisciplinaExcluir"></textarea>
</br>
    <label>Digite o ID da disciplina a excluir</label>
</br>
    <textarea minlength="1" maxlength="255" name="idDisciplinaExcluir"></textarea>
</br>
    <button type="submit" class="btn btn-danger">Excluir</button>
</form>
</br>

<?php if (!empty($mensagemDisciplinaExclusao)) : ?>
    <div class="alert alert-info mt-3"><?php echo $mensagemDisciplinaExclusao; ?></div>
<?php endif; ?>

<h2><a href="http://localhost/php/CRUD_sistema_web/index.php">Voltar para tela inicial</a></h2>

<!-- Incluir Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>