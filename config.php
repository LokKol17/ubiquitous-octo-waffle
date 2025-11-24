<?php
/**
 * Configurações da Aplicação
 * 
 * Este arquivo carrega as variáveis de ambiente do arquivo .env
 * e fornece uma interface para acessar essas configurações.
 */

class Config {
    private static $config = [];
    private static $loaded = false;

    /**
     * Carrega as variáveis de ambiente do arquivo .env
     */
    public static function load($envFile = '.env') {
        if (self::$loaded) {
            return;
        }

        $envPath = __DIR__ . '/' . $envFile;
        
        if (!file_exists($envPath)) {
            throw new Exception("Arquivo .env não encontrado: {$envPath}");
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorar comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Dividir chave=valor
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remover aspas se existirem
                $value = trim($value, '"\'');
                
                // Armazenar na configuração
                self::$config[$key] = $value;
                
                // Definir como variável de ambiente se ainda não existir
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                }
            }
        }
        
        self::$loaded = true;
    }

    /**
     * Obtém uma configuração
     * 
     * @param string $key Chave da configuração
     * @param mixed $default Valor padrão se a chave não existir
     * @return mixed
     */
    public static function get($key, $default = null) {
        self::load();
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    /**
     * Verifica se uma configuração existe
     * 
     * @param string $key Chave da configuração
     * @return bool
     */
    public static function has($key) {
        self::load();
        return isset(self::$config[$key]);
    }

    /**
     * Obtém todas as configurações
     * 
     * @return array
     */
    public static function all() {
        self::load();
        return self::$config;
    }
}

/**
 * Classe para gerenciar conexões com banco de dados
 */
class Database {
    private static $connection = null;

    /**
     * Obtém uma conexão com o banco de dados
     * 
     * @return mysqli
     * @throws Exception
     */
    public static function getConnection() {
        if (self::$connection === null) {
            Config::load();
            
            $host = Config::get('DB_HOST', 'localhost');
            $port = Config::get('DB_PORT', 3306);
            $username = Config::get('DB_USERNAME');
            $password = Config::get('DB_PASSWORD');
            $database = Config::get('DB_DATABASE');
            
            if (!$username || !$password || !$database) {
                throw new Exception("Configurações do banco de dados incompletas no arquivo .env");
            }
            
            try {
                self::$connection = new mysqli($host, $username, $password, $database, $port);
                
                if (self::$connection->connect_error) {
                    throw new Exception("Conexão falhou: " . self::$connection->connect_error);
                }
                
                // Definir charset para UTF-8
                self::$connection->set_charset("utf8mb4");
                
            } catch (mysqli_sql_exception $e) {
                throw new Exception("Erro de conexão com o banco: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }

    /**
     * Fecha a conexão com o banco de dados
     */
    public static function closeConnection() {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }

    /**
     * Executa uma query preparada
     * 
     * @param string $sql
     * @param array $params
     * @return mysqli_result|bool
     */
    public static function query($sql, $params = []) {
        $conn = self::getConnection();
        
        if (empty($params)) {
            return $conn->query($sql);
        }
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar query: " . $conn->error);
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Assumindo strings por padrão
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
}

// Carregamento automático das configurações
try {
    Config::load();
    
    // Definir timezone se especificado
    if (Config::has('TIMEZONE')) {
        date_default_timezone_set(Config::get('TIMEZONE'));
    }
    
    // Configurar exibição de erros baseado no ambiente
    if (Config::get('APP_ENV') === 'development' && Config::get('APP_DEBUG') === 'true') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        error_reporting(0);
    }
    
} catch (Exception $e) {
    die("Erro ao carregar configurações: " . $e->getMessage());
}
?>
