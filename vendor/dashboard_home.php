<?php
session_start();   
?><div id="default-dashboard">
        <header>
            <h1>Dashboard</h1>
            <div class="user-profile">
                <img src="../frontend/photos/bipul.jpg" alt="Vendor">
                <span>Vendor <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </header>

        <div class="dashboard-placeholder">
            <p>Welcome to the Vendor Dashboard. Select an option from the sidebar.</p>
        </div>
    </div>
<style>
     .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
</style>