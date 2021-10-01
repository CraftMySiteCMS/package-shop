<?php use CMS\Model\Shop\CategoryModel;
use CMS\Model\Shop\ItemModel;

$title = SHOP_CATEGORY_LIST_TITLE;
$description = SHOP_CATEGORY_LIST_DESCRIPTION;

$styles = '<link rel="stylesheet" href="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/dragula/dragula.css">';
$styles .= "<link rel='stylesheet' href='" . getenv("PATH_SUBFOLDER") . "admin/resources/vendors/sweetalert2/sweetalert2.min.css'>";
$styles .= '<style>
.nav-item, .category-object {
    cursor: grab;
}
::marker {
    content: none;
}
</style>';

$scripts = '<script type="text/javascript" src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/dragula/dragula.js"> </script>';
$scripts .= '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/jquery-ui/jquery-ui.js" ></script>';
$scripts .= '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/sweetalert2/sweetalert2.min.js" ></script>';
$scripts .= "<script>

$('.save-change').hide();

let setToNotSaved = element => {
    if(!element.classList.contains('not-saved')) {
        element.classList.add('not-saved');
        element.children[0].innerHTML = `<span title='Unsaved' class='badge bg-danger'>Unsaved</span> ` + element.children[0].innerHTML
    }
},
removeNotSaved = element => {
     element.children[0].removeChild( element.children[0].firstElementChild)
}


let categoryDrag = dragula([document.querySelector('.category-object')], {
    invalid: el => {
        return el.classList.contains('card-body')
    }
})

categoryDrag.on('drop', (el, target, source, sibling) => {
    let categoryList = [...el.offsetParent.children[1].children[0].children],
    returnCategories = []
    
    categoryList.forEach(category => returnCategories.push(category.dataset.categoryId))
    
    $.ajax({
        url    : '" . getenv('PATH_SUBFOLDER') . "cms-admin/shop/categories/setWeight',
        type   : 'POST',
        data   : {
            'list_categories': returnCategories,
        },
        success: res => {
        
                console.log(res)
                let toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                toast.fire({
                      icon: (res === -1) ? 'error' : 'success',
                      title:  (res === -1) ? '" . SHOP_ERROR_RETRY . "' : '" . SHOP_SUCCESS_ACTION . "'
               })

        },
      })
})


let itemDrag = dragula([...document.querySelectorAll('[data-category-id ^=category-]'), document.querySelector('#itemList')],{
    removeOnSpill: true,
    copy: (el, source) => {
    return source === document.getElementById('itemList')
  },
  accepts: (el, target) => {
    let elements = [...target.children],
    accept = true
    for(let item of elements) {
        let itemId = item.dataset.itemId,
        itemToAdd = el.dataset.itemId
        if(itemId === itemToAdd) {
            if(el !== item && !(el.classList.contains('dragging') && item.classList.contains('gu-transit'))) accept = false
        }
    }
    return target !== document.getElementById('itemList') && accept
  }
});

itemDrag.on('drag', (el, source) => {
    el.classList.add('dragging')
});

itemDrag.on('dragend', el => {
    el.classList.remove('dragging')
});

itemDrag.on('drop', (el, target, source, sibling) => {
      let itemModified = el.dataset.itemId,
      categoryWhereAdded = target.dataset.categoryId,
      categoryWhereRemoved = source.id === 'itemList' ? 'null-null' : source.dataset.categoryId
      
      let itemId = itemModified.split('-')[1]
      let addCategoryId = categoryWhereAdded.split('-')[1]
      let removeCategoryId = categoryWhereRemoved.split('-')[1]
      setToNotSaved(el);
      
      if(target === source) console.log('Rien en change'); //TODO : Weight to swap items
      
      $.ajax({
        url    : '" . getenv('PATH_SUBFOLDER') . "cms-admin/shop/categories/swapItem',
        type   : 'POST',
        data   : {
            'begin_category': removeCategoryId,
            'end_category': addCategoryId,
            'item_id': itemId,
        },
        success: res => {
        
                console.log(res)
                let toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                toast.fire({
                      icon: (res === -1) ? 'error' : 'success',
                      title:  (res === -1) ? '" . SHOP_ERROR_RETRY . "' : '" . SHOP_SUCCESS_ACTION . "'
               })
               
               setTimeout(() => removeNotSaved(el), 200);

        },
      })
            
      });

itemDrag.on('remove', (el, target, source) => {
    if(el.classList.contains('not-saved')) return
    
    let itemRemoved = el.dataset.itemId,
    categoryWhereRemoved = target.dataset.categoryId
    
    let removeCategoryId = categoryWhereRemoved.split('-')[1],
    itemId = itemRemoved.split('-')[1]
    
    
    
    $.ajax({
            url    : '" . getenv('PATH_SUBFOLDER') . "cms-admin/shop/categories/swapItem',
            type   : 'POST',
            data   : {
                'begin_category': removeCategoryId,
                'end_category': 'null',
                'item_id': itemId,
            },
            success: res => {
                console.log(res)
                    let toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                    toast.fire({
                          icon: (res === -1) ? 'error' : 'success',
                          title:  (res === -1) ? '" . SHOP_ERROR_RETRY . "' : '" . SHOP_SUCCESS_DELETE . "'
                   })
    
            },
          })    
})

</script>";
$scripts .= '<script>

$(".deleteButton").click((el) => {

    let successMessage = () => {
        Swal.fire(
            "' . SHOP_DELETE_TITLE . '!",
            "' . SHOP_SUCCESS_DELETE . '",
            "success",
        )
    },
        errorMessage = () => {
        Swal.fire(
            "' . SHOP_ERROR_TITLE . '",
            "' . SHOP_ERROR_RETRY . '",
            "error",
        )
    }
    let categoryId         = el.target.parentNode.dataset.categoryId

    Swal.fire({
                  title             : "' . SHOP_WARNING_TITLE . '",
                  text              : "' . SHOP_WARNING_MESSAGE . '",
                  icon              : "warning",
                  showCancelButton  : true,
                  confirmButtonColor: "#e74848",
                  cancelButtonColor : "#9f9f9f",
                  confirmButtonText : "' . SHOP_DELETE_BUTTON . '",
                  cancelButtonText  : "' . SHOP_CANCEL_BUTTON . '",
              }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                       url    : "' . getenv('PATH_SUBFOLDER') . 'cms-admin/shop/categories/delete",
                       type   : "POST",
                       data   : {
                           "categoryId": categoryId,
                       },
                       success: res => {
                           if (parseInt(res) === 1)  {
                               successMessage()
                               let column = el.target.offsetParent.offsetParent.offsetParent
                               column.remove();
                           }else {
                               errorMessage();
                           }
                       },
                   })
        }
    })

})

</script>';

ob_start();
?>

<div class="container">


    <div class="row">
        <div class="col-3 mt-5">

            <div class="card sticky-top">

                <div class="card-header">
                    <h3 class="card-title"><?= SHOP_ITEM_LIST ?></h3>
                </div>

                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column" id="itemList">

                        <?php
                        /** @var ItemModel[] $itemList fetchAll function *
                         * @var ItemModel $item
                         */
                        foreach ($itemList as $item): ?>
                            <li class="nav-item" data-item-id="item-<?= $item->itemId ?>">
                                <div class="d-block nav-link" href="#"><?= $item->itemName ?>
                                    <small>(<?= $item->itemId ?>)</small>
                                    <a class="float-right d-inline-block" href="../item/edit/<?= $item->itemId ?>">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        </div>


        <div class="col-8 ml-auto">

            <div class="card card-primary">

                <div class="card-header">
                    <h4><?= SHOP_CATEGORIES ?></h4>
                </div>
                <div class="card-body">
                    <div class="row category-object">
                        <?php
                        /** @var categoryModel[] $categoriesList fetchAll function *
                         * @var categoryModel $category
                         */
                        foreach ($categoriesList as $category): ?>
                            <div class="col-12" data-category-id="<?= $category->categoryId ?>">

                                <div class="card card-light">

                                    <div class="card-header">
                                        <div class="card-option float-left mr-3">
                                            <a href="#" data-category-id="<?= $category->categoryId ?>"
                                               class="deleteButton text-danger">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                        <h3 class="card-title"><?= $category->categoryName ?></h3>
                                        <!-- <h6><?= mb_strimwidth($category->categoryDesc, 0, 255, '...') ?></h6> -->
                                        <div class="card-tools">
                                            <a type="button" class="btn btn-tool text-indigo"
                                               href="../categories/edit/<?= $item->itemId ?>"><i
                                                        class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                                        class="fas fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="nav nav-pills flex-column card-body"
                                         data-category-id="category-<?= $category->categoryId ?>">

                                        <?php $category->categoryItemId = $category->getItems();
                                        foreach ($category->categoryItemId as $itemId):
                                            $item = new ItemModel();
                                            $item->fetch($itemId)
                                            ?>
                                            <li class="nav-item" data-item-id="item-<?= $item->itemId ?>">
                                                <div class="d-block nav-link" href="#"><?= $item->itemName ?>
                                                    <small>(<?= $item->itemId ?>)</small>
                                                    <a class="float-right d-inline-block"
                                                       href="../item/edit/<?= $item->itemId ?>">
                                                        <i class="fa fas fa-cog"></i>
                                                    </a>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </div>

                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<?php $content = ob_get_clean(); ?>

<?php require(getenv("PATH_ADMIN_VIEW") . 'template.php'); ?>

