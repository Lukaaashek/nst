# Nubium Sandbox

Popis kroků potřebných ke spuštění aplikace.

## Docker
V příkazové řádce přejděte do rootu aplikace. Zde se nachází soubory Docker a docker-compose.yml.

Docker stáhne potřebná data a spustí server po zadání příkazu:
`docker-compose up --build -d`

Po spuštění je web přístupný na localhostu na HTTP i HTTPS verzi. Bude ale ukazovat error, protože nemá databázi.

## Databáze
Nově vytvořená databáze v Dockeru bude prázdná. V rootu aplikace je soubor database.sql. Ten je potřeba nahrát do databáze.

Databáze běží na localhostu na portu 3306. Přístupové údaje jsou:
Uživatel: devuser
Heslo: devpass
Název databáze: test_db

## Soubor hosts
Pro zobrazení na testovací doméně je potřeba do souboru hosts přidat následující řádek:
`127.0.0.1 nubium-sandbox.test`

Po uložení se web začne zobrazovat na adrese nubium-sandbox.test. Funkční jsou oba protokoly.

U HTTPS verze bude hlásit prohlížeč chybu certifikátu, protože se jedná o self-signed certifikát.
Po odkliknutí bude fungovat stejně jako opravdový.

[HTTP verze](http://nubium-sandbox.test/) a 
[HTTPS verze](https://nubium-sandbox.test/)

## Aplikace
Aplikace běží na rootu domény a ukazuje výpis ukázkových článků. Nepřihlášený uživatel nevidí všechny články, ty se mu zobrazí až po přihlášení.

### Přihlašování
Aplikace umožňuje registraci, přihlášení a odhlášení uživatele a změnu uživatelského hesla.

**V reálné aplikaci bych navíc použil:**
1. Potvrzovací e-mail při registraci uživatele.
2. Zasílání zapomenutého hesla.

Potvrzovací e-mail i zapomenuté heslo by obsahovalo link s identifikátorem a vygenerovaným hashem uloženým v DB, pomocí kterého bych jednoznačně identifikoval uživatele a povolil mu aktivovat účet/změnit heslo.

### Články a hodnocení
Články je možné řadit podle data, nadpisu a hodnocení. Články je možné hodnotit bez reloadu stránky. Výchozí řazení je podle ID v DB.

Hodnocení uživatelů se ukládá pro každý článek a uživatele zvlášť, takže uživatel nemůže hodnotit vícekrát a zároveň se mu ukazuje jak hodnotil u již ohodnocených článků.

Díky ukládání hodnocení do samostatné tabulky je možné hodnocení konkrétního uživatele smazat.

Tabulka s články obsahuje souhrné hodnocení, aby se hodnocení nemuselo při každém načtení počítat znovu.