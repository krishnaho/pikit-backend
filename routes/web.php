<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\SettingController;

use App\Http\Controllers\CityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatatableController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestaurantAddonController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantDatatableController;
use App\Http\Controllers\RestaurantItemController;
use App\Http\Controllers\RestaurantOrderController;
use App\Http\Controllers\RestaurantOwnerController;
use App\Http\Controllers\RestaurantPromotionController;
use App\Http\Controllers\RestaurantReportController;
use App\Http\Controllers\UserController;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('login');
});
Route::impersonate();
Auth::routes();
require __DIR__ . '/auth.php';


Route::get('/give-permissions', [PermissionController::class, 'givePermissions']);

Route::get('/test', [SettingController::class, 'test']);
Route::get('/process-payment/{id}', [SettingController::class, 'processPayment'])->name('process.payment');
Route::get('/handle-payment/success/{id}', [SettingController::class, 'handleSuccess']);
Route::get('/handle-payment/cancel/{id}', [SettingController::class, 'handleCancel']);

Route::get('/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/declined', [PaymentController::class, 'paymentDeclined'])->name('payment.declined');
Route::get('/cancelled', [PaymentController::class, 'paymentCancelled'])->name('payment.cancelled');

Route::group(['middleware' => ['auth', 'admin']], function () {


    Route::get('/impersonate/{id}', [UserController::class, 'impersonate'])->name('admin.impersonate');


    // Dashboard
    Route::group(['middleware' => 'permission:dashboard'], function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    });

    Route::group(['middleware' => 'permission:live_orders'], function () {
        Route::get('/ajax-live-orders', [OrderController::class, 'ajaxLiveOrders'])->name('admin.ajaxLiveOrders');
        Route::get('/live-orders/{city_id}', [OrderController::class, 'viewLiveOrders'])->name('admin.viewLiveOrders');
        Route::get('/ajax-live-search-order', [OrderController::class, 'ajaxLiveSearchOrders'])->name('admin.ajaxLiveSearchOrders');
        Route::get('/order-view/{id}', [OrderController::class, 'orderView'])->name('admin.orderView');
        Route::post('/accept-order',  [OrderController::class, 'acceptOrderByAdmin'])->name('admin.acceptOrderByAdmin');
        Route::post('/ready-to-pickup-order',  [OrderController::class, 'readyToPickupByAdmin'])->name('admin.readyToPickupByAdmin');
        Route::get('/cancel-order/{id}', [OrderController::class, 'rejectOrderFromAdmin'])->name('admin.rejectOrderFromAdmin');
        Route::post('/add-items', [OrderController::class, 'addItems'])->name('admin.addItems');
        Route::get('/delete-item/{id}/{quantity}', [OrderController::class, 'deleteItem'])->name('admin.deleteOrderItem');
        Route::post('/customer-approved', [OrderController::class, 'customerApproved'])->name('admin.customerApproval');
        Route::post('/customer-mark-amount',    [OrderController::class, 'customerMarkAmount'])->name('admin.customerMarkAmount');
        Route::post('/customer-paid-amount',     [OrderController::class, 'customerPaid'])->name('admin.customerPaid');
        Route::post('/update-fleet-details',     [OrderController::class, 'updateOrderdetais'])->name('admin.updateFleetDetails');
        Route::get('/view-print-bill/{order_id} ',    [OrderController::class, 'viewThermalPrint'])->name('admin.viewThermalPrint');
        Route::get('/view-print-order-bill/{order_id}',     [OrderController::class, 'viewOrderBill'])->name('admin.viewOrderBill');
        Route::get('/download-prescription-images/{order_id}',  [OrderController::class, 'prescriptionImageDownload'])->name('admin.prescriptionImageDownload');
        Route::get('/remove-prescription-images/{order_id}',  [OrderController::class, 'removePrescriptionImages'])->name('admin.removePrescriptionImages');
        Route::get('/reviews',  [OrderController::class, 'reviews'])->name('admin.reviews');
        Route::get('/thermal-print/{id}',  [OrderController::class, 'thermalPrint'])->name('admin.thermalPrint');
    });

    // Restaurant Categories
    Route::group(['middleware' => 'permission:restaurant_categories'], function () {
        Route::get('/view-restaurant-categories', [RestaurantController::class, 'viewRestaurantCategories'])->name('admin.viewRestaurantCategories');
        Route::post('/create-restaurant-category', [RestaurantController::class, 'addRestaurantCategory'])->name('admin.addRestaurantCategory');
        Route::post('/update-restaurant-category', [RestaurantController::class, 'updateRestaurantCategory'])->name('admin.updateRestaurantCategory');
        Route::get('/toggle-restaurant-category/{id}', [RestaurantController::class, 'toggleRestaurantCategory'])->name('admin.toggleRestaurantCategory');
        Route::get('/delete-restaurant-category/{id}', [RestaurantController::class, 'deleteRestaurantCategory'])->name('admin.deleteRestaurantCategory');
    });



    // Restaurants
    Route::group(['middleware' => 'permission:restaurant'], function () {
        Route::get('/view-restaurant', [RestaurantController::class, 'viewRestaurant'])->name('admin.viewRestaurant');
        Route::get('/edit-restaurant/{id}', [RestaurantController::class, 'editRestaurant'])->name('admin.editRestaurant');
        Route::get('/sort-restaurantcategories/{id}', [RestaurantController::class, 'restaurantCategories'])->name('admin.restaurantCategories');
        Route::post('/sort-item', [RestaurantController::class, 'sortItemCategory'])->name('admin.sortItemCategory');

        Route::post('/create-restaurant', [RestaurantController::class, 'addRestaurant'])->name('admin.addRestaurant');
        Route::post('/update-restaurant', [RestaurantController::class, 'updateRestaurant'])->name('admin.updateRestaurant');
        Route::get('/toggle-restaurant/{id}', [RestaurantController::class, 'toggleRestaurant'])->name('admin.toggleRestaurant');
        Route::get('/delete-restaurant/{id}', [RestaurantController::class, 'deleteRestaurant'])->name('admin.deleteRestaurant');
        Route::get('/sync-team-to-feet/{id}', [RestaurantController::class, 'syncTeamToFeet'])->name('admin.syncTeamToFeet');
        Route::get('/view-restaurant', [RestaurantController::class, 'viewRestaurant'])->name('admin.viewRestaurant');
        Route::get('/generate-restaurant-qr-code', [RestaurantController::class, 'generateRestaurantQRCode'])->name('admin.generateRestaurantQRCode');
        Route::get('/download-restaurant-qr-code/{id}', [RestaurantController::class, 'downloadRestaurantQRCode'])->name('admin.downloadRestaurantQRCode');
    });

    // Item Categories
    Route::group(['middleware' => 'permission:item_categories'], function () {
        Route::get('/view-item-categories', [ItemController::class, 'viewItemCategories'])->name('admin.viewItemCategories');
        Route::post('/create-item-category', [ItemController::class, 'addItemCategory'])->name('admin.addItemCategory');
        Route::post('/update-item-category', [ItemController::class, 'updateItemCategory'])->name('admin.updateItemCategory');
        Route::get('/toggle-item-category/{id}', [ItemController::class, 'toggleItemCategory'])->name('admin.toggleItemCategory');
        Route::get('/delete-item-category/{id}', [ItemController::class, 'deleteItemCategory'])->name('admin.deleteItemCategory');
    });

    // Items
    Route::group(['middleware' => 'permission:items'], function () {
        Route::get('/view-items', [ItemController::class, 'viewItems'])->name('admin.viewItems');
        Route::post('/create-item', [ItemController::class, 'addItem'])->name('admin.addItem');
        Route::get('/edit-item/{id}', [ItemController::class, 'editItem'])->name('admin.editItem');
        Route::post('/update-item', [ItemController::class, 'updateItem'])->name('admin.updateItem');
        Route::get('/toggle-item/{id}', [ItemController::class, 'toggleItem'])->name('admin.toggleItem');
        Route::get('/delete-item/{id}', [ItemController::class, 'deleteItem'])->name('admin.deleteItem');
        Route::post('/select-delete', [ItemController::class, 'selectDelete'])->name('admin.selectDelete');
        Route::get('/view-item-addon-category/{id}', [ItemController::class, 'viewItemAddonCategory'])->name('admin.viewItemAddonCategory');
        Route::post('/update-item-addon-category', [ItemController::class, 'updateItemAddonCategory'])->name('admin.updateItemAddonCategory');
        Route::get('/get-restaurant-item-categories', [ItemController::class, 'getRestaurantItemCategories'])->name('admin.getRestaurantItemCategories');
        Route::get('/get-restaurant-item-category', [ItemController::class, 'getRestaurantItemCategory'])->name('admin.getRestaurantItemCategory');
    });
    Route::post('/view-item-bulk-upload', [ItemController::class, 'viewItemBulkUpload'])->name('admin.viewItemBulkUpload');
    // Item Groups
    Route::group(['middleware' => 'permission:item_groups'], function () {
        Route::get('/view-item-groups', [ItemController::class, 'viewItemGroups'])->name('admin.viewItemGroups');
        Route::post('/create-item-group', [ItemController::class, 'addItemGroup'])->name('admin.addItemGroup');
        Route::post('/update-item-group', [ItemController::class, 'updateItemGroup'])->name('admin.updateItemGroup');
        Route::get('/toggle-item-group/{id}', [ItemController::class, 'toggleItemGroup'])->name('admin.toggleItemGroup');
        Route::get('/delete-item-group/{id}', [ItemController::class, 'deleteItemGroup'])->name('admin.deleteItemGroup');
    });

    // Addon Categories
    Route::group(['middleware' => 'permission:addon_categories'], function () {
        Route::get('/view-addon-categories', [AddonController::class, 'viewAddonCategories'])->name('admin.viewAddonCategories');
        Route::post('/create-addon-category', [AddonController::class, 'addAddonCategory'])->name('admin.addAddonCategory');
        Route::post('/update-addon-category', [AddonController::class, 'updateAddonCategory'])->name('admin.updateAddonCategory');
        Route::get('/toggle-addon-category/{id}', [AddonController::class, 'toggleAddonCategory'])->name('admin.toggleAddonCategory');
        Route::get('/delete-addon-category/{id}', [AddonController::class, 'deleteAddonCategory'])->name('admin.deleteAddonCategory');
    });

    // Addons
    Route::group(['middleware' => 'permission:addons'], function () {
        Route::get('/view-addons', [AddonController::class, 'viewAddons'])->name('admin.viewAddons');
        Route::post('/create-addon', [AddonController::class, 'addAddon'])->name('admin.addAddon');
        Route::post('/update-addon', [AddonController::class, 'updateAddon'])->name('admin.updateAddon');
        Route::get('/toggle-addon/{id}', [AddonController::class, 'toggleAddon'])->name('admin.toggleAddon');
        Route::get('/delete-addon/{id}', [AddonController::class, 'deleteAddon'])->name('admin.deleteAddon');
    });

    // Cities
    Route::group(['middleware' => 'permission:cities'], function () {

        Route::get('/view-cities', [CityController::class, 'viewCities'])->name('admin.viewCities');

        Route::post('/create-city', [CityController::class, 'addCity'])->name('admin.addCity');
        Route::get('/edit-city/{id}', [CityController::class, 'editCity'])->name('admin.editCity');
        Route::post('/update-city', [CityController::class, 'updateCity'])->name('admin.updateCity');
        Route::get('/toggle-city/{id}', [CityController::class, 'toggleCity'])->name('admin.toggleCity');
        Route::get('/update-city-surge/{id}', [CityController::class, 'toggleCitySurge'])->name('admin.toggleCitySurge');
        Route::get('/update-city-user', [CityController::class, 'updateCityUser'])->name('admin.updateCityUser');
        Route::get('/delete-city/{id}', [CityController::class, 'deleteCity'])->name('admin.deleteCity');
    });

    // Settings
    Route::get('/view-settings', [SettingController::class, 'viewSettings'])->name('admin.viewSettings');
    Route::post('/platform-fee-updation', [SettingController::class, 'platformFee'])->name('admin.todayGoldRate');



    // All Users
    Route::group(['middleware' => 'permission:all_users'], function () {
        Route::get('/view-users', [UserController::class, 'viewUsers'])->name('admin.viewUsers');
        Route::post('/add-new-user', [UserController::class, 'addUser'])->name('admin.addUser');
        Route::get('/toggle-user/{id}', [UserController::class, 'toggleUser'])->name('admin.toggleUser');
        Route::get('/edit-user/{id}', [UserController::class, 'editUser'])->name('admin.editUser');
        Route::post('/update-user-info', [UserController::class, 'updateUser'])->name('admin.updateUser');
        Route::post('/update-user-role', [UserController::class, 'updateRole'])->name('admin.updateUserRole');
        Route::get('/accept-user/{id}', [UserController::class, 'acceptUser'])->name('admin.acceptUser');
        Route::get('/view-deleted-user', [UserController::class, 'viewDeletedUsers'])->name('admin.viewDeletedUsers');
        Route::get('/delete-user/{id}', [UserController::class, 'deleteUser'])->name('admin.deleteUser');
        Route::get('/manage-branch/{id}', [UserController::class, 'manageBranch'])->name('admin.manageBranch');
        Route::post('/update-bank-details', [UserController::class, 'updateBankDetails'])->name('admin.updateBankDetails');
        Route::post('/update-manage-branch', [UserController::class, 'updateManageBranch'])->name('admin.updateManageBranch');
        Route::get('/single-customer-user-schemes/{id}', [UserController::class, 'viewSingleCustomerUserSchemes'])->name('admin.viewSingleCustomerUserSchemes');
        Route::post('/add-money-to-wallet', [UserController::class, 'addMoneyToWallet'])->name('admin.addMoneyToWallet');
       
    });


    // Restaurant Owners
    Route::group(['middleware' => 'permission:restaurant_owners'], function () {
        Route::get('/view-all-restaurant-owners', [UserController::class, 'viewAllRestaurantOwners'])->name('admin.viewAllRestaurantOwners');
        Route::get('/delete-store-owner/{id}', [UserController::class, 'deleteUser'])->name('admin.deleteUser');
        Route::get('/manage-restaurant/{id}', [UserController::class, 'manageRestaurant'])->name('admin.manageRestaurant');
        Route::post('/update-manage-restaurant', [UserController::class, 'updateManageRestaurant'])->name('admin.updateManageRestaurant');
        Route::get('/edit-user/{id}', [UserController::class, 'editUser'])->name('admin.editUser');
    });


    // Customers
    Route::group(['middleware' => 'permission:customers'], function () {
        Route::get('/view-all-customers', [UserController::class, 'viewAllCustomers'])->name('admin.viewAllCustomers');
        Route::get('/delete-user/{id}', [UserController::class, 'deleteUser'])->name('admin.deleteUser');
        Route::get('/edit-user/{id}', [UserController::class, 'editUser'])->name('admin.editUser');
    });

    // Banner
    Route::group(['middleware' => 'permission:banners'], function () {
        Route::get('/view-all-banners', [PromotionController::class, 'viewAllBanners'])->name('admin.viewAllBanners');
        Route::post('/create-banner', [PromotionController::class, 'createBanner'])->name('admin.createBanner');
        Route::get('/edit-banner/{id}', [PromotionController::class, 'editBanner'])->name('admin.editBanner');
        Route::post('/update-banner', [PromotionController::class, 'updateBanner'])->name('admin.updateBanner');
        Route::get('/delete-banner/{id}', [PromotionController::class, 'deleteBanner'])->name('admin.deleteBanner');
        Route::get('/toggle-banner/{id}', [PromotionController::class, 'toggleBanner'])->name('admin.toggleBanner');

        //filters
        Route::get('/get-restaurant-category-items', [PromotionController::class, 'getRestaurantCategoryItems'])->name('admin.getRestaurantCategoryItems');
        Route::get('/get-restaurant-category-restaurant', [PromotionController::class, 'getRestaurantCategoryRestaurant'])->name('admin.getRestaurantCategoryRestaurant');
        Route::get('/get-restaurant-category-restaurants', [PromotionController::class, 'getRestaurantCategoryRestaurants'])->name('admin.getRestaurantCategoryRestaurants');
    });

    Route::group(['middleware' => 'permission:coupons'], function () {
        Route::get('/view-all-coupons', [PromotionController::class, 'viewAllCoupons'])->name('admin.viewAllCoupons');
        Route::post('/create-coupon', [PromotionController::class, 'createCoupon'])->name('admin.createCoupon');
        Route::get('/edit-coupon/{id}', [PromotionController::class, 'editCoupon'])->name('admin.editCoupon');
        Route::post('/update-coupon', [PromotionController::class, 'updateCoupon'])->name('admin.updateCoupon');
        Route::get('/delete-coupon/{id}', [PromotionController::class, 'deleteCoupon'])->name('admin.deleteCoupon');
        Route::get('/toggle-coupon/{id}', [PromotionController::class, 'toggleCoupon'])->name('admin.toggleCoupon');

        Route::get('/get-city-items', [PromotionController::class, 'getCityItems'])->name('admin.getCityItems');
        Route::get('/get-city-restaurant', [PromotionController::class, 'getCityRestaurant'])->name('admin.getCityRestaurant');
    });


    Route::group(['middleware' => 'permission:date_wise_report'], function () {
        Route::get('/view-datewise-sales', [ReportController::class, 'viewDatewiseReport'])->name('admin.viewDatewiseReport');
        Route::get('/export-datewise-sales', [ReportController::class, 'exportDateWise'])->name('admin.exportDateWise');
    });

    Route::group(['middleware' => 'permission:restaurant_payout_report'], function () {
        Route::get('/view-restaurant-payout-report', [ReportController::class, 'viewRestaurantPayoutReport'])->name('admin.viewRestaurantPayoutReport');
        Route::post('/export-restaurant-payout-report', [ReportController::class, 'exportRestaurantPayout'])->name('admin.exportRestaurantPayout');
        Route::post('/release-payout', [ReportController::class, 'releasePayouts'])->name('admin.releasePayouts');

        Route::get('/view-completed-restaurant-payout-report', [ReportController::class, 'viewCompletedRestaurantPayoutReport'])->name('admin.viewCompletedRestaurantPayoutReport');
    });


    // Permissions
    Route::group(['middleware' => 'permission:permissions'], function () {
        Route::get('/view-permissions', [PermissionController::class, 'viewPermission'])->name('admin.viewPermission');
        Route::post('/create-new-role', [PermissionController::class, 'createNewRole'])->name('admin.createNewRole');
        Route::get('/edit-role/{id}', [PermissionController::class, 'editRole'])->name('admin.editRole');
        Route::post('/update-role/{id}', [PermissionController::class, 'updateRole'])->name('admin.updateRole');
        Route::get('/delete-role/{id}', [PermissionController::class, 'deleteRole'])->name('admin.deleteRole');
        Route::post('/create-permission', [PermissionController::class, 'createNewPermission'])->name('admin.createNewPermission');
        Route::get('/edit-permission/{id}', [PermissionController::class, 'editPermission'])->name('admin.editPermission');
        Route::post('/update-permission', [PermissionController::class, 'updatePermission'])->name('admin.updatePermission');
        Route::get('/delete-permission/{id}', [PermissionController::class, 'deletePermission'])->name('admin.deletePermission');
    });


    // =====================================End Report==========================================

    //Datatable
    Route::get('/get-all-datatable-restaurant-categories', [DatatableController::class, 'getAllRestaurantCategories'])->name('admin.getAllRestaurantCategories');
    Route::get('/get-all-datatable-reviews', [DatatableController::class, 'getAllReviews'])->name('admin.getAllReviews');

    Route::get('/get-all-datatable-cities', [DatatableController::class, 'getAllCities'])->name('admin.getAllCities');
    Route::get('/get-all-datatable-restaurant', [DatatableController::class, 'getAllRestaurants'])->name('admin.getAllRestaurants');
    Route::get('/get-all-datatable-item-categories', [DatatableController::class, 'getAllItemCategories'])->name('admin.getAllItemCategories');
    Route::get('/get-all-datatable-item-groups', [DatatableController::class, 'getAllItemGroups'])->name('admin.getAllItemGroups');
    Route::get('/get-all-datatable-addon-categories', [DatatableController::class, 'getAllAddonCategories'])->name('admin.getAllAddonCategories');
    Route::get('/get-all-datatable-addon', [DatatableController::class, 'getAllAddons'])->name('admin.getAllAddons');
    Route::get('/get-all-datatable-items', [DatatableController::class, 'getAllItems'])->name('admin.getAllItems');



    Route::get('/get-all-datatable-users-datatable', [DatatableController::class, 'getAllUsers'])->name('admin.getAllUsers');
    Route::get('/get-all-datatable-customers', [DatatableController::class, 'getAllCustomers'])->name('admin.getAllCustomers');
    Route::get('/get-all-datatable-restaurant-owners', [DatatableController::class, 'getAllRestaurantOwners'])->name('admin.getAllRestaurantOwners');
    Route::get('/get-all-datatable-banners', [DatatableController::class, 'getAllBanners'])->name('admin.getAllBanners');
    Route::get('/get-all-datatable-coupons', [DatatableController::class, 'getAllCoupons'])->name('admin.getAllCoupons');
    Route::get('/get-all-datatable-transactions', [DatatableController::class, 'getAllTransactions'])->name('admin.getAllTransactions');
});


Route::group(['prefix' => 'restaurantowner', 'middleware' => 'restaurantowner'], function () {

    // Dashboard
    Route::group(['middleware' => 'permission:dashboard'], function () {
        Route::get('/restaurant-dashboard', [RestaurantOwnerController::class, 'dashboard'])->name('restaurantOwner.dashboard');
    });

    // Restaurants
    Route::group(['middleware' => 'permission:restaurant'], function () {
        Route::get('/restaurant-view-restaurant', [RestaurantOwnerController::class, 'viewRestaurant'])->name('restaurantOwner.viewRestaurant');
        Route::get('/restaurant-edit-restaurant/{id}', [RestaurantOwnerController::class, 'editRestaurant'])->name('restaurantOwner.editRestaurant');
        Route::post('/restaurant-create-restaurant', [RestaurantOwnerController::class, 'addRestaurant'])->name('restaurantOwner.addRestaurant');
        Route::post('/restaurant-update-restaurant', [RestaurantOwnerController::class, 'updateRestaurant'])->name('restaurantOwner.updateRestaurant');
        Route::get('/restaurant-toggle-restaurant/{id}', [RestaurantOwnerController::class, 'toggleRestaurant'])->name('restaurantOwner.toggleRestaurant');
        Route::get('/restaurant-delete-restaurant/{id}', [RestaurantOwnerController::class, 'deleteRestaurant'])->name('restaurantOwner.deleteRestaurant');
    });


    // Live Orders
    Route::group(['middleware' => 'permission:live_orders'], function () {
        Route::get('/restaurant-ajax-live-orders', [RestaurantOrderController::class, 'ajaxLiveOrders'])->name('restaurantOwner.ajaxLiveOrders');
        Route::get('/restaurant-live-orders/{city_id}', [RestaurantOrderController::class, 'viewLiveOrders'])->name('restaurantOwner.viewLiveOrders');
        Route::get('/restaurant-ajax-live-search-order', [RestaurantOrderController::class, 'ajaxLiveSearchOrders'])->name('restaurantOwner.ajaxLiveSearchOrders');
        Route::get('/restaurant-order-view/{id}', [RestaurantOrderController::class, 'orderView'])->name('restaurantOwner.orderView');
        Route::post('/restaurant-accept-order',  [RestaurantOrderController::class, 'acceptOrderByAdmin'])->name('restaurantOwner.acceptOrderByAdmin');
        Route::post('/restaurant-ready-to-pickup-order',  [RestaurantOrderController::class, 'readyToPickupByAdmin'])->name('restaurantOwner.readyToPickupByAdmin');
        Route::get('/restaurant-cancel-order/{id}', [RestaurantOrderController::class, 'rejectOrderFromAdmin'])->name('restaurantOwner.rejectOrderFromAdmin');
        Route::post('/restaurant-add-items', [RestaurantOrderController::class, 'addItems'])->name('restaurantOwner.addItems');
        Route::get('/restaurant-delete-item/{id}/{quantity}', [RestaurantOrderController::class, 'deleteItem'])->name('restaurantOwner.deleteOrderItem');
        Route::post('/restaurant-customer-approved', [RestaurantOrderController::class, 'customerApproved'])->name('restaurantOwner.customerApproval');
        Route::post('/restaurant-customer-mark-amount',    [RestaurantOrderController::class, 'customerMarkAmount'])->name('restaurantOwner.customerMarkAmount');
        Route::post('/restaurant-customer-paid-amount',     [RestaurantOrderController::class, 'customerPaid'])->name('restaurantOwner.customerPaid');
        Route::post('/restaurant-update-fleet-details',     [RestaurantOrderController::class, 'updateOrderdetais'])->name('restaurantOwner.updateFleetDetails');
        Route::get('/restaurant-view-print-bill/{order_id} ',    [RestaurantOrderController::class, 'viewThermalPrint'])->name('restaurantOwner.viewThermalPrint');
        Route::get('/restaurant-view-print-order-bill/{order_id}',     [RestaurantOrderController::class, 'viewOrderBill'])->name('restaurantOwner.viewOrderBill');
        Route::get('/restaurant-download-prescription-images/{order_id}',  [RestaurantOrderController::class, 'prescriptionImageDownload'])->name('restaurantOwner.prescriptionImageDownload');
        Route::get('/restaurant-remove-prescription-images/{order_id}',  [RestaurantOrderController::class, 'removePrescriptionImages'])->name('restaurantOwner.removePrescriptionImages');
        Route::get('/users-with-orders', [RestaurantOrderController::class, 'usersWithOrders'])->name('restaurantOwner.usersWithOrders');
        Route::get('/users-with-orders-data', [DatatableController::class, 'usersWithOrdersAjax'])->name('restaurantOwner.usersWithOrdersAjax');
        Route::get('/detailed-review', [RestaurantOrderController::class, 'detailedReview'])->name('restaurantOwner.detailedReview');
        Route::get('/restaurant/reviews/data', [DatatableController::class, 'detailedReviewAjax'])->name('restaurantOwner.detailedReviewAjax');

    });

    // Item Categories
    Route::group(['middleware' => 'permission:item_categories'], function () {
        Route::get('/restaurant-view-item-categories', [RestaurantItemController::class, 'viewItemCategories'])->name('restaurantOwner.viewItemCategories');
        Route::post('/restaurant-create-item-category', [RestaurantItemController::class, 'addItemCategory'])->name('restaurantOwner.addItemCategory');
        Route::post('/restaurant-update-item-category', [RestaurantItemController::class, 'updateItemCategory'])->name('restaurantOwner.updateItemCategory');
        Route::get('/restaurant-toggle-item-category/{id}', [RestaurantItemController::class, 'toggleItemCategory'])->name('restaurantOwner.toggleItemCategory');
        Route::get('/restaurant-delete-item-category/{id}', [RestaurantItemController::class, 'deleteItemCategory'])->name('restaurantOwner.deleteItemCategory');
    });

    // Items
    Route::group(['middleware' => 'permission:items'], function () {
        Route::get('/restaurant-view-items', [RestaurantItemController::class, 'viewItems'])->name('restaurantOwner.viewItems');
        Route::post('/restaurant-create-item', [RestaurantItemController::class, 'addItem'])->name('restaurantOwner.addItem');
        Route::get('/restaurant-edit-item/{id}', [RestaurantItemController::class, 'editItem'])->name('restaurantOwner.editItem');
        Route::post('/restaurant-update-item', [RestaurantItemController::class, 'updateItem'])->name('restaurantOwner.updateItem');
        Route::get('/restaurant-toggle-item/{id}', [RestaurantItemController::class, 'toggleItem'])->name('restaurantOwner.toggleItem');
        Route::get('/restaurant-delete-item/{id}', [RestaurantItemController::class, 'deleteItem'])->name('restaurantOwner.deleteItem');
        Route::get('/restaurant-view-item-addon-category/{id}', [RestaurantItemController::class, 'viewItemAddonCategory'])->name('restaurantOwner.viewItemAddonCategory');
        Route::post('/restaurant-update-item-addon-category', [RestaurantItemController::class, 'updateItemAddonCategory'])->name('restaurantOwner.updateItemAddonCategory');
        Route::get('/restaurant-get-restaurant-item-categories', [RestaurantItemController::class, 'getRestaurantItemCategories'])->name('restaurantOwner.getRestaurantItemCategories');

        Route::post('/view-item-bulk-upload', [RestaurantItemController::class, 'viewItemBulkUpload'])->name('admin.viewItemBulkUploadStore');
    });

    // Item Groups
    Route::group(['middleware' => 'permission:item_groups'], function () {
        Route::get('/restaurant-view-item-groups', [RestaurantItemController::class, 'viewItemGroups'])->name('restaurantOwner.viewItemGroups');
        Route::post('/restaurant-create-item-group', [RestaurantItemController::class, 'addItemGroup'])->name('restaurantOwner.addItemGroup');
        Route::post('/restaurant-update-item-group', [RestaurantItemController::class, 'updateItemGroup'])->name('restaurantOwner.updateItemGroup');
        Route::get('/restaurant-toggle-item-group/{id}', [RestaurantItemController::class, 'toggleItemGroup'])->name('restaurantOwner.toggleItemGroup');
        Route::get('/restaurant-delete-item-group/{id}', [RestaurantItemController::class, 'deleteItemGroup'])->name('restaurantOwner.deleteItemGroup');
    });

    // Addon Categories
    Route::group(['middleware' => 'permission:addon_categories'], function () {
        Route::get('/restaurant-view-addon-categories', [RestaurantAddonController::class, 'viewAddonCategories'])->name('restaurantOwner.viewAddonCategories');
        Route::post('/restaurant-create-addon-category', [RestaurantAddonController::class, 'addAddonCategory'])->name('restaurantOwner.addAddonCategory');
        Route::post('/restaurant-update-addon-category', [RestaurantAddonController::class, 'updateAddonCategory'])->name('restaurantOwner.updateAddonCategory');
        Route::get('/restaurant-toggle-addon-category/{id}', [RestaurantAddonController::class, 'toggleAddonCategory'])->name('restaurantOwner.toggleAddonCategory');
        Route::get('/restaurant-delete-addon-category/{id}', [RestaurantAddonController::class, 'deleteAddonCategory'])->name('restaurantOwner.deleteAddonCategory');
    });

    // Addons
    Route::group(['middleware' => 'permission:addons'], function () {
        Route::get('/restaurant-view-addons', [RestaurantAddonController::class, 'viewAddons'])->name('restaurantOwner.viewAddons');
        Route::post('/restaurant-create-addon', [RestaurantAddonController::class, 'addAddon'])->name('restaurantOwner.addAddon');
        Route::post('/restaurant-update-addon', [RestaurantAddonController::class, 'updateAddon'])->name('restaurantOwner.updateAddon');
        Route::get('/restaurant-toggle-addon/{id}', [RestaurantAddonController::class, 'toggleAddon'])->name('restaurantOwner.toggleAddon');
        Route::get('/restaurant-delete-addon/{id}', [RestaurantAddonController::class, 'deleteAddon'])->name('restaurantOwner.deleteAddon');
    });

    // Banner
    Route::group(['middleware' => 'permission:banners'], function () {
        Route::get('/restaurant-view-all-banners', [RestaurantPromotionController::class, 'viewAllBanners'])->name('restaurantOwner.viewAllBanners');
        Route::post('/restaurant-create-banner', [RestaurantPromotionController::class, 'createBanner'])->name('restaurantOwner.createBanner');
        Route::get('/restaurant-edit-banner/{id}', [RestaurantPromotionController::class, 'editBanner'])->name('restaurantOwner.editBanner');
        Route::post('/restaurant-update-banner', [RestaurantPromotionController::class, 'updateBanner'])->name('restaurantOwner.updateBanner');
        Route::get('/restaurant-delete-banner/{id}', [RestaurantPromotionController::class, 'deleteBanner'])->name('restaurantOwner.deleteBanner');
        Route::get('/restaurant-toggle-banner/{id}', [RestaurantPromotionController::class, 'toggleBanner'])->name('restaurantOwner.toggleBanner');

        //filters
        Route::get('/restaurant-get-restaurant-category-items', [RestaurantPromotionController::class, 'getRestaurantCategoryItems'])->name('restaurantOwner.getRestaurantCategoryItems');
        Route::get('/restaurant-get-restaurant-category-restaurant', [RestaurantPromotionController::class, 'getRestaurantCategoryRestaurant'])->name('restaurantOwner.getRestaurantCategoryRestaurant');
        Route::get('/restaurant-get-restaurant-category-restaurants', [RestaurantPromotionController::class, 'getRestaurantCategoryRestaurants'])->name('restaurantOwner.getRestaurantCategoryRestaurants');
    });

    Route::group(['middleware' => 'permission:coupons'], function () {
        Route::get('/restaurant-view-all-coupons', [RestaurantPromotionController::class, 'viewAllCoupons'])->name('restaurantOwner.viewAllCoupons');
        Route::post('/restaurant-create-coupon', [RestaurantPromotionController::class, 'createCoupon'])->name('restaurantOwner.createCoupon');
        Route::get('/restaurant-edit-coupon/{id}', [RestaurantPromotionController::class, 'editCoupon'])->name('restaurantOwner.editCoupon');
        Route::post('/restaurant-update-coupon', [RestaurantPromotionController::class, 'updateCoupon'])->name('restaurantOwner.updateCoupon');
        Route::get('/restaurant-delete-coupon/{id}', [RestaurantPromotionController::class, 'deleteCoupon'])->name('restaurantOwner.deleteCoupon');
        Route::get('/restaurant-toggle-coupon/{id}', [RestaurantPromotionController::class, 'toggleCoupon'])->name('restaurantOwner.toggleCoupon');

        Route::get('/restaurant-get-city-items', [RestaurantPromotionController::class, 'getCityItems'])->name('restaurantOwner.getCityItems');
        Route::get('/restaurant-get-city-restaurant', [RestaurantPromotionController::class, 'getCityRestaurant'])->name('restaurantOwner.getCityRestaurant');
    });


    Route::group(['middleware' => 'permission:date_wise_report'], function () {
        Route::get('/restaurant-view-datewise-sales', [RestaurantReportController::class, 'viewDatewiseReport'])->name('restaurantOwner.viewDatewiseReport');
        Route::get('/restaurant-export-datewise-sales', [RestaurantReportController::class, 'exportDateWise'])->name('restaurantOwner.exportDateWise');
    });

    Route::group(['middleware' => 'permission:restaurant_payout_report'], function () {
        Route::get('/restaurant-view-restaurant-payout-report', [RestaurantReportController::class, 'viewRestaurantPayoutReport'])->name('restaurantOwner.viewRestaurantPayoutReport');
        Route::post('/release-restaurant-payout', [RestaurantReportController::class, 'releaseRestaurantPayouts'])->name('restaurantOwner.releaseRestaurantPayouts');
        Route::post('/restaurant-export-restaurant-payout-report', [RestaurantReportController::class, 'exportRestaurantPayout'])->name('restaurantOwner.exportRestaurantPayout');
        Route::post('/restaurant-release-payout', [RestaurantReportController::class, 'releasePayouts'])->name('restaurantOwner.releasePayouts');

        Route::get('/view-completed-restaurant-payout-report', [RestaurantReportController::class, 'viewCompletedRestaurantPayoutReport'])->name('restaurantOwner.viewCompletedRestaurantPayoutReport');
    });

    // =====================================End Report==========================================

    //Datatable
    Route::get('/restaurant-get-all-datatable-restaurant-categories', [RestaurantDatatableController::class, 'getAllRestaurantCategories'])->name('restaurantOwner.getAllRestaurantCategories');
    Route::get('/restaurant-get-all-datatable-cities', [RestaurantDatatableController::class, 'getAllCities'])->name('restaurantOwner.getAllCities');
    Route::get('/restaurant-get-all-datatable-restaurant', [RestaurantDatatableController::class, 'getAllRestaurants'])->name('restaurantOwner.getAllRestaurants');
    Route::get('/restaurant-get-all-datatable-item-categories', [RestaurantDatatableController::class, 'getAllItemCategories'])->name('restaurantOwner.getAllItemCategories');
    Route::get('/restaurant-get-all-datatable-item-groups', [RestaurantDatatableController::class, 'getAllItemGroups'])->name('restaurantOwner.getAllItemGroups');
    Route::get('/restaurant-get-all-datatable-addon-categories', [RestaurantDatatableController::class, 'getAllAddonCategories'])->name('restaurantOwner.getAllAddonCategories');
    Route::get('/restaurant-get-all-datatable-addon', [RestaurantDatatableController::class, 'getAllAddons'])->name('restaurantOwner.getAllAddons');
    Route::get('/restaurant-get-all-datatable-items', [RestaurantDatatableController::class, 'getAllItems'])->name('restaurantOwner.getAllItems');
    Route::get('/restaurant-get-all-datatable-banners', [RestaurantDatatableController::class, 'getAllBanners'])->name('restaurantOwner.getAllBanners');
    Route::get('/restaurant-get-all-datatable-coupons', [RestaurantDatatableController::class, 'getAllCoupons'])->name('restaurantOwner.getAllCoupons');
    Route::get('/restaurant-get-all-datatable-transactions', [RestaurantDatatableController::class, 'getAllTransactions'])->name('restaurantOwner.getAllTransactions');
});
