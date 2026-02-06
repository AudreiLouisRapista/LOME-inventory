<style>
    /* Styling for the Floating Effect */
    .custom-navbar-container {
        padding: 15px 30px;
        /* Space around the bar */
        position: sticky;
        top: 0;
        z-index: 1000;
        background: #f4f7fe;
    }

    .navbar-glass {
        background: rgba(255, 255, 255, 0.9);
        /* Semi-transparent white */
        backdrop-filter: blur(10px);
        /* Blur effect for glass look */
        border-radius: 20px;
        padding: 10px 25px !important;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.18);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .brand-text {
        font-weight: 800;
        font-size: 22px;
        background: linear-gradient(90deg, #4A00E0, #8E2DE2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.5px;
    }
</style>

<div class="custom-navbar-container">
    <nav class="navbar-glass">
        <a class="navbar-brand d-flex align-items-center" href="#" style="text-decoration: none;">

            <span class="brand-text">EduSched</span>
        </a>

        <div class="d-flex align-items-center gap-3">
            <div class="mr-3 text-right d-none d-md-block">
                <small class="text-muted d-block"
                    style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Teacher Access</small>
                <span class="text-dark font-weight-bold" style="font-size: 14px;">{{ session('name') }}</span>
            </div>

            <a href="{{ route('logout') }}" class="logout-btn"
                style="
                  display: flex;
                  align-items: center;
                  gap: 8px;
                  padding: 8px 20px;
                  color: #fff;
                  background: #ff4757;
                  border-radius: 12px;
                  font-size: 13px;
                  font-weight: 600;
                  text-decoration: none;
                  transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                  box-shadow: 0 4px 12px rgba(255, 71, 87, 0.2);
               "
                onmouseover="this.style.transform='scale(1.05)'; this.style.backgroundColor='#ff6b81';"
                onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='#ff4757';">
                <i class="bi bi-box-arrow-left"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</div>
