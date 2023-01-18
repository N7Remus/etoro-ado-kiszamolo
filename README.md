# etoro-ado-kiszamolo
Etoro magyar adózáshoz készített feldolgozó hobbyprojekt 

# Működése
* Beolvassa az MNB napi árfolyamos xlsx-jét. (Ha van eggyezés a napi árfolyammal azt használja, ha nincs akkor az előző érvényes napot)
* Összesíti a napi árfolyamot külön Crypto és Egyéb (Stock)-okra (külön adózási szabályok érvényesek)
* Összesíti az osztalékokat napi árfolyamon. (ahol 0% a tax, ott +15%-ot számít rá)
* Az átváltott napiárfolyam értékeket csv vagy xlsx (excel) formátumban lehet a böngészőből exportálni.

# Telepítés
## Manuális telepítés (Ubuntu):

* telpítsük a függőségeket :
`apt install apache2 php php-xml`
* Másoljuk a www mappa tartalmát a /var/www/html mappába.
* Amennyiben szükséges frissítsük az MNB napi árfolyamos xlsx-et. (fontos, hogy az előző évről szükséges az utolsó rögzített érték)
* Opcionálisan SSL tanusítvány telepítése.
* Teszteljük!

# Használata
* Az etoro account statement menüpontból lehet exportálni egy xlsx fájlt adott időszakra.
* A exportált fájlt az oldalon lehet csatolni. 
* Az kiszámoló elvégzi a napi árfolyamváltásokat.
* Illetve, összesítőket készít az előre megadott SZJA értékekkel. (ezen értékek változhatnak)

# Demo:
https://adokiszamolo.sth.sze.hu/

# Megjegyzés:
Ez egy hobbyprojekt, így a generált adatok esetleges hibáiért/tévességéért semmilyen jellegű felelősséget nem vállalok.

Amennyiben valaki hibát észlel kérem jellezze issue-ban.
