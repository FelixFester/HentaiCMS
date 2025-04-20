# Hentai CMS
Flat-file PHP engine that using .md files to show them as pages. Now support plugins!

## Features
- Plugins support (BETA).
- Blog plugin (not so beta, but still might be not perfect yet)
- Themes support. Full support of .css out of box.
- Default theme support dark mode. To check this out, enable dark mode in your system (Android), switch to dark theme (Windows 10/11, Linux) and etc.
- Theme switcher. Open settings to select your favourite theme that would work on all pages!
- Maintenance mode. In hentaicms.php code you can adjust flag to "true" to enable maintenance. And "false" to disable. Maintenance mode also using .md file for it's page.
- Built-in ability to mark certain pages as NSFW.
- Nginx-friendly! All pages is being opened through index.php, so it's suitable for absolutely every nginx server, including the most capricious nginx servers where you cannot use permalinks like yourwebsite.com/mypage.
- Perfect and lightweight! Engine was tested on PHP 8.2 and works greatly here!

## Demo is available!
Just [open my website](https://felixfester.prtcl.icu/index.php) to test how everything works.

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

## Installation
To get all latest updates, just download code as .zip archive from GitHub, unpack in folder of your website and done!

# Requirements
- PHP.
- Nginx. You may try Apache or anything else, but 100% work here is not guaranteed.

---

**Have a question?**
- [Discord server](https://discord.gg/35wCQmp9) (English / Russian / Chinese)
- [Telegram channel](https://t.me/+fgCDiyU802s1NWZi) (mainly Russian)
- [IG](https://instagram.com/felixfester) (mainly English / Russian)
- QQ chat group ID: 195194950 (mainly English / Chinese)

---

**You can support development, if you like it!** It's not necessary but will give a huge motivation to continue <3
- [Boosty](https://boosty.to/felixfester/about)
