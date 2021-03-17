## _Environnement de developpement PHP7, Node.js 10, Postgresql 11 dockerisé_

## Contruire le contener
```bash
[Windows] docker build -t dev-lapp:1.00 .
[Linux] sudo docker build -t dev-lapp:1.00 .
 ```

 ## Lancer le conteneur
```bash 
[Windows] docker run -it --rm --name -m 2g "dev-lapp_1.00" -p 80:80 -p 443:443 -v "%cd%/www":/var/www -v "%cd%/config/postgres/persistant_data":/var/lib/postgresql/data dev-lapp:1.00
[Linux] sudo docker run -it --rm --name -m 2g  "dev-lapp_1.00" -p 80:80 -p 443:443 -v "`pwd`/www":/var/www -v "`pwd`/config/postgres/persistant_data":/var/lib/postgresql/data dev-lapp:1.00
```

# Configuration initiale

## Configurer ses hosts
Tout d'abord, tous les hosts, node.js ou apache passent par le port 80 en http, ou 443 en https.
Afin de savoir quel host est destiné à quel service, il faut tout d'abord configurer le fichier *www/node/vhosts/vhosts.json*
```
{
    "ssl"                       : [...],
    "http"                      : 80,  // Port d'ecoute http
    "https"                     : 443, // Port d'ecoute https
    "routePort"                 : {
        "apache"                : 8080,// Port forward pour apache
        "node"                  : 8081 // Port forward pour node.js
    },
    "vhosts": [
        {   "serverName"        : ["node", "node.*"],
            "forward"           : "node"
        },
        {   "serverName"        : ["*"], // "*" permet de capturer tous les hosts
            "forward"           : "apache"
        }
    ]
}
```
Le service de vhost se charge egalement d'abstraire l'acces http ou https vers de l'http.

Une fois cela fait, il faut ensuite configurer les vhosts apache *www/php/vhosts.conf* sur le port foirward precisé ci dessus
```
<VirtualHost *:8080>
	ServerName localhost
	ServerAlias localhost.local
	DocumentRoot /var/www/php
	ErrorLog /var/www/error.log
	CustomLog /var/www/access.log combined
	<Directory "/var/www/php">
		AllowOverride All
		Allow from All
</Directory>	
</VirtualHost>
```

Une configuration par default est deja faite en castant 80 > 8080 pour apache, 80 > 8081 pour node.js
Afin de verifier cela, créer vous 2 hosts locaux, un pour apache, un pour node.js. Le container contient par defaut un explorateur de fichier permettant au server de donner une réponse.

## Ajouter du code PHP
Pour cela créez vous un sous dossier dans *www/php*, puis configurez un vhost qui pointe dessus.

## Ajouter un service node.js
Pour cela, créez vous un sous dossier dans *www/node*. Pour l'amorcer au demarrage du conteneur, il faudra completer le fichier *www/node/bootstrap.json*
```
[
    { "name": "VhostService", "script": "/vhosts/bootstrap.js"    , "argv": []     },
    { "name": "HttpServer"  , "script": "/httpserver/bootstrap.js", "argv": [8081] } // <-- ici on passe le port d'ecoute en parametre pour centraliser la config
]
```
Il existe par defaut des services pour la gestion des vhosts et service d'asset node.js, il sont necessaires, ne les supprimez pas de la configuration.
Vous pouvez librement ajouter un nouveau service de cette manières avec les arguments de votre choix.

Pour associer un host speficique à un service node, accessible depuis l'exterieurs, créer un vhost specifique dans *www/node/vhosts/vhosts.json*
```
{
    [...]
    "routePort"                 : {
        [...]
        "node-custom"           : 8082, // <-- pour attribuer un port specifique
    },
    "vhosts": [
        {   "serverName"        : ["node.custom.com"], // <-- pour associer un host specifique au port que vous venez de créer
            "forward"           : "node-custom"
        },
        [...]
    ]
}
```

## Initialiser la base de données
Pour cela, un script sql sera executé au demarrage *www/create.sql*
Ce script ne sera ensuite re-executé au lancement que si ce dernier à été modifié depuis la dernière execution
Le retention de données est faite dans *config/postgres/persistant_data*
Pour purger rapidement votre base de données postgres, il faudra supprimer le contenu de ce dossier.

# Administrer sa base de données
Pour cela vous devrez disposer d'un client postgres tel que pgadmin4.
Vous pourrez alors vous connecter avec les configurations suivantes :
- Host: localhost
- User: postgres
- Pass: (none)