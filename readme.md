# Subskill online English test
This is a website to test your English sub-skill online.

# TODO(sonicmisora)
- [ ] Allow only one test a day for each student
- [ ] Consider not expose filelist for quiz
- [x] Add export table to xsml in Admin/Eword
- [ ] Only show detail for the last session of specified day for each student
- [ ] Add lookup detail for each student in Admin/Eword
- [ ] Make eword quiz question orders random
- [ ] Replace all development version js to distribution version

# Build Manual
```shell
copy .env.example .env
change APP_LOG_LEVEL APP_DEBUG to production
composer install
php artisian migrate:refresh --seed
npm install
npm run prod
```

# Build Manual on Windows server
INSTALL SOFTWARES
- composer (https://getcomposer.org/download/).
- xampp (https://www.apachefriends.org/download_success.html).
- npm (https://www.npmjs.com/get-npm)
# install node.js as well.

XAMPP
- start Apache.
Apache is running on Port 8008/443 because 80 is used by IIS.
To change port, from xampp control panel, [config] -> [Apache (httpd.conf)] -> change Listen and ServerName. 
(- mysqladmin.exe -u root password Mwe@NL124S)
- start mySQL. 
- from xampp control panel, click [Admin]. 
- login to mySQL and make a new database called 'subskill'.

Settings
- copy .env.example .env
- on shell (XAMPP control panel), 
php artisan migrate:refresh --seed
npm install
npm run prod


Security issues of XAMPP:
- The MySQL administrator (root) has no password.
[Solution] change password.
https://stackoverflow.com/questions/39590558/how-can-i-access-the-xampp-7-0-9-security-page
1. Using the shell in XAMPP Control Panel, type:
mysqladmin --user=root password "newpassword"
2. On {xampp}/phpMyAdmin/config.inc.php, type appropriate password.
# Saturday/14


- The MySQL daemon is accessible via network.
- ProFTPD uses the password "lampp" for user "daemon".

- The default users of Mercury and FileZilla are known.