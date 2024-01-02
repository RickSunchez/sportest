<?=$this->action('Shop:Commodity:Review:form', array('goods' => $goods))?>
<?=$this->partial('/shop/goods/review/list', array('items' => $items, 'users' => $users, 'pagination' => $pagination))?>