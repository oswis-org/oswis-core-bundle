# OSWIS

Informační systém pro pořádání registračních akcí. Vznikl pro provoz na produkci (Seznamovák UP, Univerzita Palackého v Olomouci), kde se používá od roku 2018 pro cca 700+ účastníků ročně. Pokrývá přihlášky, platby, e-mailovou komunikaci s účastníky, evidenci kontaktů a provozní administraci.

Kód je open source, sebehostitelný, bez závislostí na placených SaaS službách. Stack: PHP 8.4+ / Symfony 8 / Doctrine ORM 3.6 / API Platform 4 na backendu, Ionic 8 / Angular 21 pro mobilní aplikaci.

Aktuálně běží jeden produkční deploy (Seznamovák UP).

---

## Co OSWIS dělá

### Registrace účastníků

- Veřejný registrační formulář na vlastní subdoméně. K dispozici i embed verze pro vložení do externího webu (typicky se OSWIS spojí s marketingovým WordPressem na hlavní doméně).
- Magic-link login — vracející se účastník se přihlásí kliknutím na odkaz v e-mailu, bez hesla.
- Aktivace účtu přes potvrzovací e-mail po vytvoření přihlášky.
- Tokeny pro magic-link, aktivaci a reset hesla; admin je může resendnout, prodloužit nebo vytvořit nový.
- Příznaky (flags) s kapacitami a cenovými/zálohovými modifikátory — typ ubytování, dieta, doprava, velikost trička apod. Skupiny příznaků mají pravidla výběru (jeden z, alespoň jeden, libovolně).
- Kategorie účastníků: účastník, organizátor, team-member, staff. Každá s vlastním formulářem a workflow.
- Soft-delete s možností obnovy v adminu (účastník, kontakt, příznak, nabídka).
- Wizard pro hromadný přesun účastníků mezi turnusy nebo příznaky.
- Ochrana proti duplicitám: server-side deduplikace na úrovni vytvoření přihlášky (sliding window 60 s) + klient-side guard proti iOS Safari opakovanému odeslání formuláře.

### Platby

- Bankovní převod s českým QR kódem (CZ QR Payment). Klient skenuje, banka vyplní příkaz.
- Variabilní symbol = posledních 9 cifer telefonu účastníka, fallback ID přihlášky.
- Párování přijatých plateb na účastníky podle VS, jména, e-mailu, částky a aktivní akce. Nejednoznačné případy se nepárují automaticky, čekají na admina.
- Import bankovního výpisu z CSV přes admin UI.
- IMAP fetch e-mailových notifikací z banky — automatické párování bez ručního importu. Read-only přístup k mailboxu (jen SEARCH/FETCH s BODY.PEEK, žádné MOVE/STORE/EXPUNGE), tracking přes vlastní `last_seen_uid` v DB.
- Vratky a opravy plateb jako oddělené záznamy se zápornou hodnotou, s e-mailovou notifikací účastníkovi.
- Záloha + doplatek workflow — registrace se aktivuje po zaplacení zálohy, doplatek do termínu.
- Export pro účetní v CSV a XLSX, agregace po turnusech a kategoriích.
- Přehledy nezaplacených (zálohy i doplatky).

### Události

- Event s libovolně vnořenou hierarchií přes superEvent / subEvents. V praxi typicky dvě úrovně (ročník + turnus).
- Year-clone wizard — kompletní zkopírování ročníku (turnusy, ceny, příznaky, organizační účastníci, e-mailové šablony), substituce roku v názvech a slugách, úprava dat per turnus.
- Kapacity a využití přepočítané live na úrovni akce, turnusu i příznaku.
- Historický snapshot agregací — kdo byl účastník k danému dni.
- Stavy viditelnosti (public, draft, archived).
- Veřejné stránky kalendáře akcí, letáku akce, seznamů budoucích a minulých akcí.

### E-mailová komunikace

- Šablonovaný systém přes Twig + MJML (MJML build přes Node CLI binary).
- Admin editor šablon — skupiny mailů, kategorie, Twig šablony.
- Threading podle RFC 5322: `Message-ID`, `In-Reply-To`, `References`. Maily ke konkrétní přihlášce se v Gmailu/Outlooku slepí do jednoho vlákna. Threading scope je per účastník (ne per uživatel, jinak by se ročníky slévaly).
- IMAP fetch odchozích i příchozích mailů do databáze.
- Communication timeline u účastníka — mail, telefon, chat v jedné chronologické ose.
- Manuální zápisy komunikace (telefonát, osobní setkání, chat) — admin zapíše rukou.
- Ad-hoc compose — admin píše individuální e-mail účastníkovi z přehledu, s threading.
- `Auto-Submitted: auto-generated` (RFC 3834) pro systémové maily — brání mail-loopy s out-of-office respondery. Pro admin ad-hoc compose je hodnota `no`.
- Resend systémových mailů z adminu (s aktualizací tokenů a stavu).
- Auto-BCC na archivační adresu.
- České skloňování jmen v oslovení (vokativ) přes knihovnu `bigit/vokativ`.
- CLI příkaz pro doplnění chybějícího threading na historických mailech.

### Adresář kontaktů

- Osoby a organizace jako samostatné entity, vazby pozic (kdo kde co dělá).
- Strukturované adresy (ulice, obec, PSČ, stát, GPS).
- Typované kontaktní detaily (mail, telefon, web).
- Address books — sub-skupiny kontaktů.
- Připojené soubory a obrázky ke kontaktu, s automaticky generovanými variantami velikostí.
- Místa (Place) s GPS pro vazbu na události a sub-eventy.
- Poznámky ke kontaktům.

### Admin rozhraní (web)

- Detail účastníka — kontakt, registrace, platby, komunikace, poznámky, tokeny.
- Detail události — všechny turnusy, příznaky, kapacity, ceny, data, agregace.
- Soft-delete restore.
- CRUD nad katalogem příznaků, skupin příznaků, kategorií a registračních rozsahů.
- Editor e-mailových šablon.
- Bulk reassign wizard.
- Year-clone wizard.
- Aggregations — počty účastníků a plateb v různých řezech, live i historický snapshot.
- Communication module — timeline, unmatched IMAP inbox, admin compose, ruční IMAP refresh.
- Payments import — UI pro upload CSV a ruční párování.
- Notes — interní poznámky napříč entitami.

### Mobilní aplikace

- Ionic 8 + Angular 21, Capacitor 8 (Android build). iOS přes PWA.
- JWT + refresh token autentizace.
- Profil účastníka — jeho registrace, platby, kalendář.
- Mapa s místy akce. Tile vrstvy: OpenStreetMap, MapyCz, OpenTopoMap. Lokalizace polohy s kompasem (s timeout a guardem proti uvíznutému „Zjišťování polohy…").
- Komunikační historie — timeline mailů, telefonů, chatu pro vlastní účet.
- Quick-action deep-links — z mailu nebo notifikace přímo do konkrétní stránky v appce.
- Settings modal — backend switcher, cache management, push consent, diagnostika.

### Generování dokumentů

- PDF přes mPDF (přehledy, potvrzení, prezenční listina, hromadné štítky).
- XLSX přes PhpSpreadsheet.
- CSV (RFC 4180, UTF-8 BOM pro Excel).
- QR kódy přes Endroid — CZ QR platba, identifikační QR.

### Web (CMS-light)

- Statické stránky se slugem, rich-text obsahem.
- Aktuality.
- FAQ.
- Media galerie.
- Sitemap, robots.txt, Open Graph metadata.
- Site web manifest a browserconfig.xml.

### API

- REST + JSON-LD / Hydra přes API Platform 4.
- OpenAPI dokumentace automatická (Swagger UI, ReDoc).
- JWT (Lexik) s refresh tokeny (Gesdinet).
- CORS konfigurovatelné (Nelmio).
- Paginace, filtering, sorting přes API Platform.
- Serializační skupiny pro kontrolu shape resource.

### Bezpečnost

- Login throttling — 5 pokusů z IP+username za minutu (Symfony rate-limiter).
- HTTP security headers: HSTS preload, CSP, Referrer-Policy, COOP, X-Content-Type-Options.
- HTTP/2 + HTTP/3, TLS 1.3.
- `/.well-known/security.txt` (RFC 9116).
- `/.well-known/change-password` (W3C webappsec).
- Cookie Secure + HttpOnly + SameSite=Lax.
- CSRF na formulářích.
- Soft-delete a audit přes Doctrine Gedmo extensions.
- Trusted proxies pro stack s TLS terminací na nginxu.

### Provozní vlastnosti

- CLI příkazy: `oswis:imap:fetch`, `oswis:mail:backfill-threading`.
- Doctrine migrations.
- PHPStan level `max` napříč všemi bundly.
- Monolog strukturovaný logging.
- Webpack Encore pro admin assety.
- MJML CLI pipeline pro mail šablony.

### Lokalizace

- Čeština (UI, e-maily, dokumenty).
- Vokativ pro oslovení v mailech.
- UTF-8 napříč DB / HTTP / mail / PDF.
- ISO 8601 / RFC 3339 datetime v API, DD. MM. YYYY v UI, formátování CZK.

---

## Architektura

Backend: PHP 8.4+ (produkčně 8.5), Symfony 8.0, Doctrine ORM 3.6, API Platform 4.3.
DB: MariaDB 10.5+ nebo PostgreSQL 13+ (aktuálně produkčně MariaDB).
Mail: Symfony Mailer (SMTP), MJML pipeline, IMAP přes `webklex/php-imap`.
Web admin: Twig + Webpack Encore + Bootstrap 5.
Mobile: Ionic 8 / Angular 21, Capacitor 8 pro Android, PWA pro iOS.
Asset upload: Vich Uploader + Liip Imagine.
Auth: Symfony Security + Lexik JWT + Gesdinet refresh-token.
PDF: mPDF. Excel: PhpSpreadsheet. QR: Endroid + Shoptet CZ QR Payment.
Doctrine extensions: Gedmo (Timestampable, SoftDeleteable, Sluggable).
Quality gate: PHPStan level `max`.

OSWIS je rozdělen do čtyř Symfony bundlů (každý samostatný repozitář):

- `oswis-core-bundle` — uživatelé, autentizace, JWT, QR, PDF, mailer subscriber, framework primitives, Twig extensions.
- `oswis-address-book-bundle` — kontakty, osoby, organizace, kontaktní detaily, místa.
- `oswis-calendar-bundle` — události, účastníci, příznaky, platby, IMAP, CZ QR platba, communication module.
- `oswis-web-bundle` — webové stránky, aktuality, FAQ, media galerie.

Plus produkční aplikace `oswis-seznamovak-up` (Symfony app pro Seznamovák UP) a mobilní klient `seznamovak-up` (Ionic + Angular).

---

## Pro koho to dává smysl

OSWIS vznikl pro vícedenní pobytovou akci se silnou organizační složkou (Seznamovák UP). Hodí se na podobné akce — turnusy, příznaky pro ubytování a stravu, hromadné komunikace s účastníky, organizační tým s rolemi.

Nepokrývá oblasti, kde existují lepší specializované nástroje — účetnictví, daně, faktury, smlouvy, fotogalerie, externí marketing. Data se exportují ven (CSV, PDF) pro účetní a navazující SW.

---

## Self-hosting

OSWIS je standardní Symfony aplikace. Běží na VPS s PHP-FPM + nginx, na sdíleném hostingu s SSH, nebo v kontejneru. Žádné fronty ani daemony nejsou povinné (Redis a Messenger jsou volitelné, default je sync).

Potřeba:

- PHP 8.4+ (CLI a FPM)
- MariaDB 10.5+ nebo PostgreSQL 13+
- SMTP přístup pro odesílání pošty (libovolný provider)
- IMAP přístup k mailboxu, do kterého chodí bankovní notifikace, pokud chcete automatické párování plateb (read-only přístup stačí)
- Node.js pro build mail šablon a admin assetů

Není potřeba:

- Cloud, Kubernetes, message queue
- Placené API třetí strany
- Specializovaný dev tým — standardní Symfony deploy (composer, npm, migrations)

---

## Kontakt

OSWIS vyvíjí GitHub organizace [oswis-org](https://github.com/oswis-org).
Dotazy, demo, zájem o nasazení: [mail@jakubzak.eu](mailto:mail@jakubzak.eu).
