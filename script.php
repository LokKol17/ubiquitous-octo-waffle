<?php
// Carregar configurações
require_once 'config.php';

try {
    // Conectar ao MySQL (sem especificar database para poder criar)
    $host = Config::get('DB_HOST');
    $port = Config::get('DB_PORT');
    $username = Config::get('DB_USERNAME');
    $password = Config::get('DB_PASSWORD');
    
    $conn = new mysqli($host, $username, $password, "", $port);

    // Checar conexão
    if ($conn->connect_error) {
        throw new Exception("Conexão falhou: " . $conn->connect_error);
    }

    // Nome do banco a partir da configuração
    $dbname = Config::get('DB_DATABASE');

    // SQL para criar banco, tabela e inserir dados
    $sql = "
    CREATE DATABASE IF NOT EXISTS {$dbname};
    USE {$dbname};

    CREATE TABLE IF NOT EXISTS produtos (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nome VARCHAR(100) NOT NULL,
      descricao TEXT,
      preco DECIMAL(10,2) NOT NULL,
      imagem VARCHAR(255)
    );

    INSERT INTO produtos (nome, descricao, preco, imagem) VALUES
    ('IPHONE 5', 'Iphone com 128GB de armazenamento, mucho barato', 1299.00, 'iphone.jpg'),
    ('Asus Vivobook 15', 'Notebook de 15 polegadas com 8GB de RAM, roda tudo, até disco de makita', 2499.00, 'vivobook.jpg'),
    ('Ipods Pro 2', 'Fone de ouvido com cancelamento de ruído, tão barato assim? só pode ser roubado...', 499.00, 'ipods.jpg'),
    ('Galaxy Tab s10', 'Tablet com tela de 10.1 polegadas, perfeito pra jogos de ritmo', 899.00, 'tab_s10.jpg'),
    ('Samsung Galaxy Watch 7', 'Relógio inteligente com monitoramento de atividades físicas, meio inútil pra você -_-', 799.00, 'galaxy_watch_7.jpg');
    ";

    // Usar multi_query para executar vários comandos SQL
    if ($conn->multi_query($sql) === TRUE) {
        echo "Banco de dados '{$dbname}' e tabela criados, dados inseridos com sucesso.\n";
        echo "Configurações carregadas do arquivo .env\n";
    } else {
        throw new Exception("Erro ao executar o SQL: " . $conn->error);
    }

    $conn->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage() . "\n");
}
?>
