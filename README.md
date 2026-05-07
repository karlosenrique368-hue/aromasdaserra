# Pousada Aromas da Serra — Site v2.0 (não-WordPress)

Site institucional refeito do zero em PHP simples, focado em **UI/UX premium editorial** e fiel ao briefing institucional pós-revisão estratégica.

## Como rodar

1. O projeto já está em `c:\xampp\htdocs\aromasdaserra`.
2. Inicie o Apache no XAMPP.
3. Acesse: <http://localhost/aromasdaserra/>
4. Painel admin: <http://localhost/aromasdaserra/admin/>

No XAMPP, o painel usa SQLite local em `data/aromas.sqlite`, criado automaticamente no primeiro acesso. Em produção no Railway, o app usa MySQL automaticamente quando `MYSQL_URL`, `DATABASE_URL` ou as variáveis `MYSQLHOST`/`MYSQLDATABASE`/`MYSQLUSER` estiverem disponíveis.

## Stack

- **PHP** puro (sem framework) — `includes/header.php`, `includes/footer.php` e helpers simples
- **Admin** com autenticação, dashboard, editor de páginas, CRUDs e uploads premium
- **Banco**: SQLite no XAMPP; MySQL no Railway via variáveis de ambiente
- **Tailwind CSS** via CDN com tokens customizados (paleta floresta/terracota/cream/gold)
- **Tipografia editorial**: Italiana (display) + Cormorant Garamond (serif itálico) + Inter (sans)
- **Alpine.js 3.14** para menus dropdown e drawer mobile
- **Lucide 0.469** para ícones
- **Lenis** para smooth scroll
- **IntersectionObserver** para reveals (sem libs pesadas)

## Páginas implementadas

| Página | Arquivo | Status |
|---|---|---|
| Home | `index.php` | ✅ |
| A Pousada | `a-pousada.php` | ✅ |
| Chalés (Lavanda · Manjericão · Aromáticos) | `chales.php` | ✅ |
| Gastronomia Mediterrânea + Fondue | `gastronomia.php` | ✅ |
| Taberna do Monge + Ritual do Chá da Tarde | `taberna.php` | ✅ |
| Experiências (Ritual da Fogueira, Mandala, etc.) | `experiencias.php` | ✅ |
| Localização — Mar Vermelho, AL | `localizacao.php` | ✅ |
| **Itinerário até a Serra (NOVO)** | `itinerario.php` | ✅ |

## Correções aplicadas (Revisão Estratégica)

- ❌ Removido: "15 anos de história", "5 mil hóspedes", "12 experiências únicas"
- ❌ Removido: seção de depoimentos (não eram avaliações reais)
- ❌ Removido: cachoeira, bicicletas, trilhas, tour do café
- ✅ **Localização correta**: Mar Vermelho — Alagoas (não mais Viçosa)
- ✅ "Fogueira ao entardecer" → **"Ritual da Fogueira"**
- ✅ Destaque para **"Ritual do Chá da Tarde"** (página dedicada)
- ✅ "gastronomia sensorial" → **"Gastronomia Mediterrânea"**
- ✅ Adicionada **temporada de Fondue** como destaque
- ✅ Chalés:
  - **Lavanda** = único com vista panorâmica
  - **Manjericão** + **Aromáticos** (Alecrim, Capim Cidreira, Calêndula, Erva Doce, Melissa, Jasmim) = vista para o jardim
- ✅ **Página nova de Itinerário** com 6 paradas afetivas pelo trajeto
- ✅ UI premium com Embla Carousel, GLightbox, motion editorial, menu mobile fullscreen e controles touch
- ✅ Painel administrativo com editor de textos/imagens para todas as páginas principais
- ✅ Logo `assets/img/logoserra.jpg` aplicada no site, admin e favicon

## Painel administrativo

- URL local: <http://localhost/aromasdaserra/admin/>
- Login padrão: `admin@aromas.local`
- Senha padrão: `aromas2025`
- Editor de páginas: `/admin/pages.php`
- Uploads: `assets/uploads/`

## Deploy Railway

O repositório inclui `Dockerfile` e `railway.json`. Para deploy no Railway com MySQL, conecte o serviço MySQL ao app e garanta que uma destas configurações esteja disponível no serviço web:

- `MYSQL_URL` ou `DATABASE_URL`
- ou `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`

O app mantém `APP_BASE=""` em produção, então as URLs ficam na raiz do domínio Railway. No XAMPP, a base `/aromasdaserra` é detectada automaticamente.

## Imagens

Todas as imagens são placeholders de **Unsplash** (royalty-free) carregadas via CDN — substitua-as pelas fotos reais da pousada conforme forem disponibilizadas. As referências estão no código de cada página.

Recomendação: criar `assets/img/` com fotos reais e trocar as URLs no HTML.

## Customização rápida

- **Cores**: `assets/css/main.css` (variáveis no `:root`) e `tailwind.config` em `includes/header.php`
- **Tipografia**: `<link>` no `head` + `tailwind.config.fontFamily`
- **Navegação**: array `$NAV` em `includes/config.php`
- **Contatos** (telefone, email, WhatsApp): constantes em `includes/config.php`
