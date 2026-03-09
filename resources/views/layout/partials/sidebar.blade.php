<style>
    /* 1. MAIN CONTAINER: Full height flexbox */
    .main-sidebar {
        display: flex !important;
        flex-direction: column !important;
        height: 100vh !important;
        background: solid rgba(255, 255, 255, 0.96) !important;
        overflow: hidden !important;


        /* Smooth transition when resizing */
    }



    /* 3. ZONE 2: Locked Logout Container */
    .logout-container {
        flex-shrink: 0 !important;
        /* Prevents button from being squashed */
        width: 100%;
        padding: 15px 10px;
        background: rgba(255, 255, 255, 0.1);
        /* Subtle visual boundary line */
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 99;
    }

    .logout-btn-styled {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 90%;
        margin: 0 auto;
        padding: 12px;
        color: #fe0101 !important;
        /* background: #ff4757; */
        border: 1px solid #ff4757;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        /* box-shadow: 0 4px 12px rgba(255, 142, 151, 0.3); */
        transition: 0.3s ease;
    }

    .logout-btn-styled:hover {
        background: #ff4757;
        color: #fff !important;
        border-color: #ff4757;
        box-shadow: 0 6px 16px rgba(255, 142, 151, 0.5);
    }

    .user-panel {
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        padding-left: 15px !important;
        transition: all 0.3s ease;
    }

    /* The Role Text (e.g., Alexander Pierce style) */
    .user-role-link {
        font-size: 16px !important;
        font-weight: 600 !important;
        color: #2c3e50 !important;
        text-decoration: none !important;
        white-space: nowrap;
        transition: color 0.2s;
    }

    .user-role-link:hover {
        color: #ff5757 !important;
        /* Matches your theme red */
    }




    /* Sidebar Active State Shadow */
    .nav-sidebar .nav-link.active {
        box-shadow: 0 4px 10px rgba(255, 87, 87, 0.3);
    }





    /* --- Collapsed State Fixes --- */
    .sidebar-collapse .logout-text {
        display: none !important;
    }

    .sidebar-collapse .user-panel .info,
    .sidebar-collapse .user-panel .small {
        display: none !important;
    }

    .sidebar-collapse .user-panel {
        justify-content: center !important;
        padding-left: 0 !important;
    }

    /* Logic for sidebar hover while collapsed */
    .sidebar-collapse .main-sidebar:hover .user-panel .info {
        display: block !important;
        margin-left: 10px;
    }

    .sidebar-collapse .logout-btn-styled {
        width: 45px;
        height: 45px;
        padding: 0;
        border-radius: 50%;
        /* Circle icon when collapsed */
    }

    /* Restore on Hover while Collapsed */
    .sidebar-collapse .main-sidebar:hover .logout-btn-styled {
        width: 90%;
        border-radius: 12px;
        padding-left: 5px;
        justify-content: center !important;
    }

    .sidebar-collapse .main-sidebar:hover .logout-text {
        display: inline !important;
    }

    /* Hide the title text when collapsed */
    /* Hide brand text when collapsed */
    .sidebar-collapse .brand-text {
        display: none !important;

    }

    /* Center logo when collapsed - Ensure no vertical shift */
    .sidebar-collapse .sidebar-brand {
        justify-content: center !important;

        /* Add fixed height to prevent shifting */
        /* height: auto; Or match expanded height if needed */
    }

    /* Hide menu text when collapsed - Keep icons left-alignedx` and prevent vertical movement */
    .sidebar-collapse .nav-sidebar .nav-link {
        justify-content: flex-start;
        /* Keeps icons aligned to the left */
        /* Ensure consistent height/padding to prevent "moving up" */
        min-height: 40px;
        /* Match or exceed expanded height to avoid collapse */
        padding: 10px 15px;
        /* Keep padding consistent */
    }

    .sidebar-collapse .nav-sidebar .nav-link i {
        margin-right: 0 !important;
        /* Remove gap */
        font-size: 18px !important;
        /* KEEP ICON VISIBLE */
    }

    /* Hide the text of menu items in collapsed state */
    .sidebar-collapse .nav-sidebar .nav-link {
        font-size: 0 !important;

    }

    /* On hover over the entire sidebar, show all menu item texts */
    .sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link {
        font-size: 16px !important;
        /* Restore text size on sidebar hover */
    }

    .sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link i {
        margin-right: 10px !important;
        /* Restore gap on sidebar hover */
    }

    .sidebar-collapse .sidebar>div:last-child {
        position: absolute;
        bottom: 20px;
        left: 0;
        right: 0;
        /* margin-top: 0 !important; Remove the 250px margin to stick to bottom */
        padding: 20px;
        /* Keep padding for spacing */
    }



    /* Reduce the sidebar shadow */
    .main-sidebar.sidebar-light-primary.elevation-4 {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        /* Smaller, subtler shadow */
    }


    /* Ensure the sidebar inner elements blend with the gradient */
    .main-sidebar .sidebar {
        background: transparent !important;
        /* Make inner sidebar transparent to show gradient */
    }



    .user-name {

        margin-top: 10%;
        transition: opacity 0.3s, visibility 0.3s;
        color: #000000;
        /* Suggested: White text for role on gradient background */
    }




    /* On hover over the entire sidebar, show the role (h5) */
    .sidebar-collapse .main-sidebar:hover h5 {
        opacity: 1;
        /* Makes it visible on sidebar hover */
        visibility: visible;
        /* Show it */

    }

    .nav-sidebar .nav-item:hover {
        background: rgba(104, 104, 104, 0.2) !important;
        /* Subtle hover effect */
        color: #6b6b6b !important;
        transform: translateY(5px);
        /* Slight slide to the right */
        transition: all 0.3s ease;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<aside id="mainSidebar" class="main-sidebar sidebar-light-primary elevation-4">

    <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image">
            <img src="{{ asset('dist/img/lome-shoMartLogo.jpg') }}" class="logo" alt="User Image"
                style="width: 45px; height: 45px; object-fit: cover; ">
        </div>
        <div class="info ms-3">
            <span class="d-block text-muted small fw-bold text-uppercase"
                style="letter-spacing: 1px; font-size: 10px;">Access Level</span>
            <a href="#" class="d-block user-role-link">{{ session('user_role') }}</a>
        </div>
    </div>


    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false" style="padding-right:15px; gap:10px;">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap: 10px; color:{{ Route::is('admin.dashboard') ? '#fff' : '#333' }}; background:{{ Route::is('admin.dashboard') ? '#ff5757' : 'transparent' }};">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>

                {{-- Admin Profile --}}
                <li class="nav-item">
                    <a href="{{ route('admin_profile') }}"
                        class="nav-link {{ Route::is('admin_profile') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px;  color:{{ Route::is('admin_profile') ? '#fff' : '#333' }}; background:{{ Route::is('admin_profile') ? '#ff5757' : 'transparent' }};">
                        <i class="fas fa-user-cog"></i> Admin Profile
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('view_inventory') }}"
                        class="nav-link {{ Route::is('view_inventory') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px;  color:{{ Route::is('view_inventory') ? '#fff' : '#333' }}; background:{{ Route::is('view_inventory') ? '#ff5757' : 'transparent' }};">
                        <i class="bi bi-box"></i> Inventory
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('view_products') }}"class="nav-link {{ Route::is('view_products') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px;  color:{{ Route::is('view_products') ? '#fff' : '#333' }}; background:{{ Route::is('view_products') ? '#ff5757' : 'transparent' }};">
                        <i class="bi bi-boxes"></i> Products
                    </a>
                </li>



                @php
                    // 1. Check if any sub-item is active
                    $report_active = Route::is('product_report') || Route::is('inventory_report');

                    // Consistent colors
                    $primary_red = '#ff5757'; // Your $primary_indigo variable actually held a red hex
                    $text_dark = '#333';
                    $text_white = '#fff';
                @endphp

                <li class="nav-item">
                    {{-- Parent Link --}}
                    <a href="#reportsCollapse" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ $report_active ? 'true' : 'false' }}" aria-controls="reportsCollapse"
                        class="nav-link {{ $report_active ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; padding: 10px;
              color:{{ $report_active ? $text_white : $text_dark }}; 
              background:{{ $report_active ? $primary_red : 'transparent' }}; 
              border-radius: 4px; transition: all 0.3s ease; text-decoration: none;">

                        <i class="bi bi-clipboard-data"></i>
                        <span style="flex-grow: 1;">Reports</span>
                        <i class="fas fa-caret-down transition-icon {{ $report_active ? 'rotate-icon' : '' }}"></i>
                    </a>

                    {{-- Dropdown Content --}}
                    <div id="reportsCollapse" class="collapse {{ $report_active ? 'show' : '' }}"
                        style="margin-left: 10px; border-left: 2px solid {{ $primary_red }}50; margin-top: 5px;">

                        <ul class="nav flex-column" style="gap: 5px; padding-left: 10px;">
                            <li class="nav-item">
                                {{-- Fixed the Route::is check here to match your 'view_section' route --}}
                                <a href="{{ route('inventory_report') }}" class="nav-link"
                                    style="display:block; padding:8px 10px; font-size: 0.9rem; border-radius:4px; text-decoration: none;
                          color:{{ Route::is('inventory_report') ? $text_white : $text_dark }}; 
                          background:{{ Route::is('inventory_report') ? $primary_red : 'transparent' }};">
                                    Inventory Report
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('product_report') }}" class="nav-link"
                                    style="display:block; padding:8px 10px; font-size: 0.9rem; border-radius:4px; text-decoration: none;
                          color:{{ Route::is('product_report') ? $text_white : $text_dark }}; 
                          background:{{ Route::is('product_report') ? $primary_red : 'transparent' }};">
                                    Product Report
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>


                {{-- POS history --}}
                <li class="nav-item">
                    <a href="{{ route('pos_history') }}"
                        class="nav-link {{ Route::is('pos_history') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; color:{{ Route::is('pos_history') ? '#fff' : '#333' }}; background:{{ Route::is('pos_history') ? '#ff5757' : 'transparent' }};">
                        <i class="bi bi-clock-history"></i> POS History
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('purchase_invoice') }}"
                        class="nav-link {{ Route::is('purchase_invoice') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; color:{{ Route::is('purchase_invoice') ? '#fff' : '#333' }}; background:{{ Route::is('purchase_invoice') ? '#ff5757' : 'transparent' }};">
                        <i class="bi bi-bank"></i></i> Supplier Payment
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('add_invoice') }}"
                        class="nav-link {{ Route::is('add_invoice') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; color:{{ Route::is('add_invoice') ? '#fff' : '#333' }}; background:{{ Route::is('add_invoice') ? '#ff5757' : 'transparent' }};">
                        <i class="nav-icon fas fa-file-signature"></i> Invoice Encoder
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('stockMovement') }}"
                        class="nav-link {{ Route::is('stockMovement') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; color:{{ Route::is('stockMovement') ? '#fff' : '#333' }}; background:{{ Route::is('stockMovement') ? '#ff5757' : 'transparent' }};">
                        <i class="fas fa-exchange-alt"></i> Stock Ledger
                    </a>
                </li>

            </ul>
        </nav>
    </div>
    <div class="logout-container">
        <a href="{{ route('logout') }}" class="logout-btn-styled">
            <i class="bi bi-box-arrow-left"></i>
            <span class="logout-text">Logout</span>
        </a>
    </div>

</aside>
