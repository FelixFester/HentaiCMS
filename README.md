# Hentai CMS
Flat-file PHP engine that using .md files to show them as pages.

## Features
- Themes support. Full support of .css out of box.
- Theme switcher. Open settings to select your favourite theme that would work on all pages!
- Maintenance mode. In hentaicms.php code you can adjust flag to "true" to enable maintenance. And "false" to disable. Maintenance mode also using .md file for it's page.
- Nginx-friendly! All pages is being opened through index.php, so it's suitable for absolutely every server, including the most capricious nginx servers where you cannot use permalinks like yourwebsite.com/mypage.
- Perfect and lightweight! Engine was tested on PHP 8.2 and works greatly here!

## How to add content?
Write your content as markdown files in the 'content' folder.
Sample URLs and corresponding files loaded shown below:
* 'example.com/' : content/index.md
* 'example.com/index.php?page=info': content/info.md
* 'example.com/index.php?page=aboutme/games': content/aboutme/games.md

## Installation
Download latest version from releases. Unpack in your website folder and done!

You can also just download whole repository as .zip file, but keep in mind what changes here might be quite unstable. Or not.

---

# Requirements
- PHP.
- Preferably Nginx. But you may try Apache or anything else, but 100% work here is not guaranteed.

---

**Have a question?**
- [Telegram channel](https://t.me/+fgCDiyU802s1NWZi) (mainly Russian)
- [Instagram](https://instagram.com/felixfester) (mainly English / Russian)
- QQ chat group ID: 195194950 (mainly English / Chinese)
