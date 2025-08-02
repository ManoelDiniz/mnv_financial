# MNV Financial 💰

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/TailwindCSS-4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4.0">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT License">
</p>

## 📖 Sobre o Projeto

MNV Financial é uma aplicação web moderna para gestão financeira pessoal, desenvolvida com Laravel 12 e tecnologias de ponta. O sistema permite controle completo das finanças pessoais de forma intuitiva e segura.

### 🎯 Objetivo

Fornecer uma solução completa para:
- Controle de receitas e despesas
- Gestão de contas bancárias
- Planejamento orçamentário
- Relatórios financeiros detalhados
- Análise de gastos e tendências

## ⚡ Status Atual

🚧 **Projeto em Desenvolvimento Inicial**

Atualmente o projeto conta com a estrutura base do Laravel. Consulte o arquivo [AVALIACAO_PROJETO.md](./AVALIACAO_PROJETO.md) para uma análise detalhada do estado atual e recomendações.

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP moderno
- **PHP 8.2+** - Linguagem de programação
- **MySQL/PostgreSQL** - Banco de dados
- **Redis** - Cache e sessões

### Frontend
- **Tailwind CSS 4.0** - Framework CSS utilitário
- **Alpine.js** - Framework JavaScript reativo
- **Vite** - Build tool moderna
- **Blade** - Template engine do Laravel

### Ferramentas de Desenvolvimento
- **Laravel Pail** - Log monitoring
- **Laravel Pint** - Code style fixer
- **Laravel Sail** - Ambiente Docker
- **PHPUnit** - Testes automatizados

## 🚀 Instalação e Configuração

### Pré-requisitos
- PHP 8.2 ou superior
- Composer
- Node.js 18+ e NPM
- MySQL/PostgreSQL

### Passo a Passo

1. **Clone o repositório**
```bash
git clone https://github.com/ManoelDiniz/mnv_financial.git
cd mnv_financial
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
Edite o arquivo `.env` com suas credenciais de banco de dados:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mnv_financial
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. **Execute as migrations**
```bash
php artisan migrate
```

6. **Instale dependências do frontend**
```bash
npm install
```

7. **Compile os assets**
```bash
npm run dev
```

8. **Inicie o servidor**
```bash
php artisan serve
```

Acesse: `http://localhost:8000`

## 🧪 Testes

Execute os testes automatizados:

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter ExampleTest

# Com cobertura
php artisan test --coverage
```

## 📝 Desenvolvimento

### Scripts Disponíveis

```bash
# Servidor de desenvolvimento completo
composer run dev

# Apenas servidor Laravel
php artisan serve

# Linting e correção de código
composer run pint

# Testes
composer run test
```

### Estrutura do Projeto

```
app/
├── Http/Controllers/    # Controladores
├── Models/             # Modelos Eloquent
├── Services/           # Lógica de negócio
└── Providers/          # Service Providers

resources/
├── views/              # Templates Blade
├── js/                 # JavaScript
└── css/                # Estilos CSS

database/
├── migrations/         # Migrações
├── seeders/           # Seeders
└── factories/         # Factories

tests/
├── Feature/           # Testes de funcionalidade
└── Unit/              # Testes unitários
```

## 🔒 Segurança

- Autenticação Laravel Sanctum
- Validação de dados de entrada
- Proteção CSRF
- Rate limiting
- Logs de auditoria

## 📊 Funcionalidades Planejadas

- [ ] Sistema de autenticação completo
- [ ] Dashboard financeiro
- [ ] Gestão de contas bancárias
- [ ] Registro de transações
- [ ] Categorização de gastos
- [ ] Relatórios e gráficos
- [ ] Orçamentos e metas
- [ ] Importação de extratos
- [ ] Notificações e lembretes
- [ ] API RESTful

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Contato

**Manoel Diniz** - [@ManoelDiniz](https://github.com/ManoelDiniz)

Link do Projeto: [https://github.com/ManoelDiniz/mnv_financial](https://github.com/ManoelDiniz/mnv_financial)

---

⭐ **Se este projeto te ajudou, considere dar uma estrela!**
