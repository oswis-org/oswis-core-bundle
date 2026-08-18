# OSWIS

Informační systém pro pořádání registračních akcí — od přihlášky a platby přes komunikaci s účastníky
až po program, obsazení týmem a příjezd na místo.

Vznikl jako nedokončená bakalářská práce na Katedře informatiky Přírodovědecké fakulty Univerzity
Palackého v Olomouci (KMI PřF UP). Od roku 2019 ho pro Seznamovák pro studenty UP používá studentská
organizace STUDENTLIFE z.s., která Seznamovák pořádá, jako následníka původního IS na míru.

Není to prototyp: systém stojí pod ostrým provozem, kde jde o peníze a o to, aby se stovky lidí
dostaly na správné místo ve správný čas. Řádově jde o **stovky přihlášek ročně**, tisíce evidovaných
plateb a desetitisíce odeslaných zpráv za dobu běhu.

Kód je open source, sebehostitelný, bez závislostí na placených SaaS službách. Stack: PHP 8.5 /
Symfony 8.1 / Doctrine ORM 3.6 / DBAL 4 / API Platform 4.3 na backendu, Ionic 8 / Angular 22 /
Capacitor 8 pro mobilní aplikaci. Běží jeden produkční deploy (Seznamovák UP) — PHP 8.5.9,
Symfony 8.1.0, MariaDB 11.8.6, nginx → Apache → PHP-FPM.

## Obsah

- [Pro koho to dává smysl](#pro-koho-to-dává-smysl)
- **[Co systém umí](#co-systém-umí)** — [Přihlášky a účastníci](#přihlášky-a-účastníci) ·
  [Platby](#platby) · [Akce a jejich struktura](#akce-a-jejich-struktura) ·
  [Program a obsazení týmem](#program-a-obsazení-týmem) · [Příjezd a check-in](#příjezd-a-check-in) ·
  [Ubytování a spolubydlení](#ubytování-a-spolubydlení) ·
  [Stravování a jídelníček](#stravování-a-jídelníček) · [Nástěnka](#nástěnka) ·
  [Komunikace s účastníky](#komunikace-s-účastníky) · [Adresář kontaktů](#adresář-kontaktů) ·
  [Veřejný web](#veřejný-web) · [Dokumenty a exporty](#dokumenty-a-exporty)
- **[Rozhraní — kdo v čem pracuje](#rozhraní--kdo-v-čem-pracuje)** —
  [Webová administrace](#webová-administrace) · [Frontendová aplikace](#frontendová-aplikace)
- **[Platforma](#platforma)** — [Autentizace a autorizace](#autentizace-a-autorizace) ·
  [Komunikace přes API](#komunikace-přes-api) · [Bezpečnost](#bezpečnost) ·
  [Integrita dat při souběhu](#integrita-dat-při-souběhu) ·
  [Provoz a údržba](#provoz-a-údržba) · [Lokalizace a formáty](#lokalizace-a-formáty)
- **[Architektura a rozšiřitelnost](#architektura-a-rozšiřitelnost)** —
  [Rozdělení do bundlů](#rozdělení-do-bundlů) · [Dědičnost šablon](#dědičnost-šablon) ·
  [Branding a konfigurace nasazení](#branding-a-konfigurace-nasazení) ·
  [Možnosti rozšíření](#možnosti-rozšíření) · [Použité technologie](#použité-technologie) ·
  [Co se během let vyměnilo](#co-se-během-let-vyměnilo)
- [Rozpracované a plánované](#rozpracované-a-plánované)
- [Self-hosting](#self-hosting)

---

## Pro koho to dává smysl

OSWIS vznikl pro vícedenní pobytovou akci se silnou organizační složkou (Seznamovák UP). Hodí se na
podobné akce — turnusy, příznaky pro ubytování a stravu, hromadná komunikace s účastníky, organizační
tým s rolemi a programem.

Nepokrývá oblasti, kde existují lepší specializované nástroje — účetnictví, daně, faktury, smlouvy,
fotogalerie, externí marketing. Data se exportují ven (CSV, XLSX, PDF) pro účetní a navazující software.

---

## Co systém umí

### Přihlášky a účastníci

- Veřejný registrační formulář na vlastní subdoméně. K dispozici i embed verze pro vložení do externího
  webu (typicky se OSWIS spojí s marketingovým WordPressem na hlavní doméně).
- Aktivace přihlášky potvrzovacím e-mailem; přihlašování bez hesla magic-linkem →
  viz [Autentizace a autorizace](#autentizace-a-autorizace).
- Příznaky s kapacitami a cenovými i zálohovými modifikátory — typ ubytování, dieta, doprava, velikost
  trička. Skupiny příznaků mají pravidla výběru (jeden z, alespoň jeden, libovolně).
- **Dvojí kapacita: běžná a maximální.** Běžně se počítá proti běžné; správce může u konkrétního zápisu
  zaškrtnout „překročit kapacitu" a tím přepnout strop na maximální. Není to obejití limitu, jen posun
  na druhé číslo — pokud maximální není nastavená výš, zaškrtnutí nic nezmění. Bez vyplněné maximální
  kapacity znamená příznak „bez omezení".
- Kategorie účastníků: účastník, organizátor, člen týmu, staff. Každá s vlastním formulářem a workflow.
- Soft-delete s obnovou — smazaná přihláška, kontakt, příznak i nabídka se dají vrátit.
- Hromadný přesun účastníků mezi turnusy nebo příznaky (průvodce).
- Ochrana proti duplicitám: serverová deduplikace při vytváření přihlášky (krátké časové okno) a klientská
  pojistka proti opakovanému odeslání formuláře na iOS Safari.

### Platby

- Bankovní převod s českým QR kódem (CZ QR Payment) — účastník naskenuje, banka vyplní příkaz.
- Variabilní symbol = posledních 9 cifer telefonu účastníka, jinak ID přihlášky.
- Párování přijatých plateb podle VS, jména, e-mailu, částky a aktivní akce. Nejednoznačné případy se
  nepárují automaticky a čekají na obsluhu.
- Import bankovního výpisu z CSV. **Opakovaný import nic nezdvojí:** jednoznačnost platby drží
  unikátní index nad bankovním identifikátorem transakce a samotné zpracování výpisu je jištěné
  atomickým zámkem, takže dvě souběžná spuštění téhož importu proběhnou jen jednou.
- Potvrzení o přijaté platbě rozesílá cron, ne samotný import — u výpisu se stovkami řádků by
  synchronní odesílání request neuneslo.
- Vratky a opravy jako samostatné záznamy se zápornou hodnotou, s notifikací účastníkovi.
- Záloha + doplatek — přihláška se aktivuje po zaplacení zálohy, doplatek do termínu.
- Přehledy nezaplacených záloh i doplatků; agregace po turnusech a kategoriích; podklady pro účetní
  → viz [Dokumenty a exporty](#dokumenty-a-exporty).

### Akce a jejich struktura

- **Rekurzivní hierarchie** — `Event` odkazuje na `superEvent` a má kolekci `subEvents`. V praxi
  typicky ročník → turnus → podakce („Seznamovák 2026" → „1. turnus" → „Workshop"). Model hloubku
  neomezuje, takže lze modelovat i série ročníků, dlouhodobé programy nebo vícefázové akce.
- Každá úroveň má vlastní termín, místo, organizátora, kapacitu a viditelnost (veřejné / rozpracované /
  archivované). Kategorie akcí (`EventCategory`) odlišuje typy a jejich logiku.
- **Registrační nabídky** (`RegistrationOffer`) — jedna akce může mít víc nabídek (různé kategorie
  účastníků, časová okna, cenové úrovně), každá s vlastní cenou, zálohou, kapacitou a platností.
  Příznaky se na nabídku váží jako `RegistrationFlagOffer` / `RegistrationFlagGroupOffer`.
- **Docházka na podakce** (`SubEventAttendance`) — přihlášený na nadřazenou akci si vybere, kterých
  dílčích programů se zúčastní.
- **Klonování ročníku** — zkopíruje turnusy, ceny, příznaky, organizační účastníky i e-mailové šablony,
  nahradí rok v názvech a slugách a nechá upravit data per turnus.
- Kapacity a využití se počítají live na úrovni akce, turnusu i příznaku; navíc historický snapshot,
  kdo byl účastníkem k danému dni (pro účetnictví a reporting).
- **Obsah a přílohy akce** — strukturované textové bloky (`EventContent`), soubory (`EventFile`)
  a obrázky (`EventImage`) s automatickými variantami velikostí. Akce má i vlastní příznaky (`EventFlag`)
  nezávislé na příznacích přihlášky — pro vlastnosti akce, ne účastníka.
- **Skupiny účastníků** (`ParticipantGroup`) — pojmenované skupiny s **barvou** (odpovídá barvě pásku na
  ruce) a **pořadím na jídlo** (dietáři první). Drží se jich check-in, výdej stravy i tiskové seznamy,
  takže jde o provozní kostru akce, ne o štítek.
- **Podtýmy** (`StaffTeam`) — pojmenovaný tým v rámci akce se svými členy; lze na něj obsadit program
  najednou místo vypisování jednotlivců.

### Program a obsazení týmem

**Program:**

- Skládá se po dnech (`ProgramDay`) a sekcích (`EventSection`) — sekce je blok s časem, místem a typem
  (aktivita, jídlo, přesun, volno, schůze).
- **Bloky a rotace** — jedna aktivita se koná v několika paralelních bězích, kterými skupiny rotují;
  editor je rozgeneruje, neopisují se ručně.
- **Jídelní sloty** — čas výdeje po skupinách, navázaný na provoz kuchyně.
- **Výstupy** — z jednoho zdroje se generují různé pohledy: program pro účastníka, rozpis pro instruktora,
  rozpis pro kuchyň. HTML i PDF.
- **Brána zveřejnění** — účastníci program uvidí až po explicitním zveřejnění, do té doby jen tým.

**Obsazení:**

- **Funkce v týmu** (`StaffRole`) — pojmenované role s pořadím (hlavní instruktor, zdravotník, fotograf,
  kuchyň, technika).
- **Přiřazení** (`StaffAssignment`) — kdo dělá co a kdy. Obsadit lze **konkrétní osobu**, **celý podtým**
  nebo **externího člověka** (jméno bez účtu, typicky lektor zvenčí).
- **Mřížka obsazení programu** — sekce × funkce, s přehledem, co je neobsazené.
- **Rozpis služeb** — druhá mřížka, dny × funkce, pro služby nenavázané na program (kuchyň, noční hlídka,
  úklid, řidič). Připravuje se v klidu ve webové administraci, v terénu se dolaďuje v mobilu; obojí nad
  týmiž daty, ne dva modely.
- **Itinerář** — každý člen týmu vidí jen svoje bloky, v mobilu i jako PDF.

### Příjezd a check-in

- Obrazovka příjezdu per turnus — označení příjezdu, kdo dorazil a kdo ne, řazení podle skupiny
  (dietáři první), barvy pásku nebo abecedy.
- **Stanice** (`CheckInStation`) a **průchody** (`ParticipantStationVisit`) — účastník během příjezdu
  projde několika stanicemi (registrace, platba, pásek, bezpečnostní list, technika) a je vidět,
  kde se tvoří front.
- **Přehled průběhu pro vzdálené sledování** — kolik přijelo, kolik prošlo kterou stanicí, seznam
  nedorazivších; jen ke čtení, s automatickým obnovováním.
- **Papírový fallback jako plnohodnotná varianta** — tým ho drží u stolu a nespoléhá na wifi ani baterky:
  tiskový seznam příjezdu, seznam po skupinách a páscích pro výdej stravy, předvyplněné bezpečnostní
  listy k podpisu.
- **Parkování** — příznaky na přihlášce (SPZ, karta) s nulovou cenou, aby se řešilo u příjezdu, ne dodatečně.

### Ubytování a spolubydlení

- Objekty a jednotky (`Facility`, `AccommodationUnit`, `Bed`) — budova → pokoj → konkrétní postel,
  včetně přistýlek.
- Rezervace (`Reservation`) — přidělení účastníka na postel, s cenovými šablonami (`PricingTemplate`)
  pro různé typy ubytování.
- Spolubydlení (`RoommateGroup`, `RoommatePreference`) — účastníci vyjádří, s kým chtějí být;
  přidělování to bere v potaz.
- Kontroly při přidělování — upozorní na kolize (kluci a holky v jednom pokoji, přistýlka obsazená dřív
  než regulérní postel, nesplněná vzájemná preference). **Varují, neblokují.**

### Stravování a jídelníček

- **Jídelníček po turnusech** — tým zadá jídla na jednotlivé dny a časy (snídaně, oběd, večeře),
  ke každému může být víc **variant** (běžná, bezmasá, bezlepková…).
- **Volbu si dělá účastník sám** ve své aplikaci; kdo si nevybere nebo přijede bez telefonu, dostane
  volbu zapsanou od týmu **u příjezdového stolu**.
- **Brána zveřejnění** — dokud jídelníček není hotový, účastníci ho nevidí a sekce se jim vůbec
  nenabízí; žádná prázdná obrazovka.
- **Kuchyňský list ke stažení** — počty podle variant, u stravovacích omezení jmenovitě.
- Jedna volba na jedno jídlo je vynucená **databází** (unikátní index), ne jen kontrolou v kódu.

### Nástěnka

- Krátké vzkazy týmu účastníkům přímo v aplikaci — „dnes večer od 20:00", „změna kvůli počasí".
- Píše je tým ve své administraci, účastník je vidí na úvodní obrazovce portálu i ve vlastní sekci.
- Vzniklo jako náhrada za plakáty a hromadné maily u věcí, které platí jen pár hodin.

### Komunikace s účastníky

- Šablony přes Twig + MJML — HTML maily, které drží i v Outlooku. Šablony se edituje v administraci,
  ne v kódu.
- **Vlákna v poště** — maily ke konkrétní přihlášce se v Gmailu i Outlooku slepí do jednoho vlákna,
  a to per přihláška, ne per uživatel, takže se ročníky neslévají.
- **Historie komunikace u účastníka** — jedna časová osa s e-maily, telefonáty a chatem; telefonát
  a chat se zapisují ručně.
- **Individuální e-mail** účastníkovi z administrace, naváže se na existující vlákno.
- **Hromadný e-mail** vybraným přihláškám — jeden text, samostatné zprávy do vlastních vláken.
- **Automatické maily podle časového okna** — šablona se naplánuje na období a cron ji rozešle těm,
  komu ještě neodešla. Součástí je **náhled nasucho** (komu by to teď šlo a proč), aby se dávka dala
  zkontrolovat před odesláním.
- **Oznámení o změně a zrušení přihlášky** — místo přeposlání celého shrnutí se pošle **výčet toho, co se
  změnilo**; při zrušení samostatné oznámení. Stejné chování z administrace, z API i z aplikace.
- **Opětovné odeslání systémového mailu.** Typy, jejichž obsah nelze věrně zrekonstruovat (potvrzení
  konkrétní platby, aktivační odkaz), se přeposlat odmítnou — jinak by účastníkovi přišla nepravda nebo
  mrtvý odkaz. Pro ně jsou dedikované akce, které vyrobí platný obsah: **„Aktivační e-mail"** (vydá nový
  token) a **„Shrnutí přihlášky"** (pokyny k platbě a QR z aktuálních dat).
- **Import přijaté pošty přes IMAP** do historie; co se nepodařilo přiřadit, čeká v samostatné frontě.
- Archivační kopie (BCC) na zadanou adresu.
- **Detekce automatických odpovědí** (RFC 3834) — out-of-office respondery nedělají smyčky. Individuální
  mail od obsluhy se naopak prezentuje jako lidská korespondence.
- **Strukturovaná data ve shrnutí přihlášky** — JSON-LD a microdata schema.org `EventReservation`
  a přiložený `.ics`. V moderních klientech tak jde přidat akci do kalendáře jedním klikem.
- Oslovení ve správném pádu → viz [Lokalizace a formáty](#lokalizace-a-formáty).

### Adresář kontaktů

- `AbstractContact` jako polymorfní základ se dvěma konkrétními typy: `Person` a `Organization`.
  Sdílejí kontaktní detaily, adresy, soubory, poznámky i vazby na akce.
- **Pozice** (`Position`) — vazba osoba ↔ organizace s funkcí a časovým obdobím. Jedna osoba může mít
  víc pozic v různých organizacích i víc funkcí v jedné.
- **Adresy** (`ContactAddress`) — strukturovaně (ulice, číslo, město, PSČ, stát, GPS), víc adres s typem
  (domov, práce, doručovací).
- **Kontaktní detaily** (`ContactDetail`) — typované (e-mail, telefon, web, IČO, DIČ, datová schránka,
  sociální sítě) s kategorií (osobní / pracovní mail, mobil, pevná linka). Kategorie jsou rozšiřitelné
  z administrace.
- **Místa** (`Place`) — samostatná entita s GPS pro vazbu na akce, podakce a mapu. Místa mají vlastní
  hierarchii (budova → patro → místnost) a ikonu pro mapu.
- **Adresáře** (`AddressBook`) — pojmenované skupiny kontaktů (instruktoři ročníku, partneři, dárci,
  alumni); kontakt může být ve víc adresářích současně.
- Připojené soubory a obrázky s automaticky generovanými variantami velikostí.
- Poznámky ke kontaktům — interní i veřejné, s historií.

### Veřejný web

- Statické stránky se slugem a rich-text obsahem, aktuality, FAQ, media galerie.
- Kalendář akcí, leták akce, seznamy budoucích i minulých akcí.
- Stránka o zpracování osobních údajů (GDPR) jako součást skeletu — odkazuje se na ni registrační
  formulář i maily.
- Banner nad obsahem pro dočasná oznámení (změna termínu, uzavření přihlášek).
- **Účastnický portál i ve webové verzi** — kdo si nechce instalovat aplikaci, vidí své přihlášky
  a platby v prohlížeči.
- Hlavní menu a patička; položky do nich přidávají jednotlivé bundly →
  viz [Možnosti rozšíření](#možnosti-rozšíření).
- Sitemap, RSS feed (s vlastním stylesheetem, aby byl čitelný i v prohlížeči) a robots.txt.
- **Instalovatelnost jako PWA** — `site.webmanifest` (barva tématu, splash, jméno), `browserconfig.xml`
  pro Windows tiles a kompletní set ikon (favicon 16/32, Apple touch 180, Android 192, msTile,
  safari-pinned-tab, mask-icon).

**SEO a sémantika:**

- Kompletní meta tagy — title, description, autor, copyright, generator, jazyk podle Dublin Core,
  canonical URL per stránka, geo lokace (`geo.position`, `ICBM`, OG souřadnice).
- Open Graph a Twitter Card pro náhledy při sdílení.
- Meta na úrovni aplikace — `application-name`, `apple-mobile-web-app-title`, `theme-color`,
  `msapplication-TileColor` / `TileImage` — sjednocený vzhled v prohlížeči i po přidání na plochu.
- Strukturovaná data schema.org — `Event` s termínem, místem, organizátorem, hierarchií (`superEvent`),
  módem a stavem; drobečky jako `BreadcrumbList`; navigace jako `SiteNavigationElement`.
- Optimalizace načítání — preload kritických CSS/JS, DNS prefetch a preconnect pro známé externí služby,
  asynchronní fragmenty.

### Dokumenty a exporty

- **PDF** (mPDF) — přehledy, potvrzení, prezenční listiny, hromadné štítky, tiskové seznamy pro příjezd
  a kuchyň, bezpečnostní listy, rozpisy programu a služeb.
- **XLSX** (PhpSpreadsheet) a **CSV** (RFC 4180, UTF-8 BOM pro Excel).
- **Jednotná exportní pipeline** — každý typ exportu je definice (sloupce, řazení, formát), takže CSV,
  XLSX i PDF vznikají z jednoho popisu a nedivergují. Součástí jsou stropy na počet řádků, aby export
  nesestřelil provoz.
- **QR kódy** (Endroid) — CZ QR platba i identifikační kód.

---

## Rozhraní — kdo v čem pracuje

Obě rozhraní pracují nad **týmiž daty a týmiž pravidly** — nejde o dvě aplikace, ale o dvě vstupní cesty.
Co která oblast dělá, je popsané výš; tady je jen to, čím se rozhraní od sebe liší.

### Webová administrace

Nástroj pro registrační sezonu a přípravu akce — hodně dat na obrazovce, malý tým. Těžiště je desktop,
ale rozvržení je ověřené i na tabletu a telefonu (široké tabulky rolují ve vlastním rámečku, ne celou
stránkou) — u příjezdového stolu se obsluhuje z mobilu.

- **Úvodní obrazovka** — počty přihlášek po turnusech a **provozní hlídky**: kdo se přihlásil vícekrát
  a komu se nedoručilo shrnutí s pokyny k platbě. Obojí jsou stavy, které z běžného seznamu nejsou
  vidět, protože přihláška vypadá naprosto normálně.

- **Sjednocený přehled přihlášek.** Jeden seznam pro všechny řezy. Rozsah (ročník / turnus / všechny akce,
  kategorie účastníka) i filtr jsou v URL, takže je pohled odkazovatelný a dá se poslat kolegovi. Rychlé
  filtry (zaplaceno, nedoplaceno, nezaplacená záloha, přeplaceno, neaktivované, s poznámkou, stravovací
  omezení), fasety podle příznaků, řazení kliknutím na hlavičku, tiskový pohled.
- **Vyhledávání bez ohledu na diakritiku** napříč jménem, e-mailem, telefonem a variabilním symbolem,
  s našeptávačem.
- **Filtr výrazem** — booleovský výraz nad přihláškou (`hasFlag`, `hasFlagInCategory`, `isPaid`,
  `remainingPrice`, `isConfirmed`, `gender`, `eventSlug`) pro dotazy, na které se pilulky nehodí.
- **Hromadné akce nad výběrem** — smazání (vratné), export, hromadný e-mail, přesun mezi turnusy.
  S limity na dávku a výpisem, co se povedlo a co ne.
- **Detail účastníka** — kontakt, registrace, platby, historie komunikace, poznámky, tokeny.
  Včetně **editace příznaků**: i kategorie, které se při registraci nenabízejí (sleva, zkrácený pobyt,
  poznámka k platbě), textové hodnoty u příznaku a vědomé překročení kapacity.
- **Detail akce** — turnusy, příznaky, kapacity, ceny, termíny, agregace.
- **Katalog** — příznaky, skupiny příznaků, kategorie a registrační nabídky.
- **Agregace** — počty účastníků a plateb v různých řezech, live i k historickému dni.
- **Komunikační modul** — časová osa, fronta nepřiřazené pošty z IMAPu, psaní zprávy, ruční stažení pošty.
- **Import plateb** — nahrání výpisu a ruční dopárování.
- **Editor e-mailových šablon**, klonování ročníku, hromadný přesun, obnova smazaných záznamů.
- **Správa přesměrování** — stará adresa → nová, s **počítadlem zásahů a časem posledního**. Je tak vidět,
  které přesměrování ještě někdo používá a které lze zrušit; nutné, když se mezi ročníky mění slugy.
- Správa uživatelů a rolí, míst, stanic příjezdu.
- Obrazovky pro **program, rozpis služeb a příjezd** — tytéž oblasti jako v aplikaci, na velké obrazovce
  a s klávesnicí.

### Frontendová aplikace

Jedna codebase, dva režimy podle role přihlášeného uživatele. Běží jako nativní Android build, jako PWA
na iOS i jako běžná webová aplikace v prohlížeči. Slouží tomu, co se děje **v terénu** — na schůzi,
u stolu při příjezdu, v areálu.

**Účastnický portál:**

- Přehled vlastních přihlášek a jejich detail — co má zaplaceno, kolik zbývá, jaké má příznaky,
  na jakém turnusu je.
- Kalendář akcí s detailem a odskokem z místa v programu na mapu.
- Mapa míst akce (ubytování, sběrná místa, program) s vlastní polohou a několika podkladovými vrstvami.
- Výběr podakcí, kterých se zúčastní.

**Administrace pro organizační tým:**

- Účastníci se seznamem, detailem, historií komunikace a exportem vybraných; ruční zápis telefonátu
  nebo chatu. ⚠️ Úvodní obrazovka je zatím jen vítací text, ne přehled (widgety jsou naplánované).
- Akce, podakce, kapacity, ceny, příznaky, registrační nabídky.
- **Program** — rozcestník a editor: dny, sekce, aktivity, bloky a rotace.
- **Mřížka obsazení a rozpis služeb**; **itinerář** pro každého člena týmu.
- **Příjezd** — obrazovky stanic, aby se odškrtávalo u stolu na telefonu nebo tabletu.
- Adresář, kalendář, správa stránek a aktualit (formuláře se generují z popisu, takže se pro každý typ
  obsahu nepíše vlastní). ⚠️ **Ubytování obrazovku v aplikaci nemá** — jen modely a datová služba.

**Vlastní účet:** přihlášení heslem i magic-linkem a odhlášení; nastavení (přepínač backendu
test/produkce, správa lokální cache) a diagnostika (verze, prostředí, stav úložiště — k přiložení
do hlášení chyby). **Obnova zapomenutého hesla je v aplikaci hotová** — z přihlašovací obrazovky se
pošle žádost, z e-mailu se otevře obrazovka pro nastavení nového hesla (týž tokenový tok zvládá i změnu
přihlašovacího jména a e-mailu). ⚠️ Přihlášený uživatel si ale **údaje sám změnit nemůže** — do nastavení
to zavedené není a jedinou cestou zůstává odkaz z e-mailu.

**Chování, které stojí za zmínku:**

- **Zoneless change detection** — aplikace neběží na `zone.js`, změny se propagují signály. Je to
  úspornější a předvídatelnější, ale platí se za to: části Ionicu, které na `zone.js` spoléhaly, se
  musely nahradit vlastními komponentami, takže nové UI se staví signálově.
- **Vypršení tokenu řeší interceptor** — požadavek, který narazí na neautorizováno, se automaticky obnoví
  a zopakuje. Uživatel se nepřihlašuje znovu a o ničem neví.
- Lokální úložiště pro přihlášení a rozpracovaná data; odchod ze stránky s neuloženými změnami se ptá;
  role rozhodují o dostupnosti obrazovek.

**Co v aplikaci není**, ať to nikoho nemate: **push notifikace** (žádný klientský plugin ani serverová
část — oznámení chodí e-mailem), **skenování QR kódů** (QR se generují, nečtou) a **plnohodnotný offline
režim** (aplikace potřebuje připojení; lokální úložiště drží jen přihlášení a rozpracované formuláře).
Distribuce jde mimo obchody — Android jako APK, iOS jako PWA.

---

## Platforma

### Autentizace a autorizace

- **Sedm rolí v hierarchii** — `ROLE_EVERYBODY` → `ROLE_CUSTOMER` → `ROLE_USER` → `ROLE_MEMBER` →
  `ROLE_MANAGER` → `ROLE_ADMIN` → `ROLE_ROOT`. Vyšší dědí oprávnění nižších. Účastník po registraci drží
  nejnižší přihlášenou roli (`ROLE_CUSTOMER`), administrace začíná až na `ROLE_MANAGER`. Role jsou entita
  v databázi, takže se dají pojmenovat a spravovat, ne jen zadrátovat v konfiguraci.
- **Celá administrace má podlahu na `ROLE_MANAGER`** už na úrovni firewallu, ne až v kontrolerech.
  Historicky tam byl `ROLE_CUSTOMER`, tedy nejnižší přihlášená role — takže kterýkoli přihlášený účastník
  firewallem prošel a obrazovky bez vlastní kontroly role byly dosažitelné. Kontrolery navíc svou vyšší
  úroveň vynucují samostatně.
- **Přihlášení bez hesla (magic-link)** pro vracející se účastníky, klasické heslo pro tým,
  „zapamatuj si mě" na týden.
- **Typované jednorázové tokeny** s expirací: aktivace přihlášky, změna hesla, přihlášení z registrace,
  nahlášení zneužití. Obsluha je může poslat znovu, prodloužit nebo vydat nový.
- **Samoobslužná změna přístupových údajů** (`AppUserEditRequest` → `AppUserEdit`) — uživatel požádá
  o změnu e-mailu, uživatelského jména nebo hesla a změna se provede až po potvrzení odkazem s platností.
  Nová hodnota tedy nikdy nezávisí jen na tom, kdo měl otevřenou session.
- **Brzda proti hádání hesel** — nejvýše 5 pokusů na kombinaci IP a jména za minutu.
- **Autorizace na třech úrovních současně:** pravidla nad cestami (firewall), požadovaná role na
  kontroleru a bezpečnostní výraz na API resource. Nic nespoléhá na jedinou vrstvu.
- **Firewally konfiguruje sám core bundle**, ne aplikace — pět oddělených: vývojářské nástroje bez
  zabezpečení, obnovení tokenu, přihlášení do API, stateless API na JWT a stavová webová část. Aplikace
  proto nemá vlastní `security.yaml`, který by se rozcházel s tím, co bundle očekává.

### Komunikace přes API

Mobilní aplikace i portál mluví s backendem výhradně přes toto API — není to druhá cesta k datům vedle
webu, je to *ta* cesta. Cokoli se v API změní (tvar resource, serializační skupiny, autorizace), projeví
se v aplikaci.

- **REST + JSON-LD / Hydra** přes API Platform; dokumentace se generuje sama (Swagger UI, ReDoc).
- **Přihlášení** — `POST /api/login` (jméno a heslo v JSON) vrátí **JWT s platností 1 hodiny** a refresh
  token. `POST /api/token/refresh` vydá nový JWT, odhlášení refresh token zneplatní. Veřejné jsou jen tyto
  cesty a registrace; vše ostatní pod `/api` vyžaduje platný JWT.
- **Stateless** — žádná session ani cookie, každý požadavek nese `Authorization: Bearer`.
- **CORS je allowlist** — povolené originy se konfigurují prostředím (ne hvězdička), metody
  `GET, OPTIONS, POST, PUT, PATCH, DELETE`, hlavičky `Content-Type` a `Authorization`; `Link` se vystavuje
  kvůli stránkování.
- **Filtrování, řazení a stránkování** deklarativně na resource; **serializační skupiny** určují, co která
  operace vrací a přijímá, aby se do odpovědi nedostalo víc, než má.
- **Dedikované endpointy** tam, kde generický REST nestačí — změna turnusu u přihlášky, úprava příznaků,
  výstupy programu, export přihlášek. Důvod je praktický: API Platform 4 neresolvuje vnořené `{id}` relací,
  takže operace měnící vazbu mají vlastní endpoint s explicitním kontraktem místo tichého selhání.
- **Relace se posílají jako IRI** (`/api/events/12`), ne jako vnořený objekt — a při vytváření záznamu se
  nastavují setterem, ne konstruktorem, jinak zůstanou nenavázané.
- ⚠️ **Pozor při self-hostingu za WAFem:** aplikační firewall (např. OWASP CRS) běžně zahazuje `PUT`
  a `DELETE`, případně požadavky, jejichž tělo obsahuje adresu s parametry. Klient dostane chybu, která
  vypadá jako chyba API, přitom se požadavek do aplikace vůbec nedostal. Když API ze zařízení „náhodně"
  selhává, je tohle první místo, kam se podívat.

### Bezpečnost

- HTTP hlavičky: HSTS preload, CSP, Referrer-Policy, COOP, X-Content-Type-Options.
- HTTP/2 + HTTP/3, TLS 1.3.
- `/.well-known/security.txt` (RFC 9116) a `/.well-known/change-password` (W3C webappsec).
- Cookie Secure + HttpOnly + SameSite=Lax; CSRF na formulářích.
- Soft-delete a auditní stopa přes Doctrine Gedmo (kdo a kdy záznam vytvořil, změnil, smazal).
- Trusted proxies pro stack s TLS terminací na nginxu.
- Role, přihlašování a tokeny → viz [Autentizace a autorizace](#autentizace-a-autorizace).

### Integrita dat při souběhu

Zvláštní kapitola, protože klasické „zkontroluj a pak zapiš" ji neuhlídá: mezi kontrolou a zápisem je
vždy mezera, do které se vejde druhý souběžný požadavek. Rozhodnout souběh umí jedině databáze.

- **Unikátní indexy** tam, kde na jednoznačnosti záleží — bankovní identifikátor platby, aktivní
  přihlášení na podakci (přes generovaný sloupec, aby šlo po odhlášení přihlásit znovu), volba jídla,
  průchod stanicí příjezdu, spárovaná příchozí zpráva.
- **Pesimistický zámek řádku** tam, kde je podmínkou POČET, který index ohlídat neumí — kapacita
  podakce a kapacita příznaků. Zamyká se v ustáleném pořadí, aby se dvě úpravy nezablokovaly navzájem.
- **Atomický zábor** (podmíněný `UPDATE` s kontrolou dotčených řádků) u operací, které smí proběhnout
  jen jednou — zpracování importu plateb.
- **Zámek v databázi** u dávek spustitelných z více míst naráz: automatické maily jdou pustit cronem
  i tlačítkem v administraci, a databázový zámek jako jediný dosáhne přes hranici web ↔ CLI.
- Klientská pojistka proti dvojímu odeslání formuláře v celé administraci. Je to poslední vrstva,
  ne jediná — JavaScript jde obejít, server se musí ubránit sám.

### Provoz a údržba

- **Konzolové příkazy** spustitelné z cronu i ručně. Provozně nejdůležitější dva: **rozeslání
  naplánovaných mailů** (respektuje časová okna a co už komu odešlo) a **stahování pošty z IMAPu**
  (přírůstkové, se zapamatovaným stavem synchronizace, s limitem na dávku). Vedle nich sada jednorázových:
  doplnění vláken do historie, oprava jmen kontaktů, nasazení výchozích stanic příjezdu a příznaků ročníku.
- Databázové migrace (Doctrine).
- **Kvalitní brána: statická analýza + funkční smoke.** PHPStan level `max` s nulou nálezů napříč
  všemi PHP bundly a funkční sada běžící nad klonem produkční databáze, ne nad vymyšlenými fixturami.
  Není to plné pokrytí a netváří se tak: sada roste cíleně o stráže na místa, kde selhání nekřičí —
  odeslání pošty, souběžný zápis, zápis přes API, rozvržení sestav.
- **`doctrine:schema:validate` musí zůstat zelený** — mapování a databáze v souladu. Každý ručně psaný
  index nebo constraint musí mít protějšek v atributu entity, jinak by ho příští generovaná migrace
  tiše zahodila.
- Strukturované logování (Monolog).
- Asset pipeline pro administraci (Webpack Encore) a MJML pipeline pro maily.

### Lokalizace a formáty

- Čeština v UI, e-mailech i dokumentech.
- **Oslovení ve správném pádu** — vokativ („Petře" místo „Petr").
- **Řazení podle české abecedy** (Collator `cs_CZ`) — Č za C, Š za S, ne podle bajtů.
- UTF-8 napříč databází, HTTP, poštou i PDF.
- ISO 8601 / RFC 3339 v API, `DD. MM. YYYY` v UI, formátování korunových částek.

---

## Architektura a rozšiřitelnost

### Rozdělení do bundlů

OSWIS je rozdělen do čtyř Symfony bundlů, každý jako samostatný GitHub repozitář s vlastní historií
a vlastním release cyklem:

- `oswis-core-bundle` — uživatelé, autentizace, JWT, QR, PDF, mailer subscriber, primitivy frameworku,
  Twig rozšíření, skelet RSS / sitemapy / menu.
- `oswis-address-book-bundle` — kontakty, osoby, organizace, kontaktní detaily, místa.
- `oswis-calendar-bundle` — akce, účastníci, příznaky, platby, program, obsazení, příjezd, ubytování,
  IMAP, CZ QR platba, komunikační modul.
- `oswis-web-bundle` — webové stránky, aktuality, FAQ, media galerie.

Plus produkční aplikace `oswis-seznamovak-up`, která bundly slepí dohromady, a mobilní klient
`seznamovak-up` (Ionic + Angular).

Bundly na sobě nevisí těsně — komunikují přes rozšiřovací rozhraní a compiler passy. Aplikace si
v `config/bundles.php` vybere, které z nich načte.

### Dědičnost šablon

- **Web:** base layout v core bundlu definuje strukturu (head, navigace, patička, bloky pro obsah, asset
  pipeline). Konkrétní bundle si ji pro své stránky specializuje; aplikace může jakoukoli šablonu přepsat
  standardním Symfony mechanismem (`templates/bundles/<BundleName>/…`) **bez forku bundlu**.
- **E-maily:** vlastní MJML base layout (logo, hlavička, patička, ikony), který extendují konkrétní typy
  mailů. Změna brandingu se promítne centrálně.
- **Administrace:** vlastní base layout sdílený napříč bundly.

### Branding a konfigurace nasazení

- Centrální konfigurace v `oswis.yaml` — logo, barva tématu, jméno aplikace i webu, organizační údaje,
  doména pro Message-ID, výchozí odesílatel pošty, archivační BCC adresa, jazyk a lokalizace.
- Assety per nasazení v `public/assets/` — ikony, logo pro web i pro maily, OG image.
- Web i admin CSS jsou vlastní Encore entry pointy aplikace, takže lze přepsat Sass proměnné (barvy,
  fonty, breakpointy) bez zásahu do bundlu.
- **Branding per akce** — akce a podakce mají vlastní barvu, krátký název, popis, slug a organizátora;
  promítá se na veřejné stránky, do mailových šablon i do generovaných dokumentů.
- Citlivé hodnoty (SMTP, IMAP, JWT secret, TTL refresh tokenu) žijí v env proměnných, ne v deploy
  artefaktu.

### Možnosti rozšíření

OSWIS je stavěný tak, aby se vlastní funkce přidávaly **vedle** bundlů, ne do nich — fork není potřeba
a upgrade zůstane možný.

**Vlastní bundle jako plugin.** Nový Symfony bundle se zaregistruje v `config/bundles.php` a může přinést
vlastní entity, API resources, stránky, admin obrazovky, konzolové příkazy i migrace. Do existujících
částí systému se zapojí přes rozšiřovací rozhraní, která core vyhledá compiler passem — stačí službu
otagovat, nikde se nic neregistruje ručně:

| Rozhraní | K čemu |
|---|---|
| `SiteMapExtenderInterface` | vlastní položky do sitemapy pro vyhledávače |
| `RssExtenderInterface` | vlastní položky do RSS feedu |
| `WebMenuExtenderInterface` | položky do veřejného i admin menu (včetně rozklikávacích sekcí a omezení podle role) |
| `WebAdminMenuExtenderInterface` | položky specificky do administrace |
| `UpdateExtenderInterface` | vlastní krok do hromadné údržbové akce |
| `ExportDefinitionInterface` | vlastní typ exportu (sloupce, řazení, strop řádků); dostane zdarma CSV, XLSX i PDF |

**Bez psaní kódu** se dá změnit překvapivě mnoho, protože model je datově řízený: kategorie účastníků,
registrační nabídky s cenami a kapacitami, příznaky a jejich skupiny s pravidly výběru, kategorie
kontaktních detailů, funkce v týmu, stanice příjezdu, e-mailové šablony, skupiny a barvy pásků. Nová akce
jiného typu je tedy typicky konfigurace, ne vývoj.

**Přepsání vzhledu a textů** → viz [Dědičnost šablon](#dědičnost-šablon)
a [Branding](#branding-a-konfigurace-nasazení).

**Kde rozšíření naopak nemá smysl.** Provoz akce (služby, doprava, ubytování, příjezd) záměrně nežije
v entitě `Event` — ta popisuje jen *co se koná*. Provozní věci mají vlastní modely, a nová provozní funkce
by měla přidat svůj, ne rozšiřovat `Event` o další sloupce.

### Použité technologie

**Backend:**

- **PHP 8.5+** (produkčně 8.5.9), **Symfony 8.1** (8.1.0).
- **Doctrine ORM 3.6** (3.6.7) + **DBAL 4** (4.4.3); rozšíření **Gedmo** pro Timestampable, SoftDeleteable, Sluggable,
  Loggable, Blameable.
- **API Platform 4.3** (4.3.16) pro REST/JSON-LD; JWT přes **Lexik JWT Authentication Bundle** (3.2), refresh tokeny přes
  **Gesdinet JWT Refresh Token Bundle**, CORS přes **Nelmio**.
- **Symfony Mailer** s vlastním `MailerSubscriber` (Auto-Submitted, archivní BCC, Reply-To);
  **MJML** pipeline pro responsivní HTML maily.
- **webklex/php-imap** pro čtení pošty (read-only).
- **mPDF** pro PDF, **PhpSpreadsheet** pro XLSX, **Endroid QR Code** + **Shoptet CzQrPayment** pro QR.
- **bigit/vokativ** (vlastní fork) pro české skloňování jmen.
- **Vich Uploader** pro upload souborů, **Liip Imagine** pro varianty obrázků.
- **Symfony Rate Limiter** pro brzdu přihlašování.

**Webová administrace:** **Twig**, **Webpack Encore**, **Bootstrap 5**, **Stimulus**,
**Symfony WebLink** pro preload hinting.

**Frontendová aplikace:** **Ionic 8** (8.8) + **Angular 22** (22.0) + **TypeScript 6**, **Capacitor 8** (8.4) pro Android
build, PWA pro iOS; **Leaflet** pro mapy (OpenStreetMap, MapyCz, OpenTopoMap); **Formly** pro formuláře
generované z popisu.

**Databáze:** **MariaDB** (produkčně 11.8.6; DBAL 4 zvládne i 10.5+, doporučeno 10.6+ kvůli deprecacím).
Schéma využívá **generovaný sloupec** s unikátním indexem (aktivní přihlášení na podakci), protože
MariaDB nemá částečné indexy — s tím počítejte při případném přenosu na jiný stroj.
Doctrine samo umí i PostgreSQL, ale OSWIS na něm **není provozně vyzkoušený** — schéma vzniklo a je
udržované nad MariaDB, takže pro PostgreSQL počítejte s vlastním ověřením migrací.

### Co se během let vyměnilo

- **Zurb Inky** (Foundation for Emails) → **MJML** kvůli lepší podpoře v Outlooku a údržbě šablon.
- **Symfony 6.x / 7.x / 8.0** → **Symfony 8.1**.
- **API Platform 2.x / 3.x** → **API Platform 4.3**.
- **Doctrine ORM 2.x** → **3.6** (striktní kontrola kolize identit).
- **PHP 7.x → 8.x → 8.4 → 8.5.**
- **Ionic 5 + Angular 14 + Capacitor 5** → **Ionic 8 + Angular 22 + Capacitor 8**.
- Angular se `zone.js` → **zoneless** se signály. Úspornější a předvídatelnější, ale vynutilo si nahradit
  ty části Ionicu, které na `zone.js` spoléhaly, vlastními komponentami.
- iOS dříve plánován jako Capacitor build → dnes PWA (jednodušší údržba, žádný Apple Developer Program).

---

## Rozpracované a plánované

Poctivý stav, ne wishlist. OSWIS se vyvíjí proti jednomu reálnému provozu, takže se tu potkává hotový kód
s tím, co ještě nemá naplněná data nebo čeká na rozhodnutí.

**Hotové, ale zatím nepoužité v provozu.** U několika modulů stojí kód i obrazovky, ale v jediném
dnešním nasazení se ještě nepoužily — přidělování ubytování a spolubydlení, skupiny a barvy pásků,
naplnění programu konkrétního ročníku a obsazení programu týmem. Není to nedodělek kódu: chybí data,
a jejich pořízení je organizační práce. Zmiňuje se to schválně, protože **modul bez dat vypadá hotově
a selže až v provozu** — navazující výstupy (tiskové seznamy pro výdej stravy, řazení „dietáři první",
itineráře) bez naplnění nefungují.

**Rozpracované.** Informační architektura administrace se přestavuje. Úvodní obrazovka už není jen
rozcestník (nahoře jsou počty a provozní hlídky, rozcestník pod nimi). **Rozklikávací horní menu podle
oblastí je hotové, ale nevydané** — čeká jako nesloučená větev, protože se musí nasadit současně
v jádru i v aplikaci. Zbývá k němu drobečková navigace napříč stránkami.
U obsazení programu celým podtýmem chybí možnost někoho z týmu pro danou sekci **odečíst** („celý tým bez
jednoho"); bez toho by itinerář instruktora lhal, takže se to musí nejdřív dopočítat na backendu a teprve
pak nabídnout v rozhraní. Obsazení celým podtýmem je v aplikaci postavené, ale ještě nevydané.

**Ve frontendové aplikaci konkrétně.** Chybí **push notifikace** (potřebují klientský plugin i serverovou
stranu, dnes je nahrazuje e-mail), **skenování QR** (mohlo by zrychlit příjezd u stolu) a **plnohodnotný
offline režim** — u vícedenní akce v areálu se slabým signálem je to reálné omezení, ne kosmetika.
Distribuce přes obchody (Google Play, App Store) zatím není; zvýšila by dosah, ale nese si vlastní režii
vydávání a recenzí. A především: dokud se tým nezaregistruje jako účastníci s příslušnou funkcí, nemá
editor obsazení koho nabízet — chybí data, ne funkce.

**Naplánované a rozhodnuté, zatím nepostavené.** Tohle není wishlist — na každou položku existuje
rozhodnutí a u většiny i napsaný implementační plán; řadí se podle závislostí, ne podle chuti:

*Provoz a administrace* — dotažení přehledových widgetů na úvodní obrazovce (dnes tam jsou počty po
turnusech a provozní hlídky, chybí peníze a docházka) · sloučení duplicitních přihlášek (dnes se jen
hlásí, slučovat se musí ručně) · dokončení komunikace: skládací vlákna, odpověď přímo z časové osy
a přidělení vlákna řešiteli · generátor provozních tisků (jmenovky, bezpečnostní list k podpisu, seznamy
podle barvy pásku, zápisové archy) · evidence techniky s logem závad (nahradí papír v kiosku) · workflow
návštěv (externí přednášející, lektoři, partneři mají jiný příjezdový režim než účastník).

*Účastnická aplikace* — pokyny k platbě: zbývající částka, číslo účtu, variabilní symbol a QR (backend to
umí do mailu, aplikaci chybí) · aktivace přihlášky (reset hesla už hotový) · správa vlastních údajů
za běhu · dotažení programu: upozornění na kolize
a režimy přihlašování na podakce · jídelníček s volbou jídla (volí účastník, nebo tým u příjezdu; kuchyň
dostane počty, u omezení se jmény) · push oznámení místo plakátů „dnes večer" a „změna kvůli počasí"
· offline čtení pro areál se slabým signálem.

*Týmové nástroje* — zápis na podakci za účastníka a označení zaplaceno · potvrzený výdej triček
· **per-turnus funkční role** (zdravotník vidí diety, ubytování pokoje, jídelna agregace) — dnes je
přístup do administrace všechno-nebo-nic.

*Data, ne kód* — dokončení stravovacích příznaků včetně alergenů a pojmenovaného kuchyňského seznamu;
poslední roky se dieta řešila jen poznámkou v přihlášce.

**Postavené, ale úmyslně nezapnuté.** Upomínky nezaplacených plateb existují, ale v jediném dnešním
provozu se nepoužívají — je to rozhodnutí organizátorů, ne chybějící funkce.

**Kam to míří.** Hlavní kvalitní branou zůstává statická analýza na nejvyšší úrovni; funkční sada
roste cíleně, ne plošně — přibývá do ní stráž pokaždé, když něco selže tiše.

**Provozní kontroly, které samy hlásí tiché selhání**, jsou rozdělané a mají první hotové kusy: úvod
administrace už vypisuje **přihlášky bez doručeného shrnutí** (tedy lidi bez pokynů k platbě, kteří
by jinak nikoho netrápili, protože přihláška vypadá normálně) a **vícenásobné přihlášky téhož člověka**.
Zbývá pokrýt zbytek — typicky e-mail, u kterého se odeslání nepovedlo. Právě nepřítomnost akce je to,
co běžný monitoring ani typová analýza neodhalí; proto se hlídá v aplikaci, ne v logu.

Vede to k zásadě, která se v systému drží napříč: **„kód nespadl" není důkaz, že se něco stalo.**
Selhání SMTP nevyhazuje výjimku, chybějící serializační grupa vrátí `200` a pole zahodí, zakázaný
formulářový prvek se neodešle. Proto se všude, kde na tom záleží, hlásí **doložený** výsledek — u pošty
je důkazem záznam o odeslání, ne to, že odesílací metoda proběhla.

**Úvahy o větší přestavbě.** Existují návrhy na generační obměnu s čistým datovým modelem bez historické
zátěže. Nejde se do ní překlápět naráz — místo toho se její návrhy vstřebávají do dnešního systému po
modulech, aby provoz nikdy nestál na rozestavěné verzi.

---

## Self-hosting

OSWIS je standardní Symfony aplikace. Běží na VPS s PHP-FPM + nginx, na sdíleném hostingu s SSH nebo
v kontejneru. Žádné fronty ani démoni nejsou povinné (Redis a Messenger jsou volitelné, výchozí je
synchronní zpracování).

**Potřeba:**

- PHP 8.5+ (CLI i FPM)
- MariaDB 10.6+ (produkčně 11.8.6)
- SMTP pro odesílání pošty (libovolný provider)
- IMAP k mailboxu, kam chodí pošta od účastníků, pokud chcete automatický import komunikace do historie.
  Stačí read-only přístup.
- Node.js pro build mailových šablon a admin assetů. **Není volitelný:** bez nainstalovaných balíčků chybí
  MJML binárka, render mailu spadne a zpráva se neodešle — v evidenci zůstane záznam s důvodem, ale
  příjemci nepřijde nic.

**Není potřeba:**

- Cloud, Kubernetes, message queue
- Placené API třetí strany
- Specializovaný dev tým — standardní Symfony deploy (composer, npm, migrace)

---

## Kontakt

OSWIS vyvíjí Jakub Žák — [mail@jakubzak.eu](mailto:mail@jakubzak.eu),
[github.com/oswis-org](https://github.com/oswis-org).
