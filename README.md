# DelphiScreenshotTestsuite

A web platform to confirm automatically created screenshots
as correct. If the automatic screenshots look different in a later run, you'll know.
Differences between screenshots are highlighted.

## Where do the screenshots come from?

They are produced by another piece of software, e.g. a PhantomJS or CasperJS script. At QuoData Dresden, we also let out Windows Delphi software run a script to produce pictures, hence the name of this project. The screenshots produced by the script can have any name suffixed in ``-ist.file_extension``. 

A full example would be ``login_screen-ist.png``. When a picture is confirmed to be ok, another file is created by DelphiScreenshotTestsuite, e.g. ``login_screen-soll.png``. Thus, the confirmed version always ends in ``-soll.file_extension``. 

## Supported file types
- PNG
- BMP (will be automatically converted to PNG so they can be shown in a web browser)
- PDF (will be automatically converted to PNG so they can be shown in a web browser)
- RTF (shown as plain text)

## Installation

You need a WAMP installation: Windows, 
Apache, MySQL, PHP. A popular choice us
to install XAMPP.

#### MySQL credentials
If your prefered username to MySQL is not ``root`` with an empty password, you'll have to set up the file ``config/config.database.inc.php`` with content similar to this:
````
<?php
$servername = "localhost";
$username = "your_mysql_user";
$password = "your_secret_password";
$dbname = "delphiscreenshottestsuite";
````

You'll also need ImageMagick in the PATH.

## Similar projects by other authors
- https://github.com/Sereda-Fazen/VisualCeption
