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

    // Ler o arquivo SQL
    $sqlFile = 'db-setup.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Arquivo SQL não encontrado: {$sqlFile}");
    }
    
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        throw new Exception("Erro ao ler o arquivo SQL: {$sqlFile}");
    }

    // Usar multi_query para executar vários comandos SQL
    if ($conn->multi_query($sql) === TRUE) {
        echo "Banco de dados e estrutura criados com sucesso usando o arquivo db-setup.sql.\n";
        echo "Configurações carregadas do arquivo .env\n";
    } else {
        throw new Exception("Erro ao executar o SQL: " . $conn->error);
    }

    $conn->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage() . "\n");
}
?>
