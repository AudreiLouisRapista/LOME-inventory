<style>
    /* 1. MAIN CONTAINER: Full height flexbox */
    .main-sidebar {
        display: flex !important;
        flex-direction: column !important;
        height: 100vh !important;
        background: rgba(0, 0, 0, 0.96) !important;
        overflow: hidden !important;

        /* Smooth transition when resizing */
    }



    /* 3. ZONE 2: Locked Logout Container */
    .logout-container {
        flex-shrink: 0 !important;
        /* Prevents button from being squashed */
        width: 100%;
        padding: 15px 10px;
        background: rgba(0, 0, 0, 0.1);
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
        color: #fff !important;
        background: #ff4757;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(255, 71, 87, 0.3);
        transition: 0.3s ease;
    }

    /* --- Collapsed State Fixes --- */
    .sidebar-collapse .logout-text {
        display: none !important;
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

    /* Change sidebar background to a gradient similar to the active color (#6366F1) */


    /* Ensure the sidebar inner elements blend with the gradient */
    .main-sidebar .sidebar {
        background: transparent !important;
        /* Make inner sidebar transparent to show gradient */
    }



    h5 {
        text-align: center;
        margin-bottom: 50px;
        transition: opacity 0.3s, visibility 0.3s;
        color: #fff;
        /* Suggested: White text for role on gradient background */
    }

    .sidebar-collapse h5 {
        opacity: 0;
        /* Makes it invisible */
        visibility: hidden;
        /* Ensures it doesn’t take up space */
        margin-bottom: 50px;
        /* Keep the gap even when hidden */

    }

    /* On hover over the entire sidebar, show the role (h5) */
    .sidebar-collapse .main-sidebar:hover h5 {
        opacity: 1;
        /* Makes it visible on sidebar hover */
        visibility: visible;
        /* Show it */

    }

    .nav-sidebar .nav-item .nav-link:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        /* Subtle hover effect */
        color: #fff !important;
        transform: translateX(5px);
        /* Slight slide to the right */
        transition: all 0.3s ease;
    }
</style>

<aside id="mainSidebar" class="main-sidebar sidebar-light-primary elevation-4">

    <div class="sidebar-brand d-flex align-items-center justify-content-center py-3">
        <img class="rounded-circle shadow-sm"
            src="{{ session('profile') ? asset(session('profile')) . '?' . time() : asset('dist/img/avatar.png') }}"
            style="width:50px; height:50px; object-fit:cover; border:5px solid white;"
            onerror="this.onerror=null; this.src='{{ asset('dist/img/avatar.png') }}';">
    </div>
    <h5>
        {{ session('user_role') }}

    </h5>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false" style="padding-right:15px; gap:10px;">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap: 10px; color:{{ Route::is('admin.dashboard') ? '#333' : '#fff' }}; background:{{ Route::is('admin.dashboard') ? '#fff' : 'transparent' }};">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>

                {{-- Admin Profile --}}
                <li class="nav-item">
                    <a href="{{ route('admin_profile') }}"
                        class="nav-link {{ Route::is('admin_profile') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px;  color:{{ Route::is('admin_profile') ? '#333' : '#fff' }}; background:{{ Route::is('admin_profile') ? '#fff' : 'transparent' }};">
                        <i class="fas fa-user-cog"></i> Admin Profile
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('view_inventory') }}"
                        class="nav-link {{ Route::is('view_inventory') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px;  color:{{ Route::is('view_inventory') ? '#333' : '#fff' }}; background:{{ Route::is('view_inventory') ? '#fff' : 'transparent' }};">
                        <i class="bi bi-box"></i> Inventory
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('view_products') }}"class="nav-link {{ Route::is('view_products') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px;  color:{{ Route::is('view_products') ? '#333' : '#fff' }}; background:{{ Route::is('view_products') ? '#fff' : 'transparent' }};">
                        <i class="bi bi-boxes"></i> Products
                    </a>
                </li>



                {{-- Delivery --}}
                <li class="nav-item">
                    <a href="{{ route('view_section') }}"
                        class="nav-link {{ Route::is('view_section') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; color:{{ Route::is('view_section') ? '#333' : '#fff' }}; background:{{ Route::is('view_section') ? '#fff' : 'transparent' }};">
                        <i class="bi bi-truck"></i> Delivery
                    </a>
                </li>

                {{-- Reports --}}
                <li class="nav-item">
                    <a href="{{ route('view_subject') }}"
                        class="nav-link {{ Route::is('view_subject') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px;  color:{{ Route::is('view_subject') ? '#333' : '#fff' }}; background:{{ Route::is('view_subject') ? '#fff' : 'transparent' }};">
                        <i class="bi bi-clipboard-check"></i> Reports
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
