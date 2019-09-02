# wysiwyg* - Neos A/B Testing
![Neos Package](https://img.shields.io/badge/Neos-Package-blue.svg "Neos Package")
![Neos Project](https://img.shields.io/badge/Neos-%20%3E=%203.2%20-blue.svg "Neos Project")
![PHP 7.1 and above](https://img.shields.io/badge/PHP-%20%3E=%207.1%20-blue.svg "PHP >= 7.1")

This package provides a simple to use backend module and frontend container to run A/B Tests in Neos.  


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
You will find a new menu "A/B Testing" in the main menu on the left in the Neos backend.
The module "Features" will offer all necessary functions to manage A/B testing features.
Clicking on the module, you will be redirected to the A/B Testing dashboard.
* "Create Feature": Add a new A/B Test feature
* "Feature List": Shows a list of all A/B Test Features

## Settings
This package uses default values for creating the used cookie.  
We offer several settings which can be modified for your own usage.  
```
Wysiwyg:  
  ABTesting:  
    cookie:  
      name: 'WYSIWYG_AB_TESTING'  
      lifetime: '+2 years'  
```

You can change the cookie name to your own name.  
Per default the cookie has a lifetime for 2 years. Whenever you need less or a longer lifetime, please note that we use strtotime().
Whenever you change this value, it must apply [strtotime()](https://www.php.net/manual/de/function.strtotime.php).


## Regarding Privacy (i.e. GDPR)
All A/B Testing decisions will be saved into a cookie.  
By default the cookie is named "WYSIWYG_AB_TESTING".  
This cookie will be created whenever a user enters the webpage for the first time.  
Content of the cookie is a raw json string, which includes all names of the features and their decision (a or b).  
Whenever a user has a cookie and enters the page, the cookie will be checked if all active features are saved with a decision.  
If there are new features these will be added to the cookie and a new json string will be saved with all decisions.  
By default the cookie has a lifetime of two years. This is a default value which can be override.
 
## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.  

## Planned Features
We want to enhance the A/B Testing with more solid features.  
We are happy for any contribution for these features and looking forward to enhance this package.
* Decider-Chaining  
Right now it's possible to only add one decision to a feature.  
We want to make it possible to add a chaining of deciders for example DimensionDecision AND Percentage.

## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.