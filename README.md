# Hentai CMS
Flat-file PHP engine that using .md files to show them as pages.

## Features
- Themes support. Full support of .css out of box.
- Theme switcher. Open settings to select your favourite theme that would work on all pages!
- Maintenance mode. In hentaicms.php code you can adjust flag to "true" to enable maintenance. And "false" to disable. Maintenance mode also using .md file for it's page.
- Nginx-friendly! All pages is being opened through index.php, so it's suitable for absolutely every nginx server, including the most capricious nginx servers where you cannot use permalinks like yourwebsite.com/mypage.
- Perfect and lightweight! Engine was tested on PHP 8.2 and works greatly here!

## How to add content?
Write your content as markdown files in the 'content' folder.
Sample URLs and corresponding files loaded shown below:
* 'example.com/' : content/index.md
* 'example.com/index.php?page=info': content/info.md
* 'example.com/index.php?page=aboutme/games': content/aboutme/games.md

## Installation
To get all latest updates, just download code as .zip archive from GitHub, unpack in root folder of your website and done!

# Requirements
- PHP.
- Nginx. You may try Apache or anything else, but 100% work here is not guaranteed.
- Engine must be in root path of your website, otherwise, it will not work.

---

**Have a question?**
- [Telegram channel](https://t.me/+fgCDiyU802s1NWZi) (mainly Russian)
- [Instagram](https://instagram.com/felixfester) (mainly English / Russian)
- QQ chat group ID: 195194950 (mainly English / Chinese)
