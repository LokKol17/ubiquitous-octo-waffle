<?php
// Carregar configurações
require_once 'config.php';

try {
    // Conectar ao banco de dados
    $conn = Database::getConnection();
    
    // Verificar se foi passado um ID de produto
    if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $produto_id = intval($_GET['id']);

    // Buscar o produto específico
    $sql = "SELECT id, nome, descricao, preco, imagem FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o produto existe
    if ($result->num_rows == 0) {
        $stmt->close();
        header("Location: index.php");
        exit();
    }

    $produto = $result->fetch_assoc();
    $stmt->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?php echo htmlspecialchars($produto['nome']); ?> - <?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f5f5f5;
    }
    .container {
      max-width: 1200px;
      margin: 0 auto;
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    .header h1 {
      color: #333;
      margin-bottom: 10px;
    }
    .produto-detalhes {
      display: flex;
      gap: 40px;
      margin-bottom: 30px;
    }
    .produto-imagem {
      flex: 1;
      text-align: center;
    }
    .produto-imagem img {
      max-width: 100%;
      height: auto;
      max-height: 500px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .produto-info {
      flex: 1;
    }
    .produto-info h2 {
      color: #333;
      font-size: 2.5em;
      margin-bottom: 20px;
    }
    .produto-info .descricao {
      font-size: 1.2em;
      color: #666;
      line-height: 1.6;
      margin-bottom: 30px;
    }
    .produto-info .preco {
      font-size: 2.5em;
      color: #e74c3c;
      font-weight: bold;
      margin-bottom: 30px;
    }
    .produto-info .preco span {
      font-size: 0.6em;
      color: #666;
    }
    .botoes {
      display: flex;
      gap: 20px;
    }
    .btn {
      padding: 15px 30px;
      border: none;
      border-radius: 5px;
      font-size: 1.1em;
      cursor: pointer;
      text-decoration: none;
      text-align: center;
      transition: background-color 0.3s;
    }
    .btn-comprar {
      background-color: #27ae60;
      color: white;
      flex: 1;
    }
    .btn-comprar:hover {
      background-color: #219653;
    }
    .btn-voltar {
      background-color: #95a5a6;
      color: white;
      flex: 0.3;
    }
    .btn-voltar:hover {
      background-color: #7f8c8d;
    }
    .especificacoes {
      margin-top: 40px;
      padding: 20px;
      background-color: #f8f9fa;
      border-radius: 10px;
    }
    .especificacoes h3 {
      color: #333;
      margin-bottom: 15px;
    }
    .especificacoes ul {
      list-style-type: none;
      padding: 0;
    }
    .especificacoes li {
      padding: 8px 0;
      border-bottom: 1px solid #dee2e6;
    }
    .especificacoes li:last-child {
      border-bottom: none;
    }
    @media (max-width: 768px) {
      .produto-detalhes {
        flex-direction: column;
      }
      .botoes {
        flex-direction: column;
      }
      .btn-voltar {
        flex: 1;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></h1>
      <p>Sua loja de itens de excelente qualidade (e procedência duvidosa)</p>
    </div>

    <div class="produto-detalhes">
      <div class="produto-imagem">
        <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
      </div>
      
      <div class="produto-info">
        <h2><?php echo htmlspecialchars($produto['nome']); ?></h2>
        
        <div class="descricao">
          <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
        </div>
        
        <div class="preco">
          <span>Preço: </span>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
        </div>
        
        <div class="botoes">
          <button class="btn btn-comprar" onclick="comprarProduto(<?php echo $produto['id']; ?>)">
            Comprar Agora
          </button>
          <a href="index.php" class="btn btn-voltar">
            Voltar ao Catálogo
          </a>
        </div>
      </div>
    </div>

    <div class="especificacoes">
      <h3>Informações do Produto</h3>
      <ul>
        <li><strong>Código do Produto:</strong> #<?php echo str_pad($produto['id'], 5, '0', STR_PAD_LEFT); ?></li>
        <li><strong>Nome:</strong> <?php echo htmlspecialchars($produto['nome']); ?></li>
        <li><strong>Preço:</strong> R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></li>
        <li><strong>Disponibilidade:</strong> <span style="color: #27ae60;">✓ Em estoque</span></li>
        <li><strong>Frete:</strong> Calculado no checkout</li>
        <li><strong>Garantia:</strong> 90 dias (se não quebrar antes 😅)</li>
      </ul>
    </div>

    <footer style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #dee2e6;">
      <p>© Todos os direitos desreservados a <a href="https://lokkol17.dev/Portifolio/" style="color: #3498db;">MIM</a>, faça o que quiser >w<</p>
    </footer>
  </div>

  <script>
    function comprarProduto(produtoId) {
      alert('Funcionalidade de compra ainda não implementada!\nProduto ID: ' + produtoId + '\n\nEm breve teremos um carrinho de compras funcionando! 🛒');
    }
  </script>
</body>
</html>

<?php
// A conexão será fechada automaticamente no final do script
?>
