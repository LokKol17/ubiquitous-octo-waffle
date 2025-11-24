<?php
// Carregar configurações
require_once 'config.php';

try {
    // Conectar ao banco de dados
    $conn = Database::getConnection();
    
    // Buscar todos os produtos
    $sql = "SELECT id, nome, descricao, preco, imagem FROM produtos ORDER BY id";
    $result = $conn->query($sql);
    
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
  <title>Catálogo de Produtos - <?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></title>
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
    }
    .produto-card {
      background: white;
      border-radius: 10px;
      margin-bottom: 20px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      display: block;
    }
    .produto-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
      text-decoration: none;
      color: inherit;
    }
    .produto-content {
      display: flex;
      gap: 20px;
      align-items: center;
    }
    .produto-imagem {
      flex-shrink: 0;
    }
    .produto-imagem img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
    }
    .produto-info {
      flex: 1;
    }
    .produto-info h3 {
      color: #333;
      margin: 0 0 10px 0;
      font-size: 1.5em;
    }
    .produto-info p {
      color: #666;
      margin: 10px 0;
      line-height: 1.4;
    }
    .produto-preco {
      font-size: 1.8em;
      color: #e74c3c;
      font-weight: bold;
      margin: 15px 0 0 0;
    }
    .header {
      text-align: center;
      background: white;
      padding: 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .header h1 {
      color: #333;
      margin-bottom: 10px;
    }
    .no-produtos {
      text-align: center;
      padding: 40px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .footer {
      text-align: center;
      margin-top: 40px;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    @media (max-width: 768px) {
      .produto-content {
        flex-direction: column;
        text-align: center;
      }
      .produto-imagem img {
        width: 150px;
        height: 150px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></h1>
      <p>Sua loja de itens de excelente qualidade (e procedência duvidosa)</p>
      <h2>Te liga nos nossos produtos e preços</h2>
    </div>

    <?php
    // Verificar se há produtos no banco de dados
    if ($result->num_rows > 0) {
        // Iterar através dos produtos e criar cards para cada um
        while($row = $result->fetch_assoc()) {
            echo '<a href="produto.php?id=' . $row["id"] . '" class="produto-card">';
            echo '  <div class="produto-content">';
            echo '    <div class="produto-imagem">';
            echo '      <img src="' . htmlspecialchars($row["imagem"]) . '" alt="' . htmlspecialchars($row["nome"]) . '">';
            echo '    </div>';
            echo '    <div class="produto-info">';
            echo '      <h3>' . htmlspecialchars($row["nome"]) . '</h3>';
            echo '      <p>' . htmlspecialchars($row["descricao"]) . '</p>';
            echo '      <div class="produto-preco">R$ ' . number_format($row["preco"], 2, ',', '.') . '</div>';
            echo '    </div>';
            echo '  </div>';
            echo '</a>';
        }
    } else {
        echo '<div class="no-produtos">';
        echo '  <h3>Nenhum produto encontrado no catálogo.</h3>';
        echo '  <p>Em breve teremos novos produtos disponíveis!</p>';
        echo '</div>';
    }
    
    // A conexão será fechada automaticamente no final do script
    ?>

    <div class="footer">
      <p>© Todos os direitos desreservados a <a href="https://lokkol17.dev/Portifolio/">MIM</a>, faça o que quiser >w<</p>
    </div>
  </div>
</body>
</html>
