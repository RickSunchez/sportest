<section class="b-content b-page__section b-page__section_content">
    <div class="b-container">


        <div class="b-table">


            <div class="b-table-cell b-content__left">
                <?= $this->action('CMS:Core:Article:list'); ?>
            </div>


            <div class="b-table-cell b-content__center">
                <?= $this->action('CMS:Core:Html:breadcrumbs'); ?>
                <?= $response; ?>
            </div>


            <div class="b-table-cell b-content__right">
                <?= $this->action('CMS:Core:News:list'); ?>
            </div>


        </div>
        <!-- .b-table -->


    </div>
</section>
