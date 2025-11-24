-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS botbot_electronics;
USE botbot_electronics;

-- Criação da tabela de produtos
CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  preco DECIMAL(10,2) NOT NULL,
  imagem VARCHAR(255)
);

-- Inserção dos produtos
INSERT INTO produtos (nome, descricao, preco, imagem) VALUES
('IPHONE 5', 'Iphone com 128GB de armazenamento, mucho barato', 1299.00, 'iphone.jpg'),
('Asus Vivobook 15', 'Notebook de 15 polegadas com 8GB de RAM, roda tudo, até disco de makita', 2499.00, 'vivobook.jpg'),
('Ipods Pro 2', 'Fone de ouvido com cancelamento de ruído, tão barato assim? só pode ser roubado...', 499.00, 'ipods.jpg'),
('Galaxy Tab s10', 'Tablet com tela de 10.1 polegadas, perfeito pra jogos de ritmo', 899.00, 'tab_s10.jpg'),
('Samsung Galaxy Watch 7', 'Relógio inteligente com monitoramento de atividades físicas, meio inútil pra você -_-', 799.00, 'galaxy_watch_7.jpg');
