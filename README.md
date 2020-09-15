Steps to use:

1) After git clone this repo, run the composer update command on your project root directory.

2) Import the sql file: 'quickbooks.sql' into your local db.

3) Go to .env file, you have to change here database config details, then quickbook details, you have to change only: CLIENT_ID, CLIENT_SECRET, OAUTH_REDIRECT_URI. These details you will get when you setup an app in quickbook.

4) Run the laravel server on local to start the project with this command: php -S localhost:8888 -t public

5) Open the home page, there will be a button 'Connect to Quickbooks', click on it, a dialogue box window will open for authorization, after successfull authorization, your home page will be reloaded.

6) Now you can create, edit users and invoices. These will store to your local db as well as on the quickbook. If you update any info here, same update will show on the quickbook and if you update on quickbook, the updated info will show on your server. This will be in vice versa chain.
