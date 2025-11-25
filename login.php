<?php
session_start();
require_once 'config.php';

$erro = '';
$sucesso = '';

// Se já está logado, redirecionar para painel
if (isset($_SESSION['usuario_id'])) {
    header("Location: meus-pedidos.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        try {
            $conn = Database::getConnection();
            
            // Buscar usuário por email
            $sql = "SELECT id, nome, email, senha_hash FROM usuarios WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();
                
                // Para este exemplo, vamos aceitar qualquer senha (em produção usaria password_verify)
                // if (password_verify($senha, $usuario['senha_hash'])) {
                if ($senha === 'senha123') { // Senha padrão para demo
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    
                    header("Location: meus-pedidos.php");
                    exit();
                } else {
                    $erro = 'Senha incorreta.';
                }
            } else {
                $erro = 'Usuário não encontrado.';
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $erro = 'Erro no sistema: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #333;
            margin: 0;
            font-size: 2em;
        }
        .logo p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 0.9em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #3498db;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background: #2980b9;
        }
        .erro {
            background: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .sucesso {
            background: #27ae60;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #3498db;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .demo-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #3498db;
        }
        .demo-info h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .demo-info p {
            margin: 5px 0;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🔐 Login</h1>
            <p><?php echo Config::get('APP_NAME', 'Bot Bot Electronics'); ?></p>
        </div>

        <?php if ($erro): ?>
            <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="sucesso"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>

            <button type="submit" class="btn">Entrar</button>
        </form>

        <div class="demo-info">
            <h4>📝 Demo - Usuários Disponíveis:</h4>
            <p><strong>joao@email.com</strong> - João Silva</p>
            <p><strong>maria@email.com</strong> - Maria Santos</p>
            <p><strong>pedro@email.com</strong> - Pedro Oliveira</p>
            <p><strong>Senha para todos:</strong> senha123</p>
        </div>

        <div class="links">
            <a href="index.php">← Voltar ao Catálogo</a>
        </div>
    </div>
</body>
</html>
