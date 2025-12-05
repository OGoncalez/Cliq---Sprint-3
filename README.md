# CLIQ - Plataforma de Streaming de Filmes

![Logo Cliq](midias/LOGO%20CLIQ%20DEFINITIVA.png)

## Descrição

**CLIQ** é uma plataforma de streaming de filmes moderna e intuitiva, desenvolvida em PHP com MySQL, oferecendo gerenciamento completo de usuários, assinaturas, filmes e perfis. O sistema inclui uma interface administrativa robusta para gerenciadores de conteúdo e uma experiência de usuário otimizada com suporte a múltiplos perfis por conta.

---

## Funcionalidades Principais

### Para Usuários
- Catálogo de filmes organizado por categorias (Novidades, Recomendações, Lançamentos, Infantil)
- Sistema de múltiplos perfis por conta
- Busca e filtro avançado de filmes
- Interface responsiva e intuitiva
- Reprodução de filmes e trailers
- Recomendações personalizadas
- Chat de suporte integrado (Cliquetinho)
- Autenticação segura com hash de senha

### Para Administradores
- Gerenciamento completo de usuários
- CRUD de filmes (Criar, Ler, Atualizar, Deletar)
- Gerenciamento de assinaturas e planos
- Gráficos de análise (pizza chart)
- Controle de categorias de filmes
- Painéis administrativos protegidos
- Validação de dados e tratamento de erros

### Planos de Assinatura
- **Padrão**: R$ 29,99/mês (4 telas, 1080p Full HD)
- **Super**: R$ 49,99/mês (4 telas, 2K Quad HD)
- **VIP**: R$ 69,99/mês (4 telas, 4K Ultra HD)

---

## Stack Tecnológico

### Backend
- **PHP 7.4+** - Linguagem servidor
- **MySQL 5.7+** - Banco de dados
- **PDO** - Abstração de banco de dados
- **Session Management** - Controle de sessão

### Frontend
- **HTML5** - Estrutura
- **CSS3** - Estilização (com variáveis CSS, gradientes, animações)
- **JavaScript (Vanilla)** - Interatividade
- **Ionicons 7.1.0** - Ícones
- **SweetAlert2** - Alertas customizados
- **Chart.js** - Gráficos

### Ferramentas & Bibliotecas
- **Font Awesome** - Ícones adicionais
- **Poppins Font** - Tipografia
- **Bootstrap Grid** - Sistema de grid responsivo

---

## Estrutura do Projeto

```
Cliq_XAMPP/
├── midias/                      # Imagens e recursos
│   ├── LOGO CLIQ DEFINITIVA.png
│   └── criquetinho.png
├── data/
│   └── profiles.json            # Dados de perfis (JSON local)
├── uploads/                     # Diretório para uploads de imagens
│   ├── default-avatar.jpg
│   └── default-movie.jpg
│
├── PHP (Backend)
├── home.php                     # Página principal do usuário
├── kids.php                     # Seção infantil
├── perfil.php                   # Gerenciamento de perfis
├── logout.php                   # Logout
├── login.php                    # Login de usuários
├── autenticar.php               # Validação de autenticação
├── session.php                  # Gerenciamento de sessão
├── flash.php                    # Sistema de mensagens flash
├── conexao.php                  # Conexão com banco de dados
├── planos.php                   # Página de planos
├── checkout.php                 # Página de checkout
│
├── CRUD (Administrador)
├── CRUD.php                     # Gerenciamento de filmes
├── CRUDusers.php                # Gerenciamento de usuários
│
├── CSS (Estilos)
├── home.css                     # Estilos home
├── kids.css                     # Estilos kids
├── CRUD.css                     # Estilos CRUD filmes
├── CRUDusers.css                # Estilos CRUD usuários
├── formularios.css              # Estilos de formulários
├── planos.css                   # Estilos de planos
├── Iassine.css                  # Estilos gerais
│
├── JavaScript (Frontend)
├── home.js                      # Lógica home
├── kids.js                      # Lógica kids
├── CRUD.js                      # Lógica CRUD filmes
├── CRUDusers.js                 # Lógica CRUD usuários
├── perfil.js                    # Lógica perfis
│
└── README.md                    # Este arquivo
```

---

## Banco de Dados

### Tabelas Principais

#### `usuarios`
```sql
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    passHash VARCHAR(255) NOT NULL,
    perfil VARCHAR(50) DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `assinaturas`
```sql
CREATE TABLE assinaturas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    plano VARCHAR(50) NOT NULL,
    preco DECIMAL(10, 2),
    status VARCHAR(50) DEFAULT 'ativa',
    data_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_expiracao DATETIME,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```

#### `movies`
```sql
CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    year INT,
    genre VARCHAR(100),
    synopsis TEXT,
    image VARCHAR(255),
    movie_link VARCHAR(255),
    trailer_link VARCHAR(255),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `profiles` (JSON: `data/profiles.json`)
```json
[
    {
        "id": 1,
        "usuario_id": 1,
        "nome": "Meu Perfil",
        "image": "uploads/profile-1.jpg"
    }
]
```

---

## Instalação e Configuração

### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- XAMPP (recomendado) ou servidor web local
- Navegador moderno

### Passos de Instalação

1. **Clone ou baixe o projeto**
   ```bash
   cd htdocs/
   git clone https://github.com/seu-usuario/Cliq_XAMPP.git
   cd Cliq_XAMPP
   ```

2. **Crie o banco de dados**
   ```bash
   mysql -u root -p
   CREATE DATABASE db_Cliq;
   USE db_Cliq;
   # Execute os scripts SQL fornecidos
   ```

3. **Configure a conexão (conexao.php)**
   ```php
   $host = 'localhost';
   $dbname = 'db_Cliq';
   $username = 'root';
   $password = '';
   ```

4. **Crie as pastas necessárias**
   ```bash
   mkdir -p uploads data
   chmod 755 uploads data
   ```

5. **Configure o arquivo `data/profiles.json`**
   ```bash
   echo '[]' > data/profiles.json
   chmod 755 data/profiles.json
   ```

6. **Inicie o servidor XAMPP**
   - Apache + MySQL ativados

7. **Acesse a aplicação**
   ```
   http://localhost/Cliq_XAMPP/
   ```

---

## Uso

### Primeiro Acesso
1. Vá para a página de login (`login.php`)
2. Se não tem conta, acesse o registro
3. Após login, crie ou selecione um perfil
4. Se não tiver assinatura ativa, será redirecionado para os planos

### Administrador
1. Faça login com conta de administrador
2. Acesse "Administração" na navbar
3. **Gerenciar Filmes**: CRUD.php
   - Adicionar, editar e deletar filmes
   - Visualizar gráfico de gêneros
   - Filtrar por gênero e ano
4. **Gerenciar Usuários**: CRUDusers.php
   - Criar, editar e deletar usuários
   - Gerenciar assinaturas por usuário
   - Filtrar e ordenar usuários

### Usuário Regular
1. Explore o catálogo na home
2. Use a barra de pesquisa para encontrar filmes
3. Clique em um filme para ver detalhes
4. Acesse a seção "Infantil" para conteúdo kids
5. Abra "Perfil" para gerenciar múltiplos perfis

---

## Segurança

- Hashing de Senhas: Password_hash() com PASSWORD_DEFAULT
- Prepared Statements: PDO para prevenir SQL Injection
- Sessões: Session management com verificação de autenticação
- Controle de Acesso: Verificação de perfil admin/usuário
- XSS Protection: htmlspecialchars() em outputs
- CSRF Protection: Tokens implícitos via POST
- Validação de Email: filter_var() com FILTER_VALIDATE_EMAIL

---

## Customização

### Cores Principais (CSS Variables)
```css
:root {
    --primary-purple: #8e24aa;
    --dark-purple: #3a0c3a;
    --accent-pink: #eb6ad1;
    --accent-yellow: #ffeb3b;
}
```

### Modificar Planos
Edite os preços em:
- `CRUDusers.php` (função `createSubscription()`, `updateSubscription()`)
- `planos.php` (página de planos)

### Adicionar Categorias de Filme
Edite em `CRUD.php`:
```html
<select name="category" required>
    <option value="sua-categoria">Sua Categoria</option>
</select>
```

---

## Troubleshooting

### Erro: "Cannot redeclare function"
**Solução**: Verifique se está usando `require_once` em vez de `require` para arquivos como `session.php` e `flash.php`

### Erro: "Access Denied" ao conectar banco
**Solução**: Verifique usuário/senha em `conexao.php`

### Filmes não aparecem
**Solução**: Verifique se a tabela `movies` tem registros e se as URLs de imagens são válidas

### Navbar com glitches de animação
**Solução**: Limpe o cache (Ctrl+Shift+Delete) e recarregue (Ctrl+F5)

### Upload de imagem não funciona
**Solução**: Verifique permissões da pasta `uploads` (755) e tamanho máximo

---

## Funcionalidades Futuras

- Sistema de pagamento integrado (Stripe/PayPal)
- Watchlist/Favoritos
- Histórico de visualização
- Recomendações baseadas em IA
- App mobile
- Legendas em múltiplos idiomas
- Download offline
- Social sharing
- Notificações push

---

## Desenvolvedor

**Projeto CLIQ** - Plataforma de Streaming Educacional

Desenvolvido em PHP e JavaScript

---

## Licença

Este projeto está licenciado sob a MIT License - veja o arquivo LICENSE para detalhes.

---

## Contato e Suporte

Para suporte e dúvidas, utilize o chat integrado (Cliquetinho) ou entre em contato através do email de suporte.

---

## Agradecimentos

- Ionicons pela biblioteca de ícones
- Chart.js pelos gráficos
- SweetAlert2 pelos alertas
- Comunidade PHP e MySQL

---

**Última atualização**: Dezembro de 2025

---

## Changelog

### v1.0.0 (2025-12-05)
- Lançamento inicial
- Sistema de autenticação completo
- CRUD de filmes e usuários
- Gerenciamento de assinaturas
- Múltiplos perfis por usuário
- Interface responsiva
- Chat de suporte
- Gráficos de análise
