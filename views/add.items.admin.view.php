<?php use CMS\Model\Shop\CategoryModel;

$title = SHOP_ITEM_ADD_TITLE;
$description = SHOP_ITEM_ADD_DESCRIPTION;

$styles = "<link rel='stylesheet' href='" . getenv("PATH_SUBFOLDER") . "admin/resources/vendors/select2/css/select2.min.css'>";
$styles .= '<link rel="stylesheet" href="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/dragula/dragula.css">';
$styles .= "<link rel='stylesheet' href='" . getenv("PATH_SUBFOLDER") . "admin/resources/vendors/sweetalert2/sweetalert2.min.css'>";
$styles .= "<style>

.select2-container--default .select2-selection--multiple {
    border-width: 0 0 2px 0;
}


select.is-invalid ~ .select2-container--default .select2-selection--multiple {
    border-color: #dc3545;
    padding-right: 2.25rem;
    background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e\");
    background-repeat: no-repeat;
    background-position: right calc(.375em + .1875rem) center;
    background-size: calc(.75em + .375rem) calc(.75em + .375rem);
}

select.is-valid ~ .select2-container--default .select2-selection--multiple {
    border-color: #28a745;
    padding-right: 2.25rem;
    background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e\");
    background-repeat: no-repeat;
    background-position: right calc(.375em + .1875rem) center;
    background-size: calc(.75em + .375rem) calc(.75em + .375rem);
}

.select2-container--default.select2-container--focus .select2-selection--multiple {
    outline: none;
    border-width: 0 0 2px 0;
    border-color: #80bdff;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: var(--primary);
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: var(--white);
}

.nav-item, .action-object {
    cursor: grab;
}
::marker {
    content: none;
}
</style>";

$scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/jquery-validation/jquery.validate.js" ></script>';
$scripts .= '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/jquery-validation/additional-methods.js" ></script>';
$scripts .= '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/dragula/dragula.js"> </script>';
$scripts .= '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/sweetalert2/sweetalert2.min.js" ></script>';
$scripts .= '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/inputmask/jquery.inputmask.js" ></script>';
$scripts .= '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/bootstrap/bootstrap.bundle.js" ></script>';
$scripts .= '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/select2/js/select2.full.min.js" ></script>';

$scripts .= '<script>


$(() => {
    
    
    let actionDrake = dragula([document.getElementById("actionList"), document.getElementById("actionChoiceList")], {
        removeOnSpill: true,
        copy: (el, source) => {
            return source === document.getElementById("actionList")
        },
        accepts: (el, target) => {
            return target !== document.getElementById("actionList");
        }
    })
    
   actionDrake.on("drop", function (el) {
   let realNode = document.createElement("div");
   realNode.classList.add("card", "card-comments")
   htmlElement = `<div class="card-header">
                    <h5 class="text-center">ACTION: API Action</h5>
                 </div>
                 <div class="card-body p-0 pl-3">
                    <ul class="nav nav-pills flex-column" id="actionList">
                        <li class="nav-item">
                            <div class="nav-link">
                                <div class="form-group">
                                    <label for="item_name"> API URI* </label>
                                    <input name="item_name" id="item_name" type="text" class="form-control form-control-border border-width-2"
                                                   placeholder="https://api.craftmysite.help/" />
                                    </div>
                            </div>
                        </li>
                    </ul>
                 </div>`
   realNode.insertAdjacentHTML("beforeend", htmlElement);
   el.parentNode.replaceChild(realNode, el);
});
    
    
    $("#item_price").inputmask("numeric", {
        digits: 2
    });
    $("#item_stock").inputmask("integer", {min: -1});
    $("#item_userLimit").inputmask("integer", {min: -1});
    $(".custom-select").select2({
        width: "100%"
    })

    jQuery.extend(jQuery.validator.messages, {
        required: "' . SHOP_VALIDATOR_REQUIRED . '",
    });


    $("#itemForm").validate({
                                rules         : {
                                    item_name      : {required: true},
                                    item_price     : {required: true},
                                    item_userLimit : {required: true},
                                    item_stock     : {required: true},
                                },
                                messages      : {},
                                errorElement  : "span",
                                errorPlacement: (error, element) => {
                                    error.addClass("invalid-feedback");
                                    element.closest(".form-group").append(error);
                                },
                                highlight     : (element, errorClass, validClass) => {
                                    $(element).addClass("is-invalid");
                                },
                                unhighlight   : (element, errorClass, validClass) => {
                                    $(element).addClass("is-valid");
                                    $(element).removeClass("is-invalid");
                                },
                                submitHandler : () => {

                                    $.ajax({
                                               url    : "' . getenv("PATH_SUBFOLDER") . 'cms-admin/shop/items/add",
                                               type   : "POST",
                                               data   : {
                                                   "item_name"      : jQuery("#item_name").val(),
                                                   "item_price"     : jQuery("#item_price").val(),
                                                   "item_categories": jQuery("#item_categories").val(),
                                                   "item_stock"     : jQuery("#item_stock").val(),
                                                   "item_userLimit" : jQuery("#item_userLimit").val(),
                                               },
                                               success: (res) => {
                                                   
                                                   console.log(res)
                                                   res = parseInt(res)
                                                   
                                                   let toast = Swal.mixin({
                                                        toast: true,
                                                        position: "top-end",
                                                        showConfirmButton: false,
                                                        timer: 3000
                                                   });
                                                   
                                                   toast.fire({
                                                     icon: (res === -1) ? "error" : "success",
                                                     title:  (res === -1) ? "' . SHOP_ERROR_RETRY . '" : "' . SHOP_SUCCESS_ACTION . '"
                                                   })
                                                   
                                                   if(res !== -1) {
                                                       document.getElementById("itemForm").reset();
                                                        $(".select2-selection__rendered").empty()                                                   
                                                   }
                                               },
                                           })
                                },
                            })
})


</script>'
?>

<?php ob_start(); ?>
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"> <?= SHOP_ITEM_ADD_MAIN_TITLE ?></h3>
                    </div>
                    <form id="itemForm" method="post" novalidate="novalidate">
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label for="item_name"> <?= SHOP_ITEM_NAME ?>* </label>
                                            <input name="item_name" id="item_name" type="text"
                                                   class="form-control form-control-border border-width-2"
                                                   placeholder="<?= SHOP_ITEM_PLACEHOLDER_NAME ?>">
                                        </div>
                                    </div>

                                    <div class="col-3 ml-auto">
                                        <div class="form-group">
                                            <label for="item_price"> <?= SHOP_ITEM_PRICE ?>*</label>
                                            <div class="input-group mb-3">
                                                <input name="item_price" id="item_price" type="text" placeholder="42.16"
                                                       class="form-control form-control-border border-width-2">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                                class="fas fa-dollar-sign"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12 col-*-12">
                                        <div class="form-group">
                                            <label for="item_categories"><?= SHOP_ITEM_CATEGORIES ?></label>
                                            <select id="item_categories" name="item_categories"
                                                    class="custom-select select2-purple form-control-border border-width-2"
                                                    multiple="multiple"
                                                    data-placeholder="<?= SHOP_ITEM_PLACEHOLDER_CATEGORIES ?>">
                                                <?php
                                                /** @var categoryModel[] $categoriesList fetchAll function *
                                                 * @var categoryModel $category
                                                 */
                                                foreach ($categoriesList as $category): ?>

                                                    <option value="<?= $category->categoryId ?>"><?= $category->categoryName ?></option>

                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-*-12">
                                        <div class="form-group">
                                            <label for="item_stock"> <?= SHOP_ITEM_STOCK ?>* </label>
                                            <input name="item_stock" id="item_stock" type="text"
                                                   class="text-left form-control form-control-border border-width-2"
                                                   placeholder="<?= SHOP_ITEM_PLACEHOLDER_STOCK ?>">
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-*-12">
                                        <div class="form-group">
                                            <label for="item_userLimit"> <?= SHOP_ITEM_LIMIT_PER_USER ?>*</label>
                                            <input name="item_userLimit" id="item_userLimit" type="text"
                                                   class="text-left form-control form-control-border border-width-2"
                                                   placeholder="<?= SHOP_ITEM_PLACEHOLDER_STOCK ?>">
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="row">
                                            <div class="col-4">

                                                <!-- TODO Static function to actions -->

                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="text-center">Actions Possible</h5>
                                                    </div>
                                                    <div class="card-body p-0 pl-3">
                                                        <ul class="nav nav-pills flex-column" id="actionList">
                                                            <li class="nav-item">
                                                                <div class="nav-link">
                                                                    Call API
                                                                </div>
                                                            </li>

                                                            <li class="nav-item">
                                                                <div class="nav-link">
                                                                    Modification User
                                                                </div>
                                                            </li>

                                                            <li class="nav-item">
                                                                <div class="nav-link">
                                                                    Envoi Mail
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-8">

                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="text-center">Actions Possible</h5>
                                                    </div>
                                                    <div class="card-body p-0 pl-3">
                                                        <ul class="nav nav-pills flex-column p-3"
                                                            id="actionChoiceList">

                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <button type="submit"
                                        class="btn btn-primary float-right"><?= SHOP_ITEM_ADD_BUTTON ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); ?>

<?php require(getenv('PATH_ADMIN_VIEW') . 'template.php'); ?>