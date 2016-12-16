<?php
use yii\helpers\Url;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>

</head>
<body>
<?= \cliff363825\kindeditor\KindEditorWidget::widget([
    'model' => $model,
    'attribute' => 'account_name',
    'options' => [], // html attributes
    'clientOptions' => [
        'width' => '680px',
        'height' => '350px',
        'themeType' => 'default', // optional: default, simple, qq
        'langType' => \cliff363825\kindeditor\KindEditorWidget::LANG_TYPE_ZH_CN, // optional: ar, en, ko, ru, zh-CN, zh-TW
    ],
]); ?>
</body>
</html>