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

    /* .sidebar-collapse h5 {
        opacity: 0;
        visibility: hidden;
        margin-bottom: 50px;

    } */


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

<aside id="mainSidebar" class="main-sidebar sidebar-light-primary elevation-4">

    <div class="sidebar-brand d-flex align-items-center justify-content-center py-3">
        <img class="rounded-circle shadow-sm" src="{{ asset('dist/img/LOME_LOGO.png') }}" alt="LOME logo" height="60"
            width="60" style="object-fit:cover;">
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



                {{-- Delivery --}}
                <li class="nav-item">
                    <a href="{{ route('view_section') }}"
                        class="nav-link {{ Route::is('view_section') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; color:{{ Route::is('view_section') ? '#fff' : '#333' }}; background:{{ Route::is('view_section') ? '#ff5757' : 'transparent' }};">
                        <i class="bi bi-truck"></i> Supply
                    </a>
                </li>

                @php
                    // Check if any sub-item is active
                    $teacher_parent_active = Route::is('view_section') || Route::is('view_section');

                    // Consistent colors matching your other items
                    $primary_indigo = '#ff5757';
                    $text_dark = '#333';
                    $text_white = '#fff';
                @endphp

                <li class="nav-item">
                    {{-- Parent Link --}}
                    <a href="#teachersCollapse" data-bs-toggle="collapse" role="button"
                        class="nav-link {{ $teacher_parent_active ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; 
               color:{{ $teacher_parent_active ? $text_white : $text_dark }}; 
               background:{{ $teacher_parent_active ? $primary_indigo : 'transparent' }}; 
               border-radius: 4px; transition: all 0.3s ease;">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Reports</span>
                        <i class="fas fa-caret-down ms-auto"></i>
                    </a>

                    {{-- Dropdown Content --}}
                    <div id="teachersCollapse" class="collapse {{ $teacher_parent_active ? 'show' : '' }}"
                        style="margin-left: 10px; border-left: 2px solid {{ $primary_indigo }}50; margin-top: 5px;">
                        <ul class="nav flex-column" style="gap: 5px; padding-left: 10px;">
                            <li class="nav-item">
                                <a href="{{ route('view_section') }}" class="nav-link"
                                    style="display:block; padding:8px 10px; font-size: 0.9rem; border-radius:4px;
                    color:{{ Route::is('view_section') ? $text_white : $text_dark }}; 
                    background:{{ Route::is('view_section') ? $primary_indigo : 'transparent' }};">
                                    Inventory Report
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('view_section') }}" class="nav-link"
                                    style="display:block; padding:8px 10px; font-size: 0.9rem; border-radius:4px;
                    color:{{ Route::is('teacher_load_route_name') ? $text_white : $text_dark }}; 
                    background:{{ Route::is('teacher_load_route_name') ? $primary_indigo : 'transparent' }};">
                                    Product Report
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- POS history --}}
                <li class="nav-item">
                    <a href="{{ route('pos-history') }}"
                        class="nav-link {{ Route::is('pos-history') ? 'active' : '' }}"
                        style="display:flex; align-items:center; gap:10px; color:{{ Route::is('pos-history') ? '#fff' : '#333' }}; background:{{ Route::is('pos-history') ? '#ff5757' : 'transparent' }};">
                        <i class="bi bi-clock-history"></i> POS History
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
