let itemHistory = [0];
const baseUrl = window.location.origin + "/item-management/";

function escapeHTML(string) {
    if(!string) return '';
    const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#x27;',"/":'&#x2F;'};
    return string.replace(/[&<>"'/]/ig, (match) => map[match]);
}

// --- INITIALIZATION & GLOBAL CLICK EVENT HANDLERS ---
document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-dropdown')) {
        document.querySelectorAll('.dropdown-menu-list').forEach(el => el.classList.remove('show'));
    }
    // Isasara rin ang custom searchable dropdown kapag nag-click sa labas nito
    if (!e.target.closest('.custom-searchable-select')) {
        document.querySelectorAll('.select-dropdown-options').forEach(el => el.classList.remove('show'));
    }
    if (e.target.classList.contains('custom-modal')) {
        closeItemModal();
        closeDeleteModal();
    }
});

function toggleDropdown(id) {
    document.querySelectorAll('.dropdown-menu-list').forEach(el => el.classList.remove('show'));
    const menu = document.getElementById(`dropdown-item-${id}`);
    if (menu) menu.classList.toggle('show');
    if(window.event || event) (window.event || event).stopPropagation();
}

// --- DRAG AND DROP / FILE SELECTION LOGIC ---
function handleFileSelection(files) {
    if (!files || !files.length) return;
    const file = files[0];

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('drop-zone-img-element').src = e.target.result;
            document.getElementById('drop-zone-prompt').style.display = 'none';
            document.getElementById('drop-zone-preview-box').style.display = 'block';
            document.getElementById('remove-current-image').value = "0"; // May file uli kaya wag buburahin sa DB
        }
        reader.readAsDataURL(file);
    }
}

function clearModalImage(event) {
    if (event) event.stopPropagation(); 

    document.getElementById('form-item-image').value = ""; 
    document.getElementById('drop-zone-img-element').src = "";
    document.getElementById('drop-zone-preview-box').style.display = 'none';
    document.getElementById('drop-zone-prompt').style.display = 'flex';
    
    // Tiyaking nakasulat ito ng maayos
    const removeInput = document.getElementById('remove-current-image');
    if (removeInput) {
        removeInput.value = "1";
    }
}

// --- OPEN ACTIONS MODALS ---
function openAddModal() {
    document.getElementById('item-form').reset();
    document.getElementById('modal-title').innerHTML = '<i class="fa-solid fa-plus"></i> New Product Entry';
    document.getElementById('item-form').action = `${baseUrl}items/add`;
    
    // I-reset ang Drag and Drop Zone view layout
    clearModalImage(null);

    const errorBox = document.getElementById('modal-error-box');
    if (errorBox) errorBox.style.display = 'none';

    document.getElementById('item-modal').classList.add('show');
}

function openEditModal(item) {
    const errorBox = document.getElementById('modal-error-box');
    if (errorBox) errorBox.style.display = 'none';

    document.getElementById('modal-title').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Edit Product Properties';
    document.getElementById('item-form').action = `${baseUrl}items/update/${item.id}`;
    
    document.getElementById('form-item-name').value = item.item_name || item.name;
    document.getElementById('form-sku').value = item.sku || item.item_code;
    document.getElementById('form-price').value = item.price || item.cost;
    document.getElementById('form-quantity').value = item.quantity || item.qty;
    
    if (document.getElementById('brand-label')) {
        document.getElementById('form-brand-id').value = item.brand_id;
        document.getElementById('brand-label').innerText = item.brand_name || '-- Choose Brand --';
    }
    if (document.getElementById('category-label')) {
        document.getElementById('form-category-id').value = item.category_id;
        document.getElementById('category-label').innerText = item.category_name || '-- Choose Category --';
    }

    // IMAGE DRAG & DROP ZONE UPDATE RE-RENDERING
    const imgName = item.item_image || item.picture;
    const removeInput = document.getElementById('remove-current-image');
    if (removeInput) removeInput.value = "0";

    if (imgName) {
        document.getElementById('drop-zone-img-element').src = `${baseUrl}uploads/items/${imgName}`;
        document.getElementById('drop-zone-prompt').style.display = 'none';
        document.getElementById('drop-zone-preview-box').style.display = 'block';
    } else {
        document.getElementById('drop-zone-preview-box').style.display = 'none';
        document.getElementById('drop-zone-prompt').style.display = 'flex';
    }

    document.getElementById('item-modal').classList.add('show');
}

function closeItemModal() { 
    document.getElementById('item-modal').classList.remove('show'); 
}

function openDeleteModal(id) {
    document.getElementById('delete-modal-confirm-btn').href = `${baseUrl}items/delete/${id}`;
    document.getElementById('delete-modal').classList.add('show');
}
function closeDeleteModal() { document.getElementById('delete-modal').classList.remove('show'); }

// --- DATA LOGISTICS (SEARCH & PAGINATION) ---
function triggerSearch() {
    itemHistory = [0];
    loadItems();
}

function paginateItems(direction) {
    if (direction === 'next') {
        loadItems();
    } else {
        itemHistory = [0];
        loadItems();
    }
}

function loadItems() {
    const query = document.getElementById('live-search').value;
    const currentLastId = itemHistory[itemHistory.length - 1];

    fetch(`${baseUrl}items/search_items?q=${query}&last_id=${currentLastId}`)
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('items-table-body');
            tbody.innerHTML = '';

            if (res.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="padding: 30px 0; text-align: center; color: #94a3b8;"><i class="fa-solid fa-box-open" style="font-size:24px; margin-bottom:8px; display:block;"></i> Walang nakitang product.</td></tr>`;
                document.getElementById('item-next-btn').disabled = true;
                return;
            }

            res.data.forEach((item) => {
                const cleanNameForImg = encodeURIComponent(item.item_name || 'Item');
                const imgUrl = item.item_image 
                    ? `${baseUrl}uploads/items/${item.item_image}` 
                    : `https://placehold.co/150x150/f1f5f9/64748b?text=${cleanNameForImg}`;

                const cleanRowData = JSON.stringify(item).replace(/'/g, "&apos;");

                tbody.innerHTML += `
                    <tr style="border-bottom: 1px solid #f1f5f9; color: #334155;">
                        <td style="padding: 10px 5px;">
                            <img src="${imgUrl}" class="img-preview" onerror="this.src='https://placehold.co/150x150/f1f5f9/64748b?text=No+Image'">
                        </td>
                        <td style="padding: 10px 5px; font-weight: 600; color:#0f172a;">${escapeHTML(item.item_name)}</td>
                        <td style="padding: 10px 5px; font-family:monospace; color:#64748b;">${escapeHTML(item.sku)}</td>
                        <td style="padding: 10px 5px;"><span style="background:#f1f5f9; padding:3px 8px; border-radius:12px; font-size:12px;">${escapeHTML(item.brand_name || 'Unbranded')}</span></td>
                        <td style="padding: 10px 5px; color:#475569;">${escapeHTML(item.category_name || 'Uncategorized')}</td>
                        <td style="padding: 10px 5px; text-align: center; font-weight:bold; color:${item.quantity < 5 ? '#ef4444' : '#334155'};">${item.quantity}</td>
                        <td style="padding: 10px 5px; text-align: right; font-weight: 600;">₱${parseFloat(item.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td style="padding: 10px 5px; text-align: right;">
                            <div class="action-dropdown">
                                <button class="dropdown-trigger" onclick="toggleDropdown(${item.id})"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div id="dropdown-item-${item.id}" class="dropdown-menu-list">
                                    <button class="dropdown-item-btn" onclick='openEditModal(${cleanRowData})'><i class="fa-solid fa-pen"></i> Edit</button>
                                    <button class="dropdown-item-btn delete-btn" onclick="openDeleteModal(${item.id})"><i class="fa-solid fa-trash"></i> Delete</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('item-next-btn').disabled = !res.has_more;
            document.getElementById('item-prev-btn').disabled = itemHistory.length <= 1;

            if (res.has_more) {
                itemHistory.push(res.last_id);
            }
        });
}

// --- MODULE DROP LIST EXTENSION CONTROL ---
function toggleSearchableDropdown(type) {
    const targetMenu = document.getElementById(`${type}-options-box`);
    const isShowing = targetMenu.classList.contains('show');
    
    document.querySelectorAll('.select-dropdown-options').forEach(el => el.classList.remove('show'));
    
    if (!isShowing) {
        targetMenu.classList.add('show');
        targetMenu.querySelector('.select-search-input').focus();
    }
    if(window.event || event) (window.event || event).stopPropagation();
}

function selectOption(type, id, text) {
    document.getElementById(`form-${type}-id`).value = id;
    document.getElementById(`${type}-label`).innerText = text;
    document.getElementById(`${type}-options-box`).classList.remove('show');
}

function filterOptions(type) {
    const searchBox = document.getElementById(`${type}-options-box`);
    const filter = searchBox.querySelector('.select-search-input').value.toLowerCase();
    const options = searchBox.querySelectorAll('.option-item');

    options.forEach(option => {
        const text = option.innerText.toLowerCase();
        if (text.includes(filter)) {
            option.classList.remove('hidden');
        } else {
            option.classList.add('hidden');
        }
    });
}

// --- DOM INITIALIZER & EVENT BINDING ENGINE ---
document.addEventListener("DOMContentLoaded", function() {
    // 1. Patakbuhin ang paunang load ng table list entries
    loadItems();

    // 2. Drag & Drop Zone Native Bindings
    const dropZone = document.getElementById('drop-zone');
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'), false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'), false);
        });

        dropZone.addEventListener('drop', e => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length) {
                document.getElementById('form-item-image').files = files; // Pass binary reference array hooks
                handleFileSelection(files);
            }
        });
    }

    // 3. AJAX Submission interception handling
    const form = document.getElementById('item-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Harangin ang normal page reload behavior

            const formData = new FormData(this); 
            
            // MAHALAGA: Kunin ang dynamic action attribute ng form (add man o update/{id})
            const actionUrl = this.getAttribute('action') || this.action;
            
            const errorBox = document.getElementById('modal-error-box');
            const errorMsg = document.getElementById('modal-error-msg');

            if (errorBox) errorBox.style.display = 'none';

            console.log("Submitting via AJAX to:", actionUrl); // Para makita mo sa Console log kung tama ang URL

            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Standard flag para sa AJAX requests
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    if (errorMsg && errorBox) {
                        errorMsg.innerText = data.error || 'May nakitang problema sa iyong isinumite.';
                        errorBox.style.display = 'block';
                    }
                } else {
                    // SUCCESS FLOW: Isasara ang modal at ire-refresh ang page para makita ang pagbabago
                    closeItemModal();
                    window.location.reload(); 
                }
            })
            .catch(err => {
                console.error("AJAX Processing Failure:", err);
            });
        });
    }
});

// Siguraduhing malinis din ang error box tuwing binubuksan o isinasara ang modal
const originalCloseItemModal = closeItemModal;
closeItemModal = function() {
    const errBox = document.getElementById('modal-error-box');
    if (errBox) errBox.style.display = 'none';
    originalCloseItemModal();
}