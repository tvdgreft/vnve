#  VNVE

wat kan deze plugin:

Deze plugin is gemaakt voor de postzegelvereninging vn-ve. Hiermee kan een overzicht gemaakt worden van onderwerpen die in een artikel van de periodiek Grenzenloos voorkomt Er zijn 2 belangrijke functies:

*   Beheer van onderwerpen en uploaden van artikelen in pdf
*   Een zoekscherm om een onderwerp te zoeken met bijbehorend artikel.

## Beheer

De beheerfuncties zijn te vinden in het dasboard van wordpress menu-tem= vnve
Onder dit menu staan de volgende subitems:

### settings
De plugin bevat de volgende settings.

*   organisatie - de naam van de organisatie
*   introductie - introductietekst van de organisatie
*	records per pagina - keuzes die gemaakt kunnen worden uit het aantal records per pagina bij het zoeken
*   maxdocsize - Maximale grootte van een artikel. Let wel dit moet lager of gelijk zijn aan wat in php is vastgelgd
*   filetypes - Welke type bestand kan een artikel hebben
*   docdir - map voor artikelen beschreven vanaf de website root vanaf plugins/vnve
*	helpdir - map helpbestanden

settings tbv inschrijfformulier
*   emailadres ledenadministratie - Hier wordt het formulier naar toe gestuurd.
*   interessegebied - lijst van interessegebieden
*   vereningingstaken - list van verenigingstaken
*   ledenformulier - map waar verzonden formulieren worden opgeslagen

### grenzenloos

Dit is de beheersfunctie van Grenzenloos. De beheerder kan hiet artikelen beheren.
In de pagina die getoond wordt staat een 'help' knop. Wanneer hier op wordt geklikt wordt een helpbestand vnve/doc/manual_grenzenloos_manager.htlm getoond.

## frontend functies

### beheer grenzenloos
Voor de frontend kan ook de beheersfunctie van Grenzenloos, in een pagina worden opgenomen. Deze pagina moet dan wel kunnen worden geautoriseerd voor de beheerder.
In een artikel moet de volgende shortlink worden opgenomen:
[vnve prefix="grens" function="grenzenloos" task="manager"]


### zoeken in grenzenloos voor eindgebruikers

Voor het zoekscherm moet een shortlink worden aangemaakt in een artikel. Dat ziet er als volgt uit:  
{vnve prefix="grens" function="grenzenloos" task="publicsearch"}

### lidmaatschapsformulier

In de vnve-plugin zit ook een functie om een inschrijformulier te tonen. Nadat deze is ingevuld wordt deze verzonden naar.
De shortlink ziet er als volgt uit:
[vnve prefix="grens" function="leden" task="formulier"]

## database

Er wordt automatisch een tabel aangemaakt in de databank die de volgende naam heeft:  
grens_grenzenloos  

## mappen

In de map documents in de plugindirectory worden de artikelen opgeslagen.


## Initial Load

Als de datavbank tabel nog leeg is, kan deze gevuld worden met een csv-bestand. Klik op de knop "initiele gegevens". Het csv bestand moet er als volgt uitzien (voorbeeld)  
crdate;nummer;oudnummer;seizoen;titel;auteur;bladzijden;artikel  
0000-00-00 00:00:00;-6;50;maart 1970;Tentoonstellingsnieuws;;2-3;-6+50-02-03.pdf  
0000-00-00 00:00:00;-6;50;maart 1970;Nieuwe uitgiften Verenigde Naties (gehele wereld);;7; 16;-6+50-07\_16.pdf  
0000-00-00 00:00:00;-6;50;maart 1970;UNO-nieuws; Nieuwe uirgiften;W.J.A.F.R.van den Clooster, Baron;8-9;-6+50-08-09.pdf