Also supplier (also.com) API import module for Prestashop

This API module allows you to import products from also.com to Prestashop.

Please note that there is some issue with quantities and price import (they are different from those whose can be seen in also.com), but I am investigating why.

Requirements
You must be an also.com client, probably need a contract with also.com, also.com will provide you: client ID, username, password, SCI number and token.
How to install
Copy the alsoimport folder into modules directory of your PrestaShop installation.
Copy the import-update-also-xer5sd4d.php and also-tree.php in Prestashop root directory.
Using the module
Login in Prestashop adminpanel and find Alsoimport settings. Fill in necessary data, provided by also.com
Then, visit yoursite.com/also-tree.php. This will add categories from also.com in your Prestashop database's table.
After that open Alsoimport filter where you can create import rules with margins in % for each price range (for example, <100). Import rules are category based.


