# wysiwyg* - Neos A/B Testing
![Neos Package](https://img.shields.io/badge/Neos-Package-blue.svg "Neos Package")
![Neos Project](https://img.shields.io/badge/Neos-%20%3E=%203.2%20-blue.svg "Neos Project")
![PHP 7.1 and above](https://img.shields.io/badge/PHP-%20%3E=%207.1%20-blue.svg "PHP >= 7.1")

This package provides a simple to use backend module and frontend container to run A/B Tests. </br>


## Installation
Run these commands to install the package and update the database schema.
```bash
composer require wysiwyg/neos-abtesting

./flow flow:doctrine:migrate
```

## Usage
This package offers a Node-Container for displaying two different nodes for each decision, weather it should display nodes for version A or B. 

You can add the A/B Testing Container to your constraints 
`Wysiwyg.ABTesting:ABTestingContainer`  
This container has two contentCollections:
* itemsa
* itemsb

These collections accept all content nodes.
This can be changed by override the Node in your own NodeTypes.yaml file.
An editor has to put nodes in each collection for the given version.

**IMPORTANT**  
Both versions will always be rendered in the Neos backend.
Per default version a will be displayed in frontend, if no feature has been configured.

You can find an option group "A / B Testing" in each ABTestingContainer.
This group provides a dropdown to chose which feature will be used for the container.

### Backend-Module usage
You will find a new menu "A/B Testing"
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

## Planned Features
We want to enhance the A/B Testing with more solid features. <br>
We are happy for any contribution for these features and looking forward to enhance this package.
* Decider-Chaining <br>
Right now it's possible to only add one decision to a feature. <br>
We want to make it possible to add a chaining of deciders for example DimensionDecision AND Percentage.

## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.