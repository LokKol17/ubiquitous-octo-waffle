<?php
// Carregar configurações
require_once 'config.php';

$termo_busca = '';
$produtos = [];
$total_resultados = 0;

try {
    // Conectar ao banco de dados
    $conn = Database::getConnection();
    
    // Verificar se foi feita uma busca
    if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
        $termo_busca = trim($_GET['q']);
        
        // Busca em produtos, marcas e categorias
        $sql = "
            SELECT DISTINCT
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
            WHERE 
                p.nome LIKE ? OR 
                p.descricao LIKE ? OR 
                m.nome LIKE ? OR 
                c.nome LIKE ?
            ORDER BY 
                CASE 
                    WHEN p.nome LIKE ? THEN 1
                    WHEN m.nome LIKE ? THEN 2
                    WHEN c.nome LIKE ? THEN 3
                    ELSE 4
                END,
                p.nome
        ";
        
        $termo_like = '%' . $termo_busca . '%';
        $termo_exato = $termo_busca . '%';
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssss", 
            $termo_like, $termo_like, $termo_like, $termo_like,
            $termo_exato, $termo_exato, $termo_exato
        );
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }
        
        $total_resultados = count($produtos);
        $stmt->close();
    }
    
} catch (Exception $e) {
    $erro = "Erro na busca: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>
    <?php 
    if ($termo_busca) {
        echo "Busca: " . htmlspecialchars($termo_busca);
    } else {
        echo "Buscar Produtos";
    } 
    ?> - <?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?>
  </title>
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
    .busca-form {
      background: white;
      padding: 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .busca-container {
      display: flex;
      gap: 10px;
      max-width: 600px;
      margin: 0 auto;
    }
    .busca-input {
      flex: 1;
      padding: 15px 20px;
      border: 2px solid #bdc3c7;
      border-radius: 25px;
      font-size: 16px;
      outline: none;
      transition: border-color 0.3s;
    }
    .busca-input:focus {
      border-color: #3498db;
    }
    .busca-btn {
      padding: 15px 30px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }
    .busca-btn:hover {
      background-color: #2980b9;
    }
    .resultado-info {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
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
    .produto-meta .marca {
      background-color: #3498db;
      color: white;
      padding: 3px 6px;
      border-radius: 3px;
      font-size: 0.8em;
      margin-right: 6px;
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
    }
    .produto-preco {
      font-size: 1.5em;
      color: #e74c3c;
      font-weight: bold;
      margin: 10px 0 0 0;
    }
    .destaque {
      background-color: yellow;
      font-weight: bold;
    }
    .no-resultados {
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
      .busca-container {
        flex-direction: column;
      }
      .produto-content {
        flex-direction: column;
        text-align: center;
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

    <div class="busca-form">
      <form method="GET" action="">
        <div class="busca-container">
          <input 
            type="text" 
            name="q" 
            class="busca-input" 
            placeholder="Busque por produtos, marcas ou categorias..." 
            value="<?php echo htmlspecialchars($termo_busca); ?>"
            required
          >
          <button type="submit" class="busca-btn">🔍 Buscar</button>
        </div>
      </form>
    </div>

    <?php if ($termo_busca): ?>
      <div class="resultado-info">
        <?php if ($total_resultados > 0): ?>
          <h3>
            <?php echo $total_resultados; ?> 
            resultado<?php echo $total_resultados != 1 ? 's' : ''; ?> 
            encontrado<?php echo $total_resultados != 1 ? 's' : ''; ?> 
            para "<?php echo htmlspecialchars($termo_busca); ?>"
          </h3>
        <?php else: ?>
          <h3>Nenhum resultado encontrado para "<?php echo htmlspecialchars($termo_busca); ?>"</h3>
        <?php endif; ?>
      </div>

      <?php if ($total_resultados > 0): ?>
        <?php foreach ($produtos as $produto): ?>
          <a href="produto.php?id=<?php echo $produto['id']; ?>" class="produto-card">
            <div class="produto-content">
              <div class="produto-imagem">
                <img src="<?php echo htmlspecialchars($produto['imagem'] ?? 'placeholder.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($produto['nome']); ?>">
              </div>
              <div class="produto-info">
                <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                
                <div class="produto-meta">
                  <?php if (!empty($produto['marca_nome'])): ?>
                    <span class="marca"><?php echo htmlspecialchars($produto['marca_nome']); ?></span>
                  <?php endif; ?>
                  
                  <?php if (!empty($produto['categoria_nome'])): ?>
                    <span class="categoria"><?php echo htmlspecialchars($produto['categoria_nome']); ?></span>
                  <?php endif; ?>
                </div>
                
                <p><?php echo htmlspecialchars($produto['descricao']); ?></p>
                <div class="produto-preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-resultados">
          <h3>😔 Ops! Não encontramos nada...</h3>
          <p>Tente buscar com termos diferentes ou navegue pelas nossas categorias:</p>
          <p>
            <a href="categoria.php" style="color: #3498db;">Ver Categorias</a> | 
            <a href="marca.php" style="color: #3498db;">Ver Marcas</a>
          </p>
        </div>
      <?php endif; ?>

    <?php else: ?>
      <div class="no-resultados">
        <h3>🔍 Busque pelos nossos produtos!</h3>
        <p>Digite o nome do produto, marca ou categoria que você está procurando.</p>
        <p>
          Ou navegue diretamente por: 
          <a href="categoria.php" style="color: #3498db;">Categorias</a> | 
          <a href="marca.php" style="color: #3498db;">Marcas</a>
        </p>
      </div>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 40px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
      <p>© Todos os direitos desreservados a <a href="https://lokkol17.dev/Portifolio/" style="color: #3498db;">MIM</a>, faça o que quiser >w<</p>
    </div>
  </div>
</body>
</html>
