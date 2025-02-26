# Hentai CMS
Flat-file PHP engine that using .md files to show them as pages. Fork of very lightweight grootcms.

## Differences from grootcms
- Themes support. Now engine fully support .css out of box. You can also add different theme for each page by using on .css file same name as the name of your page in .md file. For example if you have page named "info.md", you can add to it in "themes" folder "info.css".
- Maintenance mode. In hentaicms.php code you can adjust flag to "true" to enable maintenance. And "false" to disable. Maintenance mode also using .md file for it's page.
- Nginx-friendly! All pages is being opened through index.php, so it's suitable for absolutely every server, including the most capricious nginx servers where you cannot use permalinks like yourwebsite.com/mypage.

## Adding content
Write your content as markdown files in the 'content' folder.
Sample URLs and corresponding files loaded shown below:
* 'example.com/' : content/index.md
* 'example.com/index.php?page=page1': content/page1.md
* 'example.com/index.php?page=page2': content/page2/index.md
* 'example.com/index.php?page=page2/a': content/page2/a.md (not tested yet)

## Styling
Groot CMS comes with no bootstrap, angular, material design, framework, twig, leaf, bells, whistles, infinity stones, whatever. But we have theme support already and engine would come with it's own .css theme that should be adjusted properly.

---

**Soon would be available**
