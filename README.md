A projektem magába foglal egy bejelentkezési rendszert, illetve egy időpontfoglaló rendszert. Az első oldal, amit a felhasználó lát, az egy bejelentkezési felület, ahol kiválaszthatja, hogy bejelentkezni kíván, új fiókot létrehozni.
Mindezek mellett, még két gomb található a főoldalon, az egyik, ahol felhasználói regisztráció nélkül megtekintheti az elérhető szolgáltatásokat. A másik gomb pedig, egy "Elfelejtettem a jelszavamat" gomb, ahol abban az esetben,
hogy, ha a felhasználó elfelejtette a jelszavát, egy token generálással, az e-mail címét megadva, beírhat egy új jelszavat.

Fiók regisztráció:

- Név
- E-mail
- Jelsző
- Felhasználói feltételek elfogadása.

Bejelentkezés:

- Abban az esetben, ha felhasználóként jelentkezünk be:

Bejelentkezéskor a Főoldalon, a weblap köszönti a felhasználót a nevén, kiírja, az e-mail címét, valamint nem volt követelmény, de a weblap az avatar alatt, kiírja, a legutóbbi foglalását a felhasználónak (abban az esetben, ha egyszerre
foglalt több szolgáltatást, a legutolsó jelenik meg), az avatar-ját módosíthatja akármikor, csak és kizárólag JPEG/PNG fájlokat tölthet fel a felhasználó, ami nem haladhatja meg a 2MB-ot.

A második gombbal, megtudja tekinteni a foglalásait, ahol láthatja, ha egy admin üzenetet hagyott neki a foglalásával kapcsolatosan, valamint lehetősége van lemondani a foglalását.

A harmadik gombbal új foglalást hoz létre, előszőr kiválasztja a kívánt szolgáltatást, majd kiválaszt egy időpontot és lefoglalja az adott időpontra az adott szolgáltatást. Ezek után az előleg említett foglaláskezelőnél megjelenik a foglalása,
"pending" státuszban, az addig nem változik, amíg egy admin nem fogadja el vagy éppen utasítja vissza, visszautasítás esetén, a foglalás törlődik és a felhasználónak is törlődik a foglalás.
Fontos megjegyeznem, hogy a felhasználó nem foglalhat ugyanarra az időpontra, ugyanolyan szolgáltatást.

- Abban az esetben, ha adminként jelentkezünk be:

A szolgáltatások módosítő menü fogad minket, itt az admin módosíthatja, törölheti vagy éppen készíthet egy új szolgáltatást. A kötelező mezők kitöltése szükséges:

- A szolgáltatás neve.
- Mennyi időt vesz igénybe.
- Az ára.
- Aktív-e vagy nem.


Az ablak tetején lapot tudunk váltani, a következő lap az órarend adminisztrációja. Először kiválasztja az admin, hogy melyik szolgáltatasnák az órarendjével szeretne műveletet végezni, majd rendszerint:
-A dátum.
-A kezdeti idő.
-A befejezési idő.
-Kapacitás (hány kliens tud egy adott intervallumra jelentkezni.
-Valamint, hogy nyitva van-e az időpont vagy zárva. Ha nyitva még lehetséges rá jelentkezni, ha zárt, akkor nem.

Az utolsó lap pedig, az admin látja, a foglalásokat, látja a felhasználó nevét, hogy milyen szolgáltatást foglalt a vendég, milyen dátumra és időpontra, valamint a foglalás státuszat ("pending", "confirmed", "denied"), valamint egy felület, ahol beállíthatja hogy milyen a státusza
illetve egy textbox, ahol beírhat egy megjegyzést, és egy update gomb, ami véglegesíti a frissítést.


Telepítési lépések:

A GitHub-ról letöltendő zipként a fájlok, majd ennek kicsomagolása a wamp64/www mappába. Wamp szerver futtatása és a localhost:8080/mappa_neve link beírása a böngészőbe.


Alapértelmezett tesztfelhasználók:

- Felhásználóként: <br> E-mail: 123@123.com <br> Jelszó: 123
- Adminként: <br> E-mail: root@root.com <br> Jelszó: root

  


