# Bot Bot Electronics 🛒

Uma loja online de produtos eletrônicos com uma estrutura de banco de dados normalizada e interface ### 📋 Instalação Manual

1. Configure um servidor web (Apache/Nginx) com PHP 8.1+
2. Configure MySQL/MariaDB
3. Crie arquivo `.env` com as configurações do banco:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   DB_DATABASE=botbot_electronics
   APP_NAME="Bot Bot Electronics"
   ```
4. Execute o script SQL: `mysql < db-setup.sql`
5. Configure o servidor web para servir os arquivos

### 🎮 Como Testar o Sistema

1. **Acesse**: http://localhost:8080
2. **Navegue** pelos produtos
3. **Faça login** com:
   - Email: `joao@email.com` (ou maria@email.com, pedro@email.com)
   - Senha: `senha123`
4. **Compre produtos** clicando em "Comprar Agora"
5. **Veja seus pedidos** na área "Meus Pedidos"️ Estrutura do Banco de Dados

### Tabelas Principais

#### `produtos`
- `id` (INT, PK, AUTO_INCREMENT)
- `nome` (VARCHAR(100), NOT NULL)
- `descricao` (TEXT)
- `preco` (DECIMAL(10,2), NOT NULL)
- `marca_id` (INT, FK → marcas.id)
- `categoria_id` (INT, FK → categorias.id)

#### `marcas`
- `id` (INT, PK, AUTO_INCREMENT)
- `nome` (VARCHAR(50), NOT NULL)

#### `categorias`
- `id` (INT, PK, AUTO_INCREMENT)
- `nome` (VARCHAR(50), NOT NULL)
- `descricao` (TEXT)

#### `produto_imagens`
- `id` (INT, PK, AUTO_INCREMENT)
- `produto_id` (INT, FK → produtos.id)
- `nome_arquivo` (VARCHAR(255), NOT NULL)
- `eh_principal` (BOOLEAN, DEFAULT FALSE)

### Tabelas de E-commerce

#### `usuarios`
- `id` (INT, PK, AUTO_INCREMENT)
- `nome` (VARCHAR(100), NOT NULL)
- `email` (VARCHAR(150), NOT NULL, UNIQUE)
- `senha_hash` (VARCHAR(255), NOT NULL)
- `telefone` (VARCHAR(20))
- `data_cadastro` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

#### `enderecos`
- Endereços de entrega e cobrança dos usuários

#### `pedidos`
- Histórico completo de pedidos

#### `pedido_itens`
- Itens individuais de cada pedido

#### `status_pedidos`
- Status possíveis dos pedidos (Pendente, Confirmado, Enviado, Entregue, Cancelado)

## 📁 Estrutura de Arquivos

```
ubiquitous-octo-waffle/
├── config.php              # Configurações e classe Database
├── db-setup.sql            # Script completo de criação do banco
├── script.php              # Script de inicialização (backup)
├── index.php               # Página principal com catálogo
├── produto.php             # Página de detalhes do produto
├── categoria.php           # Listagem por categoria
├── marca.php               # Listagem por marca
├── busca.php               # Sistema de busca
├── login.php               # Sistema de login
├── logout.php              # Logout
├── meus-pedidos.php        # Área do cliente
├── comprar.php             # Sistema de compra
├── docker-compose.yml      # Configuração Docker
├── .env                    # Variáveis de ambiente
└── *.jpg                   # Imagens dos produtos
```

## 🚀 Funcionalidades

### ✅ Implementadas
- **Catálogo de Produtos**: Listagem com marcas, categorias e imagens
- **Detalhes do Produto**: Página individual com galeria de imagens
- **Navegação por Categoria**: Filtragem por tipos de produtos
- **Navegação por Marca**: Filtragem por fabricantes
- **Sistema de Busca**: Busca inteligente em produtos, marcas e categorias
- **Sistema de Login**: Autenticação de usuários
- **Área do Cliente**: Visualização de pedidos do usuário
- **Sistema de Compra**: Carrinho simplificado e criação de pedidos
- **Design Responsivo**: Interface adaptável para dispositivos móveis
- **Banco Normalizado**: Estrutura otimizada com relacionamentos

### � Sistema de Usuários
- **Login/Logout**: Sistema de autenticação
- **3 usuários demo**: joao@email.com, maria@email.com, pedro@email.com
- **Senha padrão**: senha123 (para demonstração)
- **Área de pedidos**: Visualização completa do histórico de compras

## 🛠️ Tecnologias

- **Backend**: PHP 8+ com MySQLi
- **Banco de Dados**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Containerização**: Docker & Docker Compose

## 📦 Instalação

### 🐳 Usando Docker (Recomendado)

**Instalação simples em 2 comandos:**

1. Clone o repositório e execute:
   ```bash
   git clone <repo-url>
   cd ubiquitous-octo-waffle
   docker-compose up -d
   ```

2. Acesse no navegador:
   ```
   http://localhost:8080
   ```

**O que acontece automaticamente:**
- ✅ MySQL é configurado e populado com dados de exemplo
- ✅ PHP 8.1 + Apache são configurados
- ✅ Extensões PHP (mysqli, pdo) são instaladas
- ✅ Banco de dados é criado e inicializado com `db-setup.sql`
- ✅ Website fica disponível imediatamente

**Para parar:**
```bash
docker-compose down
```

### 📋 Instalação Manual

1. Configure um servidor web (Apache/Nginx) com PHP
2. Configure MySQL/MariaDB
3. Crie arquivo `.env` com as configurações do banco:
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   DB_DATABASE=botbot_electronics
   APP_NAME="Bot Bot Electronics"
   ```
4. Execute `php script.php` para criar o banco
5. Configure o servidor web para servir os arquivos

## 🎨 Design

### Características Visuais
- **Paleta de Cores**: 
  - Azul (#3498db) para marcas
  - Roxo (#9b59b6) para categorias
  - Vermelho (#e74c3c) para preços
  - Cinza (#95a5a6) para elementos secundários

- **Tipografia**: Arial, sans-serif
- **Layout**: Cards responsivos com hover effects
- **Navegação**: Links categorizados e sistema de busca

### Componentes
- Cards de produto com imagens, preços e metadados
- Galeria de imagens com miniaturas
- Badges para marca e categoria
- Sistema de navegação por breadcrumbs

## 📊 Dados de Exemplo

O banco vem pré-populado com:
- **5 produtos** (iPhone, Notebook, Fones, Tablet, Smartwatch)
- **3 marcas** (Apple, Asus, Samsung)
- **5 categorias** (Smartphones, Notebooks, Acessórios, Tablets, Wearables)
- **3 usuários** de exemplo com pedidos
- **3 pedidos** de demonstração com itens e status diferentes

### 👥 Usuários Demo
- **João Silva** (joao@email.com) - Tem pedido enviado
- **Maria Santos** (maria@email.com) - Tem pedido confirmado  
- **Pedro Oliveira** (pedro@email.com) - Tem pedido pendente

**Senha para todos**: `senha123`

## 🔍 Sistema de Busca

Busca inteligente que procura em:
- Nome dos produtos
- Descrições
- Nomes das marcas
- Nomes das categorias

Resultados ordenados por relevância (nome exato → marca → categoria → descrição).

## 🔒 Recursos de Segurança

- **Configuração segura**: Credenciais não ficam hardcoded no código
- **SQL Injection Protection**: Uso de prepared statements
- **XSS Prevention**: Escape de dados com `htmlspecialchars()`
- **Input Validation**: Validação rigorosa de parâmetros de entrada
- **Environment-based Settings**: Configurações diferentes para dev/prod

## ⚙️ Configuração

### Variáveis de Ambiente (.env)

```env
# Configurações do Banco de Dados
DB_HOST=db                    # 'db' para Docker, '127.0.0.1' para local
DB_PORT=3306
DB_USERNAME=usuario_teste
DB_PASSWORD=senha_teste
DB_DATABASE=botbot_electronics

# Configurações da Aplicação
APP_NAME=Bot Bot Electronics
APP_ENV=development
APP_DEBUG=true
TIMEZONE=America/Sao_Paulo
```

### Classes Principais

- **Config**: Gerencia variáveis de ambiente do arquivo .env
- **Database**: Singleton para conexões MySQL com pool de conexões

## 🏗️ Arquitetura

### 🐳 Docker Compose

A aplicação usa dois containers:

#### 📊 `botbot_mysql`
- **Image**: mysql:latest
- **Porta**: 3306
- **Recursos**: 
  - Inicialização automática do banco com `db-setup.sql`
  - Health check para garantir que está pronto
  - Volume persistente para dados
  - Usuário e banco criados automaticamente

#### 🌐 `botbot_web`  
- **Image**: php:8.1-apache
- **Porta**: 8080 → 80
- **Recursos**:
  - Extensões PHP instaladas automaticamente
  - Código fonte montado em `/var/www/html`
  - Aguarda MySQL estar saudável antes de iniciar
  - Variáveis de ambiente configuradas

### 📋 Fluxo da Aplicação

1. `docker-compose up` → Inicia MySQL e aguarda ficar saudável
2. MySQL executa `db-setup.sql` automaticamente na primeira inicialização
3. Container web inicia com PHP + Apache configurados
4. `config.php` carrega as configurações do `.env`
5. `Database::getConnection()` conecta ao MySQL via hostname `db`
6. `index.php` lista produtos com navegação por categoria/marca
7. `produto.php` exibe informações detalhadas com galeria de imagens
8. `login.php` + `meus-pedidos.php` oferecem sistema de usuários
9. `comprar.php` permite criar novos pedidos

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob uma licença permissiva. Veja os comentários no código para mais detalhes.

## 👨‍💻 Autor

**LokKol17**
- Site: [lokkol17.dev](https://lokkol17.dev/Portifolio/)
- GitHub: [@LokKol17](https://github.com/LokKol17)

---

*"Sua loja de itens de excelente qualidade (e procedência duvidosa)" >w<*
