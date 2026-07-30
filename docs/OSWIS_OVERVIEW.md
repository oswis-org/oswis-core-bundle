# OSWIS

Informační systém pro pořádání registračních akcí. Vznikl jako nedokončená bakalářská práce na Katedře informatiky Přírodovědecké fakulty Univerzity Palackého v Olomouci (KMI PřF UP). Od roku 2019 ho pro Seznamovák pro studenty UP používá studentská organizace STUDENTLIFE z.s. (která Seznamovák pořádá), jako následník původního IS na míru — ročně eviduje 450+ uživatelů (účastníci včetně organizačního týmu). Pokrývá přihlášky, platby, e-mailovou komunikaci s účastníky, evidenci kontaktů a provozní administraci.

Kód je open source, sebehostitelný, bez závislostí na placených SaaS službách. Stack: PHP 8.5 / Symfony 8.1 / Doctrine ORM 3.6 / DBAL 4 / API Platform 4.3 na backendu, Ionic 8 / Angular 22 / Capacitor 8 pro mobilní aplikaci.

Aktuálně běží jeden produkční deploy (Seznamovák UP) — PHP 8.5.8, Symfony 8.1.0, MariaDB 11.8, nginx → Apache → PHP-FPM.

---

## Co OSWIS dělá

### Registrace účastníků

- Veřejný registrační formulář na vlastní subdoméně. K dispozici i embed verze pro vložení do externího webu (typicky se OSWIS spojí s marketingovým WordPressem na hlavní doméně).
- Magic-link login — vracející se účastník se přihlásí kliknutím na odkaz v e-mailu, bez hesla.
- Aktivace účtu přes potvrzovací e-mail po vytvoření přihlášky.
- Tokeny pro magic-link, aktivaci a reset hesla; admin je může resendnout, prodloužit nebo vytvořit nový.
- Samoobslužná změna přístupových údajů (`AppUserEditRequest` → `AppUserEdit`) — uživatel si sám požádá o změnu
  e-mailu, uživatelského jména nebo hesla; přijde mu potvrzovací odkaz s platností a změna se provede až po
  potvrzení. Nová hodnota tedy nikdy nezávisí jen na tom, kdo měl otevřenou session.
- Příznaky (flags) s kapacitami a cenovými/zálohovými modifikátory — typ ubytování, dieta, doprava, velikost trička apod. Skupiny příznaků mají pravidla výběru (jeden z, alespoň jeden, libovolně).
- Kategorie účastníků: účastník, organizátor, team-member, staff. Každá s vlastním formulářem a workflow.
- Soft-delete s možností obnovy v adminu (účastník, kontakt, příznak, nabídka).
- Wizard pro hromadný přesun účastníků mezi turnusy nebo příznaky.
- Ochrana proti duplicitám: server-side deduplikace na úrovni vytvoření přihlášky (krátký časový limit) plus klient-side guard proti iOS Safari opakovanému odeslání formuláře.

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
- Obsah a přílohy akce — strukturované textové bloky (`EventContent`), připojené soubory (`EventFile`)
  a obrázky (`EventImage`) s automatickými variantami velikostí. Akce má i vlastní příznaky (`EventFlag`)
  nezávislé na příznacích přihlášky — pro vlastnosti samotné akce, ne účastníka.
- Skupiny účastníků (`ParticipantGroup`) — pojmenované skupiny s **barvou** (odpovídá barvě pásku na ruce)
  a **pořadím na jídlo** (`mealOrder`, dietáři první). Drží se jich check-in, výdej stravy i tiskové seznamy,
  takže jde o provozní kostru akce, ne jen o štítek.
- Podtýmy (`StaffTeam`) — pojmenovaný tým v rámci akce se svými členy. Na podtým lze obsadit program
  najednou, místo vypisování jednotlivců.
- Veřejné stránky: kalendář akcí, leták akce, seznamy budoucích a minulých akcí.

### Program akce

- Program se skládá po dnech (`ProgramDay`) a sekcích (`EventSection`) — sekce je blok programu s časem, místem a typem (aktivita, jídlo, přesun, volno, schůze).
- Bloky a rotace — jedna aktivita se může konat v několika paralelních běhech, kterými skupiny rotují; editor to umí rozgenerovat, ne opisovat ručně.
- Jídelní sloty — čas výdeje jídla po skupinách (dietáři první), navázané na provoz kuchyně.
- Výstupy programu — z jednoho zdroje se generují různé pohledy: program pro účastníka, rozpis pro instruktora, rozpis pro kuchyň. HTML i PDF.
- Brána zveřejnění — program se účastníkům zobrazí teprve po explicitním zveřejnění; do té doby ho vidí jen tým.

### Obsazení programu týmem

- Funkce v týmu (`StaffRole`) — pojmenované role s pořadím (hlavní instruktor, zdravotník, fotograf, kuchyň, technika…).
- Přiřazení (`StaffAssignment`) — kdo dělá co a kdy. Obsadit lze **konkrétní osobu**, **celý podtým** nebo **externího člověka** (jméno bez účtu, typicky lektor zvenčí).
- Rozvrh obsazení („rošt") — mřížka sekce × role, s přehledem, co je neobsazené. Editovatelná ve webovém adminu i v mobilní aplikaci.
- Rozpis pro instruktora — každý člen týmu vidí jen svoje bloky, v mobilu i jako PDF.
- **Rozpis služeb** — druhá mřížka, dny × funkce, pro služby nenavázané na konkrétní program (kuchyň, noční
  hlídka, úklid, řidič). Připravuje se v klidu ve webovém adminu, v terénu se dolaďuje v mobilu; obojí nad
  týmiž daty, ne dva modely. Tiskne se jako PDF.

### Check-in a příjezd

- Check-in obrazovka per turnus — označení příjezdu, přehled kdo dorazil / nedorazil, řazení podle skupiny (dietáři první), barvy pásku nebo abecedy.
- Stanice check-inu (`CheckInStation`) a průchody (`ParticipantStationVisit`) — účastník během příjezdu projde několika stanicemi (registrace, platba, pásek, bezpečnostní list, technika) a je vidět, kde se tvoří front.
- Přehled průběhu pro vzdálené sledování — kolik přijelo, kolik prošlo kterou stanicí, seznam nedorazivších; read-only, auto-refresh.
- Papírový fallback jako plnohodnotná varianta (tým ho drží u stolu — nespoléhá na wifi a baterky): tiskový seznam check-inu, seznam po skupinách a páscích pro výdej stravy, předvyplněné bezpečnostní listy k podpisu.
- Parkování — příznaky na přihlášce (SPZ, karta) s nulovou cenou, aby se řešilo u příjezdu, ne dodatečně.

### Ubytování a spolubydlení

- Objekty a jednotky (`Facility`, `AccommodationUnit`, `Bed`) — budova → pokoj → konkrétní postel, včetně přistýlek.
- Rezervace (`Reservation`) — přidělení účastníka na postel, s cenovými šablonami (`PricingTemplate`) pro různé typy ubytování.
- Spolubydlení (`RoommateGroup`, `RoommatePreference`) — účastníci si mohou vyjádřit, s kým chtějí být; přidělování to bere v potaz.
- Kontroly při přidělování (`AccommodationWarning`) — upozorní na kolize (kluci a holky v jednom pokoji, přistýlka obsazená dřív než regulérní postel, nesplněná vzájemná preference); varují, neblokují.

### E-mailová komunikace

- Šablonovaný systém přes Twig + MJML (HTML maily, které drží i v Outlooku).
- Admin editor šablon — skupiny mailů, kategorie, vlastní Twig šablony.
- Vlákna v poště — maily ke konkrétní přihlášce se v Gmailu / Outlooku slepí do jednoho vlákna (per účastník, ne per uživatel, takže se ročníky neslévají).
- Historie komunikace u účastníka — chronologická osa s e-maily, telefonáty a chatem; telefonáty a chat se zapisují ručně.
- Ad-hoc compose — admin píše individuální e-mail účastníkovi z přehledu, naváže se na existující vlákno.
- Hromadný e-mail vybraným přihláškám — výběr v seznamu (i podle filtru), jeden text, samostatné zprávy do vlastních vláken.
- Automatické maily podle časového okna — šablona se naplánuje na období a cron ji rozešle těm, komu ještě neodešla. Součástí je **dry-run náhled** (komu by to teď šlo a proč), aby se dávka dala zkontrolovat před odesláním.
- Oznámení o změně a zrušení přihlášky — místo přeposlání celého shrnutí se pošle **výčet toho, co se změnilo**; při zrušení samostatné oznámení. Stejné chování z webového adminu, z API i z aplikace.
- Resend systémových mailů z adminu (s aktualizací tokenů a stavu). Typy, jejichž obsah nelze věrně zrekonstruovat (potvrzení konkrétní platby, aktivační odkaz), se odmítnou přeposlat — pro ně jsou dedikované akce, které vyrobí platný obsah: **„Aktivační e-mail"** (nový token) a **„Shrnutí přihlášky"** (pokyny k platbě a QR z aktuálních dat).
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

- **Sjednocený přehled přihlášek** — jeden seznam pro všechny řezy. Rozsah (ročník / turnus / všechny akce, kategorie účastníka) a filtr jsou v URL, takže je pohled odkazovatelný a dá se poslat kolegovi. Rychlé filtry (zaplaceno, nedoplaceno, nezaplacená záloha, přeplaceno, neaktivované, s poznámkou, stravovací omezení), fasety podle příznaků, řazení kliknutím na hlavičku a tiskový pohled.
- **Vyhledávání bez diakritiky** napříč jménem, e-mailem, telefonem a variabilním symbolem, s našeptávačem.
- **Pokročilý filtr výrazem** — booleovský výraz nad přihláškou (`hasFlag`, `hasFlagInCategory`, `isPaid`, `remainingPrice`, `isConfirmed`, `gender`, `eventSlug` …) pro dotazy, na které se pilulky nehodí.
- **Hromadné akce nad výběrem** — smazání (vratné), export (CSV/PDF), hromadný e-mail, přesun mezi turnusy. S limity na dávku a záznamem, co se povedlo a co ne.
- Detail účastníka — kontakt, registrace, platby, komunikace, poznámky, tokeny.
- Editace příznaků u přihlášky — včetně kategorií, které se při registraci nenabízejí (sleva, zkrácený pobyt, poznámka k platbě), textových hodnot u příznaku a možnosti vědomě překročit kapacitu.
- Check-in obrazovky turnusu, přehled průběhu příjezdu a tiskové seznamy.
- Editor programu — dny, sekce, bloky a rotace, výstupy, obsazení týmem.
- Správa přesměrování (`WebRedirect`) — stará adresa → nová, s **počítadlem zásahů a časem posledního**, takže
  je vidět, které přesměrování ještě někdo používá a které lze zrušit. Nutné, když se mezi ročníky mění slugy.
- Správa uživatelů a rolí, správa míst, správa stanic check-inu, rozpis služeb.
- České řazení podle abecedy (Collator `cs_CZ`) — Č za C, Š za S, ne podle bajtů.
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

### Frontendová aplikace (mobil / portál / administrace)

Jedna codebase, dva režimy podle role přihlášeného uživatele. Běží jako nativní Android build,
jako PWA na iOS i jako běžná webová aplikace v prohlížeči.

**Účastnický portál** (pro přihlášeného účastníka):

- Přehled vlastních přihlášek a jejich detail — co má zaplaceno, kolik zbývá, jaké má příznaky, na jakém turnusu je.
- Kalendář akcí s detailem a možností odskočit z místa v programu na mapu.
- Mapa míst akce (ubytování, sběrná místa, program) s vlastní polohou; několik podkladových vrstev.
- Historie komunikace — vlastní e-maily, telefonáty a záznamy kontaktu v jedné časové ose.
- Docházka na podakce — účastník si vybere, kterých dílčích programů se zúčastní.

**Administrace pro organizační tým:**

- Dashboard s přehledy.
- Účastníci — seznamy a detail s registracemi, platbami, příznaky a poznámkami; ruční zápis telefonátu nebo chatu do historie; export vybraných.
- Události — přehled, detail, podakce, kapacity, ceny, příznaky, registrační nabídky.
- **Program** — rozcestník a editor: dny, sekce, aktivity, bloky a rotace.
- **Mřížka obsazení a rozpis služeb** — kdo má jakou funkci v které sekci; obsadit lze osobu, celý podtým nebo externího člověka.
- **Itinerář** — každý člen týmu vidí jen svoje bloky.
- **Check-in** — obrazovky stanic pro příjezd, aby se odškrtávalo u stolu na telefonu nebo tabletu.
- Adresář — osoby, organizace, místa, pozice a funkce v organizacích.
- Kalendář všech akcí v časové ose.
- Web — správa stránek a aktualit včetně formulářů generovaných z popisu (nemusí se pro každý typ obsahu psát vlastní).
- Ubytování — přehled objektů, jednotek a přidělení.

**Vlastní účet:**

- Přihlášení heslem i magic-linkem, odhlášení.
- Změna hesla a žádost o změnu údajů, potvrzovaná odkazem v e-mailu.
- Nastavení — přepínač backendu (test/produkce), správa lokální cache, obecné volby.
- Diagnostika (o aplikaci / o zařízení) — verze, prostředí, stav úložiště; k přiložení do hlášení chyby.

Technologie a chování:

- **Ionic 8** + **Angular 22** + **TypeScript 6**, **Capacitor 8** pro Android build, PWA pro iOS
  („Přidat na plochu" ze Safari — žádný Apple Developer Program).
- **Zoneless change detection** — aplikace neběží na `zone.js`, změny se propagují signály. Je to výrazně
  úspornější, ale platí se za to: části Ionicu, které na `zone.js` spoléhaly, se musely nahradit vlastními
  komponentami. Nové UI se proto staví signálově, ne přes zapouzdřené overlaye.
- **JWT + refresh token** sdílené s REST API. Vypršení tokenu se řeší v HTTP interceptoru: požadavek, který
  narazí na neautorizováno, se **automaticky obnoví a zopakuje** — uživatel o tom neví a nepřihlašuje se znovu.
- Lokální úložiště pro přihlášení a rozpracovaná data; přepínač backendu, aby se šlo připojit na testovací instanci.
- Ochrana rozpracované práce — odchod ze stránky s neuloženými změnami se ptá, role rozhodují o dostupnosti obrazovek.
- **Leaflet** pro mapy (OpenStreetMap, MapyCz, OpenTopoMap).
- Formuláře generované z popisu (Formly) tam, kde by jinak vznikaly desítky téměř stejných šablon.

Co v aplikaci **není**, ať to nikoho nemate: **push notifikace** (žádný plugin ani serverová část — oznámení
chodí e-mailem), **skenování QR kódů** (QR se generují, nečtou) a **plnohodnotný offline režim** (aplikace
potřebuje připojení; lokální úložiště drží jen přihlášení a rozpracované formuláře). Distribuce zatím jde
mimo obchody — Android jako APK, iOS jako PWA.

### Generování dokumentů

- PDF přes mPDF (přehledy, potvrzení, prezenční listina, hromadné štítky).
- XLSX přes PhpSpreadsheet.
- CSV (RFC 4180, UTF-8 BOM pro Excel).
- Jednotná exportní pipeline — každý typ exportu je definice (sloupce, řazení, formát), takže CSV, XLSX i PDF
  vznikají z jednoho popisu a nedivergují. Součástí jsou stropy na počet řádků, aby export nesestřelil provoz.
- QR kódy přes Endroid — CZ QR platba, identifikační QR.

### Web a stránky

- Statické stránky se slugem a rich-text obsahem, aktuality, FAQ, media galerie.
- Stránka o zpracování osobních údajů (GDPR) jako součást skeletu — odkazuje se na ni registrační formulář i maily.
- Banner nad obsahem pro dočasná oznámení (změna termínu, uzavření přihlášek).
- Účastnický portál i ve webové verzi — kdo si nechce instalovat aplikaci, vidí své přihlášky a platby v prohlížeči.
- Hlavní menu a footer, položky lze přidávat z různých bundlů.
- Sitemap a RSS feed — každý bundle si do nich přidává vlastní položky přes extender interfaces (`SitemapExtenderInterface`, `RssExtenderInterface`); sitemap pro vyhledávače, RSS pro čtečky (vlastní stylesheet pro hezké zobrazení v prohlížeči). Robots.txt.
- PWA — instalovatelnost přes `site.webmanifest` (theme color, splash, jméno aplikace), `browserconfig.xml` pro Windows tiles, kompletní set ikon (favicon 16/32, Apple touch 180, Android 192, msTile, safari-pinned-tab, mask-icon).

### SEO a sémantika

- Kompletní HTML meta tagy — title, description, autor, copyright, generator, Dublin Core jazyk, Revisit-After, canonical URL per stránka, geo lokace (`geo.position`, `ICBM`, OG latitude/longitude).
- Otevřený graf (Open Graph) a Twitter Card pro hezké náhledy při sdílení (title, description, image, locale, type, URL).
- App-level meta — `application-name`, `apple-mobile-web-app-title`, `theme-color`, `msapplication-TileColor` / `TileImage` — sjednocený vzhled v prohlížečích i jako home-screen app.
- Strukturovaná data schema.org pro vyhledávače — `Event` s datem začátku/konce, místem, organizátorem, hierarchií (`superEvent`), módem (`eventAttendanceMode`), stavem (`eventStatus`); breadcrumbs jako `BreadcrumbList`; navigace jako `SiteNavigationElement`.
- Optimalizace načítání — preload kritických CSS/JS, DNS prefetch a preconnect pro známé externí služby (Google Tag Manager, Analytics, fonty), asynchronní fragmenty přes hinclude.

### Autentizace a autorizace

- **Sedm rolí v hierarchii** — `ROLE_EVERYBODY` → `ROLE_CUSTOMER` → `ROLE_USER` → `ROLE_MEMBER` →
  `ROLE_MANAGER` → `ROLE_ADMIN` → `ROLE_ROOT`. Vyšší role dědí oprávnění nižších. Účastník po registraci
  drží nejnižší přihlášenou roli (`ROLE_CUSTOMER`), administrace začíná až na `ROLE_MANAGER`. Role jsou
  entita v databázi, takže se dají pojmenovat a spravovat, ne jen zadrátovat v konfiguraci.
- **Celá administrace má podlahu na `ROLE_MANAGER`** už na úrovni firewallu, ne až v kontrolerech.
  Historicky tam byl `ROLE_CUSTOMER`, takže kterýkoli přihlášený účastník prošel firewallem a obrazovky
  bez vlastní kontroly role byly dosažitelné; kontrolery navíc svou vyšší úroveň vynucují samostatně.
- **Přihlášení bez hesla (magic-link)** — vracející se účastník klikne na odkaz v e-mailu. Vedle toho
  klasické přihlášení heslem pro tým a „zapamatuj si mě" na týden.
- **Typované jednorázové tokeny** s expirací: aktivace přihlášky, změna hesla, přihlášení z registrace,
  nahlášení zneužití. Admin je může poslat znovu, prodloužit nebo vydat nový.
- **Brzda proti hádání hesel** — nejvýše 5 pokusů na kombinaci IP a jména za minutu.
- **Autorizace na třech úrovních současně:** pravidla nad cestami (firewall), atribut požadované role
  na kontroleru a bezpečnostní výraz na API resource. Nic nespoléhá na jedinou vrstvu.
- **Firewally konfiguruje sám core bundle** (ne aplikace) — pět oddělených: vývojářské nástroje bez
  zabezpečení, obnovení tokenu, přihlášení do API, stateless API na JWT a stavová webová část.
  Aplikace tedy nemá vlastní `security.yaml`, který by se rozcházel s tím, co bundle očekává.

### Komunikace přes API

Mobilní aplikace i portál mluví s backendem výhradně přes toto API — není to druhá cesta k datům
vedle webu, je to *ta* cesta. Cokoli se v API změní (tvar resource, serializační skupiny, autorizace),
projeví se v aplikaci.

- **REST + JSON-LD / Hydra** přes API Platform 4.3, dokumentace se generuje sama (Swagger UI, ReDoc).
- **Přihlášení** — `POST /api/login` (jméno a heslo v JSON) vrátí **JWT s platností 1 hodiny** a refresh token.
  `POST /api/token/refresh` vydá nový JWT, odhlášení refresh token zneplatní. Veřejné jsou jen tyto
  cesty a registrace; vše ostatní pod `/api` vyžaduje platný JWT.
- **Stateless** — API nemá session ani cookie; každý požadavek nese `Authorization: Bearer`.
  Vypršení tokenu si klient neřeší ručně: interceptor požadavek obnoví a zopakuje.
- **CORS** je allowlist — povolené originy se konfigurují prostředím (ne hvězdička), povolené metody
  `GET, OPTIONS, POST, PUT, PATCH, DELETE`, hlavičky `Content-Type` a `Authorization`, `Link` se vystavuje
  kvůli stránkování.
- **Filtrování, řazení a stránkování** deklarativně na resource; **serializační skupiny** určují, co která
  operace vrací a přijímá, aby se do odpovědi nedostalo víc, než má.
- **Dedikované endpointy** tam, kde generický REST nestačí — změna turnusu u přihlášky, úprava příznaků,
  výstupy programu, export přihlášek. Důvod je praktický: API Platform 4 neresolvuje vnořené `{id}` relací,
  takže operace měnící vazbu mají vlastní endpoint s explicitním kontraktem místo tichého selhání.
- **Relace se posílají jako IRI** (`/api/events/12`), ne jako vnořený objekt — a při vytváření záznamu se
  nastavují setterem, ne konstruktorem, jinak zůstanou nenavázané.
- ⚠️ **Pozor při self-hostingu za WAFem:** aplikační firewall (např. OWASP CRS) běžně zahazuje `PUT`
  a `DELETE`, případně požadavky, jejichž tělo obsahuje adresu s parametry — a klient dostane chybu, která
  vypadá jako chyba API, přitom se do aplikace vůbec nedostala. Pokud API ze zařízení „náhodně" selhává,
  je to první místo, kam se podívat.

### Bezpečnost

- HTTP security headers: HSTS preload, CSP, Referrer-Policy, COOP, X-Content-Type-Options.
- HTTP/2 + HTTP/3, TLS 1.3.
- `/.well-known/security.txt` (RFC 9116).
- `/.well-known/change-password` (W3C webappsec).
- Cookie Secure + HttpOnly + SameSite=Lax.
- CSRF na formulářích. Role, přihlašování, tokeny a brzda proti hádání hesel → viz *Autentizace a autorizace*.
- Soft-delete a audit přes Doctrine Gedmo extensions.
- Trusted proxies pro stack s TLS terminací na nginxu.

### Provozní vlastnosti

- CLI příkazy pro operativní úlohy — spustitelné z cronu i ručně. Provozně nejdůležitější dva: **rozeslání
  naplánovaných mailů** (respektuje časová okna a co už komu odešlo) a **stahování pošty z IMAPu**
  (přírůstkové, se zapamatovaným stavem synchronizace, s limitem na dávku). Vedle nich sada jednorázových:
  doplnění vláken do historie, oprava jmen kontaktů, nasazení výchozích stanic check-inu a příznaků ročníku.
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

Bundly mezi sebou nejsou tight-coupled — komunikují přes extender interfaces a compiler passy. Aplikace si v `config/bundles.php` vybere, které z bundlů načte.

### Dědičnost šablon

- Twig: base layout v core-bundle definuje strukturu (head, navigace, footer, bloky pro obsah, asset pipeline). Konkrétní bundle si pro vlastní stránky specializuje, aplikace si přes standardní Symfony override mechanism (`templates/bundles/<BundleName>/...`) může jakoukoli šablonu přepsat **bez nutnosti forkovat bundle**.
- E-maily: vlastní MJML base layout (logo, hlavička, patička, ikony, jednotný vzhled), který extendují konkrétní typy mailů (shrnutí přihlášky, potvrzení platby, ad-hoc admin compose…). Změny brandingu se promítnou centrálně.
- Admin: vlastní base layout (`page-skeleton-web-admin.html.twig`) sdílený napříč bundly.

### Branding a tenant konfigurace

- Centrální konfigurace v `oswis.yaml` (PHP DI extension) — logo, theme color, jméno aplikace, jméno webu, organizační údaje, doména pro Message-ID, výchozí odesílatel pošty, archivační BCC adresa, jazyk a lokalizace.
- Asset overrides — ikony (favicon, Apple touch, Android, msTile, safari-pinned-tab), logo pro web i pro maily, OG image jsou per-deploy v `public/assets/`.
- Webové CSS i admin CSS přes Webpack Encore — aplikace má vlastní entry pointy a může přepsat Sass proměnné (barvy, fonty, breakpointy) bez zásahu do bundlu.
- Per-event branding — události a podakce mají vlastní barvu, krátký název, popis, slug, organizátora; promítá se na veřejných stránkách, do mailových šablon i do generovaných dokumentů.
- Konfigurace SMTP, IMAP, JWT secret, refresh token TTL a další citlivé hodnoty žijí mimo veřejné config soubory (env vars / `.env.local`), nikoli v deploy artefaktu.

### Možnosti rozšíření

OSWIS je stavěný tak, aby se vlastní funkce přidávaly **vedle** bundlů, ne do nich — fork není potřeba
a upgrade zůstane možný.

**Vlastní bundle jako plugin.** Nový Symfony bundle se zaregistruje v `config/bundles.php` a může přinést
vlastní entity, API resources, stránky, admin obrazovky, konzolové příkazy i migrace. Do existujících částí
systému se zapojí přes rozšiřovací rozhraní, která core vyhledá compiler passem — takže stačí službu
otagovat, nikde se nic neregistruje ručně:

- `SiteMapExtenderInterface` — vlastní položky do sitemapy pro vyhledávače.
- `RssExtenderInterface` — vlastní položky do RSS feedu.
- `WebMenuExtenderInterface` — položky do veřejného menu i do admin menu (včetně rozklikávacích sekcí a omezení podle role).
- `WebAdminMenuExtenderInterface` — položky specificky do administrace.
- `UpdateExtenderInterface` — vlastní krok do hromadné údržbové akce.
- `ExportDefinitionInterface` — vlastní typ exportu (sloupce, řazení, strop řádků); dostane zdarma CSV, XLSX i PDF.

**Bez psaní kódu** se dá změnit překvapivě mnoho, protože model je datově řízený: kategorie účastníků,
registrační nabídky s cenami a kapacitami, příznaky a jejich skupiny s pravidly výběru, kategorie
kontaktních detailů, funkce v týmu, stanice check-inu, e-mailové šablony (Twig se edituje v adminu),
skupiny a barvy pásků. Nová akce jiného typu je tedy typicky konfigurace, ne vývoj.

**Přepsání vzhledu a textů.** Jakoukoli Twig šablonu bundlu lze přepsat v aplikaci standardním Symfony
mechanismem (`templates/bundles/<BundleName>/...`) — bez forku. Branding (logo, barvy, ikony, patička,
odesílatel pošty) je konfigurace a assety, ne kód. Admin i web CSS jsou vlastní Encore entry pointy
aplikace, takže se dají přepsat Sass proměnné bez zásahu do bundlu.

**Kde rozšíření naopak nemá smysl.** Provoz akce (služby, doprava, ubytování, check-in) záměrně nežije
v entitě `Event` — ta popisuje jen *co se koná*. Pro provozní věci existují vlastní modely, a nová
provozní funkce by měla přidat svůj, ne rozšiřovat `Event` o další sloupce.

### Použité technologie

Backend:

- **PHP 8.5+** (produkčně 8.5.8), **Symfony 8.1**.
- **Doctrine ORM 3.6** + **DBAL 4** pro databázi; rozšíření **Gedmo** pro Timestampable, SoftDeleteable, Sluggable, Loggable, Blameable.
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

- **Ionic 8** + **Angular 22** + **TypeScript 6**, **Capacitor 8** pro Android build, PWA pro iOS.
- **Leaflet** pro mapy (OpenStreetMap, MapyCz, OpenTopoMap).

Databáze: **MariaDB** (produkčně 11.8; DBAL 4 zvládne i 10.5+, doporučeno 10.6+ kvůli deprecacím).
Doctrine samo umí i PostgreSQL, ale OSWIS na něm **není provozně vyzkoušený** — schéma vzniklo a je udržované
nad MariaDB, takže pro PostgreSQL počítejte s vlastním ověřením migrací.

Quality gate: **PHPStan** level `max` napříč všemi bundly.

### Historicky použité technologie

OSWIS prošel postupnou modernizací — některé volby z dřívějších let už neplatí:

- **Zurb Inky** (Foundation for Emails) pro responsivní HTML maily — nahrazeno **MJML** pipeline kvůli lepší podpoře v Outlooku a údržbě šablon.
- **Symfony 6.x / 7.x / 8.0** → aktuálně **Symfony 8.1**.
- **API Platform 2.x / 3.x** → aktuálně **API Platform 4.3**.
- **Doctrine ORM 2.x** → **Doctrine ORM 3.6** (strict identity collision check).
- **PHP 7.x → 8.x → 8.4 → 8.5** (postupné upgrady).
- **Ionic 5 + Angular 14 + Capacitor 5** → aktuálně **Ionic 8 + Angular 22 + Capacitor 8**.
- Mobilní iOS dříve plánována jako Capacitor build → dnes distribuovaná jako PWA (jednodušší údržba, žádný Apple Developer Program).
- Angular se `zone.js` → **zoneless** se signály. Úspornější a předvídatelnější, ale vynutilo si nahradit
  ty části Ionicu, které na `zone.js` spoléhaly, vlastními komponentami.

---

## Rozpracované a plánované

Poctivý stav, ne wishlist. OSWIS se vyvíjí proti jednomu reálnému provozu, takže se tu potkává hotový
kód s tím, co ještě nemá naplněná data nebo čeká na rozhodnutí.

**Hotové, ale zatím nepoužívané v provozu.** Ubytování a spolubydlení (objekty, pokoje, postele,
rezervace, preference spolubydlících) je postavené a v administraci dostupné, ale reálně se ještě
nepoužilo — přidělování dosud probíhá mimo systém. Totéž platí pro skupiny a barvy pásků: model stojí,
ale dokud je tým nenaplní, čekají na ně tiskové seznamy pro výdej stravy a řazení „dietáři první".
Program má editor i výstupy; naplnění konkrétního ročníku je organizační práce, ne vývoj.

**Rozpracované.** Informační architektura administrace se přestavuje — horní menu už je rozklikávací
podle oblastí, zbývá dotáhnout drobečkovou navigaci napříč stránkami a zeštíhlit úvodní obrazovku.
U obsazení programu celým podtýmem chybí možnost někoho z týmu pro danou sekci **odečíst**
(„celý tým bez jednoho") — bez toho by rozpis pro instruktora lhal, takže se to nejdřív musí dopočítat
na backendu a teprve pak nabídnout v rozhraní.

**Ve frontendové aplikaci konkrétně.** Editor programu i mřížka obsazení jsou hotové a v provozu;
obsazení **celým podtýmem** je postavené, ale ještě nevydané. Chybí to, co není vývoj, ale data — dokud se
tým nezaregistruje jako účastníci s příslušnou funkcí, nemá editor koho nabízet. Dál chybí **push
notifikace** (potřebují klientský plugin i serverovou stranu, dnes je nahrazuje e-mail), **skenování QR**
(mohlo by zrychlit check-in u stolu) a **plnohodnotný offline režim** — u vícedenní akce v areálu s horším
signálem je to reálné omezení, ne kosmetika. Distribuce přes obchody (Google Play, App Store) zatím není;
zvýšila by dosah, ale přináší si vlastní režii vydávání a recenzí.

**Postavené, ale úmyslně nezapnuté.** Upomínky nezaplacených plateb existují, ale v jediném dnešním
provozu se nepoužívají — je to rozhodnutí organizátorů, ne chybějící funkce.

**Kam to míří.** Automatizované testy jsou zatím tenké: hlavní kvalitní branou je statická analýza
na nejvyšší úrovni plus ruční a smoke ověření. Rozšiřování funkčního pokrytí je průběžná práce.
Vedle toho se plánují provozní kontroly, které samy hlásí tiché selhání — typicky „potvrzená přihláška
bez odeslaného shrnutí" nebo „e-mail, u kterého se odeslání nepovedlo" — protože právě nepřítomnost
akce je to, co běžný monitoring ani typová analýza neodhalí.

**Úvahy o větší přestavbě.** Existují návrhy na generační obměnu (čistý datový model bez historické
zátěže). Nejde se do ní překlápět naráz — místo toho se její návrhy vstřebávají do dnešního systému
po modulech, aby provoz nikdy nestál na rozestavěné verzi.

## Pro koho to dává smysl

OSWIS vznikl pro vícedenní pobytovou akci se silnou organizační složkou (Seznamovák UP). Hodí se na podobné akce — turnusy, příznaky pro ubytování a stravu, hromadné komunikace s účastníky, organizační tým s rolemi.

Nepokrývá oblasti, kde existují lepší specializované nástroje — účetnictví, daně, faktury, smlouvy, fotogalerie, externí marketing. Data se exportují ven (CSV, PDF) pro účetní a navazující SW.

---

## Self-hosting

OSWIS je standardní Symfony aplikace. Běží na VPS s PHP-FPM + nginx, na sdíleném hostingu s SSH, nebo v kontejneru. Žádné fronty ani daemony nejsou povinné (Redis a Messenger jsou volitelné, default je sync).

Potřeba:

- PHP 8.5+ (CLI a FPM)
- MariaDB 10.6+ (produkčně 11.8)
- SMTP přístup pro odesílání pošty (libovolný provider)
- IMAP přístup k mailboxu, kam chodí pošta od účastníků (info@…), pokud chcete automatický import vlákna komunikace do admin timeline. Read-only přístup stačí.
- Node.js pro build mail šablon a admin assetů. **Není volitelný:** bez nainstalovaných balíčků chybí MJML
  binárka, render mailu spadne a zpráva se neodešle — v evidenci zůstane záznam s důvodem, ale příjemci nic nepřijde.

Není potřeba:

- Cloud, Kubernetes, message queue
- Placené API třetí strany
- Specializovaný dev tým — standardní Symfony deploy (composer, npm, migrations)

---

## Kontakt

OSWIS vyvíjí Jakub Žák — [mail@jakubzak.eu](mailto:mail@jakubzak.eu), [github.com/oswis-org](https://github.com/oswis-org).
