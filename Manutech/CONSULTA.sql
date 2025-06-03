DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100),
  email VARCHAR(100),
  senha_hash VARCHAR(100)
);

INSERT INTO usuarios (nome, email, senha_hash) VALUES
('João Gerente', 'gerente@empresa.com', '123456'),
('Maria Funcionária', 'funcionaria@empresa.com', '123456');
