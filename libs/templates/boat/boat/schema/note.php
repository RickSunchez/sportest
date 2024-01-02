<article class="b-category b-category_full" itemscope itemtype="http://schema.org/ItemList">

    <header class="b-category__header">
        <h1 itemprop="name" class="b-category__title">

            <? if ($note->title): ?>
                <?= $note->title ?>
            <? else: ?>
                <?= $note->name ?>. <?= $vendor ? $vendor->name : '' ?> <?= $schema->name ?>
            <? endif; ?>

        </h1>
    </header>


    <div class="b-table b-note">

        <div class="b-table-cell b-note-image">

            <? if ($image->loaded()): ?>
                <img src="<?= $image->normal ?>"
                     alt="<?= $this->escape($note->name); ?>">
            <? endif; ?>

        </div>
        <div class="b-table-cell  b-note-list">
            <table class="table table-condensed table-bordered table-hover table-striped ">
                <tr class="info">
                    <th width="20">No</th>
                    <th class="i-center-td">Название</th>
                    <th width="100" class="i-center-td">Цена</th>
                    <th width="20"></th>
                </tr>


<style>
    .fold-btn {
        background-color: #278f62;
    }
    .fold-btn:hover {
        background-color: #196645;
    }
    .fold-hidden {
        display: none;
    }
    .fold-hidden.active {
        display: table-row;
        border-left: solid 3px #196645;
        border-right: solid 3px #196645;
    }
</style>
<script>
    window.addEventListener("load", (ev)=>{
        var foldControllers = document.querySelectorAll("span.fold-btn");

        for (let fc of foldControllers) {
            fc.addEventListener("click", (ev)=>{
                const id = fc.getAttribute("fold-id");
                const act = fc.getAttribute("action");
                const hiddens = document.querySelectorAll(`.fold-hidden[fold-id='${id}']`);
                
                for (let h of hiddens) {
                    if (act == "show") {
                        h.classList.add("active");
                    } else {
                        h.classList.remove("active");
                    }
                }

                fc.setAttribute("action", (act=="show") ? "hide" : "show");
                fc.textContent = (act=="show")
                    ? "Свернуть"
                    : "Развернуть";
            });
        }
    })
</script>
<?php 
    function console_log($output,$with_script_tags=true){$js_code='console.log('.json_encode($output,JSON_HEX_TAG).');';if($with_script_tags){$js_code='<script>'.$js_code.'</script>';}
    echo $js_code;};

    function price_me($price) {
        return intval(
            str_replace(
                "&nbsp;р.", "",
                str_replace(
                    " ", "",
                    $price
                )
        ));
    };

    $filterProducts = array();
    $filteredItems = array();
    foreach ($items as $item) {
        if (isset($products[$item->pid])) {
            $filterProducts[$item->pid] = $products[$item->pid];
            $filteredItems[] = $item;
        }
    }

    $foldID = 0;
    $foldCount = 0;
    $minPrice = 0;
    $minPriceFormatted = "";
    $L = count($filteredItems);
    $deprecated = [
        '/ *.оригинал*./iu',
        '/ *.оргинал*./iu',
        '/ *.дубликат*./iu'
    ];

    for ($i=0; $i<$L; $i++) {
        $item = $filteredItems[$i];

        $f = 1;

        $minPriceFormatted = $filterProducts[$item->pid]->getPrice();
        $minPrice = price_me($minPriceFormatted);
        $ftmp = $minPriceFormatted;
        
        while (
            $foldCount==0 && 
            ($item->number == $filteredItems[$i+$f]->number)
        ) {
            $pf = $filterProducts[$filteredItems[$i+$f]->pid]->getPrice();
            $p = price_me($pf);

            if ($p < $minPrice) {
                $minPriceFormatted = $pf;
                $minPrice = $p;
            }

            $f++;
            if ($i + $f > $L-1) break;
        }
        
        if ($f > 1) {
            $foldCount = $f;
            $foldID = "fold-" . $item->number;

            $name = $item->name;
            foreach($deprecated as $d) {
                $name = preg_replace($d, "", $name);
            }
            
            ?>
            <tr>
                <td class="i-center-td">
                    <?= $item->number ?>
                </td>
                <td class="i-middle-td">
                    <div><?= $name ?></div>
                </td>
                <td class="i-center-td">
                    от <?= $minPriceFormatted ?>
                </td>
                <td class="i-middle-td">
                    <span 
                        class="b-btn b-btn_small b-btn_order fold-btn" 
                        fold-id="<?= $foldID; ?>"
                        action="show"
                    >
                        Развернуть
                    </span>
                </td>
            </tr>
            <?php
        }

        $foldClass = "";
        if ($foldCount > 0) {
            $foldCount--;
            $foldClass = "fold-hidden";
        }

        ?>
            <tr 
                class="<?= $foldClass; ?>" 
                fold-id="<?= $foldID; ?>"
            >
                <td class="i-center-td"><?= $item->number ?></td>
                <td class="i-middle-td">
                    <div><?= $item->name ?></div>
                    <b><?= $item->article ?></b>
                </td>
                <td class="i-center-td">
                    <? if (isset($filterProducts[$item->pid]) &&
                        $filterProducts[$item->pid]->status == 1 &&
                        $filterProducts[$item->pid]->is_amount == 1): ?>
                        <?= $filterProducts[$item->pid]->getPrice(); ?>
                    <? else: ?>
                        <?= $filterProducts[$item->pid] ? $filterProducts[$item->pid]->getPrice() : ""; ?>
                        <span class="b-note-none">Нет в наличии</span>
                    <? endif; ?>
                </td>
                <td class="i-middle-td">

                    <? if (isset($filterProducts[$item->pid]) &&
                        $filterProducts[$item->pid]->status == 1 &&
                        $filterProducts[$item->pid]->is_amount == 1): ?>

                        <a class="b-btn b-btn_small" target="_blank"
                            href="<?= $filterProducts[$item->pid]->link(); ?>">Купить</a>
                    <? else: ?>

                        <a class="b-btn b-btn_small b-btn_order"
                            href="<?= $filterProducts[$item->pid]->link(); ?>">Заказать</a>
                    <? endif; ?>

                </td>
            </tr>
        <?php
    }
?>

            </table>
        </div>

    </div>


</article>