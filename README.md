Semestrálna práca VAII 2025/2026

O projekte
Tento projekt predstavuje moju semestrálnu prácu z predmetu Vývoj aplikácií pre internet a intranet (VAII) na Fakulte riadenia a informatiky Žilinskej univerzity.

Cieľom práce bolo vytvorenie webovej aplikácie požičovne s využitím frameworku Vaiiko, ktorý demonštruje princípy MVC architektúry.

Návod na lokálne spustenie
Na spustenie webovej aplikácie je potrebné mať nainštalovaný Docker (napr. Docker Desktop) a vývojové prostredie pre PHP (napr. PhpStorm).

1. Stiahnutie repozitára
Napríklad:
git clone https://github.com/Robinqos/Web-Barbershop
2. Spustenie aplikácie
Spustite Docker (Docker Desktop)

Otvorte priečinok projektu

Spustite služby pomocou súboru docker-compose.yml

V PhpStorm: kliknite pravým tlačidlom na docker-compose.yml → Run

3. Kontrola spustených služieb
Po úspešnom spustení by sa v Docker Desktop v sekcii Containers mali objaviť tieto služby:

adminer - nástroj na správu databázy

mariadb - databázový server

thevajko/vaii-web-server:main - webový server s aplikáciou

4. Prístup k aplikácii
Webová aplikácia: http://localhost/

Adminer (databáza): http://localhost:8080/


V priečinku /docker/sql sa nachádzajú SQL skripty s tabuľkami a vzorovými dátami. Ak by nastal problém s automatickým vytvorením databázy, môžete tieto súbory manuálne importovať cez Adminer.
