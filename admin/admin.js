// Navigation
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');
        
        // Update active nav
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
        
        // Show section
        document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
        document.getElementById(section + '-section').classList.add('active');
        
        // Load section data
        loadSection(section);
    });
});

// Load section data
function loadSection(section) {
    switch(section) {
        case 'menu':
            loadMenu();
            break;
        case 'pages':
            loadPages();
            break;
        case 'translations':
            loadTranslations('en');
            break;
        case 'settings':
            loadSettings();
            break;
    }
}

// Menu Management
function loadMenu() {
    const container = document.getElementById('menu-list');
    if (!menuData || menuData.length === 0) {
        container.innerHTML = '<p>No menu items. Click "Add Menu Item" to create one.</p>';
        return;
    }
    
    container.innerHTML = menuData.map(item => `
        <div class="data-item">
            <div class="data-item-content">
                <strong>${item.label_en || item.label_lv || item.label_ru}</strong>
                <div style="margin-top: 5px; color: #666; font-size: 14px;">
                    EN: ${item.label_en || '-'} | LV: ${item.label_lv || '-'} | RU: ${item.label_ru || '-'}
                </div>
                <div style="margin-top: 5px; color: #999; font-size: 12px;">URL: ${item.url || '#'}</div>
            </div>
            <div class="data-item-actions">
                <button class="btn btn-primary" onclick="editMenuItem(${item.id})">Edit</button>
                <button class="btn btn-danger" onclick="deleteMenuItem(${item.id})">Delete</button>
            </div>
        </div>
    `).join('');
}

function addMenuItem() {
    const newId = menuData.length > 0 ? Math.max(...menuData.map(m => m.id)) + 1 : 1;
    editMenuItem(newId);
}

function editMenuItem(id) {
    const item = menuData.find(m => m.id === id) || {
        id: id,
        label_en: '',
        label_lv: '',
        label_ru: '',
        url: '#',
        order: menuData.length + 1
    };
    
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modal-body');
    
    modalBody.innerHTML = `
        <h2>${item.id === id && menuData.find(m => m.id === id) ? 'Edit' : 'Add'} Menu Item</h2>
        <form id="menu-item-form">
            <input type="hidden" name="id" value="${item.id}">
            <div class="form-group">
                <label>Label (English)</label>
                <input type="text" name="label_en" value="${item.label_en || ''}" required>
            </div>
            <div class="form-group">
                <label>Label (Latviešu)</label>
                <input type="text" name="label_lv" value="${item.label_lv || ''}" required>
            </div>
            <div class="form-group">
                <label>Label (Русский)</label>
                <input type="text" name="label_ru" value="${item.label_ru || ''}" required>
            </div>
            <div class="form-group">
                <label>URL</label>
                <input type="text" name="url" value="${item.url || '#'}" required>
            </div>
            <div class="form-group">
                <label>Order</label>
                <input type="number" name="order" value="${item.order || menuData.length + 1}" required>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    `;
    
    modal.classList.add('active');
    
    document.getElementById('menu-item-form').addEventListener('submit', function(e) {
        e.preventDefault();
        saveMenuItem(new FormData(this));
    });
}

function saveMenuItem(formData) {
    const item = {
        id: parseInt(formData.get('id')),
        label_en: formData.get('label_en'),
        label_lv: formData.get('label_lv'),
        label_ru: formData.get('label_ru'),
        url: formData.get('url'),
        order: parseInt(formData.get('order'))
    };
    
    const index = menuData.findIndex(m => m.id === item.id);
    if (index >= 0) {
        menuData[index] = item;
    } else {
        menuData.push(item);
    }
    
    // Sort by order
    menuData.sort((a, b) => a.order - b.order);
    
    saveData('menu', menuData);
    closeModal();
    loadMenu();
}

function deleteMenuItem(id) {
    if (!confirm('Are you sure you want to delete this menu item?')) return;
    
    menuData = menuData.filter(m => m.id !== id);
    saveData('menu', menuData);
    loadMenu();
}

// Pages Management
function loadPages() {
    const container = document.getElementById('pages-list');
    if (!pagesData || pagesData.length === 0) {
        container.innerHTML = '<p>No pages. Click "Add Page" to create one.</p>';
        return;
    }
    
    container.innerHTML = pagesData.map(page => `
        <div class="data-item">
            <div class="data-item-content">
                <strong>${page.title_en || page.title_lv || page.title_ru}</strong>
                <div style="margin-top: 5px; color: #666; font-size: 14px;">Slug: ${page.slug || '-'}</div>
            </div>
            <div class="data-item-actions">
                <button class="btn btn-primary" onclick="editPage('${page.slug}')">Edit</button>
                <button class="btn btn-danger" onclick="deletePage('${page.slug}')">Delete</button>
            </div>
        </div>
    `).join('');
}

function addPage() {
    editPage(null);
}

function editPage(slug) {
    // Similar to menu item editing
    alert('Page editing will be implemented');
}

// Translations Management
function loadTranslations(lang) {
    const container = document.getElementById('translations-content');
    const data = translationsData[lang] || {};
    
    container.innerHTML = `
        <div class="translation-item">
            <div><strong>Key</strong></div>
            <div><strong>Translation</strong></div>
        </div>
        ${Object.entries(data).map(([key, value]) => `
            <div class="translation-item">
                <div><code>${key}</code></div>
                <div><input type="text" value="${value}" onchange="updateTranslation('${lang}', '${key}', this.value)"></div>
            </div>
        `).join('')}
        <div style="margin-top: 20px;">
            <button class="btn btn-primary" onclick="addTranslation('${lang}')">Add Translation</button>
        </div>
    `;
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        loadTranslations(this.getAttribute('data-lang'));
    });
});

function updateTranslation(lang, key, value) {
    if (!translationsData[lang]) translationsData[lang] = {};
    translationsData[lang][key] = value;
    saveData('translations', translationsData);
}

function addTranslation(lang) {
    const key = prompt('Enter translation key:');
    if (!key) return;
    const value = prompt('Enter translation value:');
    if (!value) return;
    
    if (!translationsData[lang]) translationsData[lang] = {};
    translationsData[lang][key] = value;
    saveData('translations', translationsData);
    loadTranslations(lang);
}

// Settings
function loadSettings() {
    document.getElementById('settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const settings = {};
        for (let [key, value] of formData.entries()) {
            settings[key] = value;
        }
        saveData('settings', settings);
        alert('Settings saved!');
    });
}

// Save data to server
function saveData(type, data) {
    fetch('api/save.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            type: type,
            data: data
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            console.log('Data saved successfully');
        } else {
            alert('Error saving data: ' + (result.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving data. Please try again.');
    });
}

// Modal
function closeModal() {
    document.getElementById('modal').classList.remove('active');
}

document.querySelector('.modal-close').addEventListener('click', closeModal);

document.getElementById('modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Load initial section
loadSection('menu');

