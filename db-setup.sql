-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS botbot_electronics;
USE botbot_electronics;

-- Tabela de categorias
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  descricao TEXT
);

-- Tabela de marcas
CREATE TABLE marcas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL
);

-- Tabela de produtos normalizada
CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  preco DECIMAL(10,2) NOT NULL,
  marca_id INT,
  categoria_id INT,
  FOREIGN KEY (marca_id) REFERENCES marcas(id),
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabela de imagens dos produtos
CREATE TABLE produto_imagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  nome_arquivo VARCHAR(255) NOT NULL,
  eh_principal BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Tabela de usuários
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  telefone VARCHAR(20),
  data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de endereços dos usuários
CREATE TABLE enderecos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  tipo ENUM('entrega', 'cobranca') NOT NULL,
  cep VARCHAR(10) NOT NULL,
  logradouro VARCHAR(200) NOT NULL,
  numero VARCHAR(10) NOT NULL,
  complemento VARCHAR(100),
  bairro VARCHAR(100) NOT NULL,
  cidade VARCHAR(100) NOT NULL,
  estado CHAR(2) NOT NULL,
  eh_principal BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de status dos pedidos
CREATE TABLE status_pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  descricao TEXT
);

-- Tabela de pedidos
CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  endereco_entrega_id INT NOT NULL,
  status_id INT NOT NULL,
  data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  data_entrega DATE,
  valor_total DECIMAL(10,2) NOT NULL,
  observacoes TEXT,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (endereco_entrega_id) REFERENCES enderecos(id),
  FOREIGN KEY (status_id) REFERENCES status_pedidos(id)
);

-- Tabela de itens do pedido
CREATE TABLE pedido_itens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Inserção das marcas
INSERT INTO marcas (nome) VALUES
('Apple'),
('Asus'),
('Samsung');

-- Inserção das categorias
INSERT INTO categorias (nome, descricao) VALUES
('Smartphones', 'Telefones inteligentes e celulares'),
('Notebooks', 'Laptops e notebooks'),
('Acessórios', 'Fones, capas e outros acessórios'),
('Tablets', 'Tablets e dispositivos móveis'),
('Wearables', 'Relógios inteligentes e dispositivos vestíveis');

-- Inserção dos produtos
INSERT INTO produtos (nome, descricao, preco, marca_id, categoria_id) VALUES
('IPHONE 5', 'Iphone com 128GB de armazenamento, mucho barato', 1299.00, 1, 1),
('Asus Vivobook 15', 'Notebook de 15 polegadas com 8GB de RAM, roda tudo, até disco de makita', 2499.00, 2, 2),
('Ipods Pro 2', 'Fone de ouvido com cancelamento de ruído, tão barato assim? só pode ser roubado...', 499.00, 1, 3),
('Galaxy Tab s10', 'Tablet com tela de 10.1 polegadas, perfeito pra jogos de ritmo', 899.00, 3, 4),
('Samsung Galaxy Watch 7', 'Relógio inteligente com monitoramento de atividades físicas, meio inútil pra você -_-', 799.00, 3, 5);

-- Inserção das imagens dos produtos
INSERT INTO produto_imagens (produto_id, nome_arquivo, eh_principal) VALUES
(1, 'iphone.jpg', TRUE),
(2, 'vivobook.jpg', TRUE),
(3, 'ipods.jpg', TRUE),
(4, 'tab_s10.jpg', TRUE),
(5, 'galaxy_watch_7.jpg', TRUE);

-- Inserção dos status de pedidos
INSERT INTO status_pedidos (nome, descricao) VALUES
('Pendente', 'Pedido realizado, aguardando confirmação'),
('Confirmado', 'Pedido confirmado, preparando para envio'),
('Enviado', 'Pedido enviado para entrega'),
('Entregue', 'Pedido entregue ao destinatário'),
('Cancelado', 'Pedido cancelado');

-- Inserção de usuários de exemplo
INSERT INTO usuarios (nome, email, senha_hash, telefone) VALUES
('João Silva', 'joao@email.com', '$2b$12$example_hash_1', '11999887766'),
('Maria Santos', 'maria@email.com', '$2b$12$example_hash_2', '11988776655'),
('Pedro Oliveira', 'pedro@email.com', '$2b$12$example_hash_3', '11977665544');

-- Inserção de endereços de exemplo
INSERT INTO enderecos (usuario_id, tipo, cep, logradouro, numero, bairro, cidade, estado, eh_principal) VALUES
(1, 'entrega', '01234-567', 'Rua das Flores', '123', 'Centro', 'São Paulo', 'SP', TRUE),
(2, 'entrega', '02345-678', 'Av. Brasil', '456', 'Jardins', 'São Paulo', 'SP', TRUE),
(3, 'entrega', '03456-789', 'Rua Augusta', '789', 'Vila Madalena', 'São Paulo', 'SP', TRUE);

-- Inserção de pedidos de exemplo
INSERT INTO pedidos (usuario_id, endereco_entrega_id, status_id, valor_total, observacoes) VALUES
(1, 1, 3, 1798.00, 'Entrega pela manhã, se possível'),
(2, 2, 2, 2499.00, NULL),
(3, 3, 1, 1698.00, 'Apartamento 45, interfone quebrado');

-- Inserção de itens dos pedidos
INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) VALUES
(1, 1, 1, 1299.00, 1299.00),
(1, 3, 1, 499.00, 499.00),
(2, 2, 1, 2499.00, 2499.00),
(3, 1, 1, 1299.00, 1299.00),
(3, 4, 1, 899.00, 899.00);
