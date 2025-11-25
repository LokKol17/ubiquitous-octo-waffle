<?php
session_start();
require_once 'config.php';

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    // Salvar o produto na sessão e redirecionar para login
    if (isset($_GET['produto_id'])) {
        $_SESSION['produto_para_comprar'] = $_GET['produto_id'];
    }
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = '';
$erro = '';

// Processar compra se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'])) {
    try {
        $conn = Database::getConnection();
        $produto_id = intval($_POST['produto_id']);
        $quantidade = max(1, intval($_POST['quantidade'] ?? 1));
        
        // Buscar produto
        $sql_produto = "SELECT id, nome, preco FROM produtos WHERE id = ?";
        $stmt = $conn->prepare($sql_produto);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $erro = 'Produto não encontrado.';
        } else {
            $produto = $result->fetch_assoc();
            $subtotal = $produto['preco'] * $quantidade;
            
            // Buscar endereço principal do usuário
            $sql_endereco = "SELECT id FROM enderecos WHERE usuario_id = ? AND eh_principal = TRUE LIMIT 1";
            $stmt_endereco = $conn->prepare($sql_endereco);
            $stmt_endereco->bind_param("i", $usuario_id);
            $stmt_endereco->execute();
            $result_endereco = $stmt_endereco->get_result();
            
            if ($result_endereco->num_rows === 0) {
                $erro = 'Endereço de entrega não encontrado. Por favor, cadastre um endereço.';
            } else {
                $endereco = $result_endereco->fetch_assoc();
                
                // Criar pedido
                $sql_pedido = "
                    INSERT INTO pedidos (usuario_id, endereco_entrega_id, status_id, valor_total, observacoes)
                    VALUES (?, ?, 1, ?, 'Pedido realizado através da loja online')
                ";
                $stmt_pedido = $conn->prepare($sql_pedido);
                $stmt_pedido->bind_param("iid", $usuario_id, $endereco['id'], $subtotal);
                $stmt_pedido->execute();
                
                $pedido_id = $conn->insert_id;
                
                // Adicionar item ao pedido
                $sql_item = "
                    INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal)
                    VALUES (?, ?, ?, ?, ?)
                ";
                $stmt_item = $conn->prepare($sql_item);
                $stmt_item->bind_param("iiidd", $pedido_id, $produto_id, $quantidade, $produto['preco'], $subtotal);
                $stmt_item->execute();
                
                $mensagem = "Pedido #" . str_pad($pedido_id, 5, '0', STR_PAD_LEFT) . " realizado com sucesso!";
                
                $stmt_pedido->close();
                $stmt_item->close();
            }
            $stmt_endereco->close();
        }
        $stmt->close();
        
    } catch (Exception $e) {
        $erro = 'Erro ao processar pedido: ' . $e->getMessage();
    }
}

// Buscar produto para exibir
$produto = null;
if (isset($_GET['produto_id']) || isset($_POST['produto_id'])) {
    $produto_id = intval($_GET['produto_id'] ?? $_POST['produto_id']);
    
    try {
        $conn = Database::getConnection();
        
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
            WHERE p.id = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $produto = $result->fetch_assoc();
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        $erro = 'Erro ao buscar produto: ' . $e->getMessage();
    }
}

// Se há produto na sessão para comprar (vindo do login)
if (!$produto && isset($_SESSION['produto_para_comprar'])) {
    header("Location: comprar.php?produto_id=" . $_SESSION['produto_para_comprar']);
    unset($_SESSION['produto_para_comprar']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - <?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .compra-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .produto-info {
            padding: 30px;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .produto-imagem img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .produto-detalhes {
            flex: 1;
        }
        .produto-detalhes h2 {
            color: #333;
            margin: 0 0 10px 0;
        }
        .produto-meta {
            margin-bottom: 15px;
        }
        .marca, .categoria {
            background-color: #3498db;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            margin-right: 8px;
        }
        .categoria {
            background-color: #9b59b6;
        }
        .preco {
            font-size: 1.8em;
            color: #e74c3c;
            font-weight: bold;
            margin: 15px 0;
        }
        .form-compra {
            background: #f8f9fa;
            padding: 30px;
            border-top: 1px solid #dee2e6;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .quantidade-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 200px;
        }
        .btn-quantidade {
            background: #3498db;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .btn-quantidade:hover {
            background: #2980b9;
        }
        .total-compra {
            background: #27ae60;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.3em;
            font-weight: bold;
        }
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-comprar {
            background: #27ae60;
            color: white;
            width: 100%;
        }
        .btn-comprar:hover {
            background: #219653;
        }
        .btn-voltar {
            background: #95a5a6;
            color: white;
            margin-right: 10px;
        }
        .btn-voltar:hover {
            background: #7f8c8d;
        }
        .mensagem {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .sucesso {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .erro {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .user-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .produto-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Finalizar Compra</h1>
            <p><?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></p>
        </div>

        <?php if ($mensagem): ?>
            <div class="mensagem sucesso">
                <?php echo htmlspecialchars($mensagem); ?>
                <br><br>
                <a href="meus-pedidos.php" class="btn btn-comprar" style="width: auto; padding: 10px 20px;">Ver Meus Pedidos</a>
                <a href="index.php" class="btn btn-voltar" style="padding: 10px 20px;">Continuar Comprando</a>
            </div>
        <?php elseif ($erro): ?>
            <div class="mensagem erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <?php if ($produto && !$mensagem): ?>
            <div class="compra-card">
                <div class="produto-info">
                    <div class="produto-imagem">
                        <img src="<?php echo htmlspecialchars($produto['imagem'] ?? 'placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                    </div>
                    <div class="produto-detalhes">
                        <h2><?php echo htmlspecialchars($produto['nome']); ?></h2>
                        
                        <div class="produto-meta">
                            <?php if ($produto['marca_nome']): ?>
                                <span class="marca"><?php echo htmlspecialchars($produto['marca_nome']); ?></span>
                            <?php endif; ?>
                            <?php if ($produto['categoria_nome']): ?>
                                <span class="categoria"><?php echo htmlspecialchars($produto['categoria_nome']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
                        
                        <div class="preco">
                            R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                        </div>
                    </div>
                </div>

                <form method="POST" action="" class="form-compra">
                    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                    
                    <div class="user-info">
                        <strong>👤 Comprando como:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                        <br>
                        <strong>📧 E-mail:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?>
                    </div>

                    <div class="form-group">
                        <label for="quantidade">Quantidade:</label>
                        <div class="quantidade-selector">
                            <button type="button" class="btn-quantidade" onclick="alterarQuantidade(-1)">-</button>
                            <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="10" onchange="calcularTotal()">
                            <button type="button" class="btn-quantidade" onclick="alterarQuantidade(1)">+</button>
                        </div>
                    </div>

                    <div class="total-compra" id="total">
                        Total: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-comprar">
                            🛒 Confirmar Pedido
                        </button>
                    </div>
                </form>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-voltar">← Voltar ao Produto</a>
                <a href="index.php" class="btn btn-voltar">🏠 Ir ao Catálogo</a>
            </div>
        <?php elseif (!$mensagem): ?>
            <div class="compra-card" style="padding: 40px; text-align: center;">
                <h3>❌ Produto não encontrado</h3>
                <p>O produto que você está tentando comprar não foi encontrado.</p>
                <a href="index.php" class="btn btn-comprar">Voltar ao Catálogo</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const precoUnitario = <?php echo $produto['preco'] ?? 0; ?>;

        function alterarQuantidade(delta) {
            const input = document.getElementById('quantidade');
            let novaQuantidade = parseInt(input.value) + delta;
            
            if (novaQuantidade < 1) novaQuantidade = 1;
            if (novaQuantidade > 10) novaQuantidade = 10;
            
            input.value = novaQuantidade;
            calcularTotal();
        }

        function calcularTotal() {
            const quantidade = parseInt(document.getElementById('quantidade').value);
            const total = precoUnitario * quantidade;
            
            document.getElementById('total').textContent = 
                'Total: R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Calcular total inicial
        calcularTotal();
    </script>
</body>
</html>
