# HCMS (Hentai CMS)
Flat-file PHP engine that using .md files to show them as pages. Fork of [grootcms](https://github.com/kdqed/grootcms).


❗ Sometimes you might be able to find more recent updates on [AtomGit/GitCode repository](https://atomgit.com/felixfester/HCMS).


## Features
- Plugins support.
- Built-in blog.
- Themes support. Full support of .css out of box.
- Default theme support dark mode. To check this out, enable dark mode in your system (Android), switch to dark theme (Windows 10/11, Linux) and etc.
- Theme switcher. Open settings to select your favourite theme that would work on all pages.
- Advanced captcha plugin for website protection with support of Cloudflare Turnstile and some small surprises for AI agents (tested on GLM-5-Turbo). Older versions of plugin are available as files "captcha_v2.php" and "captcha_v1.php".
- Maintenance mode. In hentaicms.php code you can adjust flag to "true" to enable maintenance. And "false" to disable. Maintenance mode also using .md file for it's page.
- Ability to mark certain pages as NSFW.
- Nginx-friendly. All pages is being opened through index.php, so it's suitable for absolutely every nginx server, including the most capricious nginx servers where you cannot use permalinks like yourwebsite.com/mypage.
- Perfect and lightweight. Engine has been tested on PHP 8.2 and works perfect here! It work even on ancient versions of Google Chrome. (tested in Chrome ~80 on Android)
- Gently vibe-coded. Main goal was to create "something cool that just works" **in collaboration between human and AIs**. I believe what this is different from what people can call as "AI slop", where AI can be used absolutely mindlessly. While in Hentai CMS almost every change has been tested at least several times manually. So, what you can see here is an efforts done by me with ChatGPT, Grok, DeepSeek and other powerful AI models.

## How to add content?
Write your content as markdown files in the 'content' folder.
Sample URLs and corresponding files loaded shown below:
* 'example.com/' : content/index.md
* 'example.com/index.php?page=info': content/info.md
* 'example.com/index.php?page=aboutme/games': content/aboutme/games.md

## How to mark pages as NSFW?
Open hentaicms.php, find a line with mentioned NSFW pages and add names of your own pages that you would like to mark. Then save and just open them as usually like that:
* 'example.com/index.php?page=nsfw': content/nsfw.md
* 'example.com/index.php?page=arts/hentai': content/arts/hentai.md

## How to put my website into maintenance?
Find a line in hentaicms.php that says '$maintenanceEnabled = false;' and instead of "false" put here "true". To edit maintenance page, open maintenance.md in folder "content". Yes, it's very simple.

## How to make plugin?
Create a new .php file in "plugins" folder:

```
<?php
// Your plugin for HCMS
// Located in plugins/plugin_name.php

// Prevent direct access
if (!defined('HENTAI_CMS_PLUGIN')) {
    header('Location: /index.php');
    exit();
}

// Optional: display errors during development
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

displayHentaiHeader();
?>

The rest of your code can be here

<?php
displayHentaiFooter();
?>
```

and... Yes, this should work for you. After that open your website and try to go to URL like **/index.php?pluginname** to test your plugin.

Also don't forget about making your own custom CSS code if it's necessary and use default one as well, wrapping everything around ```<div class="markdown-content">``` for better result.

Parameters **displayHentaiHeader();** and **displayHentaiFooter();** is necessary (as their name says) for parsing header and footers from engine itself. Header of engine already contains theme and all necessary stuff.

## Installation
To get all latest updates, just download code as .zip archive from AtomGit/GitCode, unpack in folder of your website and done!

# Requirements
- PHP.
- Nginx. You may try Apache or anything else, but 100% work here is not guaranteed.
- Keys for Cloudflare Turnstile from the dashboard.

# Additional things
- If you're going to run HCMS on not nginx server, you might need to add this in .htaccess

```
#Default 404 error 
ErrorDocument 404 /index.php?page=404
```

Just make sure what there is no 404.md file in "content" folder. But if it exist, replace "404" in URL with anything else.



Keep in mind that some things might be opened for curious people, such as direct access to markdown files from URL. There unlikely exist universal solution, so you might need to adjust .htaccess file manually on your server/hosting to prevent this.

---

Have a question? Find links [on my personal website](http://felixfester.run.place/index.php) or go to [my Telegram channel](https://t.me/+fgCDiyU802s1NWZi).
