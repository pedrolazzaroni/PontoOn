# 🕒 PontoOn - Sistema de Gerenciamento de Ponto  

O **PontoOn** é um sistema web desenvolvido para facilitar o controle de entrada e saída de funcionários, permitindo que gestores acompanhem registros de ponto, gerenciem usuários e gerem relatórios detalhados com gráficos e análises de desempenho.  

## 🚀 Funcionalidades  

### 👥 Usuário Master e Vinculação de Funcionários  
- O usuário master pode criar e gerenciar funcionários vinculados a ele.  
- Acompanhamento dos registros de ponto em tempo real.  

### 📊 Painel Administrativo Completo  
- Histórico total de registros de ponto.  
- Visualização de horas extras e atrasos.  

### ⚙️ Gestão de Usuários  
- Criar, editar, ativar e desativar usuários.  

### 📅 Geração de Relatórios Inteligentes  
- Relatórios completos de todos os funcionários ou de um específico.  
- Filtros por período de tempo.  
- Gráficos de desempenho e resumos automáticos.  

## 🛠️ Tecnologias Utilizadas  

- **Laravel** - Framework PHP  
- **MySQL** - Banco de dados relacional  
- **Blade** - Template engine  
- **Bootstrap** - Interface responsiva  
- **Chart.js** - Geração de gráficos interativos  

## 📦 Instalação  

Siga os passos abaixo para rodar o projeto localmente:  

### 1️⃣ Clonar o repositório  
```bash
git clone https://github.com/seuusuario/PontoOn.git
cd PontoOn
```

### 2️⃣ Instalar as dependências do Composer  
```bash
composer install
```

### 3️⃣ Configurar o ambiente  
Copie o arquivo de exemplo **.env** e configure suas credenciais do banco de dados:  
```bash
cp .env.example .env
```

Edite o arquivo **.env** e ajuste as informações conforme necessário.  

### 4️⃣ Gerar a chave da aplicação  
```bash
php artisan key:generate
```

### 5️⃣ Criar as tabelas e popular o banco  
```bash
php artisan migrate
```

### 6️⃣ Iniciar o servidor local  
```bash
php artisan serve
```

Acesse o projeto no navegador: **http://localhost:8000**  

## 👥 Contribuição  

Sinta-se à vontade para contribuir! Você pode:  
- Abrir issues relatando bugs ou sugerindo melhorias.  
- Enviar pull requests com novas funcionalidades.  

## 🙌 Agradecimentos  

A ideia inicial do projeto surgiu graças à minha amiga [Julia](https://github.com/Juliaal390), que me inspirou a desenvolver essa solução. 

## 📄 Licença  

Este projeto é licenciado sob a **MIT License**.  

---

📢 **Gostou do projeto? Deixe uma ⭐ no repositório!**  

