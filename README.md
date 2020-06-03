<h1 align="center">
  <img src="https://i.imgur.com/gVEqftv.jpg" />

  WordPress OOP Settings API

</h1>


[![Tweet for help](https://img.shields.io/twitter/follow/mrahmadawais.svg?style=social&label=Tweet%20@MrAhmadAwais)](https://twitter.com/mrahmadawais/) [![GitHub stars](https://img.shields.io/github/stars/ahmadawais/WP-OOP-Settings-API.svg?style=social&label=Stars)](https://github.com/ahmadawais/WP-OOP-Settings-API/stargazers) [![GitHub followers](https://img.shields.io/github/followers/ahmadawais.svg?style=social&label=Follow)](https://github.com/ahmadawais?tab=followers) — :point_up: Make sure you :star: and :eyes: this repository!

> Ever wanted to build custom settings inside your WordPress plugin or theme and didn't like the non-DRY approach for creating custom settings via WordPress API? Well, that's why and when I wrote this OOP Wrapper for WordPress Settings API. 🎊

![Screenshots](http://on.ahmda.ws/qPBC/c)

## Screenshots

![](https://i.imgur.com/EXUoeLZ.png)
![](https://i.imgur.com/sc9816W.png)
![](https://i.imgur.com/0SWjn4A.png)

## Extensible

If you want to add to settings page from child plugin:

  * Make Parent plugin instance is publicly accessible: `self::$settings_page = new Settings_Page();`
  * In child plugin: self::$wposa_obj = `Parent_plugin_class::$settings_page::$wposa_obj;
  * Now `self::$wposa_obj->add_section` adds to Parent plugin page.

## TODO:
- [x] Basic Settings Page
- [x] Tabs on Settings Page with JS
- [x] Tabs on Settings Page with JS
- [x] Documentation for code workflow
- [x] Create Field: `text`
- [x] Create Field: `textarea`
- [x] Create Field: `url`
- [x] Create Field: `number`
- [x] Create Field: `checkbox`
- [x] Create Field: `multicheck`
- [x] Create Field: `radio`
- [x] Create Field: `select`
- [x] Create Field: `html`
- [x] Create Field: `wysiwyg`
- [x] Create Field: `file`
- [x] Create Field: `image`
- [x] Create Field: `password`
- [x] Create Field: `color`
- [ ] Tutorials
- [ ] Blog post
- [ ] Documentation
- [ ] Re-factor the code with WP Standards
- [ ] Re-factor the code into classes

![License](http://on.ahmda.ws/qNys/c)

## License
Release under GNU GPL v2.0


![Credits](http://on.ahmda.ws/qOxs/c)

## Credits

@AhmadAwais, @deviorobert, @MaedahBatool
AND @WordPress, @tareq1988, @royboy789, @twigpress.


---
![image](http://on.ahmda.ws/qP9p/c)
### 🙌 [WPCOUPLE PARTNERS](https://WPCouple.com/partners):
This open source project is maintained by the help of awesome businesses listed below. What? [Read more about it →](https://WPCouple.com/partners)

<table width='100%'>
	<tr>
		<td width='333.33'><a target='_blank' href='https://www.gravityforms.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtrE/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://kinsta.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mu5O/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://wpengine.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mto3/c' /></a></td>
	</tr>
	<tr>
		<td width='333.33'><a target='_blank' href='https://www.sitelock.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtyZ/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://wp-rocket.me/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtrv/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://blogvault.net/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtph/c' /></a></td>
	</tr>
	<tr>
		<td width='333.33'><a target='_blank' href='http://cridio.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtmy/c' /></a></td>
		<td width='333.33'><a target='_blank' href='http://wecobble.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtrW/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://www.cloudways.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mu0C/c' /></a></td>
	</tr>
	<tr>
		<td width='333.33'><a target='_blank' href='https://www.cozmoslabs.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mu9W/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://wpgeodirectory.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtwv/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://www.wpsecurityauditlog.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtkh/c' /></a></td>
	</tr>
	<tr>
		<td width='333.33'><a target='_blank' href='https://mythemeshop.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/n3ug/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://www.liquidweb.com/?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mtnt/c' /></a></td>
		<td width='333.33'><a target='_blank' href='https://WPCouple.com/contact?utm_source=WPCouple&utm_medium=Partner'><img src='http://on.ahmda.ws/mu3F/c' /></a></td>
	</tr>
</table>
