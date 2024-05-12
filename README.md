# etoro-ado-kiszamolo
Ez az alkalmazás egy feldolgozó hobbyprojek, mely az eToro platformon keresztüli tözsdézés, magyarországi adózásának megkönnyítésére készült.
# Működése
* Beolvassa az MNB napi árfolyamos xlsx-jét. (Ha van eggyezés a napi árfolyammal azt használja, ha nincs akkor az előző érvényes napot)
* Összesíti a napi árfolyamot külön Crypto és Egyéb (Stock)-okra (külön adózási szabályok érvényesek)
* Összesíti az osztalékokat napi árfolyamon. (ahol 15%< a tax, ott +15%-ot számít rá)
* Az átváltott napiárfolyam értékeket csv vagy xlsx (excel) formátumban lehet a böngészőből exportálni.


# Használata
* Az etoro account statement menüpontból lehet exportálni egy xlsx fájlt adott időszakra.
* A exportált fájlt az oldalon lehet csatolni. 
* Az kiszámoló elvégzi a napi árfolyamváltásokat.
* Illetve, összesítőket készít az előre megadott SZJA értékekkel. (ezen értékek változhatnak)

# Demo:
https://adokiszamolo.sth.sze.hu/

# Telepítés
## Manuális telepítés (Ubuntu):

* telpítsük a függőségeket :
`apt install apache2 php php-xml`
* Másoljuk a www mappa tartalmát a /var/www/html mappába.
* Amennyiben szükséges frissítsük az MNB napi árfolyamos xlsx-et. (fontos, hogy az előző évről szükséges az utolsó rögzített érték)
* Opcionálisan SSL tanusítvány telepítése.
* Teszteljük!


# Megjegyzés:
Ez egy hobbyprojekt, így a generált adatok esetleges hibáiért/tévességéért semmilyen jellegű felelősséget nem vállalok.

Amennyiben valaki hibát észlel kérem jellezze issue-ban.
# Változások a verziókban
0.3.1 Az SZJA számolás csak a 0%-os adót tartalmazó sorokra érvényesült, most már  15%-nál kissebb is összegződik.

0.3 Az alakalmazás most már visszajelzi, hogy mikori árfolyam adatokkal dolgozunk, illetve a javítottam a formázáson.

0.2 táblázatok finomítása

0.1 kezdeti állapot
