# whoa, look at this shit! 

## yes! Another fucking page that works!

You can even go to main page [like this](index.php)! Isn't that cool?

Just don't forget to add links like that `index.php?page=YOUR PAGE FILE NAME IN content/ FOLDER WITHOUT .md`. Or, if your .md file located in **content/** inside folder, you can do for example `index.php?page=aboutme/games` where "aboutme" is name of folder and "games" is a name of file "games.md". Another ways of opening pages that was used before might not work. 

Hentai CMS also support per-page theming. To implement different theme on certain page, you can add in **themes/** folder something like **games.css** (name of .css file should have same name as name of .md page where you are going to use this theme). Then create a .md page in **content/** with same name as in your theme if you didn't do it yet and done! It should be now themed. To keep engine lightweight, I will not add this example, so you can test it by yourself and say if there's any problems or not.

If you using Hentai CMS on certain hosting on nginx without access to configuration, you still will have 404 error when someone tries to open page through index.php. Also you can add index.php with JavaScript redirection to main page in folders to secure them on such nginx servers as well, if your hosting have such thing when without index.php it directly listing all files in folder so anyone can peek inside your website.

If you have any more questions, feel free to go to GitHub and find here link to Telegram or Instagram channels or QQ chat ID. Just click in footer below.

### 💀

----

Powered by [Hentai CMS](https://github.com/FelixFester/HentaiCMS).