<h1>Обновление данных в таблице</h1>


<form class="form-horizontal well" role="form" method="post" action="">

    <div class="form-group">
        <label class="col-sm-3 control-label" for="table_name">Название таблицы</label>

        <div class="col-sm-9">
            <input type="text" id="table_name" name="table_name" class="form-control"
                   placeholder="Название таблицы для обновления"/>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="table_column">Название поля</label>

        <div class="col-sm-9">
            <input type="text" id="table_column" name="table_column" class="form-control"
                   placeholder="Название поля у этой таблицы"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-info">Обновить</button>
        </div>
    </div>
</form>
