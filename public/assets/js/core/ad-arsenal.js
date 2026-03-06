/**
 * @file ad-arsenal.js
 * @description Logic for Ad Arsenal Management (Admin Panel).
 * Handles CRUD with image upload support via FormData.
 */

import { api } from '../services/api.js';
import { showToast } from '../utils/helpers.js';

// --- STATE ---
let editingArsenalId = null;
let selectedImageFile = null;

// --- INIT ---
async function initAdArsenal() {
    const tableBody = document.getElementById('arsenal-table-body');
    if (!tableBody) return;

    try {
        // Admin endpoint returns ALL cards (active + inactive)
        const ads = await api.get('/admin/arsenal', { useApiPrefix: true });
        renderArsenalTable(tableBody, ads);
    } catch (error) {
        console.error("Failed to load arsenal:", error);
        tableBody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-rose-500">Failed to load cards.</td></tr>';
    }
}

// --- RENDER TABLE ---
function renderArsenalTable(tableBody, ads) {
    if (!ads || ads.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-slate-500">No promotional cards found. Click "Add New Card" to create one.</td></tr>';
        return;
    }

    tableBody.innerHTML = '';
    ads.forEach((data) => {
        const row = document.createElement('tr');
        row.className = 'border-b border-slate-700 hover:bg-slate-800/50 transition';

        const statusBadge = data.is_active
            ? '<span class="px-2 py-1 text-xs font-bold bg-emerald-500/20 text-emerald-400 rounded">Active</span>'
            : '<span class="px-2 py-1 text-xs font-bold bg-slate-500/20 text-slate-400 rounded">Inactive</span>';

        const tagColors = {
            'HOT': 'bg-rose-500/20 text-rose-400',
            'NEW': 'bg-blue-500/20 text-blue-400',
            'FOUNDATION': 'bg-emerald-500/20 text-emerald-400',
            'PREMIUM': 'bg-purple-500/20 text-purple-400'
        };
        const tagClass = tagColors[data.tag] || 'bg-slate-500/20 text-slate-400';

        const imgCell = data.image_url
            ? `<img src="${data.image_url}" class="w-12 h-12 rounded-lg object-cover border border-slate-600" alt="${data.title}">`
            : `<div class="w-12 h-12 rounded-lg bg-slate-700 flex items-center justify-center text-slate-500"><i class="fas fa-image"></i></div>`;

        // Escape strings for onclick to prevent apostrophe issues
        const safeTitle = (data.title || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
        const safeDesc = (data.description || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
        const safeLink = (data.link || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
        const safeImgUrl = (data.image_url || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');

        row.innerHTML = `
            <td class="p-4 text-sm text-white font-mono">${data.sort_order}</td>
            <td class="p-4">${imgCell}</td>
            <td class="p-4 text-sm text-white font-medium">${data.title}</td>
            <td class="p-4"><span class="px-2 py-1 text-xs font-bold ${tagClass} rounded">${data.tag}</span></td>
            <td class="p-4 text-xs text-slate-400 truncate max-w-xs">${(data.description || '').substring(0, 50)}...</td>
            <td class="p-4">${statusBadge}</td>
            <td class="p-4">
                <div class="flex gap-2">
                    <button onclick="editArsenalCard(${data.id}, '${safeTitle}', '${safeDesc}', '${data.tag}', '${safeLink}', ${data.sort_order}, ${data.is_active}, '${safeImgUrl}')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs rounded-lg transition">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <button onclick="deleteArsenalCard(${data.id})" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs rounded-lg transition">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// --- IMAGE HANDLING ---
window.handleArsenalImageSelect = (input) => {
    const file = input.files[0];
    if (!file) return;

    const errorEl = document.getElementById('arsenal-image-error');
    const maxSize = 200 * 1024; // 200KB

    if (file.size > maxSize) {
        errorEl.classList.remove('hidden');
        errorEl.textContent = `Ukuran file ${(file.size / 1024).toFixed(0)}KB melebihi batas 200KB!`;
        input.value = '';
        selectedImageFile = null;
        return;
    }

    errorEl.classList.add('hidden');
    selectedImageFile = file;

    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('arsenal-image-placeholder').classList.add('hidden');
        const previewWrap = document.getElementById('arsenal-image-preview-wrap');
        previewWrap.classList.remove('hidden');
        document.getElementById('arsenal-image-preview').src = e.target.result;

        const sizeKB = (file.size / 1024).toFixed(1);
        const sizeEl = document.getElementById('arsenal-image-size');
        sizeEl.textContent = `${sizeKB} KB`;
        sizeEl.className = `text-xs font-mono ${file.size > 150 * 1024 ? 'text-amber-400' : 'text-emerald-400'}`;
    };
    reader.readAsDataURL(file);
};

window.clearArsenalImage = () => {
    selectedImageFile = null;
    document.getElementById('arsenal-image-input').value = '';
    document.getElementById('arsenal-image-placeholder').classList.remove('hidden');
    document.getElementById('arsenal-image-preview-wrap').classList.add('hidden');
    document.getElementById('arsenal-image-error').classList.add('hidden');
};

// Drag & Drop
document.addEventListener('DOMContentLoaded', () => {
    const dropzone = document.getElementById('arsenal-image-dropzone');
    if (!dropzone) return;

    ['dragenter', 'dragover'].forEach(evt => {
        dropzone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropzone.classList.add('border-emerald-500', 'bg-emerald-900/10');
        });
    });

    ['dragleave', 'drop'].forEach(evt => {
        dropzone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-emerald-500', 'bg-emerald-900/10');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const input = document.getElementById('arsenal-image-input');
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            handleArsenalImageSelect(input);
        }
    });
});

// --- MODAL ---
window.openArsenalModal = (id = null, title = '', description = '', tag = 'NEW', link = '', sort_order = 0, is_active = true, image_url = '') => {
    editingArsenalId = id;
    selectedImageFile = null;

    const modal = document.getElementById('arsenal-modal');
    const modalTitle = document.getElementById('arsenal-modal-title');
    const form = document.getElementById('arsenal-form');

    // Reset image UI
    clearArsenalImage();
    document.getElementById('arsenal-current-image-wrap').classList.add('hidden');

    if (id) {
        modalTitle.textContent = 'Edit Promotional Card';
        document.getElementById('arsenal-title').value = title;
        document.getElementById('arsenal-description').value = description;
        document.getElementById('arsenal-tag').value = tag;
        document.getElementById('arsenal-link').value = link;
        document.getElementById('arsenal-order').value = sort_order;
        document.getElementById('arsenal-active').checked = is_active;

        // Show current image if exists
        if (image_url) {
            const currentWrap = document.getElementById('arsenal-current-image-wrap');
            document.getElementById('arsenal-current-image').src = image_url;
            currentWrap.classList.remove('hidden');
        }
    } else {
        modalTitle.textContent = 'Add New Promotional Card';
        form.reset();
        document.getElementById('arsenal-active').checked = true;
    }

    modal.classList.remove('hidden');
};

window.closeArsenalModal = () => {
    document.getElementById('arsenal-modal').classList.add('hidden');
    editingArsenalId = null;
    selectedImageFile = null;
};

// --- SAVE (FormData for file upload) ---
window.saveArsenalCard = async () => {
    const title = document.getElementById('arsenal-title').value.trim();
    const description = document.getElementById('arsenal-description').value.trim();
    const tag = document.getElementById('arsenal-tag').value;
    const link = document.getElementById('arsenal-link').value.trim();
    const sort_order = parseInt(document.getElementById('arsenal-order').value);
    const is_active = document.getElementById('arsenal-active').checked;

    if (!title || !description || !link) {
        showToast('Please fill all required fields', 'error');
        return;
    }

    const saveBtn = document.getElementById('save-arsenal-btn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
    saveBtn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('title', title);
        formData.append('description', description);
        formData.append('tag', tag);
        formData.append('link', link);
        formData.append('sort_order', sort_order);
        formData.append('is_active', is_active ? '1' : '0');

        if (selectedImageFile) {
            formData.append('image', selectedImageFile);
        }

        // Build fetch config — NO Content-Type header (browser sets multipart boundary)
        const token = localStorage.getItem('auth_token');
        const headers = {
            'Accept': 'application/json',
            ...(token ? { 'Authorization': `Bearer ${token}` } : {})
        };

        let url, method;
        if (editingArsenalId) {
            url = `/api/admin/arsenal/${editingArsenalId}/update`;
            method = 'POST';
        } else {
            url = '/api/admin/arsenal';
            method = 'POST';
        }

        const response = await fetch(url, { method, headers, body: formData, credentials: 'same-origin' });
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to save');
        }

        showToast(editingArsenalId ? 'Card updated successfully' : 'Card created successfully', 'success');
        closeArsenalModal();
        initAdArsenal();
    } catch (e) {
        console.error(e);
        showToast(e.message || 'Failed to save card', 'error');
    } finally {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    }
};

// --- EDIT / DELETE ---
window.editArsenalCard = (id, title, description, tag, link, sort_order, is_active, image_url) => {
    openArsenalModal(id, title, description, tag, link, sort_order, is_active, image_url);
};

window.deleteArsenalCard = (id) => {
    if (!confirm("Delete this card?")) return;

    api.delete(`/admin/arsenal/${id}`, { useApiPrefix: true })
        .then(() => {
            showToast('Card deleted successfully', 'success');
            initAdArsenal();
        })
        .catch(e => {
            console.error(e);
            showToast('Failed to delete card', 'error');
        });
};

// --- INIT ON LOAD ---
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('arsenal-table-body')) initAdArsenal();
    });
} else {
    if (document.getElementById('arsenal-table-body')) initAdArsenal();
}

// Seeder Stub
window.seedAdArsenal = () => {
    showToast("Please use 'php artisan db:seed' for seeding.", "info");
};

window.initAdArsenal = initAdArsenal;
