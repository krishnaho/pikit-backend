<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CartApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CustomerApiController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\DeliveryPartnerApiController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderApiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreOwnerController;
use App\Http\Controllers\StoreOwnerInventoryController;
use App\Http\Controllers\StoreOwnerOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorApiController;
use App\Http\Controllers\FleetController;


Route::post('/get-all-restuarants', [CustomerApiController::class, 'getAllRestaurants']);
Route::post('/get-single-store-category', [CustomerApiController::class, 'getSingleStoreCategory']);
Route::post('/get-single-product', [CustomerApiController::class, 'getSingleProduct']);

Route::post('/get-single-slider', [CustomerApiController::class, 'getSingleSlider']);
Route::post('/get-single-banner', [CustomerApiController::class, 'getSingleBanner']);
Route::post('/get-single-category', [CustomerApiController::class, 'getSingleCategory']);

Route::post('/get-single-order-app', [CustomerApiController::class, 'getSingleOrder']);

Route::post('/test-api', ['uses' => 'AuthenticationController@test']);
Route::post('/register', ['uses' => 'AuthenticationController@register']);
Route::post('/login', ['uses' => 'AuthenticationController@login']);
Route::post('/give-permissions', [PermissionController::class, 'givePermissions']);

Route::post('/fleet-task-complete', [FleetController::class, 'taskComplete']);
Route::post('/fleet-task-update', [FleetController::class, 'taskUpdate']);

Route::impersonate();
Route::group(['middleware' => ['jwt.auth']], function () {

    Route::post('/impersonate', [AuthenticationController::class, 'StoreOwnerImpersonation']);
    //permission
    Route::post('/get-all-permissions', [PermissionController::class, 'getPermissions']);
    Route::post('/create-permission', [PermissionController::class, 'createPermission']);
    Route::post('/delete-permission', [PermissionController::class, 'deletePermission']);
    Route::post('/update-permission-role', [PermissionController::class, 'togglePermission']);
    Route::post('/update-side-bar-permission', [PermissionController::class, 'updateSideBarPermission']);

    //dashboard
    Route::post('/get-dashboard-data', [DashBoardController::class, 'getDashBoardData']);

    //city
    Route::post('/get-all-cities', [CityController::class, 'getAllCities']);
    Route::post('/create-city', [CityController::class, 'createCity']);
    Route::post('/update-city', [CityController::class, 'updateCity']);
    Route::post('/toggle-city', [CityController::class, 'toggleCity']);
    Route::post('/toggle-city-surge', [CityController::class, 'toggleCitySurge']);
    Route::post('/delete-city', [CityController::class, 'deleteCity']);
    Route::post('/get-all-city-users', [CityController::class, 'getAllCityUsers']);
    Route::post('/update-city-users', [CityController::class, 'updateCityUsers']);
    Route::post('/remove-all-city-users', [CityController::class, 'removeAllCityUsers']);

    //users
    Route::post('/get-all-roles', [UserController::class, 'getAllRoles']);
    Route::post('/get-all-users', [UserController::class, 'getAllUsers']);
    Route::post('/get-single-user', [UserController::class, 'getSingleUser']);
    Route::post('/create-user', [UserController::class, 'createUser']);
    Route::post('/update-user', [UserController::class, 'updateUserDetails']);
    Route::post('/update-user-role', [UserController::class, 'updateUserRole']);
    Route::post('/get-user-orders', [UserController::class, 'getAllUserOrder']);
    Route::post('/get-user-refferal', [UserController::class, 'getAllUserRefferal']);
    Route::post('/get-user-transaction', [UserController::class, 'getUserTransaction']);
    Route::post('/add-money-user-wallet', [UserController::class, 'addMoneyToWallet']);
    Route::post('/update-user-bank-details', [UserController::class, 'updateUserBankDetails']);
    Route::post('/update-delivery-guy-details', [UserController::class, 'updateDeliveryGuyDetails']);
    Route::post('/toggle-user-ban', [UserController::class, 'toggleUserBan']);
    Route::post('/delete-user', [UserController::class, 'deleteUser']);

    //vendor
    Route::post('/get-all-store-owners', [UserController::class, 'getAllVendors']);
    Route::post('/assign-store-to-vender', [UserController::class, 'assignStoreToVendor']);

    //customers
    Route::post('/get-all-customers', [UserController::class, 'getAllCustomers']);

    //delivery Partner
    Route::post('/get-all-delivery-partners', [UserController::class, 'getAllDeliveryPartners']);
    Route::post('/accept-or-reject-delivery-partner', [UserController::class, 'acceptRejectDeliveryPartner']);
    Route::post('/update-delivery-guy-store', [UserController::class, 'updateDeliveryGuyStore']);

    //store category
    Route::post('/get-all-store-categories', [StoreController::class, 'getAllStoreCategories']);
    Route::post('/create-store-category', [StoreController::class, 'createStoreCategory']);
    Route::post('/update-store-category', [StoreController::class, 'updateStoreCategory']);
    Route::post('/toggle-store-category', [StoreController::class, 'toggleStoreCategory']);
    Route::post('/delete-store-category', [StoreController::class, 'deleteStoreCategory']);
    Route::post('/get-all-city-category', [StoreController::class, 'getAllCityAndCategory']);

    //store
    Route::post('/get-all-stores', [StoreController::class, 'getAllStores']);
    Route::post('/get-all-pending-stores', [StoreController::class, 'getAllPendingStores']);
    Route::post('/get-single-store', [StoreController::class, 'getSingleStore']);
    Route::post('/create-store', [StoreController::class, 'createStore']);
    Route::post('/update-store', [StoreController::class, 'updateStore']);
    Route::post('/bulk-upload-store', [StoreController::class, 'storeBulkUpload']);

    Route::post('/update-store-schedule', [StoreController::class, 'updateStoreSchedule']);
    Route::post('/toggle-store', [StoreController::class, 'toggleStore']);
    Route::post('/delete-store', [StoreController::class, 'deleteStore']);
    Route::post('/accept-store', [StoreController::class, 'acceptStore']);
    Route::post('/get-store-item-categories', [StoreController::class, 'getStoreItemCategories']);
    Route::post('/get-store-items', [StoreController::class, 'getStoreItems']);
    Route::post('/get-store-addon-categories', [StoreController::class, 'getStoreAddonCategories']);
    Route::post('/get-store-item', [StoreController::class, 'getStoreItem']);
    Route::post('/update-store-item', [StoreController::class, 'UpdateStoreItem']);


    //Market category
    Route::post('/get-all-market-categories', [InventoryController::class, 'getAllMarketCategories']);
    Route::post('/create-market-category', [InventoryController::class, 'createMarketCategory']);
    Route::post('/update-market-category', [InventoryController::class, 'updateMarketCategory']);
    Route::post('/toggle-market-category', [InventoryController::class, 'toggleMarketCategory']);
    Route::post('/delete-market-category', [InventoryController::class, 'deleteMarketCategory']);
    Route::post('/get-single-market-category', [InventoryController::class, 'getSingleMarketCategory']);

    //item category
    Route::post('/get-all-item-categories', [InventoryController::class, 'getAllItemCategories']);
    Route::post('/create-item-category', [InventoryController::class, 'createItemCategory']);
    Route::post('/update-item-category', [InventoryController::class, 'updateItemCategory']);
    Route::post('/toggle-item-category', [InventoryController::class, 'toggleItemCategory']);
    Route::post('/delete-item-category', [InventoryController::class, 'deleteItemCategory']);

    //item
    Route::post('/get-all-items', [InventoryController::class, 'getAllItems']);
    Route::post('/get-item-schedule-data', [InventoryController::class, 'getItemScheduleData']);
    Route::post('/update-item-schedule-data', [InventoryController::class, 'updateItemScheduleData']);
    Route::post('/create-item', [InventoryController::class, 'createItem']);
    Route::post('/get-single-item', [InventoryController::class, 'getSingleItem']);
    Route::post('/update-item', [InventoryController::class, 'updateItem']);
    Route::post('/toggle-item', [InventoryController::class, 'toggleItem']);
    Route::post('/delete-item', [InventoryController::class, 'deleteItem']);

    //item group
    Route::post('/get-all-item-groups', [InventoryController::class, 'getAllItemGroups']);
    Route::post('/create-item-group', [InventoryController::class, 'createItemGroup']);
    Route::post('/update-item-group', [InventoryController::class, 'updateItemGroup']);
    Route::post('/toggle-item-group', [InventoryController::class, 'toggleItemGroup']);
    Route::post('/link-item-group-store', [InventoryController::class, 'linkItemGroupStore']);
    Route::post('/delete-item-group', [InventoryController::class, 'deleteItemGroup']);

    //addon category
    Route::post('/get-all-addon-categories', [InventoryController::class, 'getAllAddonCategories']);
    Route::post('/create-addon-category', [InventoryController::class, 'createAddonCategory']);
    Route::post('/update-addon-category', [InventoryController::class, 'updateAddonCategory']);
    Route::post('/toggle-addon-category', [InventoryController::class, 'toggleAddonCategory']);
    Route::post('/delete-addon-category', [InventoryController::class, 'deleteAddonCategory']);
    Route::post('/get-item-addon-category', [InventoryController::class, 'viewItemAddonCategory']);
    Route::post('/update-item-addon-category', [InventoryController::class, 'updateItemAddonCategory']);

    //addon
    Route::post('/get-all-addons', [InventoryController::class, 'getAllAddons']);
    Route::post('/create-addon', [InventoryController::class, 'createAddon']);
    Route::post('/update-addon', [InventoryController::class, 'updateAddon']);
    Route::post('/toggle-addon', [InventoryController::class, 'toggleAddon']);
    Route::post('/delete-addon', [InventoryController::class, 'deleteAddon']);

    //banner
    Route::post('/get-all-banners', [PromotionController::class, 'getAllBanners']);
    Route::post('/create-banner', [PromotionController::class, 'createBanner']);
    Route::post('/update-banner', [PromotionController::class, 'updateBanner']);
    Route::post('/toggle-banner', [PromotionController::class, 'toggleBanner']);
    Route::post('/delete-banner', [PromotionController::class, 'deleteBanner']);

    //coupon
    Route::post('/get-all-coupons', [PromotionController::class, 'getAllCoupons']);
    Route::post('/create-coupon', [PromotionController::class, 'createCoupon']);
    Route::post('/update-coupon', [PromotionController::class, 'updateCoupon']);
    Route::post('/toggle-coupon', [PromotionController::class, 'toggleCoupon']);
    Route::post('/delete-coupon', [PromotionController::class, 'deleteCoupon']);

    //slider
    Route::post('/get-all-sliders', [PromotionController::class, 'getAllSliders']);
    Route::post('/create-slider', [PromotionController::class, 'createSlider']);
    Route::post('/update-slider', [PromotionController::class, 'updateSlider']);
    Route::post('/toggle-slider', [PromotionController::class, 'toggleSlider']);
    Route::post('/delete-slider', [PromotionController::class, 'deleteSlider']);

    //scratch cards
    Route::post('/get-all-scratch-cards', [PromotionController::class, 'getAllScratchCards']);
    Route::post('/create-scratch-card', [PromotionController::class, 'createScratchCard']);
    Route::post('/update-scratch-card', [PromotionController::class, 'updateScratchCard']);
    Route::post('/delete-scratch-card', [PromotionController::class, 'deleteScratchCard']);

    //scratch card winners
    Route::post('/get-scratch-card-winners', [PromotionController::class, 'getScratchCardWinners']);

    //order
    Route::post('/get-all-orders', [OrderController::class, 'getAllOrders']);
    Route::post('/get-live-orders', [OrderController::class, 'getLiveOrders']);
    Route::post('/get-single-order', [OrderController::class, 'getSingleOrder']);
    Route::post('/get-order-delivery-partners', [OrderController::class, 'getOrderDeliveryPartners']);
    Route::post('/update-order-status', [OrderController::class, 'updateOrderStatus']);
    Route::post('/accept-order', [OrderController::class, 'acceptOrder']);
    Route::post('/assign-delivery-guy-to-order', [OrderController::class, 'assignDeliveryGuy']);
    Route::post('/cancel-order', [OrderController::class, 'cancelOrder']);

    //report
    Route::post('/get-pending-store-payouts', [ReportController::class, 'getStorePendingPayouts']);
    Route::post('/get-completed-store-payouts', [ReportController::class, 'getCompletedStorePayouts']);
    Route::post('/release-store-payout', [ReportController::class, 'releasePayouts']);
    Route::get('/export-store-payouts', [ReportController::class, 'exportStorePayouts']);
    Route::post('/get-all-order-report', [ReportController::class, 'getOrderReport']);
    Route::get('/export-sales-order-report', [ReportController::class, 'exportOrderReport']);

    //delivery collection
    Route::post('/delivery_collection', [ReportController::class, 'deliveryCollection'])->name('admin.deliveryCollection');
    Route::post('/delivery-collection/collect', [ReportController::class, 'collectDeliveryCollection'])->name('admin.collectDeliveryCollection');
    Route::post('/delivery_collection_log', [ReportController::class, 'deliveryCollectionLogs'])->name('admin.deliveryCollectionLogs');
    Route::post('/delivery-collection-log/single-user', [ReportController::class, 'deliveryCollectionLogsForSingleUser'])->name('admin.deliveryCollectionLogsForSingleUser');
    Route::get('/delivery_collection_log/export-log', [ReportController::class, 'exportDeliveryCollectionLog'])->name('admin.exportDeliveryCollectionLog');
    Route::get('/delivery-collection/export', [ReportController::class, 'exportDeliveryCollection'])->name('admin.exportDeliveryCollection');

    //messages
    Route::post('/get-all-messages', [CityController::class, 'getAllMessages'])->name('admin.getAllMessages');
    Route::post('/create-message', [CityController::class, 'createMessage'])->name('admin.createMessage');
    Route::post('/update-message', [CityController::class, 'updateMessage'])->name('admin.updateMessage');
    Route::post('/delete-message', [CityController::class, 'deleteMessage'])->name('admin.deleteMessage');
    Route::post('/toggle-message', [CityController::class, 'toggleMessage'])->name('admin.toggleMessage');

    // storeowner routes
    //dashboard
    Route::post('/get-all-store-owner-dashboard-data', [StoreOwnerController::class, 'storeOwnerDashboardData']);

    //store
    Route::post('/get-all-store-owner-stores', [StoreOwnerController::class, 'getStoreOwnerStores']);
    Route::post('/update-store-owner-store', [StoreOwnerController::class, 'updateStore']);
    Route::post('/update-store-owner-store-schedule', [StoreOwnerController::class, 'updateStoreSchedule']);
    Route::post('/toggle-store-owner-store', [StoreOwnerController::class, 'toggleStore']);

    //item category
    Route::post('/get-all-store-owner-item-categories', [StoreOwnerInventoryController::class, 'getStoreItemCategories']);
    Route::post('/create-store-owner-item-category', [StoreOwnerInventoryController::class, 'createItemCategory']);
    Route::post('/update-store-owner-item-category', [StoreOwnerInventoryController::class, 'updateItemCategory']);
    Route::post('/toggle-store-owner-item-category', [StoreOwnerInventoryController::class, 'toggleItemCategory']);
    Route::post('/delete-store-owner-item-category', [StoreOwnerInventoryController::class, 'deleteItemCategory']);

    //item
    Route::post('/get-all-store-owner-items', [StoreOwnerInventoryController::class, 'getStoreOwnerItems']);
    Route::post('/create-store-owner-item', [StoreOwnerInventoryController::class, 'createItem']);
    Route::post('/update-store-owner-item', [StoreOwnerInventoryController::class, 'updateItem']);
    Route::post('/toggle-store-owner-item', [StoreOwnerInventoryController::class, 'toggleItem']);
    Route::post('/delete-store-owner-item', [StoreOwnerInventoryController::class, 'deleteItem']);

    //addon category
    Route::post('/get-all-store-owner-addon-categories', [StoreOwnerInventoryController::class, 'getStoreOwnerAddonCategories']);
    Route::post('/create-store-owner-addon-category', [StoreOwnerInventoryController::class, 'createAddonCategory']);
    Route::post('/update-store-owner-addon-category', [StoreOwnerInventoryController::class, 'updateAddonCategory']);
    Route::post('/toggle-store-owner-addon-category', [StoreOwnerInventoryController::class, 'toggleAddonCategory']);
    Route::post('/delete-store-owner-addon-category', [StoreOwnerInventoryController::class, 'deleteAddonCategory']);

    //addon
    Route::post('/get-all-store-owner-addons', [StoreOwnerInventoryController::class, 'getStoreOwnerAddons']);
    Route::post('/create-store-owner-addon', [StoreOwnerInventoryController::class, 'createAddon']);
    Route::post('/update-store-owner-addon', [StoreOwnerInventoryController::class, 'updateAddon']);
    Route::post('/toggle-store-owner-addon', [StoreOwnerInventoryController::class, 'toggleAddon']);
    Route::post('/delete-store-owner-addon', [StoreOwnerInventoryController::class, 'deleteAddon']);

    //order
    Route::post('/get-all-store-owner-order', [StoreOwnerOrderController::class, 'getStoreOrders']);
    Route::post('/get-single-store-owner-order', [StoreOwnerOrderController::class, 'getSingleOrder']);
    Route::post('/accept-store-owner-order', [StoreOwnerOrderController::class, 'acceptOrder']);
    Route::post('/cancel-store-owner-order', [StoreOwnerOrderController::class, 'cancelOrder']);


    //store pending payouts
    Route::post('/get-pending-store-owner-store-payouts', [StoreOwnerOrderController::class, 'getStoreOwnerPendingPayouts']);
    Route::post('/export-store-owner-store-payouts', [StoreOwnerOrderController::class, 'exportStorePendingPayouts']);

    // ---------------------------------------------------------------------------------------
    //==================================== Customer App ======================================
    // ---------------------------------------------------------------------------------------

    //Address
    Route::post('/get-all-address', [CustomerApiController::class, 'getAllAddress']);
    Route::post('/change-default-address', [CustomerApiController::class, 'changeDefaultAddress']);

    Route::post('/get-wallet', [CustomerApiController::class, 'getWallet']);
    Route::post('/send-order-review', [CustomerApiController::class, 'sendOrderReview']);
    Route::post('/get-reorder-items', [CustomerApiController::class, 'getReOrderItems']);

    //user
    Route::post('/get-user-profile', [CustomerApiController::class, 'getUserProfile']);
    Route::post('/update-user-data', [CustomerApiController::class, 'updateUserData']);
    //faq


    // Favorite

    Route::post('/add-favorite', [CustomerApiController::class, 'addToFavorite']);
    Route::post('/remove-favorite', [CustomerApiController::class, 'removeFromFavorite']);
    Route::post('/get-all-favorite-items', [CustomerApiController::class, 'getAllFavoriteItems']);


    //Order place
    Route::post('/apply-coupon', [CartApiController::class, 'applyCoupon']);
    Route::post('/get-all-cart-coupons', [CartApiController::class, 'getAllCartCoupons']);
    Route::post('/place-order', [OrderApiController::class, 'placeOrder'])
        ->middleware('throttle:1,0.083'); // Throttle 5 sec, 0.083 is 5 seconds, represented in minutes (5 seconds ÷ 60 = 0.083).

    Route::post('/get-track-order', [OrderApiController::class, 'getTrackOrder']);

    //Scratch cards
    Route::post('/get-user-scratch-cards', [CustomerApiController::class, 'getUserSCratchCards']);
    Route::post('/scratch-card-collected', [CustomerApiController::class, 'scratchCardCollected']);


    // notification
    Route::post('/notification-check', [CustomerApiController::class, 'NotificationCheck']);
});
Route::post('/get-all-home-datas', [CustomerApiController::class, 'getAllHomeDatas']);
Route::post('/get-one-store', [CustomerApiController::class, 'getSingleStore']);


Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('/get-every-orders', [CustomerApiController::class, 'getAllOrders']);
    Route::post('/get-review-single-order', [CustomerApiController::class, 'getReviewSingleOrder']);
    Route::post('/update-feedback', [CustomerApiController::class, 'updateFeedback']);
    Route::post('/cancel-new-order', [CustomerApiController::class, 'cancelNewOrder']);

    Route::post('/get-all-search-datas', [CustomerApiController::class, 'getAllSearchDatas']);


    Route::post('/toggle-favorite', [CustomerApiController::class, 'toggleFavorite']);
});
// ---------------------------------------------------------------------------------------
//==================================== Customer App ======================================
// ---------------------------------------------------------------------------------------
Route::post('/get-user-data', [CustomerApiController::class, 'getUserData']);

Route::post('/get-faq-data', [CustomerApiController::class, 'getFaqData']);




Route::post('/send-otp', [AdminController::class, 'sendOtp']);
Route::post('/verify-otp', [AdminController::class, 'verifyOtp']);
Route::post('/user-register', [AdminController::class, 'userRegister']);
Route::post('/update-user-data', [CustomerApiController::class, 'updateUserData']);
Route::post('/delete-user-data', [CustomerApiController::class, 'deleteUserData']);

// Location
Route::post('/get-address-to-coordinates', [CustomerApiController::class, 'coordinatesToAddress']);
Route::post('/add-new-address', [CustomerApiController::class, 'addNewAddress']);

//notification

Route::post('/get-all-notification', [CustomerApiController::class, 'getAllNotification']);
Route::post('/read-notification', [CustomerApiController::class,  'readNotification']);


//cart
Route::post('/get-cart-restaurant', [CartApiController::class, 'getCartRestaurant']);
Route::post('/calcualte-restaurant-max-distance', [CartApiController::class, 'calculateRestaurantMaxDistance']);
Route::post('/calcualte-restaurant-charge-tax', [CartApiController::class, 'calculateRestaurantChargeTax']);


// ---------------------------------------------------------------------------------------
//==================================== Vendor App ======================================
// ---------------------------------------------------------------------------------------

Route::post('/login-vendor', [VendorApiController::class, 'loginVendor']);
Route::post('/register-vendor', [VendorApiController::class, 'RegisterVendorapp']);

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('/get-vendor-home-data', [VendorApiController::class, 'getVendorHomeData']);
    Route::post('/get-new-vendor-orders', [VendorApiController::class, 'getNewOrder']);
    Route::post('/get-ongoing-vendor-orders', [VendorApiController::class, 'getOngoingOrder']);
    Route::post('/get-completed-vendor-orders', [VendorApiController::class, 'getCompletedOrder']);
    Route::post('/get-cancelled-vendor-orders', [VendorApiController::class, 'getCancelledOrder']);
    Route::post('/get-vendor-single-order', [VendorApiController::class, 'getSingleVendorOrder']);
    Route::post('/accept-cancel-order', [VendorApiController::class, 'acceptCancelOrder']);
    Route::post('/add-extra-time', [VendorApiController::class, 'addExtraTime']);
    Route::post('/ready-to-pickup-order', [VendorApiController::class, 'readyToPickupOrder']);
    Route::post('/picked-up-order', [VendorApiController::class, 'changeOrderStatusToPickedUp']);
    Route::post('/delivered-order', [VendorApiController::class, 'changeOrderStatusToDelivered']);
    Route::post('/get-all-pending-orders', [VendorApiController::class, 'getAllPendingOrders']);
    Route::post('/get-all-orders', [VendorApiController::class, 'getAllOrders']);
    Route::post('/get-vendor-user-profile', [VendorApiController::class, 'getVendorUserProfile']);

    Route::post('/get-all-item-categories-vendor', [VendorApiController::class, 'getAllItemCategories']);
    Route::post('/get-all-addon-categories-vendor', [VendorApiController::class, 'getAllAddonCategories']);

    Route::post('/add-new-item-vendor', [VendorApiController::class, 'addNewItem']);
    Route::post('/add-item-category-vendor', [VendorApiController::class, 'addNewCategory']);

    Route::post('/add-addon-category-vendor', [VendorApiController::class, 'addNewAddonCategory']);
    Route::post('/add-addon-vendor', [VendorApiController::class, 'addNewAddon']);
    Route::post('/add-out-of-Stock-Time', [VendorApiController::class, 'addOutOfStockTime']);
    Route::post('/add-out-of-Stock-Time-for-items', [VendorApiController::class, 'addOutOfStockTimeForItems']);
    Route::post('/add-out-of-stock-time-for-addon-category', [VendorApiController::class, 'addOutOfStockTimeForAddonCategory']);
    Route::post('/add-out-of-stock-time-for-add-addon', [VendorApiController::class, 'addOutOfStockTimeForAddAddon']);


    Route::post('/add-addon-category-vendor', [VendorApiController::class, 'addNewAddonCategory']);
    Route::post('/get-completed-orders-with-date', [VendorApiController::class, 'getCompletedOrderWithDate']);
    Route::post('/get-cancelled-orders-with-date', [VendorApiController::class, 'getCancelledOrderWithDate']);
    Route::get('/export-completed-orders', [VendorApiController::class, 'exportCompletedOrders']);

    Route::post('/vendor-store-update', [VendorApiController::class, 'vendorStoreUpdate']);
    Route::post('/vendor-get-single-store', [VendorApiController::class, 'getSingleStore']);
    //vender create
    Route::post('/vendor-get-all-inventory-items', [VendorApiController::class, 'getAllInventoryItems']);
    Route::post('/vendor-get-category', [VendorApiController::class, 'getAllCategory']);
    Route::post('/vendor-get-item', [VendorApiController::class, 'getAllItems']);
    Route::post('/vendor-get-addoncategory', [VendorApiController::class, 'getAllAddoncategory']);
    Route::post('/vendor-get-addon', [VendorApiController::class, 'getAllAddon']);
    //toggles
    Route::post('/vendor-app-toggle-addon', [VendorApiController::class, 'toggleAddon']);
    Route::post('/vendor-app-toggle-item', [VendorApiController::class, 'toggleItem']);
    Route::post('/vendor-app-toggle-category', [VendorApiController::class, 'toggleCategory']);
    Route::post('/vendor-app-toggle-addon-category', [VendorApiController::class, 'toggleAddonCategory']);
    //orderCreation

    Route::post('/vendor-get-all-item', [VendorApiController::class, 'getAllStoreItems']);
    // extra time
    Route::post('/get-extra-time', [VendorApiController::class, 'getExtraTime']);


    Route::post('/vendor-place-order', [VendorApiController::class, 'placeOrder']);
    Route::post('/toggle-vendor', [VendorApiController::class, 'toggleVendor']);
});
Route::post('/get-vendor-address-to-coordinates', [VendorApiController::class, 'vendorcoordinatesToAddress']);
Route::post('/vendor-add-new-address', [VendorApiController::class, 'addNewAddress']);

Route::get('/vendor-print-order/{order_id}', [VendorApiController::class, 'printOrderBill']);
Route::get('/vendor-print-order/instructions/{order_id}', [VendorApiController::class, 'printOrderBillInstructions']);
Route::get('/vendor-kot-print-order/instructions/{order_id}', [VendorApiController::class, 'printOrderKOTBillInstructions']);
Route::get('/vendor-print-order/image/{order_id}', [VendorApiController::class, 'printOrderBillImage']);
Route::get('/vendor-print-order/view/{order_id}', [VendorApiController::class, 'printOrderBillView']);


Route::get('/vendor-print-kot-order/{order_id}', [VendorApiController::class, 'printKOTOrderBill']);


Route::post('/vendor-get-cart-restaurant', [VendorApiController::class, 'getCartRestaurant']);
Route::post('/vendor-calcualte-restaurant-max-distance', [VendorApiController::class, 'calculateRestaurantMaxDistance']);
Route::post('/vendor-calcualte-restaurant-charge-tax', [VendorApiController::class, 'calculateRestaurantChargeTax']);

Route::post('/vendor-get-userdata', [VendorApiController::class, 'getUserdata']);
Route::post('/vendor-order-user-reg', [VendorApiController::class, 'vendorOrderUserRegister']);



Route::get('/test-print', function () {
    $a = array();

    // Sending text entry
    $obj1 = new stdClass();
    $obj1->type = 0; // text
    $obj1->content = 'My Title'; // any string	
    $obj1->bold = 1; // 0 if no, 1 if yes
    $obj1->align = 2; // 0 if left, 1 if center, 2 if right
    $obj1->format = 3; // 0 if normal, 1 if double Height, 2 if double Height + Width, 3 if double Width, 4 if small
    array_push($a, $obj1);

    // Sending image entry
    $obj2 = new stdClass();
    $obj2->type = 1; // image
    $obj2->path = 'https://i.ibb.co/yNbVD6P/Screenshot-2024-09-26-at-2-56-00-PM.png'; // complete filepath on your web server
    $obj2->align = 2; // 0 if left, 1 if center, 2 if right
    array_push($a, $obj2);

    // Sending barcode entry
    $obj3 = new stdClass();
    $obj3->type = 2; // barcode
    $obj3->value = '1234567890123'; // valid barcode value
    $obj3->height = 50; // valid barcode height 10 to 80
    $obj3->align = 0; // 0 if left, 1 if center, 2 if right
    array_push($a, $obj3);

    // Sending QR entry
    $obj4 = new stdClass();
    $obj4->type = 3; // QR code
    $obj4->value = 'sample qr text'; // valid QR code value
    $obj4->size = 40; // valid QR code size in mm (Min 40)
    $obj4->align = 2; // 0 if left, 1 if center, 2 if right
    array_push($a, $obj4);

    // Sending empty line
    $obj6 = new stdClass();
    $obj6->type = 0; // text
    $obj6->content = ' '; // empty line
    $obj6->bold = 0;
    $obj6->align = 0;
    array_push($a, $obj6);

    // Sending multi-lines text
    $obj7 = new stdClass();
    $obj7->type = 0; // text
    $obj7->content = 'This text has<br />two lines'; // multiple lines text
    $obj7->bold = 0;
    $obj7->align = 0;
    array_push($a, $obj7);

    return json_encode($a, JSON_FORCE_OBJECT);
});
