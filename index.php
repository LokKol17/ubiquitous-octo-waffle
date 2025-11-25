<?php
// Carregar configurações
require_once 'config.php';

try {
    // Conectar ao banco de dados
    $conn = Database::getConnection();
    
    // Buscar todas as produtos com suas marcas, categorias e imagem principal
    $sql = "
        SELECT 
            p.id, 
            p.nome, 
            p.descricao, 
            p.preco,
            m.nome as marca_nome,
            c.nome as categoria_nome,
            pi.nome_arquivo as imagem
        FROM produtos p
        LEFT JOIN marcas m ON p.marca_id = m.id
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN produto_imagens pi ON p.id = pi.produto_id AND pi.eh_principal = TRUE
        ORDER BY p.id
    ";
    $result = $conn->query($sql);
    
    // Buscar categorias para navegação
    $sql_categorias = "SELECT id, nome FROM categorias ORDER BY nome";
    $categorias_result = $conn->query($sql_categorias);
    
    // Buscar marcas para navegação
    $sql_marcas = "SELECT id, nome FROM marcas ORDER BY nome";
    $marcas_result = $conn->query($sql_marcas);
    
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
    .produto-meta {
      margin-bottom: 10px;
    }
    .produto-meta .marca {
      background-color: #3498db;
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.8em;
      margin-right: 8px;
    }
    .produto-meta .categoria {
      background-color: #9b59b6;
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.8em;
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
    .categorias-nav {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .categorias-nav h3 {
      margin-top: 0;
      margin-bottom: 15px;
      color: #333;
      text-align: center;
    }
    .categorias-links {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
    }
    .categoria-link {
      display: inline-block;
      padding: 10px 20px;
      background-color: #ecf0f1;
      color: #2c3e50;
      text-decoration: none;
      border-radius: 25px;
      transition: all 0.3s;
      font-weight: bold;
    }
    .categoria-link:hover {
      background-color: #3498db;
      color: white;
      text-decoration: none;
      transform: translateY(-2px);
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
      <p><a href="busca.php" style="color: #3498db; text-decoration: none; font-size: 1.1em;">🔍 Buscar Produtos</a></p>
    </div>

    <div class="categorias-nav">
      <h3>🛍️ Compre por Categoria</h3>
      <div class="categorias-links">
        <?php while($cat = $categorias_result->fetch_assoc()): ?>
          <a href="categoria.php?id=<?php echo $cat['id']; ?>" class="categoria-link">
            <?php echo htmlspecialchars($cat['nome']); ?>
          </a>
        <?php endwhile; ?>
        <a href="categoria.php" class="categoria-link">Ver Todas</a>
      </div>
    </div>

    <div class="categorias-nav">
      <h3>🏷️ Compre por Marca</h3>
      <div class="categorias-links">
        <?php while($marca = $marcas_result->fetch_assoc()): ?>
          <a href="marca.php?id=<?php echo $marca['id']; ?>" class="categoria-link">
            <?php echo htmlspecialchars($marca['nome']); ?>
          </a>
        <?php endwhile; ?>
        <a href="marca.php" class="categoria-link">Ver Todas</a>
      </div>
    </div>

    <?php
    // Verificar se há produtos no banco de dados
    if ($result->num_rows > 0) {
        // Iterar através dos produtos e criar cards para cada um
        while($row = $result->fetch_assoc()) {
            echo '<a href="produto.php?id=' . $row["id"] . '" class="produto-card">';
            echo '  <div class="produto-content">';
            echo '    <div class="produto-imagem">';
            echo '      <img src="' . htmlspecialchars($row["imagem"] ?? 'placeholder.jpg') . '" alt="' . htmlspecialchars($row["nome"]) . '">';
            echo '    </div>';
            echo '    <div class="produto-info">';
            echo '      <h3>' . htmlspecialchars($row["nome"]) . '</h3>';
            
            // Exibir marca e categoria se disponíveis
            if (!empty($row["marca_nome"]) || !empty($row["categoria_nome"])) {
                echo '      <div class="produto-meta">';
                if (!empty($row["marca_nome"])) {
                    echo '        <span class="marca">' . htmlspecialchars($row["marca_nome"]) . '</span>';
                }
                if (!empty($row["categoria_nome"])) {
                    echo '        <span class="categoria">' . htmlspecialchars($row["categoria_nome"]) . '</span>';
                }
                echo '      </div>';
            }
            
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
