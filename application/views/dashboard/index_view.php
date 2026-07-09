<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'IMS Project'; ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-wrapper">
    
    <div class="sidebar">
        <div class="sidebar-header">IMS Project</div>
        
        <div class="sidebar-user">
            <div style="font-weight: bold; color: #fff;"><?= $full_name ?></div>
            <div style="font-size: 12px; color: #2ecc71; font-weight: bold; text-transform: uppercase; margin-top: 3px;">
                <i class="fa-solid fa-circle" style="font-size: 10px; margin-right: 4px;"></i> <?= $role ?>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="<?= base_url('dashboard') ?>" class="sidebar-link active">
                <i class="fa-solid fa-chart-simple" style="margin-right: 8px; width: 16px;"></i> Dashboard Home
            </a>
            <a href="<?= base_url('settings') ?>" class="sidebar-link">
                <i class="fa-solid fa-gear" style="margin-right: 8px; width: 16px;"></i> Brand & Category
            </a>
            <a href="<?= base_url('items') ?>" class="sidebar-link">
                <i class="fa-solid fa-box-open" style="margin-right: 8px; width: 16px;"></i> Items & Inventory
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="<?= base_url('dashboard/logout') ?>">
                <i class="fa-solid fa-right-from-bracket" style="margin-right: 6px;"></i> Logout Account
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h2 style="margin: 0; color: #2c3e50; font-size: 24px;">Dashboard Overview</h2>
            <div style="color: #7f8c8d; font-size: 14px;">Date: <strong><?= date('F d, Y') ?></strong></div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; margin-bottom: 20px; border-radius: 6px; border-left: 5px solid #28a745;">
                <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="welcome-banner">
            <h3 style="margin: 0 0 10px 0; font-size: 22px;">Mabuhay, <?= $full_name ?>!</h3>
            <p style="margin: 0; opacity: 0.9; font-size: 15px;">Maligayang pagbabalik sa Inventory Management System.</p>
        </div>

        <div style="display: flex; gap: 20px;">
            <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-top: 4px solid #2ecc71;">
                <div style="font-size: 14px; color: #95a5a6; text-transform: uppercase; font-weight: bold;">System Status</div>
                <div style="font-size: 24px; font-weight: bold; color: #2c3e50; margin-top: 5px;">
                    <i class="fa-solid fa-circle-check" style="color: #2ecc71; margin-right: 6px;"></i> Online & Active
                </div>
            </div>
            <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-top: 4px solid #3498db;">
                <div style="font-size: 14px; color: #95a5a6; text-transform: uppercase; font-weight: bold;">Your Role</div>
                <div style="font-size: 24px; font-weight: bold; color: #3498db; margin-top: 5px; text-transform: capitalize;">
                    <i class="fa-solid fa-user-shield" style="margin-right: 6px;"></i> <?= $role ?>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>