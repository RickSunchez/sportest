<h1><?= _t('CMS:Users','Recover password');?></h1>
<p>Вы запросили ваши регистрационные данные.</p>
<p>Для смены пароля перейдите по следующей ссылке:<br />
    <a href="http://<?= $_SERVER['HTTP_HOST'];?><?= link_to('user_login',array('action'=>'forgot','hash'=>$user->hash,'id'=>$user->pk()))?>">http://<?= $_SERVER['HTTP_HOST'];?><?= link_to('user_login',array('action'=>'forgot','hash'=>$user->hash,'id'=>$user->pk()))?></a>
</p>
<p>Сообщение сгенерировано автоматически.</p>