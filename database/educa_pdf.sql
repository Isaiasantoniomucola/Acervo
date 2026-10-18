-- EDUCA PDF - Banco de dados MySQL
CREATE DATABASE IF NOT EXISTS educa_pdf CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE educa_pdf;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  perfil ENUM('aluno','professor') NOT NULL DEFAULT 'aluno',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(80) NOT NULL UNIQUE,
  icone VARCHAR(40) NOT NULL DEFAULT 'bi-folder'
) ENGINE=InnoDB;

CREATE TABLE livros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(160) NOT NULL,
  subtitulo VARCHAR(160) NULL,
  autor VARCHAR(120) NOT NULL,
  descricao TEXT NULL,
  conteudo TEXT NULL COMMENT 'Um tópico por linha',
  categoria_id INT NULL,
  arquivo_pdf VARCHAR(190) NOT NULL,
  capa VARCHAR(190) NULL,
  paginas INT NOT NULL DEFAULT 0,
  tamanho_mb DECIMAL(6,1) NOT NULL DEFAULT 0,
  downloads INT NOT NULL DEFAULT 0,
  destaque TINYINT(1) NOT NULL DEFAULT 0,
  professor_id INT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
  FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE biblioteca (
  usuario_id INT NOT NULL,
  livro_id INT NOT NULL,
  favorito TINYINT(1) NOT NULL DEFAULT 0,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (usuario_id, livro_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE avaliacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  livro_id INT NOT NULL,
  usuario_id INT NOT NULL,
  nota TINYINT NOT NULL,
  comentario VARCHAR(500) NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_av (livro_id, usuario_id),
  FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE downloads_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  livro_id INT NOT NULL,
  usuario_id INT NULL,
  data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Dados iniciais. Senha dos utilizadores de teste: password
INSERT INTO usuarios (nome,email,senha,perfil) VALUES
('Prof. Carlos Silva','professor@educapdf.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','professor'),
('Aluno Teste','aluno@educapdf.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','aluno');

INSERT INTO categorias (nome,icone) VALUES
('Matemática','bi-calculator'),('Português','bi-chat-left-text'),('História','bi-bank'),
('Ciências Naturais','bi-flask'),('Tecnologia','bi-pc-display'),('Línguas','bi-translate'),
('Artes','bi-palette'),('Geografia','bi-globe-americas'),('Outras','bi-three-dots');

INSERT INTO livros (titulo,subtitulo,autor,descricao,conteudo,categoria_id,arquivo_pdf,paginas,tamanho_mb,downloads,destaque,professor_id) VALUES
('Matemática Básica','Teoria e Exercícios','João Silva','Livro completo de Matemática Básica, com teoria explicada e exercícios resolvidos, ideal para o ensino médio e superior.',
 'Números e Operações\nÁlgebra\nGeometria\nTrigonometria\nEstatística',1,'exemplo.pdf',320,2.4,12000,1,1),
('História de Angola','Da Colónia à Independência','Maria Fernandes','Visão geral dos principais acontecimentos da história de Angola, com mapas e cronologia.',
 'Período pré-colonial\nColonização\nLuta de libertação\nIndependência\nAngola contemporânea',3,'exemplo.pdf',280,3.1,982,1,1),
('Biologia Celular','Estrutura e Função','Ana Costa','Introdução à célula, organelas, divisão celular e metabolismo.',
 'Célula\nMembrana\nNúcleo\nMitose e meiose\nMetabolismo',4,'exemplo.pdf',450,3.8,642,1,1),
('Gramática da Língua Portuguesa','Língua Portuguesa','Pedro Alves','Gramática prática com exemplos e exercícios.',
 'Fonética\nMorfologia\nSintaxe\nOrtografia\nInterpretação de texto',2,'exemplo.pdf',368,2.9,765,0,1),
('Geografia do Mundo','Continentes e Países','Luís Mendes','Textos de apoio sobre a geografia física e humana.',
 'Continentes\nClima\nPopulação\nEconomia\nMeio ambiente',8,'exemplo.pdf',412,3.6,598,0,1),
('Física Moderna','Conceitos e Aplicações','Rita Gomes','Relatividade, física quântica e aplicações tecnológicas.',
 'Relatividade\nQuântica\nFísica nuclear\nSemicondutores',4,'exemplo.pdf',245,2.8,410,0,1);
