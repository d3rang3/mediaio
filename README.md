
  

# MediaIO - az Árpád Média webapp-ja. ![built on php](https://badgen.net/badge/icon/php?icon=php&label)



  

## Mi a MediaIO, miért készült?

  

A MediaIO egy all-in-one menedzsment felület a raktárkezeléstől kezdve a naptáron át a takarítási rend beosztásáig. Habár rengeteg hasonló alkalmazás létezik, szükségesnek láttuk egy saját rendszer tervezését.

   

### Legfontosabb elemek

  

A webapp a következő funkciókkal rendelkezik

  

- Tárgyak kivételének, visszahozásának, sérülés esetén ezek bejelentésének kezelése.

- Belső naptárrendszer (Google Calendar API szinkronizált), ezekhez tartozó munkalapokkal.

- Takarítási rend

- Pénzügyek kezelése

- Felhasználó elérhetőségei, statisztikák

- Hibabejelentő

- Belsőleg alkalmazott pontrendszer

- Tárgykövetési rendszer

- (Hamarosan) rendszerintegráció nextCloud tárhelyrenszerrel

- és a lista folyamatosan bővül

  

## Telepítés

A repo klónozása után állítsuk be a .env fájlunkban az example.env-ben is látott értékeket, majd futtassuk a gyökérmappában a `docker-compose --env-file <env filename>.env up --build` parancsot.

Ezután a php konténeren belül futtassuk  a `www/io` mappában a `composer install` parancsot. 
Az adatbázis strukrúához és a mail api key-hez segítséget a `www/io/examples` mappában kaphatunk. A kitöltött JSON fájlokat a `www/io/utility` mappába tegyük

**Minden segítségre szükség van!!**

### Adatbázis beállítása

A `database` mappa tartalmazza azt az SQL fájlt, amelynek segítségével létre lehet hozni az adatbázis szerkezetét, az adatok nélkül. Ezután be kell regisztrálni egy felhasználót, akihez hozzárendeljük a `system` és `admin` jogosultságot. Ezt a `users` táblában tehetjük meg, a következő JSON objektum hozzáadásával az adatbázisban:
```
{
    "groups": [
        "admin",
        "system"
    ]
}
```

#### Adatbázis belépési adatainak beállítása
A szerveroldali szkriptek az io `server/dbCredentials.json` fájlból olvassák ki a belépési adatokat. A **NAS_** kezdetű beállítások a SYNOLOGY API-ra vonatkoznak, ezt csak akkor kell beállítani, ha használni szeretnénk az API-t.

```
{
  "username": "IO_username",
  "password": "IO_password",
  "schema": "databaseName",
  "NAS_domain": "domain.com",
  "NAS_username": "APIUserName",
  "NAS_password": "APIPASS"
}

```
  

###### Bármilyen egyéb kérdés esetén vedd fel a kapcsolatot a projekt GitHUb oldalán [the project page](https://github.com/gutasiadam/mediaio  "the project's page") vagy a GitHubomon. [my GitHub](https://github.com/gutasiadam  "my GitHub").