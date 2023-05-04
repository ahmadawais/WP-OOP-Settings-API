<h1 align="center">
  <img src="https://i.imgur.com/gVEqftv.jpg" />

  WordPress OOP Settings And Metabox API

</h1>


[![Tweet for help](https://img.shields.io/twitter/follow/mrahmadawais.svg?style=social&label=Tweet%20@MrAhmadAwais)](https://twitter.com/mrahmadawais/) [![GitHub stars](https://img.shields.io/github/stars/ahmadawais/WP-OOP-Settings-API.svg?style=social&label=Stars)](https://github.com/ahmadawais/WP-OOP-Settings-API/stargazers) [![GitHub followers](https://img.shields.io/github/followers/ahmadawais.svg?style=social&label=Follow)](https://github.com/ahmadawais?tab=followers) — :point_up: Make sure you :star: and :eyes: this repository!

> Ever wanted to build custom settings (or metaboxes) inside your WordPress plugin or theme and didn't like the non-DRY approach for creating custom settings and metaboxes via WordPress API? Well, that's why and when I wrote this OOP Wrapper for WordPress Settings API. 🎊

![Screenshots](https://on.ahmda.ws/qPBC/c)



## Screenshots

![](https://i.imgur.com/EXUoeLZ.png)
![](https://i.imgur.com/sc9816W.png)
![](https://i.imgur.com/0SWjn4A.png)


## COMPOSER INSTALL

* As for now, this package is not yet submited to packagist, you'll have the repository to your composer file like this :
```json
{
    "require": {
        "ahmadawais/WP-OOP-Settings-API": "dev-master"
    },
    "repositories": [
        {
            "type": "git",
            "url":  "https://github.com/ahmadawais/WP-OOP-Settings-API.git"
        }
    ]
}

```

* You'll be able to use WP_OSA class after requiring vendor/autoload.php

## USAGE

### USAGE For Setting Page
* Prepare an array of options then instanciate WP_OSA
```php
$options = 
[
    'name' => 'MY_AWESOME_FEATURE',
    'title' => 'My Awesome Feature',
    'fields' => [
        [
            'id' => 'ACTIVE',
            'type' => 'checkbox',
            'title' => 'The feature is active' ,
        ],
        [
            'id' => 'FIRST_SETTING',
            'type' => 'number',
            'title' => 'First setting' ,
            'default' => 0 ,
            // This setting will be included only if the first checkbox is checked
            'show_if' => function(){ return defined('MY_AWESOME_FEATURE_ACTIVE') && MY_AWESOME_FEATURE_ACTIVE == 'on'; }
        ]
    ]
];
$setting = new WP_OSA($options);
```
* Once the options are saved, constants MY\_\AWESOME\_FEATURE\_ACTIVE will be available and will be able to set the first setting MY\_AWESOME\_FEATURE\_FIRST\_SETTING 


### USAGE For Post Metabox
* Prepare an array of options as well as the metabox definition then instanciate WP_OSA

```php
$options = 
[
    'name' => 'MY_AWESOME_FEATURE',
    'title' => 'My Awesome Feature',
    'fields' => [
        [
            'id' => 'ACTIVE',
            'type' => 'checkbox',
            'title' => 'The feature is active' ,
        ],
        [
            'id' => 'FIRST_SETTING',
            'type' => 'number',
            'title' => 'First setting' ,
            'default' => 0 ,
            // This setting will be included only if the first checkbox is checked
            'show_if' => function(){ return defined('MY_AWESOME_FEATURE_ACTIVE') && MY_AWESOME_FEATURE_ACTIVE == 'on'; }
        ]
    ]
];
$metabox = [
    'id' => 'my_metabox',
    'title' => 'My Awesome Metabox',
    'post_types' => ['post'], // Post types to display meta box
    'context' => 'advanced',
    'priority' => 'default',
];
$metabox = new WP_OSA($options , $metabox);
```

* Once the metabox is saved, fields will be saved as post metas : MY\_AWESOME\_FEATURE\_FIRST\_ACTIVE  and   MY\_AWESOME\_FEATURE\_FIRST\_SETTING 



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
- [x] Create Field: `email`
- [x] Create Field: `date`
- [x] Create Field (generated content with callback): `content`
- [x] Create Field: `range`
- [x] Support for post metabox
- [ ] Tutorials
- [ ] Blog post
- [ ] Documentation
- [ ] Re-factor the code with WP Standards
- [ ] Re-factor the code into classes

![License](https://on.ahmda.ws/qNys/c)

## License
Release under GNU GPL v2.0


![Credits](https://on.ahmda.ws/qOxs/c)

## Credits

@AhmadAwais, @deviorobert, @MaedahBatool
AND @WordPress, @tareq1988, @royboy789, @twigpress, @rahal.


---
![Hello](https://on.ahmda.ws/3dea3a3b1de3/c)

### 🙌 [THEDEVCOUPLE PARTNERS](https://TheDevCouple.com/partners)

This open source project is maintained by the help of awesome businesses listed below. What? [Read more about it →](https://TheDevCouple.com/partners)

<table width='100%'>
	<tr>
		<td width='500'><a target='_blank' href='https://kinsta.com/?kaid=WMDAKYHJLNJX&utm_source=TheDevCouple&utm_medium=Partner'><img src='https://on.ahmda.ws/73cedc/c' /></a></td>
		<td width='500'><a target='_blank' href='https://ahmda.ws/USES_WPE?utm_source=TheDevCouple&utm_medium=Partner'><img src='https://on.ahmda.ws/ff40fe/c' /></a></td>
	</tr>
	<tr>
		<td width='500'><a target='_blank' href='https://mythemeshop.com/?utm_source=TheDevCouple&utm_medium=Partner'><img src='https://on.ahmda.ws/3166d9/c' /></a></td>
		<td width='500'><a target='_blank' href='https://ipapi.com/?utm_source=TheDevCouple&utm_medium=Partner'><img src='https://d2ddoduugvun08.cloudfront.net/items/1R190r2U0p3N3L0U0b2u/ip-api.png'/></a></td>
	</tr>
</table>

<br />
<br />
<p align="center">
<strong>For anything else, tweet at <a href="https://twitter.com/MrAhmadAwais/" target="_blank" rel="noopener noreferrer">@MrAhmadAwais</a></strong>
</p>

<div align="center">
	<p>I have released a video course to help you become a better developer — <a href="https://VSCode.pro/?utm_source=GitHubFOSS" target="_blank">Become a VSCode Power User →</a></p>
    <br />
  <a href="https://VSCode.pro/?utm_source=GitHubFOSS" target="_blank">
  <img src="https://raw.githubusercontent.com/ahmadawais/shades-of-purple-vscode/master/images/vscodeproPlay.jpg" /><br>VSCode</a>

  _<small><a href="https://VSCode.pro/?utm_source=GitHubFOSS" target="_blank">VSCode Power User Course →</a></small>_
</div>
