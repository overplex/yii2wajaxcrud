yii2wajaxcrud 
=============

Gii CRUD template for Single Page Ajax Administration for yii2 

Features
------------
+ Create, read, update, delete in onpage with Ajax
+ Bulk delete suport
+ Pjax widget suport
+ Export function(pdf,html,text,csv,excel,json)
+ Editable suport
+ Daterange suport
+ Internationalization

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist overplex/yii2wajaxcrud "^2.1"
or
php composer.phar require --prefer-dist overplex/yii2wajaxcrud "^3.0"
```

or add

```
"overplex/yii2wajaxcrud": "^2.1"
or
"overplex/yii2wajaxcrud": "^3.0"
```

to the require section of your `composer.json` file.


Usage
-----
For first you must enable Gii module Read more about [Gii code generation tool](http://www.yiiframework.com/doc-2.0/guide-tool-gii.html)

Because this extension used [kartik-v/yii2-grid](https://github.com/kartik-v/yii2-grid) extensions so we must config gridview module before

Let 's add into modules config in your main config file
````php
'modules' => [
    'gridview' =>  [
        'class' => '\kartik\grid\Module'
    ]       
]
````

gii config like
````php
$config['bootstrap'][] = 'gii';
$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
];
$config['modules']['gii']['generators']['wodrowmodel'] = [
    'class' => \overplex\wajaxcrud\generators\model\Generator::class,
    'showName' => "Model Generator 2",
];
$config['modules']['gii']['generators']['wodrowwajaxcrud'] = [
    'class' => \overplex\wajaxcrud\generators\crud\Generator::class,
    'showName' => "Ajax CRUD Generator 2",
];
````

You can then access Gii through the following URL:

http://localhost/path/to/index.php?r=gii

and you can see <b>YOUR AJAX CRUD GENERATOR</b>

![exp1](https://i.loli.net/2019/05/09/5cd3a7c2cb95a.png)
![exp2](https://i.loli.net/2019/05/09/5cd3a7c2cee7a.png)
![exp3](https://i.loli.net/2019/05/09/5cd3a7c2d14a9.png)
