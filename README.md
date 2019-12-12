# Simplesamlphp-autentisering mot Scoutnet

Används av Scouterna för scoutid.se, skapat av <a href="https://github.com/magnushasselquist">@magnushasselquist</a>.

För att installera:

		composer config repositories.2 vcs https://github.com/Scouterna/simplesamlphp-module-scoutnetauth.git
		composer require scouterna/simplesamlphp-module-scoutnetauth:dev-master

Installera en specifik version (git-tag):

		composer require scouterna/simplesamlphp-module-scoutnetauth:dev-master#v1.0.0 
    

## Konfiguration

Justera scoutnet-hostnamn genom att sätt miljövariabeln `SCOUTNET_HOSTNAME`
till hostname för den scoutnet-installation du vill autentisera mot.

Obs: Det här är mest intressant för utveckling av scoutid.se. Om du vill
autentisera användare på din webbsida och använda Scoutnet som källa är det
bättre att använda Scoutid. Kontakta scoutid@scouterna.se så hjälper vi dig att
komma igång!
