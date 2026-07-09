<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .search-control { width: 100%; max-width: 350px; padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; margin-bottom: 20px; outline: none; }
        .pag-btn { background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer; color: #334155; }
        .pag-btn:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
        .action-dropdown { position: relative; display: inline-block; }
        .dropdown-trigger { background: transparent; border: none; color: #64748b; cursor: pointer; padding: 6px 10px; border-radius: 4px; font-size: 16px; }
        .dropdown-trigger:hover { background: #f1f5f9; color: #0f172a; }
        .dropdown-menu-list { display: none; position: absolute; right: 0; top: 100%; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 10; min-width: 120px; padding: 4px 0; }
        .dropdown-menu-list.show { display: block; }
        .dropdown-item-btn { display: block; width: 100%; padding: 8px 12px; text-align: left; background: transparent; border: none; font-size: 13px; color: #334155; cursor: pointer; font-weight: 500; }
        .dropdown-item-btn:hover { background: #f8fafc; color: #0f172a; }
        .dropdown-item-btn.delete-btn:hover { background: #fef2f2; color: #dc2626; }
        
        .custom-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.4); align-items: center; justify-content: center; }
        .custom-modal.show { display: flex !important; }
        .modal-content-card { background: #ffffff; border-radius: 8px; width: 100%; max-width: 500px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; overflow: hidden; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #f8fafc; }
        .modal-header h3 { margin: 0; font-size: 16px; color: #0f172a; }
        .modal-close-x { background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 16px; }
        .modal-body { padding: 20px; color: #334155; font-size: 14px; max-height: 70vh; overflow-y: auto; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 14px 20px; border-top: 1px solid #f1f5f9; background: #f8fafc; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #475569; font-size: 13px; }
        .form-input { width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; outline: none; }
        .form-input:focus { border-color: #0284c7; }
        .img-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0; }
    </style>
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
            <a href="<?= base_url('dashboard') ?>" class="sidebar-link"><i class="fa-solid fa-chart-simple" style="margin-right: 8px; width: 16px;"></i> Dashboard Home</a>
            <a href="<?= base_url('settings') ?>" class="sidebar-link"><i class="fa-solid fa-gear" style="margin-right: 8px; width: 16px;"></i> Brand & Category</a>
            <a href="<?= base_url('items') ?>" class="sidebar-link active"><i class="fa-solid fa-box-open" style="margin-right: 8px; width: 16px;"></i> Items & Inventory</a>
        </div>
        <div class="sidebar-footer"><a href="<?= base_url('dashboard/logout') ?>"><i class="fa-solid fa-right-from-bracket" style="margin-right: 6px;"></i> Logout Account</a></div>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h2>Items & Inventory Tracking</h2>
            <div>Date: <strong><?= date('F d, Y') ?></strong></div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div style="background-color: #f0f9ff; color: #0284c7; padding: 12px 20px; margin-bottom: 25px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 14px;">
                <i class="fa-solid fa-circle-check"></i> <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>

        

        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
            <input type="text" id="live-search" placeholder="&#xf002; Search item name or SKU..." class="search-control" style="font-family: 'Arial', 'Font Awesome 6 Free'; font-weight: 600;" onkeyup="triggerSearch()">
            <button class="pag-btn" style="background:#0284c7; color:white; border:none; padding: 8px 16px;" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> New Product</button>
        </div>

        <div style="background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b;">
                        <th style="padding: 10px 5px; width: 60px;">Image</th>
                        <th style="padding: 10px 5px;">Item Details</th>
                        <th style="padding: 10px 5px;">SKU / Code</th>
                        <th style="padding: 10px 5px;">Brand</th>
                        <th style="padding: 10px 5px;">Category</th>
                        <th style="padding: 10px 5px; text-align: center;">Qty</th>
                        <th style="padding: 10px 5px; text-align: right;">Price</th>
                        <th style="padding: 10px 5px; text-align: right; width: 60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="items-table-body"></tbody>
            </table>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                <button id="item-prev-btn" class="pag-btn" onclick="paginateItems('prev')" disabled><i class="fa-solid fa-arrow-rotate-left"></i> Reset</button>
                <button id="item-next-btn" class="pag-btn" onclick="paginateItems('next')" disabled>Next <i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>

<div id="item-modal" class="custom-modal">
    <div class="modal-content-card">
        <div class="modal-header">
            <h3 id="modal-title">New Product</h3>
            <button type="button" class="modal-close-x" onclick="closeItemModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="item-form" method="POST" enctype="multipart/form-data" style="margin:0;">
            <div class="modal-body">
                <div id="modal-error-box" style="display: none; background-color: #fef2f2; color: #ef4444; padding: 10px 14px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #fca5a5; font-size: 13px; font-weight: 500;">
                    <i class="fa-solid fa-circle-exclamation"></i> <span id="modal-error-msg"></span>
                </div>
                <div class="form-group">
                    <label>Item Product Name:</label>
                    <input type="text" id="form-item-name" name="item_name" class="form-input" required>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>SKU (Unique Code):</label>
                        <input type="text" id="form-sku" name="sku" class="form-input" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Item Price (PHP):</label>
                        <input type="number" id="form-price" name="price" step="0.01" class="form-input" required>
                    </div>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Brand Selection:</label>
                    <div class="searchable-select-container" id="brand-select-container">
                        <input type="hidden" id="form-brand-id" name="brand_id" required>
                        <div class="select-box-trigger" onclick="toggleSearchableDropdown('brand')">
                            <span id="brand-label">-- Choose Brand --</span>
                            <i class="fa-solid fa-chevron-down" style="font-size:12px; color:#94a3b8;"></i>
                        </div>
                        <div class="select-dropdown-options" id="brand-options-box">
                            <input type="text" class="select-search-input" placeholder="Maghanap ng brand..." onkeyup="filterOptions('brand')">
                            <div class="option-item" onclick="selectOption('brand', '', '-- Choose Brand --')">-- Choose Brand --</div>
                            <?php foreach($brands as $b): ?>
                                <div class="option-item" data-value="<?= $b->id ?>" onclick="selectOption('brand', '<?= $b->id ?>', '<?= htmlspecialchars($b->brand_name, ENT_QUOTES) ?>')"><?= htmlspecialchars($b->brand_name) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Category Group:</label>
                    <div class="searchable-select-container" id="category-select-container">
                        <input type="hidden" id="form-category-id" name="category_id" required>
                        <div class="select-box-trigger" onclick="toggleSearchableDropdown('category')">
                            <span id="category-label">-- Choose Category --</span>
                            <i class="fa-solid fa-chevron-down" style="font-size:12px; color:#94a3b8;"></i>
                        </div>
                        <div class="select-dropdown-options" id="category-options-box">
                            <input type="text" class="select-search-input" placeholder="Maghanap ng kategorya..." onkeyup="filterOptions('category')">
                            <div class="option-item" onclick="selectOption('category', '', '-- Choose Category --')">-- Choose Category --</div>
                            <?php foreach($categories as $c): ?>
                                <div class="option-item" data-value="<?= $c->id ?>" onclick="selectOption('category', '<?= $c->id ?>', '<?= htmlspecialchars($c->category_name, ENT_QUOTES) ?>')"><?= htmlspecialchars($c->category_name) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Initial Stock (Quantity):</label>
                    <input type="number" id="form-quantity" name="quantity" class="form-input" required style="width: 100%; box-sizing: border-box;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Product Image Attachment:</label>
                    
                    <div id="drop-zone" class="image-drop-zone" onclick="document.getElementById('form-item-image').click()">
                        <div id="drop-zone-prompt" class="drop-prompt">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; color: #94a3b8; margin-bottom: 6px;"></i>
                            <p style="margin: 0; font-size: 13px; color: #64748b;">Drag & drop image here or <span style="color: #0284c7; font-weight: 600;">Browse</span></p>
                            <span style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Supports: JPG, JPEG, PNG (Max 2MB)</span>
                        </div>
                        
                        <div id="drop-zone-preview-box" class="drop-preview-container" style="display: none;">
                            <img id="drop-zone-img-element" src="" alt="Preview">
                            <button type="button" class="drop-remove-btn" onclick="clearModalImage(event)">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <input type="file" id="form-item-image" name="item_image" accept="image/*" style="display: none;" onchange="handleFileSelection(this.files)">
                    <input type="hidden" id="remove-current-image" name="remove_image" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="pag-btn" onclick="closeItemModal()">Cancel</button>
                <button type="submit" class="pag-btn" style="background: #0284c7; color: white; border: none;"><i class="fa-solid fa-floppy-disk"></i> Save Data</button>
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
            <div style="font-size: 40px; color: #ef4444; margin-bottom: 12px;"><i class="fa-solid fa-circle-exclamation"></i></div>
            <h4 style="margin: 0 0 8px 0; font-size: 16px; color: #0f172a;">Are you sure you want to Delete?</h4>
        </div>
        <div class="modal-footer">
            <button type="button" class="pag-btn" onclick="closeDeleteModal()">Cancel</button>
            <a id="delete-modal-confirm-btn" href="#" class="pag-btn" style="background: #ef4444; color: white; border: none; text-decoration: none; display: inline-block; line-height: 20px;"><i class="fa-solid fa-trash-can"></i> Delete</a>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/items.js'); ?>"></script>
</body>
</html>