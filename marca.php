<?php
// Carregar configurações
require_once 'config.php';

try {
    // Conectar ao banco de dados
    $conn = Database::getConnection();
    
    // Verificar se foi passado um ID de marca
    $marca_id = null;
    $marca_info = null;
    
    if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $marca_id = intval($_GET['id']);
        
        // Buscar informações da marca
        $sql_marca = "SELECT id, nome FROM marcas WHERE id = ?";
        $stmt_marca = $conn->prepare($sql_marca);
        $stmt_marca->bind_param("i", $marca_id);
        $stmt_marca->execute();
        $result_marca = $stmt_marca->get_result();
        
        if ($result_marca->num_rows > 0) {
            $marca_info = $result_marca->fetch_assoc();
        }
        $stmt_marca->close();
    }
    
    // Buscar produtos (filtrados por marca se especificada)
    if ($marca_id) {
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
            WHERE p.marca_id = ?
            ORDER BY p.nome
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $marca_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
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
            ORDER BY m.nome, p.nome
        ";
        $result = $conn->query($sql);
    }
    
    // Buscar todas as marcas para navegação
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
  <title><?php echo $marca_info ? 'Marca: ' . htmlspecialchars($marca_info['nome']) : 'Todas as Marcas'; ?> - <?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></title>
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
    .marcas-nav {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .marcas-nav h3 {
      margin-top: 0;
      margin-bottom: 15px;
      color: #333;
    }
    .marcas-links {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    .marca-link {
      display: inline-block;
      padding: 8px 16px;
      background-color: #ecf0f1;
      color: #2c3e50;
      text-decoration: none;
      border-radius: 20px;
      transition: background-color 0.3s;
    }
    .marca-link:hover, .marca-link.ativa {
      background-color: #3498db;
      color: white;
      text-decoration: none;
    }
    .marca-info {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
    }
    .produto-info {
      flex: 1;
    }
    .produto-info h3 {
      color: #333;
      margin: 0 0 10px 0;
      font-size: 1.3em;
    }
    .produto-meta {
      margin-bottom: 10px;
    }
    .produto-meta .categoria {
      background-color: #9b59b6;
      color: white;
      padding: 3px 6px;
      border-radius: 3px;
      font-size: 0.8em;
    }
    .produto-info p {
      color: #666;
      margin: 10px 0;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .produto-preco {
      font-size: 1.5em;
      color: #e74c3c;
      font-weight: bold;
      margin: 10px 0 0 0;
    }
    .no-produtos {
      text-align: center;
      padding: 40px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .voltar-link {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 20px;
      background-color: #95a5a6;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s;
    }
    .voltar-link:hover {
      background-color: #7f8c8d;
      text-decoration: none;
      color: white;
    }
    @media (max-width: 768px) {
      .produto-content {
        flex-direction: column;
        text-align: center;
      }
      .marcas-links {
        flex-direction: column;
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

    <a href="index.php" class="voltar-link">← Voltar ao Catálogo Principal</a>

    <div class="marcas-nav">
      <h3>Navegue por Marca</h3>
      <div class="marcas-links">
        <a href="marca.php" class="marca-link <?php echo !$marca_id ? 'ativa' : ''; ?>">Todas</a>
        <?php while($marca = $marcas_result->fetch_assoc()): ?>
          <a href="marca.php?id=<?php echo $marca['id']; ?>" 
             class="marca-link <?php echo $marca_id == $marca['id'] ? 'ativa' : ''; ?>">
            <?php echo htmlspecialchars($marca['nome']); ?>
          </a>
        <?php endwhile; ?>
      </div>
    </div>

    <?php if ($marca_info): ?>
    <div class="marca-info">
      <h2>Produtos da marca <?php echo htmlspecialchars($marca_info['nome']); ?></h2>
    </div>
    <?php endif; ?>

    <?php
    // Verificar se há produtos
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
            
            // Exibir categoria se disponível
            if (!empty($row["categoria_nome"])) {
                echo '      <div class="produto-meta">';
                echo '        <span class="categoria">' . htmlspecialchars($row["categoria_nome"]) . '</span>';
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
        if ($marca_info) {
            echo '  <h3>Nenhum produto encontrado da marca "' . htmlspecialchars($marca_info['nome']) . '".</h3>';
        } else {
            echo '  <h3>Nenhum produto encontrado.</h3>';
        }
        echo '  <p>Em breve teremos novos produtos disponíveis!</p>';
        echo '</div>';
    }
    ?>

    <div style="text-align: center; margin-top: 40px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
      <p>© Todos os direitos desreservados a <a href="https://lokkol17.dev/Portifolio/" style="color: #3498db;">MIM</a>, faça o que quiser >w<</p>
    </div>
  </div>
</body>
</html>

<?php
if (isset($stmt)) $stmt->close();
?>
