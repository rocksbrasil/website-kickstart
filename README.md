# Website Kickstart 🚀

Bem-vindo ao **Website Kickstart**! Este projeto fornece um ponto de partida ágil e eficiente para o desenvolvimento de sites em PHP, utilizando o padrão **MVC** e o sistema de templates **Smarty**.

---

## 📌 Recursos

✅ Estrutura básica para projetos PHP
✅ URLs amigáveis configuradas via `.htaccess`
✅ Integração com **Smarty Templates**
✅ Organização em camadas (MVC)
✅ Código limpo e fácil de entender

---

## 📂 Estrutura do Projeto

```
website-kickstart/
├── css/            # Arquivos de estilo (CSS)
├── engines/        # Controllers PHP das páginas
├── plugins/        # Plugins adicionais
├── templates/      # Templates do Smarty em HTML
├── .htaccess       # Configuração do Apache para URLs amigáveis
├── config.php      # Configuração principal do projeto
├── functions.php   # Funções auxiliares
├── index.php       # Ponto de entrada da aplicação
├── session.php     # Gerenciamento de sessões do PHP
```

---

## 🚀 Como Começar

### 1️⃣ Clonar o repositório
O código não possui dependencias, e não necessita de composer, somente cole os arquivos no diretório de um servidor HTTPD com PHP.
```bash
git clone https://github.com/rocksbrasil/website-kickstart.git
```


### 4️⃣ Executar a aplicação
Você pode utilizar um servidor local como o embutido do PHP:
```bash
php -S localhost:8000
```

Acesse no navegador: [http://localhost:8000](http://localhost:8000)

---

## 📄 Como Criar uma Nova Página

### 1️⃣ Criar uma página nova, é muito fácil, cada / após a url, é o nome de sua página, um exemplo https://meusite.com/[nomedapagina]
No diretório `templates/`, crie um novo arquivo `.html`, por exemplo, `nomedapagina.html`:
```smarty
{include file="cabecalho.tpl" titulo="Título da Nova Página"}

<h1>{$titulo}</h1>
<p>{$conteudo}</p>

{include file="rodape.tpl"}
```

### 2️⃣ Criar o controlador da nova página
Crie um arquivo PHP no diretório `templates/`, por exemplo, `nomedapagina.php`, e codifique o backend de sua página.
Usamos uma variável (array) global chamada $_TEMPLATE, onde você pode enviar variáveis para seus templates. (views).
```php
<?php
$_TEMPLATE['titulo'] = "Meu Título";
$_TEMPLATE['conteudo'] = "Conteúdo";
?>
```

### 3️⃣ Criar estlização (CSS)
Crie um arquivo CSS no diretório `css/`, por exemplo, `nomedapagina.css`, e codifique a estilização de sua página.
Você também pode colocar estilos globais no arquivo `css/estilo.css`, destinado a estilizações que estão presentes em mais de uma página.
O arquivo de css da página, é automaticamente incluído no head da página.



### 4️⃣ Acessar a nova página
Após salvar os arquivos, acesse a nova página pelo navegador:
```
http://localhost:8000/nova_pagina.php
```


---

## 🤝 Contribuindo

Contribuições são bem-vindas! Se você encontrou um problema ou tem uma ideia para melhoria, sinta-se à vontade para abrir uma **issue** ou enviar um **pull request**.

---

## 📜 Licença

Este projeto está sob a licença **MIT**. Sinta-se livre para usá-lo e modificá-lo conforme necessário.

---

🚀 **Vamos construir algo incrível juntos!** 💡
