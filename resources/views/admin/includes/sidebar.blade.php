<div class="nk-sidebar nk-sidebar-fixed is-light " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="flex-grow-1 nk-sidebar-brand d-flex align-items-center mt-2">
            <img class="mx-1" src="{{ asset('assets/images/logo.jpg') }}" style="width:12rem;object-fit:contain"
                alt="logo" />
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 22px" fill="currentColor" class="bi bi-arrow-left"
                    viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                </svg>
            </a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex"
                data-target="sidebarMenu">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 22px" fill="currentColor" class="bi bi-list"
                    viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                </svg>
            </a>
        </div>
    </div>
    @role('Super Admin')
        <div class="nk-sidebar-element">
            <div class="nk-sidebar-content">
                <div class="nk-sidebar-menu" data-simplebar>
                    <ul class="nk-menu">
                        <li class="nk-menu-item">
                            <a href="{{ route('admin.dashboard') }}" class="nk-menu-link">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-dashboard-fill"></em>
                                </span>
                                <span class="nk-menu-text">Dashboard</span>
                            </a>
                        </li>
                       

                        <li class="nk-menu-item">
                            <a href="{{ route('admin.viewLiveOrders', 'All') }}" class="nk-menu-link">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-cart-fill"></em>
                                </span>
                                <span class="nk-menu-text">Live Orders</span>
                            </a>
                        </li>

                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-home-fill"></em>
                                </span>
                                <span class="nk-menu-text">Manage Restaurants</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewRestaurant') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Restaurants</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-file-text"></em>
                                </span>
                                <span class="nk-menu-text">Manage Inventory</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewItemCategories') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Item Categories</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewItemGroups') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Item Group</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewAddonCategories') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Addon Categories</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewAddons') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Addons</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewItems') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Items</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-users-fill"></em>
                                </span>
                                <span class="nk-menu-text">Manage Users</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewUsers') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">All Users</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewAllCustomers') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Customers</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewAllRestaurantOwners') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Restaurant Owners</span>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-files-fill"></em>
                                </span>
                                <span class="nk-menu-text">Manage Promotions</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewAllBanners') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Banners</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewAllCoupons') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Coupons</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-bar-chart"></em>
                                </span>
                                <span class="nk-menu-text">Manage Reports</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewDatewiseReport') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Datewise Report</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewRestaurantPayoutReport') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Restaurant Payout Report</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-opt"></em>
                                </span>
                                <span class="nk-menu-text">Others</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewSettings') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Settings</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.reviews') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Reviews</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewCities') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">City</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.viewPermission') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Permission</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endrole

    @role('Restaurant Owner')
        <div class="nk-sidebar-element">
            <div class="nk-sidebar-content">
                <div class="nk-sidebar-menu" data-simplebar>
                    <ul class="nk-menu">
                        <li class="nk-menu-item">
                            <a href="{{ route('restaurantOwner.dashboard') }}" class="nk-menu-link">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-dashboard-fill"></em>
                                </span>
                                <span class="nk-menu-text">Dashboard</span>
                            </a>
                        </li>

                         <li class="nk-menu-item">
                            <a href="{{ route('restaurantOwner.usersWithOrders') }}" class="nk-menu-link">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-users-fill"></em>
                                </span>
                                <span class="nk-menu-text">Users</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('restaurantOwner.viewLiveOrders', 'All') }}" class="nk-menu-link">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-cart-fill"></em>
                                </span>
                                <span class="nk-menu-text">Live Orders</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('restaurantOwner.viewRestaurant') }}" class="nk-menu-link">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-home-fill"></em>
                                </span>
                                <span class="nk-menu-text">Restaurant</span>
                            </a>
                        </li>


                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-file-text"></em>
                                </span>
                                <span class="nk-menu-text">Manage Inventory</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('restaurantOwner.viewItemCategories') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Item Categories</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('restaurantOwner.viewAddonCategories') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Addon Categories</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('restaurantOwner.viewAddons') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Addons</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('restaurantOwner.viewItems') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Items</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-bar-chart"></em>
                                </span>
                                <span class="nk-menu-text">Manage Reports</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('restaurantOwner.viewDatewiseReport') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Datewise Report</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ route('restaurantOwner.viewRestaurantPayoutReport') }}"
                                        class="nk-menu-link">
                                        <span class="nk-menu-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22px" fill="currentColor"
                                                class="bi bi-dot" viewBox="0 0 16 16">
                                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                            </svg>
                                        </span>
                                        <span class="nk-menu-text">Restaurant Payout Report</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                         <li class="nk-menu-item">
                            <a href="{{ route('restaurantOwner.detailedReview') }}" class="nk-menu-link">
                                <span class="nk-menu-icon">
                                    <em class="icon ni ni-star-fill"></em>
                                </span>
                                <span class="nk-menu-text">Reviews</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endrole
</div>
