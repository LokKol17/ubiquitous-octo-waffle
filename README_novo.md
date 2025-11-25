# Bot Bot Electronics 🛒

Uma loja online de produtos eletrônicos com uma estrutura de banco de dados normalizada e interface moderna.

## 🗄️ Estrutura do Banco de Dados

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
├── script.php              # Script de inicialização usando db-setup.sql
├── index.php               # Página principal com catálogo
├── produto.php             # Página de detalhes do produto
├── categoria.php           # Listagem por categoria
├── marca.php               # Listagem por marca
├── busca.php               # Sistema de busca
├── docker-compose.yml      # Configuração Docker
└── *.jpg                   # Imagens dos produtos
```

## 🚀 Funcionalidades

### ✅ Implementadas
- **Catálogo de Produtos**: Listagem com marcas, categorias e imagens
- **Detalhes do Produto**: Página individual com galeria de imagens
- **Navegação por Categoria**: Filtragem por tipos de produtos
- **Navegação por Marca**: Filtragem por fabricantes
- **Sistema de Busca**: Busca inteligente em produtos, marcas e categorias
- **Design Responsivo**: Interface adaptável para dispositivos móveis
- **Banco Normalizado**: Estrutura otimizada com relacionamentos

### 🚧 Em Desenvolvimento
- Carrinho de compras
- Sistema de usuários
- Processamento de pedidos
- Painel administrativo

## 🛠️ Tecnologias

- **Backend**: PHP 8+ com MySQLi
- **Banco de Dados**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Containerização**: Docker & Docker Compose

## 📦 Instalação

### Usando Docker (Recomendado)

1. Clone o repositório
2. Execute o Docker Compose:
   ```bash
   docker-compose up -d
   ```
3. Execute o script de inicialização:
   ```bash
   docker-compose exec web php script.php
   ```
4. Acesse `http://localhost:8080`

### Instalação Manual

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
- **3 usuários** de exemplo
- **Pedidos** de demonstração

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
DB_HOST=127.0.0.1
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

### Fluxo da Aplicação

1. `config.php` carrega as configurações do `.env`
2. `Database::getConnection()` estabelece conexão MySQL
3. `index.php` lista produtos com navegação por categoria/marca
4. `produto.php` exibe informações detalhadas com galeria de imagens
5. `busca.php` oferece sistema de busca inteligente
6. `categoria.php` e `marca.php` filtram produtos por tipo

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
