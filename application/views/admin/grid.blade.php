<table class="table table-striped" id="datalist">
    <thead>
        <tr>
            <th>#</th>
            <? foreach ($gridFields as $field) : ?>
            <th><?=ucfirst($field)?></th>
            <? endforeach ?>
            <th id="actions">Acties</th>
        </tr>
    </thead>
    <tbody>

        <? foreach ($items as $key=>$item): ?>
        <tr data-position="<?=$item->sortorder?>" id="<?=$item->sortorder?>">
            <td><?= ( $key + 1 ) ?></td>
            <? foreach ($gridFields as $field ) : ?>
                <td><?=$item->$field?></td>
            <? endforeach ?>
            <td>
   				<a href="/admin/<?=$className?>/form/<?=$item->id?>"><i class="icon-pencil"></i></a>
   				<a href="/admin/<?=$className?>/delete/<?=$item->id?>" class="delete"><i class="icon-trash"></i></a>
    		 </td>
        </tr>
   		<? endforeach ?>
    </tbody>

</table>
