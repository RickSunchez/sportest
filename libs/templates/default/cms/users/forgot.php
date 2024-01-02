<div class="row" >
    <div class="col-md-offset-3 col-md-5">
        <br /><br />
        <h1>Изменения старого пароля</h1>
        <form method="post" action="<?= link_to('user_login',array('action'=>'forgot','hash'=>$user->hash,'id'=>$user->pk()))?>" class="form-horizontal">

            <?if($error):?>
            <div class="alert alert-danger ">
                <strong>Ошибка!</strong>
                <?= $error?>
            </div>
            <?endif;?>
            <div class="form-group required">
                <label for="password1" class="required col-sm-4 control-label">Новый пароль</label>

                <div class="col-sm-8">
                    <input id="password1" type="password"  name="password1"  class="form-control"/>
                </div>
            </div>
            <div class="form-group required">
                <label for="password2" class="required col-sm-4 control-label">Повторить пароль</label>

                <div class="col-sm-8">
                    <input id="password2" type="password" name="password2" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <input type="submit" value="Изменить" class="btn-lg btn-default btn" >
                </div>
            </div>

        </form>


    </div>

</div>