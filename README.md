# wysiwyg* - Neos A/B Testing
![Neos Package](https://img.shields.io/badge/Neos-Package-blue.svg "Neos Package")
![Neos Project](https://img.shields.io/badge/Neos-%20%3E=%203.2%20-blue.svg "Neos Project")

This package provides a simple to use backend module and frontend container to run A/B Tests. </br>


## Installation
Run these commands to install the package and update the database schema.
```bash
composer require wysiwyg/neos-abtesting

./flow flow:doctrine:migrate
```

## Usage
Für die Ausspielung, ob jeweils Version A oder B ausgespielt wird, bietet dieses Package einen Node-Container.
  
Dieser Node-Container kann unter folgenden Namen in Constraints hinzugefügt werden:  
`'Wysiwyg.ABTesting:ABTestingContainer'`  
In diesem Container finden sich zwei Content-Collections:  
* itemsa
* itemsb

Für diese Collections sind bisher keine Constrains vorgesehen.  
Dies kann jedoch je nach Bedarf angepasst werden, durch überschreibung in eigenen NodeTypes.yaml Dateien.  
In diesen Collections ist es vorgesehen, für die jeweilige Version die Content Elemente hinzuzufügen.  
Dies geschieht durch den Redakteur.  

**IMPORTANT**  
Im Neos Backend werden immer beide Versionen ausgespielt.  
Im Frontend wird per default Version A ausgespielt.  

Im Inspector hat dieser Container eine neue Gruppe "A / B Testing".  
Diese Gruppe beinhaltet ein Dropdown, in dem man den Test (bzw das Feature) auswählt.  
Alle Tests, die hier erscheinen werden im dazugehörigen Backend-Modul definiert und erscheinen dann in diesem Dropdown.  

### Bedienung des Backend-Modules
Im Menü links erscheint ein neues Module "A/B Testing" und als Submodule "Tests".  
Bei klick auf das Module, wird man auf das Dashboard weitergeleitet, welches alle Submodules anzeigt.  
Das Tests Submodule bietet in dieser Übersicht direkt zwei Buttons zur Auswahl:  
* "Create Feature": ein neues Test Feature anlegen
* "Feature List": listet eine Übersicht aller Tests Features auf

### Regarding Privacy (i.e. GDPR)
Die A/B Testing Entscheidungen, welche Version ausgespielt wird, werden in einem Cookie gespeichert.  
Dieser Cookie wird unter den Namen "WYSIWYG_AB_TESTING" abgespeichert, wenn man die Webseite zum ersten Mal aufruft.  
Der Inhalt dieses Cookies ist ein json String, welcher alle Namen der Features und die Entscheidung (a oder b) enthaltet.  
Bei jedem Aufruf auf der Seite wird ein geprüft, ob neue Tests existieren, die nicht im Cookie abgespeichert sind.  
Sollte das der Fall sein, wird der Wert des Cookies neugeschrieben, mit den jeweiligen neuen json string, der alle Entscheidungen beinhaltet.  

 
## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
 
## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.