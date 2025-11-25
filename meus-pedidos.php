<?php
session_start();
require_once 'config.php';

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

try {
    $conn = Database::getConnection();
    
    // Buscar pedidos do usuário com detalhes
    $sql = "
        SELECT 
            p.id as pedido_id,
            p.data_pedido,
            p.data_entrega,
            p.valor_total,
            p.observacoes,
            sp.nome as status_nome,
            sp.descricao as status_descricao,
            e.logradouro,
            e.numero,
            e.bairro,
            e.cidade,
            e.estado,
            e.cep
        FROM pedidos p
        LEFT JOIN status_pedidos sp ON p.status_id = sp.id
        LEFT JOIN enderecos e ON p.endereco_entrega_id = e.id
        WHERE p.usuario_id = ?
        ORDER BY p.data_pedido DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $pedidos_result = $stmt->get_result();
    
    $pedidos = [];
    while ($pedido = $pedidos_result->fetch_assoc()) {
        $pedido_id = $pedido['pedido_id'];
        $pedidos[$pedido_id] = $pedido;
        
        // Buscar itens do pedido
        $sql_itens = "
            SELECT 
                pi.quantidade,
                pi.preco_unitario,
                pi.subtotal,
                pr.nome as produto_nome,
                pri.nome_arquivo as produto_imagem
            FROM pedido_itens pi
            LEFT JOIN produtos pr ON pi.produto_id = pr.id
            LEFT JOIN produto_imagens pri ON pr.id = pri.produto_id AND pri.eh_principal = TRUE
            WHERE pi.pedido_id = ?
        ";
        
        $stmt_itens = $conn->prepare($sql_itens);
        $stmt_itens->bind_param("i", $pedido_id);
        $stmt_itens->execute();
        $itens_result = $stmt_itens->get_result();
        
        $pedidos[$pedido_id]['itens'] = [];
        while ($item = $itens_result->fetch_assoc()) {
            $pedidos[$pedido_id]['itens'][] = $item;
        }
        $stmt_itens->close();
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}

function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'pendente': return '#f39c12';
        case 'confirmado': return '#3498db';
        case 'enviado': return '#9b59b6';
        case 'entregue': return '#27ae60';
        case 'cancelado': return '#e74c3c';
        default: return '#95a5a6';
    }
}

function getStatusIcon($status) {
    switch (strtolower($status)) {
        case 'pendente': return '⏳';
        case 'confirmado': return '✅';
        case 'enviado': return '📦';
        case 'entregue': return '🎉';
        case 'cancelado': return '❌';
        default: return '📋';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - <?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></title>
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
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .user-info span {
            color: #666;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
        .pedido-card {
            background: white;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .pedido-header {
            background: #3498db;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pedido-header h3 {
            margin: 0;
            font-size: 1.2em;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .pedido-content {
            padding: 20px;
        }
        .pedido-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        .info-item strong {
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        .itens-pedido {
            margin-top: 20px;
        }
        .itens-pedido h4 {
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .item-imagem img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .item-info {
            flex: 1;
        }
        .item-info h5 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .item-quantidade {
            color: #666;
            font-size: 0.9em;
        }
        .item-preco {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1em;
        }
        .endereco-entrega {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .endereco-entrega strong {
            color: white;
        }
        .no-pedidos {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .no-pedidos h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .no-pedidos p {
            color: #666;
            margin-bottom: 20px;
        }
        .btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background: #2980b9;
        }
        .total-pedido {
            text-align: right;
            margin-top: 15px;
            font-size: 1.3em;
            font-weight: bold;
            color: #27ae60;
        }
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            .pedido-info {
                grid-template-columns: 1fr;
            }
            .item {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>👤 Meus Pedidos</h1>
                <p>Acompanhe seus pedidos em <?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></p>
            </div>
            <div class="user-info">
                <span>Olá, <strong><?php echo htmlspecialchars($usuario_nome); ?></strong>!</span>
                <a href="logout.php" class="logout-btn">Sair</a>
            </div>
        </div>

        <?php if (empty($pedidos)): ?>
            <div class="no-pedidos">
                <h3>🛒 Nenhum pedido encontrado</h3>
                <p>Você ainda não fez nenhum pedido em nossa loja.</p>
                <a href="index.php" class="btn">Começar a Comprar</a>
            </div>
        <?php else: ?>
            <?php foreach ($pedidos as $pedido): ?>
                <div class="pedido-card">
                    <div class="pedido-header">
                        <h3>📦 Pedido #<?php echo str_pad($pedido['pedido_id'], 5, '0', STR_PAD_LEFT); ?></h3>
                        <div class="status-badge" style="background-color: <?php echo getStatusColor($pedido['status_nome']); ?>;">
                            <?php echo getStatusIcon($pedido['status_nome']); ?>
                            <?php echo htmlspecialchars($pedido['status_nome']); ?>
                        </div>
                    </div>
                    
                    <div class="pedido-content">
                        <div class="pedido-info">
                            <div class="info-item">
                                <strong>📅 Data do Pedido:</strong>
                                <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                            </div>
                            
                            <?php if ($pedido['data_entrega']): ?>
                            <div class="info-item">
                                <strong>🚚 Data de Entrega:</strong>
                                <?php echo date('d/m/Y', strtotime($pedido['data_entrega'])); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="info-item">
                                <strong>💰 Valor Total:</strong>
                                R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?>
                            </div>
                            
                            <div class="info-item">
                                <strong>📋 Status:</strong>
                                <?php echo htmlspecialchars($pedido['status_descricao']); ?>
                            </div>
                        </div>

                        <?php if ($pedido['observacoes']): ?>
                        <div class="info-item">
                            <strong>📝 Observações:</strong>
                            <?php echo nl2br(htmlspecialchars($pedido['observacoes'])); ?>
                        </div>
                        <?php endif; ?>

                        <div class="itens-pedido">
                            <h4>📦 Itens do Pedido</h4>
                            <?php foreach ($pedido['itens'] as $item): ?>
                                <div class="item">
                                    <div class="item-imagem">
                                        <img src="<?php echo htmlspecialchars($item['produto_imagem'] ?? 'placeholder.jpg'); ?>" 
                                             alt="<?php echo htmlspecialchars($item['produto_nome']); ?>">
                                    </div>
                                    <div class="item-info">
                                        <h5><?php echo htmlspecialchars($item['produto_nome']); ?></h5>
                                        <div class="item-quantidade">
                                            Quantidade: <?php echo $item['quantidade']; ?>x | 
                                            Preço unitário: R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>
                                        </div>
                                    </div>
                                    <div class="item-preco">
                                        R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="total-pedido">
                                Total: R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?>
                            </div>
                        </div>

                        <div class="endereco-entrega">
                            <strong>🏠 Endereço de Entrega:</strong><br>
                            <?php echo htmlspecialchars($pedido['logradouro']); ?>, <?php echo htmlspecialchars($pedido['numero']); ?><br>
                            <?php echo htmlspecialchars($pedido['bairro']); ?> - <?php echo htmlspecialchars($pedido['cidade']); ?>/<?php echo htmlspecialchars($pedido['estado']); ?><br>
                            CEP: <?php echo htmlspecialchars($pedido['cep']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn">← Voltar ao Catálogo</a>
        </div>
    </div>
</body>
</html>
