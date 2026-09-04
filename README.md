# Portal Administrativo de Cartas

Portal para gestão de cartas de Magic, Pokémon e Yu-Gi-Oh!, com acesso por login e senha e CRUD
completo de cartas.

Back-end em PHP 8.3 sem framework, sobre MySQL 8. Front-end em HTML5, CSS3 e JavaScript vanilla com
módulos ES nativos. Não tem Composer, não tem npm e nada de terceiros é carregado no navegador. O
código é todo autoral.

---

## Onde está cada item do enunciado

| Pedido | Onde está |
| ------ | --------- |
| Área administrativa com login e senha | `public/index.html` e a sessão de servidor em `src/Infrastructure/Security` |
| Listar, incluir, editar e excluir cartas | `public/cartas.html` e os casos de uso em `src/Application/Card` |
| Nome em inglês, e nome em português quando existe | tabela `cards`, coluna `name_pt` opcional |
| `<select>` de card game com os três jogos | tabela `games`, carregada por `GET /api/catalog` |
| Edição desabilitada, com fetch, loading e reset ao trocar de jogo | `public/assets/js/hooks/useEditionSelect.js` |
| JSON de edições do enunciado | tabelas `games` e `editions`, com os mesmos códigos |
| Imagem e raridade da carta | upload em `src/Infrastructure/Storage`, tabela `rarities` |
| PHP sem framework e MySQL | `src/`, sem Composer, e `database/init/` |
| HTML5, CSS3 e JavaScript vanilla | `public/assets`, sem npm e sem nada vindo de CDN |
| Schema e massa inicial | `database/init/01-schema.sql` e `02-seed.sql` |
| README com passos, credenciais e decisões | as três seções seguintes |

---

## Como inicializar

### Com Docker

Pré-requisito: Docker com o plugin Compose. Um comando só, sem nenhum passo antes ou depois:

```bash
docker compose up
```

O Compose constrói a imagem do PHP, o MySQL cria o schema e a massa inicial na primeira subida, e a
aplicação sobe depois que o healthcheck do banco passa. Do zero isso leva cerca de 20 segundos.
Quando o log parar, abra:

```
http://localhost:8080
```

Se esse endereço não abrir, tente `http://127.0.0.1:8080`. Em algumas instalações do Windows o
`localhost` resolve primeiro para IPv6 (`::1`) e o Docker Desktop publica a porta só em IPv4, então o
navegador bate num socket sem ninguém escutando. A aplicação funciona igual nos dois endereços,
porque o front monta as chamadas a partir de `window.location.origin` e nada no código fixa um host.

Comandos úteis:

```bash
docker compose up -d           # deixar rodando em segundo plano
docker compose up -d --build   # reconstruir a imagem depois de mexer no Dockerfile
docker compose logs -f app     # acompanhar o Apache e os erros de PHP
docker compose down            # parar, preservando o banco
docker compose down -v         # parar e apagar o banco, para recomeçar do zero
```

As portas 8080 (aplicação) e 3307 (MySQL) podem estar ocupadas na sua máquina. Para trocar, copie
`.env.example` para `.env` e ajuste `APP_HOST_PORT` e `DB_HOST_PORT`.

### Sem Docker

Pré-requisitos: PHP 8.1 ou superior com as extensões `pdo_mysql` e `fileinfo`, e um MySQL 8
acessível.

```bash
mysql -u root -p -e "CREATE DATABASE cartas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p cartas < database/init/01-schema.sql
mysql -u root -p cartas < database/init/02-seed.sql

export DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=cartas DB_USER=root DB_PASS=sua-senha
php -S localhost:8000 -t public router.php
```

O `router.php` existe só para o servidor embutido do PHP. Com Apache, quem faz esse papel são os dois
`.htaccess` em `public/`.

---

## Credenciais para teste

| E-mail | Senha | Perfil |
| ------ | ----- | ------ |
| `admin@cartas.local` | `admin123` | Administrador |
| `editor@cartas.local` | `editor123` | Editor |

Os dois usuários têm os mesmos poderes. O segundo existe para testar a listagem com outra sessão
aberta. A tela de login tem um botão que preenche as credenciais do primeiro, para poupar digitação
durante a avaliação.

---

## Decisões de UX e produto

### 1. O campo de edição diz em que estado está

O `<select>` de edição começa desabilitado com o texto "Selecione um card game primeiro". Durante a
busca ele mostra "Carregando edições..." com um indicador girando ao lado. Quando a lista chega, o
campo é habilitado e a legenda informa quantas edições vieram. Se a requisição falha, o campo avisa e
pede para escolher o card game de novo.

Escolhi assim porque um campo desabilitado e sem texto é o que mais trava quem não tem familiaridade
com a tecnologia: a pessoa clica, nada acontece, e ela não tem como adivinhar que primeiro precisa
escolher o card game. Com o estado escrito ali, a ordem de preenchimento se explica sozinha.

Um detalhe que veio junto: em rede local a resposta volta em poucos milissegundos, e o aviso de
carregamento piscaria rápido demais para ser lido. O front segura o estado por 320ms antes de popular
a lista, o suficiente para a pessoa perceber que a lista veio de uma busca de verdade.

### 2. Excluir acontece em duas etapas, com a carta na tela

O botão de excluir abre uma confirmação que repete a imagem, o nome da carta, o card game e a edição,
com os botões "Manter carta" e "Excluir".

Excluir é a única coisa que não dá para desfazer no portal, e as linhas da tabela ficam bem coladas
umas nas outras. Mostrar o registro dentro da confirmação resolve o erro mais comum aqui, que é
apagar a linha vizinha da que a pessoa queria. Os botões levam o nome do efeito de cada um no lugar
de "Sim" e "Não" porque, na pressa, quem decide olha o botão antes de ler a pergunta.

### Outras escolhas do mesmo tipo

- **Os filtros ficam na URL.** Busca, filtros, ordenação, página e formato de visualização vão para a
  query string. Recarregar devolve a mesma listagem, o botão de voltar funciona e um endereço como
  `/cartas.html?search=dragon&game_id=3` pode ser colado no chat da equipe para combinar exatamente
  qual lista está em discussão.
- **O formulário abre sobre a lista.** Incluir e editar usam o mesmo `<dialog>` por cima da listagem.
  Cadastrar cartas é um trabalho repetitivo, e manter a lista visível atrás do formulário evita a
  navegação de ida e volta a cada registro.
- **A mesma listagem em tabela e em galeria.** Conferir edição e raridade de várias cartas de uma vez
  pede as colunas alinhadas da tabela. Achar uma carta específica pede a imagem, porque quem lida com
  cartas reconhece a arte antes de ler o nome.

---

## Cadastro de cartas

| Campo | Regra |
| ----- | ----- |
| Nome em inglês | Obrigatório, até 180 caracteres |
| Nome em português | Opcional |
| Card game | Obrigatório, `<select>` com Magic: The Gathering, Pokémon e Yu-Gi-Oh! |
| Edição | Obrigatória, carregada por fetch a partir do card game escolhido |
| Raridade | Obrigatória, oito valores cadastrados |
| Imagem | Opcional, JPG, PNG, WEBP ou GIF de até 2 MB, com pré-visualização antes de salvar |

O comportamento do campo de edição está em `public/assets/js/hooks/useEditionSelect.js`: ele nasce
desabilitado, dispara `GET /api/editions?game=<id ou slug>` quando o card game muda, exibe o estado de
carregamento, popula o `<select>` com o resultado e limpa a seleção anterior a cada troca de jogo. Se
a pessoa trocar de jogo no meio de uma busca, a requisição anterior é abortada com `AbortController`
para a resposta atrasada não sobrescrever a lista nova.

O JSON de edições do enunciado virou as tabelas `games` e `editions`, com os mesmos códigos (`dom`,
`base1`, `lob` e os demais). Cadastrar uma edição nova é um `INSERT`, sem tocar no código.

### Massa inicial

O seed traz seis cartas com arte, servida de `public/assets/img/cards/`, e com datas de cadastro
espalhadas nos últimos meses em vez de todas no dia da primeira subida. As imagens vêm de Scryfall,
pokemontcg.io e YGOPRODeck. A arte pertence a Wizards of the Coast, The Pokémon Company e Konami, e
está aqui só para ilustrar o desafio.

### O que mais tem na listagem

Busca por nome em inglês ou português, filtro por card game e por raridade, ordenação, paginação,
resumo do acervo por card game no topo e layout responsivo. Abaixo de 760px a tabela vira uma lista
de cartões empilhados.

---

## Identidade visual

O portal segue a linguagem visual da LigaMagic, para quem já usa o site reconhecer os elementos no
primeiro acesso. Os valores saíram das variáveis CSS do próprio site (`template-package-v99-min.css`):
laranja `#ff5a00` para marca e navegação, `#f05626` na barra sob o cabeçalho, verde `#22b14c` na ação
que grava, vermelho reservado para excluir, Open Sans na tipografia e cantos de 4px e 8px.

A fonte é declarada como `"Open Sans", "Segoe UI", system-ui, Arial, sans-serif`. Nada é baixado de
CDN, então a aplicação abre igual sem conexão com a internet.

---

## Arquitetura

O back-end segue Clean Architecture com o domínio modelado em DDD. As dependências apontam sempre
para dentro, e o domínio não conhece PDO, sessão de PHP nem HTTP.

```
Presentation  ->  Application  ->  Domain  <-  Infrastructure
```

**Domain** guarda as entidades (`Card`, `User`, `Game`), os objetos de valor (`CardName`, `Email`,
`CardImage`, `CardCriteria`, os identificadores tipados) e as interfaces de repositório. Um
identificador inválido ou um nome vazio não conseguem existir, porque o construtor recusa.

**Application** tem um caso de uso por operação (`AuthenticateUser`, `CreateCard`, `UpdateCard`,
`DeleteCard`, `ListCards`), cada um invocável por `__invoke`. As portas em `Application/Port`
(`AuthenticationSession`, `LoginThrottle`, `ImageStorage`) dizem o que a aplicação precisa, sem dizer
como.

**Infrastructure** implementa essas portas com o que é específico da plataforma: PDO nos
repositórios, sessão de PHP na autenticação, sistema de arquivos no armazenamento de imagem, e a
camada HTTP com `Request`, `Response`, `Router` e `Kernel`. O `Container` é a única classe que conhece
todas as outras e faz o wiring à mão.

**Presentation** traduz HTTP para os casos de uso e o resultado para JSON, nos controllers e
presenters.

Leitura e escrita seguem caminhos separados. A escrita passa pelo agregado `Card` e pelo
`CardRepository`. A listagem usa `CardQueryService`, uma porta de leitura cujo adaptador devolve
`CardView` com jogo, edição e raridade resolvidos em um único JOIN. Sem essa separação, montar a tela
de listagem custaria uma consulta por carta.

Os erros de domínio são exceções tipadas (`ValidationFailed`, `EntityNotFound`,
`AuthenticationFailed`, `AccessDenied`, `TooManyAttempts`). Só o `ErrorResponder`, na borda HTTP, sabe
traduzi-las em 422, 404, 401, 403 e 429.

No front a divisão é a mesma ideia em outro vocabulário: `services` falam com a API, `hooks` guardam
comportamento com estado, `components` desenham pedaços da tela, `helpers` são funções puras e `pages`
orquestram. Nenhum componente chama `fetch` direto, e nenhum service toca no DOM.

O código não tem comentários. Os nomes, os tipos e o tamanho dos arquivos carregam a explicação, e
nenhum arquivo do projeto passa de 200 linhas.

---

## Estrutura do projeto

```
.
├── docker-compose.yml
├── router.php                      servidor embutido do PHP, uso opcional
├── database/init/                  schema e seed, executados na primeira subida
├── docker/php/                     imagem PHP-Apache, vhost e php.ini
├── public/
│   ├── index.html                  login
│   ├── cartas.html                 gerenciador
│   ├── api/index.php               front controller da API
│   ├── uploads/                    imagens enviadas
│   └── assets/
│       ├── img/cards/              arte das cartas da massa inicial
│       ├── css/
│       │   ├── main.css            importa os módulos abaixo
│       │   ├── base/               tokens, reset e responsivo
│       │   ├── layout/             cabeçalho, página e barra de filtros
│       │   ├── components/         botões, formulários, tabela, galeria, modais
│       │   └── pages/              tela de login
│       └── js/
│           ├── pages/              login.js e cards.js, os pontos de entrada
│           ├── components/         tabela, galeria, modais, toast, paginação
│           ├── hooks/              sessão, filtros na URL, debounce, requisição
│           ├── services/           httpClient e um service por recurso
│           └── helpers/            DOM, formatação, validação, estado de formulário
└── src/
    ├── autoload.php                autoloader PSR-4 próprio
    ├── Domain/
    │   ├── Shared/                 identificadores, ErrorBag e exceções de domínio
    │   ├── Identity/               User, Email, HashedPassword e as interfaces
    │   ├── Catalog/                Game, Edition, Rarity e as interfaces
    │   └── Card/                   agregado Card, objetos de valor e CardRepository
    ├── Application/
    │   ├── Port/                   sessão, trava de login e armazenamento de imagem
    │   ├── Identity/               autenticação, usuário atual e logout
    │   ├── Catalog/                catálogo e edições de um jogo
    │   └── Card/                   casos de uso e ReadModel da listagem
    ├── Infrastructure/
    │   ├── Config/                 leitura do ambiente
    │   ├── Persistence/            conexão PDO, repositórios e query service
    │   ├── Security/               sessão, bcrypt, CSRF e trava de tentativas
    │   ├── Storage/                gravação da imagem em disco
    │   ├── Http/                   Request, Response, Router, Kernel e erros
    │   └── Container.php           wiring manual
    └── Presentation/Http/          controllers, presenters e as rotas
```

---

## API

Todas as rotas respondem JSON. As que alteram dados exigem o cabeçalho `X-CSRF-Token`, cujo valor sai
de `GET /api/auth/me`.

| Método | Rota | O que faz |
| ------ | ---- | --------- |
| `GET` | `/api/auth/me` | Sessão atual e token anti-CSRF |
| `POST` | `/api/auth/login` | Autentica por e-mail e senha |
| `POST` | `/api/auth/logout` | Encerra a sessão |
| `GET` | `/api/catalog` | Card games e raridades |
| `GET` | `/api/editions?game=magic` | Edições de um card game |
| `GET` | `/api/cards` | Lista com `search`, `game_id`, `rarity_id`, `sort`, `page`, `per_page` |
| `POST` | `/api/cards` | Cadastra (multipart quando há imagem) |
| `GET` | `/api/cards/{id}` | Detalhe de uma carta |
| `PUT` | `/api/cards/{id}` | Atualiza |
| `DELETE` | `/api/cards/{id}` | Exclui, junto com a imagem no disco |

---

## Segurança

- Senhas em bcrypt via `password_hash`, conferidas com `password_verify`.
- Toda consulta ao banco usa prepared statement com parâmetros nomeados.
- Token anti-CSRF exigido em POST, PUT e DELETE, já que a sessão viaja em cookie.
- Cookie de sessão com `HttpOnly`, `SameSite=Lax` e id renovado no login.
- Cinco tentativas de login erradas seguidas travam novas tentativas por um minuto.
- O tipo da imagem é conferido pelo conteúdo do arquivo com `finfo`, o nome é gerado pelo servidor, e
  o Apache desliga o interpretador PHP dentro de `public/uploads`.
- A validação do formulário roda no navegador e de novo no servidor, que ainda confere se a edição
  enviada pertence mesmo ao card game enviado.

---

## Verificação

O fluxo foi exercitado ponta a ponta no ambiente Docker antes da entrega:

- Login com credencial errada, campos vazios e credencial válida, incluindo a trava por tentativas.
- CRUD completo pela API, com upload de imagem, troca de imagem, remoção de imagem e exclusão,
  conferindo que o arquivo sai do disco junto com o registro.
- Rejeição de arquivo que não é imagem, de requisição sem token CSRF e de acesso sem sessão.
- Busca com acento, busca com curinga `%` escapado, filtros, ordenação e paginação.
- Pelo navegador: troca de card game recarregando as edições e limpando a seleção anterior, estado de
  carregamento visível, validação campo a campo, criação, edição, exclusão com confirmação, troca
  entre tabela e galeria com a escolha sobrevivendo ao recarregamento, e o layout em 390px de largura.
- `php -l` em todos os arquivos PHP e `node --check` em todos os módulos do front.
