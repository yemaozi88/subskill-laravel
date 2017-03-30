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