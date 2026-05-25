# OSWIS

Informační systém pro pořádání registračních akcí. Vznikl jako nedokončená bakalářská práce na Katedře informatiky Přírodovědecké fakulty Univerzity Palackého v Olomouci (KMI PřF UP). Od roku 2019 ho pro Seznamovák pro studenty UP používá studentská organizace STUDENTLIFE z.s. (která Seznamovák pořádá), jako následník původního IS na míru — ročně eviduje 450+ uživatelů (účastníci včetně organizačního týmu). Pokrývá přihlášky, platby, e-mailovou komunikaci s účastníky, evidenci kontaktů a provozní administraci.

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
- Vratky a opravy plateb jako oddělené záznamy se zápornou hodnotou, s e-mailovou notifikací účastníkovi.
- Záloha + doplatek workflow — registrace se aktivuje po zaplacení zálohy, doplatek do termínu.
- Export pro účetní v CSV a XLSX, agregace po turnusech a kategoriích.
- Přehledy nezaplacených (zálohy i doplatky).

### Události

- Hierarchie událostí je rekurzivní (libovolný počet úrovní) — `Event` se odkazuje na `superEvent` a má kolekci `subEvents`. V praxi typicky: **ročník → turnus → sub-event** (např. „Seznamovák 2026" → „1. turnus" → „Workshop", „Sportovní odpoledne"). Datový model neomezuje hloubku, takže lze modelovat třeba sérii ročníků, dlouhodobé programy, vícefázové akce.
- Kategorie událostí (`EventCategory`) — pro odlišení typů (Seznamovák, workshop, schůze, výlet, ...) a jejich vlastní logiky.
- Každá úroveň má vlastní termín (start/end), místo, organizátora (jako Participant typu *organizer*), kapacitu a viditelnost (public / draft / archived).
- Registrační rozsahy (`RegistrationOffer`) — jeden Event může mít několik nabídek registrace (pro různé kategorie účastníků, různé časové okno, různé cenové úrovně). Každý rozsah má vlastní cenu, zálohu, kapacitu, datum od/do.
- Příznaky (`ParticipantFlag`) seskupené do `FlagGroup` (s vlastní kategorií) jsou navázané na rozsah jako `RegistrationFlagOffer` / `RegistrationFlagGroupOffer` — modifikátor ceny/zálohy a kapacity (typ ubytování, dieta, doprava, velikost trička, slevové kódy). Skupina má pravidla výběru (jeden z, alespoň jeden, libovolně).
- Sub-event docházka (`SubEventAttendance`) — účastník přihlášený na nadřazenou akci si může vybrat konkrétní podakce, kterých se zúčastní.
- Year-clone wizard — kompletní zkopírování ročníku (turnusy, ceny, příznaky, organizační účastníci, e-mailové šablony), substituce roku v názvech a slugách, úprava dat per turnus.
- Kapacity a využití přepočítané live na úrovni akce, turnusu i příznaku; navíc historický snapshot — kdo byl účastník k danému dni (pro účetnictví a reporting).
- Veřejné stránky: kalendář akcí, leták akce, seznamy budoucích a minulých akcí.

### E-mailová komunikace

- Šablonovaný systém přes Twig + MJML (HTML maily, které drží i v Outlooku).
- Admin editor šablon — skupiny mailů, kategorie, vlastní Twig šablony.
- Vlákna v poště — maily ke konkrétní přihlášce se v Gmailu / Outlooku slepí do jednoho vlákna (per účastník, ne per uživatel, takže se ročníky neslévají).
- Historie komunikace u účastníka — chronologická osa s e-maily, telefonáty a chatem; telefonáty a chat se zapisují ručně.
- Ad-hoc compose — admin píše individuální e-mail účastníkovi z přehledu, naváže se na existující vlákno.
- Resend systémových mailů z adminu (s aktualizací tokenů a stavu).
- IMAP import přijaté pošty od účastníků do timeline.
- Auto-BCC na archivační adresu.
- Detekce automatických mailů (RFC 3834) — out-of-office respondery nedělají loopy. Ad-hoc compose se naopak prezentuje jako lidská korespondence.
- České skloňování jmen v oslovení (vokativ — „Petře" místo „Petr").
- Strukturovaná data pro shrnutí přihlášky — JSON-LD a HTML5 microdata schema.org `EventReservation`, plus přiložený `.ics` kalendář. Příjemce má v moderních mail klientech jednoklikem „Přidat do kalendáře", konkrétní podpora závisí na klientovi.

### Adresář kontaktů

- `AbstractContact` jako polymorfní základ — dva konkrétní typy: `Person` (osoba) a `Organization` (organizace). Sdílejí kontaktní detaily, adresy, soubory, poznámky, vazby na akce.
- Pozice (`Position`) — vazba osoba ↔ organizace s funkcí. Jedna osoba může mít více pozic v různých organizacích, v jedné organizaci i v různých funkcích, vázáno na časové období.
- Adresy (`ContactAddress`) — strukturovaně (ulice, číslo popisné, město, PSČ, stát, GPS souřadnice). Kontakt může mít víc adres (domov, práce, doručovací) s typem.
- Kontaktní detaily (`ContactDetail`) — typované (e-mail, telefon, web, IČO, DIČ, datová schránka, sociální sítě). Kontakt může mít víc detailů s rozlišením kategorie (osobní mail / pracovní mail / mobil / pevná linka / …). Kategorie (`ContactDetailCategory`) jsou rozšiřitelné — admin si přidá vlastní typ.
- Místa (`Place`) — samostatná entita s GPS pro vazbu na události, sub-eventy a mapu. Místa mohou tvořit vlastní hierarchii (`subPlaces` / `parentPlace`), označení patra a místnosti, vlastní ikonu pro mapu.
- Adresáře (`AddressBook`) — pojmenované skupiny kontaktů (instruktoři ročníku, partneři, dárci, alumni). Kontakt v ní je přes connection entitu, takže může být ve více adresářích současně.
- Připojené soubory a obrázky ke kontaktu, s automaticky generovanými variantami velikostí přes Liip Imagine.
- Poznámky ke kontaktům — interní / veřejné, s historií.

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

### Ionic aplikace (mobilní / portál / admin)

Jedna codebase, dva režimy podle role uživatele.

**Účastnický portál** (pro přihlášeného účastníka):

- Profil — vlastní registrace, platby, kalendář akcí.
- Mapa s místy akce (ubytování, sběrná místa, program) s lokalizací polohy a kompasem.
- Komunikační historie — timeline e-mailů, telefonátů, chatu k vlastnímu účtu.
- Quick-action deep-links — z mailu nebo notifikace přímo do konkrétní stránky v appce.
- Settings modal — backend switcher (test/prod), cache management, push consent, diagnostika.

**Administrátorské rozhraní** (pro organizační tým):

- Dashboard s přehledy.
- Účastníci — seznamy, detail s registracemi, platbami, příznaky, poznámkami; ruční zápis telefonátu / chatu do timeline.
- Události — přehled, detail, podakce (sub-events), kapacity, ceny, příznaky.
- Kalendář — všechny akce v časové ose.
- Adresář — osoby, organizace, místa, pozice.
- Web — správa stránek, aktualit.

Technologie a distribuce:

- **Ionic 8** + **Angular 21**.
- **Capacitor 8** — build native Android APK; iOS distribuovaná jako PWA (instalace „Add to Home Screen" ze Safari).
- JWT + refresh token autentizace, sdílená s REST API backendem.
- **Leaflet** pro mapy, s podporou několika tile vrstev (OpenStreetMap, MapyCz, OpenTopoMap).

### Generování dokumentů

- PDF přes mPDF (přehledy, potvrzení, prezenční listina, hromadné štítky).
- XLSX přes PhpSpreadsheet.
- CSV (RFC 4180, UTF-8 BOM pro Excel).
- QR kódy přes Endroid — CZ QR platba, identifikační QR.

### Web a stránky

- Statické stránky se slugem a rich-text obsahem, aktuality, FAQ, media galerie.
- Hlavní menu a footer, položky lze přidávat z různých bundlů.
- Sitemap a RSS feed — každý bundle si do nich přidává vlastní položky přes extender interfaces (`SitemapExtenderInterface`, `RssExtenderInterface`); sitemap pro vyhledávače, RSS pro čtečky (vlastní stylesheet pro hezké zobrazení v prohlížeči). Robots.txt.
- PWA — instalovatelnost přes `site.webmanifest` (theme color, splash, jméno aplikace), `browserconfig.xml` pro Windows tiles, kompletní set ikon (favicon 16/32, Apple touch 180, Android 192, msTile, safari-pinned-tab, mask-icon).

### SEO a sémantika

- Kompletní HTML meta tagy — title, description, autor, copyright, generator, Dublin Core jazyk, Revisit-After, canonical URL per stránka, geo lokace (`geo.position`, `ICBM`, OG latitude/longitude).
- Otevřený graf (Open Graph) a Twitter Card pro hezké náhledy při sdílení (title, description, image, locale, type, URL).
- App-level meta — `application-name`, `apple-mobile-web-app-title`, `theme-color`, `msapplication-TileColor` / `TileImage` — sjednocený vzhled v prohlížečích i jako home-screen app.
- Strukturovaná data schema.org pro vyhledávače — `Event` s datem začátku/konce, místem, organizátorem, hierarchií (`superEvent`), módem (`eventAttendanceMode`), stavem (`eventStatus`); breadcrumbs jako `BreadcrumbList`; navigace jako `SiteNavigationElement`.
- Optimalizace načítání — preload kritických CSS/JS, DNS prefetch a preconnect pro známé externí služby (Google Tag Manager, Analytics, fonty), asynchronní fragmenty přes hinclude.

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

OSWIS je rozdělen do čtyř Symfony bundlů, každý jako samostatný GitHub repozitář s vlastní historií a vlastním release cyklem:

- `oswis-core-bundle` — uživatelé, autentizace, JWT, QR, PDF, mailer subscriber, framework primitives, Twig extensions, RSS / sitemap / menu skeleton.
- `oswis-address-book-bundle` — kontakty, osoby, organizace, kontaktní detaily, místa.
- `oswis-calendar-bundle` — události, účastníci, příznaky, platby, IMAP, CZ QR platba, communication module.
- `oswis-web-bundle` — webové stránky, aktuality, FAQ, media galerie.

Plus produkční aplikace `oswis-seznamovak-up` (Symfony app, která 4 bundly slepí dohromady) a mobilní klient `seznamovak-up` (Ionic + Angular).

Bundly mezi sebou nejsou tight-coupled — komunikují přes extender interfaces a compiler passy. Aplikace si v `config/bundles.php` vybere, které z bundlů načte; novou položku do sitemapy, RSS feedu, menu nebo widgetu na úvodní stránce přidá libovolný bundle bez nutnosti změny core. To je hlavní mechanismus rozšíření OSWIS o vlastní funkce — `SitemapExtenderInterface`, `RssExtenderInterface`, `WebMenuExtenderInterface`, `UpdateExtenderInterface`.

### Použité technologie

Backend:

- **PHP 8.4+** (produkčně 8.5), **Symfony 8.0**.
- **Doctrine ORM 3.6** + **DBAL** pro databázi; rozšíření **Gedmo** pro Timestampable, SoftDeleteable, Sluggable, Loggable, Blameable.
- **API Platform 4.3** pro REST/JSON-LD API; integrace JWT přes **Lexik JWT Authentication Bundle** + refresh tokeny přes **Gesdinet JWT Refresh Token Bundle**; CORS přes **Nelmio**.
- **Symfony Mailer** s vlastním `MailerSubscriber` (Auto-Submitted, archiv BCC, Reply-To). **MJML** CLI pipeline pro responsivní HTML maily.
- **webklex/php-imap** pro IMAP fetch (read-only).
- **mPDF** pro PDF generování, **PhpSpreadsheet** pro Excel, **Endroid QR Code** + **Shoptet CzQrPayment** pro QR kódy.
- **bigit/vokativ** (vlastní fork) pro české skloňování jmen.
- **Vich Uploader** pro upload souborů, **Liip Imagine** pro varianty obrázků.
- **Symfony Rate Limiter** pro login throttling.

Frontend (admin web):

- **Twig** šablony, **Webpack Encore** asset pipeline.
- **Bootstrap 5**, **Stimulus** pro interaktivitu, **Symfony WebLink** pro preload hinting.

Frontend (mobilní / účastnický portál):

- **Ionic 8** + **Angular 21**, **Capacitor 8** pro Android build, PWA pro iOS.
- **Leaflet** pro mapy (OpenStreetMap, MapyCz, OpenTopoMap).

Databáze: **MariaDB 10.5+** nebo **PostgreSQL 13+** (aktuálně produkčně MariaDB).

Quality gate: **PHPStan** level `max` napříč všemi bundly.

### Historicky použité technologie

OSWIS prošel postupnou modernizací — některé volby z dřívějších let už neplatí:

- **Zurb Inky** (Foundation for Emails) pro responsivní HTML maily — nahrazeno **MJML** pipeline kvůli lepší podpoře v Outlooku a údržbě šablon.
- **Symfony 6.x / 7.x** → aktuálně **Symfony 8.0**.
- **API Platform 2.x / 3.x** → aktuálně **API Platform 4.3**.
- **Doctrine ORM 2.x** → **Doctrine ORM 3.6** (strict identity collision check).
- **PHP 7.x → 8.x → 8.4 → 8.5** (postupné upgrady).
- **Ionic 5 + Angular 14 + Capacitor 5** → aktuálně **Ionic 8 + Angular 21 + Capacitor 8**.
- Mobilní iOS dříve plánována jako Capacitor build → dnes distribuovaná jako PWA (jednodušší údržba, žádný Apple Developer Program).

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
- IMAP přístup k mailboxu, kam chodí pošta od účastníků (info@…), pokud chcete automatický import vlákna komunikace do admin timeline. Read-only přístup stačí.
- Node.js pro build mail šablon a admin assetů

Není potřeba:

- Cloud, Kubernetes, message queue
- Placené API třetí strany
- Specializovaný dev tým — standardní Symfony deploy (composer, npm, migrations)

---

## Kontakt

OSWIS vyvíjí GitHub organizace [oswis-org](https://github.com/oswis-org).
Dotazy, demo, zájem o nasazení: [mail@jakubzak.eu](mailto:mail@jakubzak.eu).
