# Bot Bot Electronics - Sistema de Catálogo de Produtos

Uma loja online simples em PHP com MySQL para exibir produtos eletrônicos.

## 📁 Estrutura do Projeto

```
ubiquitous-octo-waffle/
├── .env                    # Configurações de ambiente (não versionar)
├── .env.example           # Exemplo de configurações
├── .gitignore             # Arquivos a serem ignorados pelo Git
├── config.php             # Sistema de configuração e conexão com BD
├── index.php              # Página principal do catálogo
├── produto.php            # Página de detalhes do produto
├── script.php             # Script de setup do banco de dados
├── db-setup.sql          # SQL para criação do banco
├── docker-compose.yml    # Configuração do Docker para MySQL
└── *.jpg                 # Imagens dos produtos
```

## ⚙️ Configuração

### 1. Configurar Variáveis de Ambiente

Copie o arquivo de exemplo e configure suas variáveis:
```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:
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
```

### 2. Iniciar o MySQL (Docker)

```bash
docker-compose up -d
```

### 3. Criar Banco e Tabelas

```bash
php script.php
```

### 4. Iniciar o Servidor PHP

```bash
php -S localhost:8000
```

Acesse: http://localhost:8000

## 🛠️ Funcionalidades

- ✅ **Catálogo de Produtos**: Listagem de todos os produtos com imagens
- ✅ **Detalhes do Produto**: Página individual com informações completas
- ✅ **Design Responsivo**: Interface moderna que funciona em desktop e mobile
- ✅ **Configuração por .env**: Credenciais seguras através de variáveis de ambiente
- ✅ **Segurança**: Prepared statements, validação de entrada e escape de HTML
- ✅ **Error Handling**: Tratamento robusto de erros e exceções

## 🔒 Recursos de Segurança

- **Configuração segura**: Credenciais não ficam hardcoded no código
- **SQL Injection Protection**: Uso de prepared statements
- **XSS Prevention**: Escape de dados com `htmlspecialchars()`
- **Input Validation**: Validação rigorosa de parâmetros de entrada
- **Environment-based Settings**: Configurações diferentes para dev/prod

## 📱 Design

- **Layout moderno** com cards clicáveis
- **Efeitos hover** e transições suaves
- **Tipografia limpa** e hierarquia visual clara
- **Responsivo** para todas as telas
- **Loading states** e feedback visual

## 🏗️ Arquitetura

### Classes Principais

- **Config**: Gerencia variáveis de ambiente do arquivo .env
- **Database**: Singleton para conexões MySQL com pool de conexões

### Fluxo da Aplicação

1. `config.php` carrega as configurações do `.env`
2. `Database::getConnection()` estabelece conexão MySQL
3. `index.php` lista produtos com links para detalhes
4. `produto.php` exibe informações detalhadas do produto selecionado

## 🚀 Próximos Passos

- [ ] Implementar carrinho de compras
- [ ] Adicionar sistema de usuários e login
- [ ] Criar painel administrativo
- [ ] Implementar API RESTful
- [ ] Adicionar cache para melhor performance
- [ ] Implementar testes automatizados