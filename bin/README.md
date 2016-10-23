# Binaries

I created this script to build the skeleton of this component. Although I've tried
to make the automatic generation as exact as possible some elements require some
tweaks.

For example it is easy to guess that an `alpha_2` property will have  length of
2 characters but there is one exception I've found so it is not correct to make such 
guesses when trying to generate the MySQL data dump. Same thing happens with 
translatable fields therefor the classes will build the translation base but won't 
hook the translations to the fields.

So be warned, **running these scripts requires to write some code or it WILL break the
code**.

Available scripts are:

- `build-data.php` clones ISO Codes and copies the data json files and translation files to the appropiate paths for this component. This scripts takes some time to complete as translation files are also compiled into binary format.
- `build-model.php` builds the interface and class for the different ISO types. The classes don't fully match those of the project as it is not possible to automate all the process.
- `build-mysql.php` creates a dump file of the data which can be uploaded to MySQL server. The dumped file does **not** contain indexes are field lengths are set to 255 characters. Once again some tweaking must be performed. Please keep in mind that direct import is usually needed for this file (I got errors trying to upload it through PHPMyAdmin.