// assets/js/settings.js

let currentTab = "brands-section";
const baseUrl = window.location.origin + "/item-management/";

// Keyset Pagination state markers
let brandHistory = [0];
let categoryHistory = [0];

// Anti-XSS Security Filter
function escapeHTML(string) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#x27;',
        "/": '&#x2F;'
    };
    return string.replace(/[&<>"'/]/ig, (match) => map[match]);
}

// Global Click Handler para sa UI elements
document.addEventListener('click', function(e) {
    // Isara ang lahat ng active dropdowns kapag nag-click sa labas
    if (!e.target.closest('.action-dropdown')) {
        document.querySelectorAll('.dropdown-menu-list').forEach(el => el.classList.remove('show'));
    }
    // Isara ang mga modals kapag pinindot ang translucent outer background overlay
    if (e.target.classList.contains('custom-modal')) {
        closeEditModal();
        closeDeleteModal();
    }
});

// Dropdown Action Toggle
function toggleDropdown(id, type) {
    // Isara muna ang ibang nakabukas na dropdown menu
    document.querySelectorAll('.dropdown-menu-list').forEach(el => el.classList.remove('show'));
    
    // Buksan ang pinindot na menu target
    const menu = document.getElementById(`dropdown-${type}-${id}`);
    if (menu) menu.classList.toggle('show');
    
    if(event) event.stopPropagation();
}

// --- EDIT MODAL CONTROLS ---
function openEditModal(id, currentName, type) {
    const modal = document.getElementById('edit-modal');
    const title = document.getElementById('modal-title');
    const input = document.getElementById('modal-input-name');
    const form = document.getElementById('modal-form');

    title.innerHTML = type === 'brand' 
        ? '<i class="fa-solid fa-pen-to-square"></i> Edit Brand Name' 
        : '<i class="fa-solid fa-pen-to-square"></i> Edit Category Name';
        
    input.value = currentName;
    form.action = `${baseUrl}settings/update_${type}/${id}`;

    modal.classList.add('show');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.remove('show');
}

// --- CUSTOM DELETE MODAL CONTROLS ---
function openDeleteModal(id, type) {
    const modal = document.getElementById('delete-modal');
    const confirmBtn = document.getElementById('delete-modal-confirm-btn');
    
    // I-route ang execution button sa tamang controller deletion rules
    confirmBtn.href = `${baseUrl}settings/delete_${type}/${id}`;
    
    modal.classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.remove('show');
}

// --- NAVIGATION TABS LOGIC ---
function switchTab(sectionId) {
    currentTab = sectionId;
    
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    if(event) event.currentTarget.classList.add('active');
    
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    document.getElementById(sectionId).classList.add('active');
    
    // Linisin ang live search input tuwing maglilipat ng view mode
    document.getElementById('live-search').value = '';
    
    // I-reset ang Keyset parameters sa base structure page
    brandHistory = [0];
    categoryHistory = [0];
    triggerSearch();
}

// Live Search Keyup Router
function triggerSearch() {
    if (currentTab === 'brands-section') {
        brandHistory = [0]; // I-reset ang data marker layers sa simula
        loadBrands();
    } else {
        categoryHistory = [0];
        loadCategories();
    }
}

// --- BRANDS ENGINE CONTROLS ---
function paginateBrands(direction) {
    if (direction === 'next') {
        loadBrands();
    } else {
        brandHistory = [0]; // Keyset safety rollback sa front rows
        loadBrands();
    }
}

function loadBrands() {
    const query = document.getElementById('live-search').value;
    const currentLastId = brandHistory[brandHistory.length - 1];

    fetch(`${baseUrl}settings/search_brands?q=${query}&last_id=${currentLastId}`)
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('brands-table-body');
            tbody.innerHTML = '';

            if (res.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" style="padding: 20px 0; text-align: center; color: #94a3b8;"><i class="fa-solid fa-folder-open"></i> Walang nahanap na record.</td></tr>`;
                document.getElementById('brand-next-btn').disabled = true;
                return;
            }

            res.data.forEach((brand) => {
                const cleanName = escapeHTML(brand.brand_name);
                tbody.innerHTML += `
                    <tr style="border-bottom: 1px solid #f1f5f9; color: #334155;">
                        <td style="padding: 10px 5px;">${brand.id}</td>
                        <td style="padding: 10px 5px; font-weight: 500;"><i class="fa-solid fa-tag" style="color: #94a3b8; margin-right: 6px;"></i> ${cleanName}</td>
                        <td style="padding: 10px 5px; text-align: right;">
                            <div class="action-dropdown">
                                <button class="dropdown-trigger" onclick="toggleDropdown(${brand.id}, 'brand')"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div id="dropdown-brand-${brand.id}" class="dropdown-menu-list">
                                    <button class="dropdown-item-btn" onclick="openEditModal(${brand.id}, '${cleanName.replace(/'/g, "\\'")}', 'brand')"><i class="fa-solid fa-pen" style="width:14px;"></i> Edit</button>
                                    <button class="dropdown-item-btn delete-btn" onclick="openDeleteModal(${brand.id}, 'brand')"><i class="fa-solid fa-trash" style="width:14px;"></i> Delete</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('brand-next-btn').disabled = !res.has_more;
            document.getElementById('brand-prev-btn').disabled = brandHistory.length <= 1;

            if (res.has_more) {
                brandHistory.push(res.last_id);
            }
        });
}

// --- CATEGORIES ENGINE CONTROLS ---
function paginateCategories(direction) {
    if (direction === 'next') {
        loadCategories();
    } else {
        categoryHistory = [0];
        loadCategories();
    }
}

function loadCategories() {
    const query = document.getElementById('live-search').value;
    const currentLastId = categoryHistory[categoryHistory.length - 1];

    fetch(`${baseUrl}settings/search_categories?q=${query}&last_id=${currentLastId}`)
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('categories-table-body');
            tbody.innerHTML = '';

            if (res.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" style="padding: 20px 0; text-align: center; color: #94a3b8;"><i class="fa-solid fa-folder-open"></i> Walang nahanap na record.</td></tr>`;
                document.getElementById('category-next-btn').disabled = true;
                return;
            }

            res.data.forEach((cat) => {
                const cleanName = escapeHTML(cat.category_name);
                tbody.innerHTML += `
                    <tr style="border-bottom: 1px solid #f1f5f9; color: #334155;">
                        <td style="padding: 10px 5px;">${cat.id}</td>
                        <td style="padding: 10px 5px; font-weight: 500;"><i class="fa-solid fa-layer-group" style="color: #94a3b8; margin-right: 6px;"></i> ${cleanName}</td>
                        <td style="padding: 10px 5px; text-align: right;">
                            <div class="action-dropdown">
                                <button class="dropdown-trigger" onclick="toggleDropdown(${cat.id}, 'cat')"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div id="dropdown-cat-${cat.id}" class="dropdown-menu-list">
                                    <button class="dropdown-item-btn" onclick="openEditModal(${cat.id}, '${cleanName.replace(/'/g, "\\'")}', 'category')"><i class="fa-solid fa-pen" style="width:14px;"></i> Edit</button>
                                    <button class="dropdown-item-btn delete-btn" onclick="openDeleteModal(${cat.id}, 'category')"><i class="fa-solid fa-trash" style="width:14px;"></i> Delete</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('category-next-btn').disabled = !res.has_more;
            document.getElementById('category-prev-btn').disabled = categoryHistory.length <= 1;

            if (res.has_more) {
                categoryHistory.push(res.last_id);
            }
        });
}