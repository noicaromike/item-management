<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="dashboard-wrapper">
    <div class="sidebar">
        <div class="sidebar-header">IMS Project</div>
        <div class="sidebar-user">
            <div><?= !empty($full_name) ? $full_name : 'Admin Admin'; ?></div>
            <div>● <?= !empty($role) ? $role : 'STAFF'; ?></div>
        </div>
        <div class="sidebar-menu">
            <a href="<?= base_url('dashboard') ?>" class="sidebar-link">
                <i class="fa-solid fa-chart-simple" style="margin-right: 8px; width: 16px;"></i> Dashboard Home
            </a>
            <a href="<?= base_url('settings') ?>" class="sidebar-link active">
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
            <h2>Settings Configuration</h2>
            <div>Date: <strong><?= date('F d, Y') ?></strong></div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div style="background-color: #f0f9ff; color: #0284c7; padding: 12px 20px; margin-bottom: 25px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 14px;">
                ✓ <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="tabs-container">
            <button class="tab-btn active" onclick="switchTab('brands-section')">
                <i class="fa-solid fa-tags" style="margin-right: 6px;"></i> Brands
            </button>
            <button class="tab-btn" onclick="switchTab('categories-section')">
                <i class="fa-solid fa-layer-group" style="margin-right: 6px;"></i> Categories
            </button>
        </div>

        <div>
            <input type="text" id="live-search" placeholder="&#xf002; Search item..." class="search-control" style="font-family: 'Arial', 'Font Awesome 6 Free'; font-weight: 600;" onkeyup="triggerSearch()">
        </div>

        <div style="background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #e2e8f0;">
            
            <div id="brands-section" class="tab-content active">
                <form action="<?= base_url('settings/add_brand') ?>" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px; max-width: 500px;">
                    <input type="text" name="brand_name" placeholder="Add new brand name..." required style="flex: 1; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    <button type="submit" class="pag-btn" style="background:#0284c7; color:white; border:none;">Add Brand</button>
                </form>

                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b;">
                            <th style="padding: 10px 5px; width: 60px;">ID</th>
                            <th style="padding: 10px 5px;">Brand Name</th>
                            <th style="padding: 10px 5px; text-align: right; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="brands-table-body"></tbody>
                </table>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                    <button id="brand-prev-btn" class="pag-btn" onclick="paginateBrands('prev')" disabled>← Reset</button>
                    <button id="brand-next-btn" class="pag-btn" onclick="paginateBrands('next')" disabled>Next →</button>
                </div>
            </div>

            <div id="categories-section" class="tab-content">
                <form action="<?= base_url('settings/add_category') ?>" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px; max-width: 500px;">
                    <input type="text" name="category_name" placeholder="Add new category name..." required style="flex: 1; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    <button type="submit" class="pag-btn" style="background:#0284c7; color:white; border:none;">Add Category</button>
                </form>

                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b;">
                            <th style="padding: 10px 5px; width: 60px;">ID</th>
                            <th style="padding: 10px 5px;">Category Name</th>
                            <th style="padding: 10px 5px; text-align: right; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="categories-table-body"></tbody>
                </table>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                    <button id="category-prev-btn" class="pag-btn" onclick="paginateCategories('prev')" disabled>← Reset</button>
                    <button id="category-next-btn" class="pag-btn" onclick="paginateCategories('next')" disabled>Next →</button>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="edit-modal" class="custom-modal">
    <div class="modal-content-card">
        <div class="modal-header">
            <h3 id="modal-title"><i class="fa-solid fa-pen-to-square"></i> Edit Item</h3>
            <button type="button" class="modal-close-x" onclick="closeEditModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form id="modal-form" action="" method="POST" style="margin: 0;">
            <div class="modal-body">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #475569; font-size: 13px;">Bagong Pangalan:</label>
                <input type="text" id="modal-input-name" name="update_name" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; outline: none;">
            </div>
            
            <div class="modal-footer">
                <button type="button" class="pag-btn" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="pag-btn" style="background: #0284c7; color: white; border: none;"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="delete-modal" class="custom-modal">
    <div class="modal-content-card" style="max-width: 400px;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i> Confirm Deletion</h3>
            <button type="button" class="modal-close-x" onclick="closeDeleteModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="modal-body" style="text-align: center; padding: 24px 20px;">
            <div style="font-size: 40px; color: #ef4444; margin-bottom: 12px;">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <h4 style="margin: 0 0 8px 0; font-size: 16px; color: #0f172a;">Are you sure you want to Delete?</h4>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="pag-btn" onclick="closeDeleteModal()">Cancel</button>
            <a id="delete-modal-confirm-btn" href="#" class="pag-btn" style="background: #ef4444; color: white; border: none; text-decoration: none; display: inline-block; line-height: 20px;">
                <i class="fa-solid fa-trash-can"></i> Delete Na
            </a>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/settings.js'); ?>"></script>
<script>
    currentTab = "<?= $this->session->flashdata('active_tab') ? $this->session->flashdata('active_tab') : 'brands-section' ?>";
    document.addEventListener("DOMContentLoaded", () => {
        loadBrands();
        loadCategories();
        if (currentTab === 'categories-section') {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-btn')[1].classList.add('active');
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.getElementById('categories-section').classList.add('active');
        }
    });
</script>
</body>
</html>