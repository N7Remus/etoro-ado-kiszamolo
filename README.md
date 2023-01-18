# etoro-ado-kiszamolo
Etoro magyar adózáshoz készített feldolgozó hobbyprojekt 

# Telepítés
## Manuális telepítés:

Ubuntu :
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
