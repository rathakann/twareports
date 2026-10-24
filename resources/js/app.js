import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import jQuery from 'jquery';
import { createIcons, icons } from 'lucide';
import select2 from 'select2';
import Swal from 'sweetalert2';
import DataTable from 'datatables.net-bs5';

window.bootstrap = bootstrap;
window.Alpine = Alpine;
window.$ = window.jQuery = jQuery;

// Flatpickr with Global Presets (Today, This week, This month, Last month, Last 3 month) & Footer Actions
const getPresetDateRange = (preset) => {
    const now = new Date();
    const formatDate = (d) => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    switch (preset) {
        case 'today': {
            const today = formatDate(now);
            return [today, today];
        }
        case 'this_week': {
            const dayOfWeek = now.getDay();
            const diffToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
            const monday = new Date(now.getFullYear(), now.getMonth(), now.getDate() + diffToMonday);
            const sunday = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate() + 6);
            return [formatDate(monday), formatDate(sunday)];
        }
        case 'this_month': {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            return [formatDate(firstDay), formatDate(lastDay)];
        }
        case 'last_month': {
            const firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
            return [formatDate(firstDay), formatDate(lastDay)];
        }
        case 'last_3_months':
        case 'last_3_month': {
            const firstDay = new Date(now.getFullYear(), now.getMonth() - 2, 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            return [formatDate(firstDay), formatDate(lastDay)];
        }
        default:
            return null;
    }
};
window.getPresetDateRange = getPresetDateRange;

const applyPresetToFlatpickr = (instance, preset) => {
    if (!instance) return;
    if (preset === 'clear') {
        instance.clear(true);
        if (instance.altInput) {
            instance.altInput.value = '';
            instance.altInput.dispatchEvent(new Event('input', { bubbles: true }));
            instance.altInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        instance.input.value = '';
        instance.input.dispatchEvent(new Event('input', { bubbles: true }));
        instance.input.dispatchEvent(new Event('change', { bubbles: true }));
        if (window.jQuery) {
            window.jQuery(instance.input).val('').trigger('change');
            if (instance.altInput) {
                window.jQuery(instance.altInput).val('').trigger('change');
            }
        }
        instance.close();
        return;
    }

    const range = getPresetDateRange(preset);
    if (!range) return;

    if (instance.config && instance.config.mode === 'range') {
        instance.setDate(range, true);
    } else {
        instance.setDate(range[0], true);
    }

    if (instance.altInput) {
        instance.altInput.dispatchEvent(new Event('input', { bubbles: true }));
        instance.altInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
    instance.input.dispatchEvent(new Event('input', { bubbles: true }));
    instance.input.dispatchEvent(new Event('change', { bubbles: true }));
    if (window.jQuery) {
        window.jQuery(instance.input).trigger('change');
        if (instance.altInput) {
            window.jQuery(instance.altInput).trigger('change');
        }
    }
    instance.close();
};
window.applyPresetToFlatpickr = applyPresetToFlatpickr;

const attachFlatpickrFooter = (instance) => {
    if (!instance || !instance.calendarContainer) return;

    // Attach presets bar if not already present
    if (!instance.calendarContainer.querySelector('.flatpickr-presets-bar')) {
        const presetsBar = document.createElement('div');
        presetsBar.className = 'flatpickr-presets-bar';
        presetsBar.innerHTML = `
            <button type="button" class="flatpickr-btn-preset" data-preset="today">Today</button>
            <button type="button" class="flatpickr-btn-preset" data-preset="this_week">This week</button>
            <button type="button" class="flatpickr-btn-preset" data-preset="this_month">This month</button>
            <button type="button" class="flatpickr-btn-preset" data-preset="last_month">Last month</button>
            <button type="button" class="flatpickr-btn-preset" data-preset="last_3_months">Last 3 month</button>
        `;

        presetsBar.querySelectorAll('.flatpickr-btn-preset').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                applyPresetToFlatpickr(instance, btn.dataset.preset);
            });
        });

        instance.calendarContainer.insertBefore(presetsBar, instance.calendarContainer.firstChild);
    }

    // Attach bottom footer actions if not already present
    if (!instance.calendarContainer.querySelector('.flatpickr-footer-actions')) {
        const footer = document.createElement('div');
        footer.className = 'flatpickr-footer-actions d-flex align-items-center justify-content-between';
        footer.innerHTML = `
            <button type="button" class="btn btn-sm flatpickr-btn-clear">Clear</button>
            <button type="button" class="btn btn-sm flatpickr-btn-today">Today</button>
        `;

        footer.querySelector('.flatpickr-btn-today').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            applyPresetToFlatpickr(instance, 'today');
        });

        footer.querySelector('.flatpickr-btn-clear').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            applyPresetToFlatpickr(instance, 'clear');
        });

        instance.calendarContainer.appendChild(footer);
    }
};

window.attachFlatpickrFooter = attachFlatpickrFooter;

const originalFlatpickr = flatpickr;
const wrappedFlatpickr = function (target, config = {}) {
    const userOnReady = config.onReady;
    const userOnOpen = config.onOpen;

    config.onReady = function (selectedDates, dateStr, instance) {
        attachFlatpickrFooter(instance);
        if (typeof userOnReady === 'function') {
            userOnReady(selectedDates, dateStr, instance);
        } else if (Array.isArray(userOnReady)) {
            userOnReady.forEach((fn) => typeof fn === 'function' && fn(selectedDates, dateStr, instance));
        }
    };

    config.onOpen = function (selectedDates, dateStr, instance) {
        attachFlatpickrFooter(instance);
        if (typeof userOnOpen === 'function') {
            userOnOpen(selectedDates, dateStr, instance);
        } else if (Array.isArray(userOnOpen)) {
            userOnOpen.forEach((fn) => typeof fn === 'function' && fn(selectedDates, dateStr, instance));
        }
    };

    return originalFlatpickr(target, config);
};
Object.assign(wrappedFlatpickr, originalFlatpickr);
window.flatpickr = wrappedFlatpickr;

window.initReportDateRangePicker = function (target, options = {}) {
    return window.flatpickr(target, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'M d, Y',
        allowInput: true,
        ...options
    });
};

window.lucide = {
    createIcons: (options = {}) => createIcons({ icons, ...(options || {}) }),
    icons,
};
window.Swal = Swal;
window.DataTable = DataTable;
DataTable.use(jQuery);
DataTable.use(bootstrap);
select2(jQuery);
Alpine.start();

// =========================================================================
// DataTables Global Features: Column Visibility & Data Export
// =========================================================================
const escapeHtml = (text) => {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
};

const escapeCsv = (val) => {
    if (val === null || val === undefined) return '';
    const str = String(val).replace(/"/g, '""');
    return /[",\n\r]/.test(str) ? `"${str}"` : str;
};

const downloadFile = (content, fileName, mimeType) => {
    const blob = new Blob([content], { type: mimeType });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => {
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }, 150);
};

const getExportableColumns = (table) => {
    const cols = [];
    const count = table.columns().count();
    const settings = table.settings()[0];
    const columnDefs = settings?.columns || settings?.aoColumns || [];
    for (let i = 0; i < count; i++) {
        const headerEl = table.column(i).header();
        const headerText = headerEl ? jQuery(headerEl).text().trim() : '';
        const colDef = columnDefs[i] || {};
        const colName = String(colDef.name || colDef.data || colDef.mData || '').toLowerCase();
        if (headerText.toLowerCase() === 'actions' || headerText.toLowerCase() === 'action' || colName === 'action') {
            continue;
        }
        cols.push({
            index: i,
            title: headerText || colDef.title || `Column ${i + 1}`,
            dataProp: colDef.data || colDef.name || colDef.mData || i,
            name: String(colDef.name || colDef.data || '')
        });
    }
    return cols;
};

const formatCellValue = (val) => {
    if (val === null || val === undefined) return '';
    if (Array.isArray(val)) return val.join(', ');
    if (typeof val === 'object') {
        try { return JSON.stringify(val); } catch (e) { return String(val); }
    }
    return String(val).replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
};

const extractRowDataFromNode = (table, rowIndex, exportColumns) => {
    const rowData = [];
    exportColumns.forEach((col) => {
        const cell = table.cell(rowIndex, col.index).node();
        if (cell) {
            const $clone = jQuery(cell).clone();
            $clone.find('.tooltip, .dropdown-menu, .btn-group, script, style, .form-switch, input[type="checkbox"]').remove();
            const text = $clone.text().replace(/\s+/g, ' ').trim();
            rowData.push(text);
        } else {
            rowData.push('');
        }
    });
    return rowData;
};

const showExportSuccessToast = (rowCount, type) => {
    if (window.Swal) {
        window.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: type === 'Clipboard' ? `Copied ${rowCount} rows to clipboard!` : `Exported ${rowCount} rows to ${type}!`,
            showConfirmButton: false,
            timer: 2500
        });
    }
};

const executeExport = (format, title, headers, rows) => {
    const dateStr = new Date().toISOString().slice(0, 10);
    const safeTitle = title.replace(/[^\w-]/g, '_').toLowerCase();

    if (format === 'csv') {
        const csvContent = '\uFEFF' + [
            headers.map(escapeCsv).join(','),
            ...rows.map(r => r.map(escapeCsv).join(','))
        ].join('\r\n');
        downloadFile(csvContent, `${safeTitle}_export_${dateStr}.csv`, 'text/csv;charset=utf-8;');
        showExportSuccessToast(rows.length, 'CSV');
    } else if (format === 'excel') {
        let xml = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        xml += '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        xml += `<x:Name>${safeTitle.slice(0, 31)}</x:Name>`;
        xml += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        xml += '<style>th { background-color: #1b4fe0; color: #ffffff; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px 10px; } td { border: 1px solid #e2e8f0; padding: 5px 8px; }</style>';
        xml += '</head><body><table border="1">';
        xml += '<thead><tr>';
        headers.forEach(h => { xml += `<th>${escapeHtml(h)}</th>`; });
        xml += '</tr></thead><tbody>';
        rows.forEach(r => {
            xml += '<tr>';
            r.forEach(c => { xml += `<td>${escapeHtml(c)}</td>`; });
            xml += '</tr>';
        });
        xml += '</tbody></table></body></html>';
        downloadFile(xml, `${safeTitle}_export_${dateStr}.xls`, 'application/vnd.ms-excel;charset=utf-8');
        showExportSuccessToast(rows.length, 'Excel');
    } else if (format === 'copy') {
        const text = [
            headers.join('\t'),
            ...rows.map(r => r.join('\t'))
        ].join('\r\n');
        navigator.clipboard.writeText(text).then(() => {
            showExportSuccessToast(rows.length, 'Clipboard');
        }).catch(() => {
            alert(`Copied ${rows.length} rows to clipboard`);
        });
    } else if (format === 'print') {
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Please allow popups to print table data.');
            return;
        }
        const humanTitle = title.replace(/[-_]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const printHtml = `<!doctype html><html><head><meta charset="utf-8"><title>${escapeHtml(humanTitle)} - Print View</title><style>body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;padding:24px;color:#1e293b}.hdr{display:flex;justify-content:space-between;align-items:baseline;border-bottom:2px solid #1b4fe0;padding-bottom:8px;margin-bottom:16px}h1{margin:0;font-size:20px;font-weight:700}.meta{color:#64748b;font-size:12px;margin:0}table{width:100%;border-collapse:collapse;font-size:12px;margin-top:12px}th{background:#f8fafc;color:#475569;font-weight:700;text-transform:uppercase;font-size:11px;border:1px solid #cbd5e1;padding:8px 10px;text-align:left}td{border:1px solid #e2e8f0;padding:7px 10px;vertical-align:top}tr:nth-child(even) td{background:#f8fafc}@media print{body{padding:0}@page{margin:1.2cm}}</style></head><body><div class="hdr"><h1>${escapeHtml(humanTitle)}</h1><p class="meta">Exported on ${new Date().toLocaleString()} · <strong>${rows.length} records</strong></p></div><table><thead><tr>${headers.map(h => `<th>${escapeHtml(h)}</th>`).join('')}</tr></thead><tbody>${rows.map(r => `<tr>${r.map(c => `<td>${escapeHtml(c)}</td>`).join('')}</tr>`).join('')}</tbody></table></body></html>`;
        printWindow.document.write(printHtml);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => printWindow.print(), 350);
    }
};

const defaultExportFormats = [
    { format: 'excel', label: 'Excel Spreadsheet (.xls)', icon: 'file-spreadsheet', iconClass: 'text-success' },
    { format: 'csv', label: 'CSV Document (.csv)', icon: 'file-text', iconClass: 'text-primary' },
    { format: 'copy', label: 'Copy to Clipboard', icon: 'copy', iconClass: 'text-secondary' },
    { format: 'print', label: 'Print Table', icon: 'printer', iconClass: 'text-secondary', dividerBefore: true }
];

function userCanExport(format, tableId, settings) {
    if (!window.AppUser) return true;
    if (window.AppUser.isSuperAdmin) return true;

    const perms = Array.isArray(window.AppUser.permissions) ? window.AppUser.permissions : [];
    if (perms.includes('*') || perms.includes('export.all')) return true;

    let moduleName = '';
    const tableNode = settings ? (settings.table || settings.nTable) : (tableId ? document.getElementById(tableId) : null);
    if (tableNode) {
        moduleName = tableNode.getAttribute('data-export-module') || '';
    }
    if (!moduleName && settings && settings.oInit && settings.oInit.exportModule) {
        moduleName = settings.oInit.exportModule;
    }
    if (!moduleName && tableId) {
        moduleName = tableId.replace(/[-_]table$/i, '').toLowerCase();
    }

    const fmt = String(format || '').toLowerCase().trim();
    const mod = String(moduleName || '').toLowerCase().trim();

    // 1. Direct module-specific format permission (e.g. "users.export.excel")
    if (mod && fmt && perms.includes(`${mod}.export.${fmt}`)) {
        return true;
    }

    // 2. Global format permission (e.g. "export.excel")
    const hasGlobalFormat = fmt ? perms.includes(`export.${fmt}`) : false;

    // 3. Module-level export permission (e.g. "users.export")
    const hasModuleExport = mod ? perms.includes(`${mod}.export`) : false;

    // If user has both global format AND module export
    if (hasGlobalFormat && hasModuleExport) {
        return true;
    }

    // Check if user has ANY module-scoped export permissions (e.g. users.export, roles.export)
    const hasAnyModuleExport = perms.some(p => p.endsWith('.export'));

    // If user has format permission (e.g. export.excel):
    // Allow if user has no other module restrictions, or has this module's export
    if (hasGlobalFormat && (!hasAnyModuleExport || hasModuleExport)) {
        return true;
    }

    // Check if user has ANY specific format permissions (e.g. export.excel, export.csv)
    const hasAnyFormatExport = perms.some(p => p.startsWith('export.') && p !== 'export.all');

    // If user has module export (users.export) and no specific format restriction:
    if (hasModuleExport && !hasAnyFormatExport) {
        return true;
    }

    return false;
}

function buildToolbarElement(tableId, settings) {
    const cols = settings.columns || settings.aoColumns || [];

    let colvisItemsHtml = '';
    cols.forEach((col, idx) => {
        let title = (col.title || col.sTitle || '').replace(/<[^>]+>/g, '').trim();
        const tableNode = settings.table || settings.nTable;
        if (!title && tableNode) {
            title = jQuery(tableNode).find('thead th').eq(idx).text().trim();
        }
        if (!title && typeof (col.data || col.mData) === 'string') {
            title = String(col.data || col.mData).replace(/[-_]/g, ' ');
        }
        if (!title) title = `Column ${idx + 1}`;
        const savedVis = localStorage.getItem(`dt_colvis_${tableId}_${idx}`);
        const isChecked = savedVis !== null ? (savedVis === '1') : (col.visible !== false && col.bVisible !== false);
        colvisItemsHtml += `
            <label class="dropdown-item d-flex align-items-center gap-2 px-3 py-1.5 mb-0" style="cursor: pointer; font-size: 13px;">
                <input type="checkbox" class="form-check-input mt-0 dt-colvis-checkbox" data-table-id="${tableId}" data-col-idx="${idx}" ${isChecked ? 'checked' : ''} style="cursor: pointer;">
                <span>${escapeHtml(title)}</span>
            </label>
        `;
    });

    // Check table-configured formats if present, e.g. data-export-formats="excel,csv"
    let formats = defaultExportFormats;
    const tableNode = settings.table || settings.nTable;
    if (tableNode && tableNode.getAttribute('data-export-formats')) {
        const allowedFormats = tableNode.getAttribute('data-export-formats').split(',').map(f => f.trim().toLowerCase());
        formats = defaultExportFormats.filter(f => allowedFormats.includes(f.format));
    }

    const permittedFormats = formats.filter(item => userCanExport(item.format, tableId, settings));

    let exportDropdownHtml = '';
    if (permittedFormats.length > 0) {
        let itemsHtml = '';
        permittedFormats.forEach((item, index) => {
            if (item.dividerBefore && index > 0) {
                itemsHtml += `<li><hr class="dropdown-divider my-1"></li>`;
            }
            itemsHtml += `
                <li>
                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 dt-export-action" data-table-id="${tableId}" data-format="${escapeHtml(item.format)}">
                        <i data-lucide="${escapeHtml(item.icon)}" class="${escapeHtml(item.iconClass || 'text-secondary')}" style="width: 15px; height: 15px;"></i>
                        <span>${escapeHtml(item.label)}</span>
                    </button>
                </li>
            `;
        });

        exportDropdownHtml = `
            <!-- Export Dropdown -->
            <div class="dropdown dt-export-dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-2 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Export table data">
                    <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                    <span>Export</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2 dt-export-menu" style="min-width: 190px;">
                    <li><span class="dropdown-header text-uppercase small fw-bold text-secondary px-3 py-1">Export Data</span></li>
                    ${itemsHtml}
                </ul>
            </div>
        `;
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'dt-action-buttons d-inline-flex align-items-center gap-2 me-2';
    wrapper.setAttribute('data-dt-actions-for', tableId);
    wrapper.innerHTML = `
        <!-- Column Visibility Dropdown -->
        <div class="dropdown dt-colvis-dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-2 shadow-sm" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Customize visible columns">
                <i data-lucide="columns-3" style="width: 16px; height: 16px;"></i>
                <span>Columns</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-sm p-0 dt-colvis-menu" style="min-width: 220px;">
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom dt-colvis-header">
                    <span class="text-uppercase small fw-bold text-secondary" style="font-size: 11px;">Visible Columns</span>
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none dt-colvis-reset text-primary" data-table-id="${tableId}" style="font-size: 11px; font-weight: 600;">Reset All</button>
                </div>
                <div class="dt-colvis-list py-1" style="max-height: 280px; overflow-y: auto;">
                    ${colvisItemsHtml}
                </div>
            </div>
        </div>

        ${exportDropdownHtml}
    `;

    setTimeout(() => {
        createIcons({ icons });
    }, 0);

    return wrapper;
}

function applySavedColumnVisibility(tableNode, customApi) {
    const $table = jQuery(tableNode);
    const api = customApi || $table.DataTable();
    const settings = api.settings()[0];
    if (!settings) return;
    const tableId = settings.tableId || settings.sTableId || (settings.nTable ? settings.nTable.id : '') || (settings.table ? settings.table.id : '') || $table.attr('id');
    if (!tableId) return;

    const count = api.columns().count();
    let changed = false;
    for (let i = 0; i < count; i++) {
        const savedVis = localStorage.getItem(`dt_colvis_${tableId}_${i}`);
        if (savedVis !== null) {
            const shouldBeVisible = (savedVis === '1');
            if (api.column(i).visible() !== shouldBeVisible) {
                api.column(i).visible(shouldBeVisible, false);
                changed = true;
            }
        }
    }
    if (changed) {
        api.columns.adjust().draw(false);
    }

    const $dropdown = jQuery(`.dt-action-buttons[data-dt-actions-for="${tableId}"] .dt-colvis-menu`);
    if ($dropdown.length) {
        for (let i = 0; i < count; i++) {
            const savedVis = localStorage.getItem(`dt_colvis_${tableId}_${i}`);
            if (savedVis !== null) {
                $dropdown.find(`.dt-colvis-checkbox[data-col-idx="${i}"]`).prop('checked', savedVis === '1');
            }
        }
    }
}

function setupDataTableExtensions(tableNode, customApi) {
    const $table = jQuery(tableNode);
    const api = customApi || $table.DataTable();
    const settings = api.settings()[0];
    if (!settings) return;
    const tableId = settings.tableId || settings.sTableId || (settings.nTable ? settings.nTable.id : '') || (settings.table ? settings.table.id : '') || $table.attr('id') || 'table_' + Math.floor(Math.random() * 10000);

    applySavedColumnVisibility(tableNode, api);

    const $container = $table.closest('.dt-container, .dataTables_wrapper');
    if ($container.find(`.dt-action-buttons[data-dt-actions-for="${tableId}"]`).length) {
        return;
    }
    const toolbar = buildToolbarElement(tableId, settings);
    const $target = $container.find('.dt-layout-end, .dataTables_filter');
    if ($target.length) {
        $target.addClass('d-flex align-items-center gap-2 flex-wrap');
        $target.prepend(toolbar);
    } else {
        $table.before(toolbar);
    }
    createIcons({ icons });
}

const actionsToolbarFeature = function (settings) {
    const tableId = settings.tableId || settings.sTableId || (settings.nTable ? settings.nTable.id : '') || (settings.table ? settings.table.id : '') || 'table_' + Math.floor(Math.random() * 10000);
    return buildToolbarElement(tableId, settings);
};

DataTable.feature.register('actionsToolbar', actionsToolbarFeature, 'B');
DataTable.feature.register('B', actionsToolbarFeature, 'B');

if (DataTable.ext && Array.isArray(DataTable.ext.feature)) {
    DataTable.ext.feature.push({
        cFeature: 'B',
        fnInit: actionsToolbarFeature
    });
}

const defaultDom = "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>";

DataTable.defaults.pagingType = 'full_numbers';
DataTable.defaults.dom = defaultDom;
delete DataTable.defaults.layout;

DataTable.defaults.language = Object.assign(DataTable.defaults.language || {}, {
    lengthMenu: 'Show _MENU_ entries',
    paginate: {
        first: 'First',
        previous: 'Previous',
        next: 'Next',
        last: 'Last'
    }
});

DataTable.defaults.stateSave = true;

DataTable.defaults.stateSaveParams = function (settings, data) {
    if (data && data.search) {
        data.search.search = '';
    }
    if (data) {
        data.start = 0;
    }
};

DataTable.defaults.stateLoadCallback = function (settings) {
    const tableId = settings.tableId || settings.sTableId || (settings.nTable ? settings.nTable.id : '') || (settings.table ? settings.table.id : '');
    const count = settings.columns ? settings.columns.length : 0;
    const nativeKey = 'DataTables_' + (settings.unique || tableId) + '_' + window.location.pathname;
    let state = {};
    try {
        const raw = localStorage.getItem(nativeKey);
        if (raw) state = JSON.parse(raw);
    } catch (e) {}

    if (!state.columns) {
        state.columns = [];
    }
    state.time = Date.now();

    for (let i = 0; i < count; i++) {
        if (!state.columns[i]) {
            state.columns[i] = {};
        }
        const savedVis = localStorage.getItem(`dt_colvis_${tableId}_${i}`);
        if (savedVis !== null) {
            state.columns[i].visible = (savedVis === '1');
        }
    }
    return state;
};

function renderRowNumber(data, type, row, meta) {
    let start = 0;
    if (meta && meta.settings) {
        if (typeof meta.settings.displayStart === 'number') {
            start = meta.settings.displayStart;
        } else if (typeof meta.settings._iDisplayStart === 'number') {
            start = meta.settings._iDisplayStart;
        } else if (typeof meta.settings._displayStart === 'number') {
            start = meta.settings._displayStart;
        }
    }
    const rowIdx = (meta && typeof meta.row === 'number' && !isNaN(meta.row)) ? meta.row : 0;
    return start + rowIdx + 1;
}

window.formatReportDate = function (val) {
    if (!val) return '<span class="text-muted">-</span>';
    const raw = String(val).trim();
    const parts = raw.split('T')[0].split('-');
    if (parts.length === 3) {
        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        if (!isNaN(d.getTime())) {
            const formatted = d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
            return `<span class="report-date-badge d-inline-flex align-items-center"><i data-lucide="calendar" class="text-muted me-1.5" style="width: 14px; height: 14px; flex-shrink: 0;"></i><span>${formatted}</span></span>`;
        }
    }
    const d = new Date(val);
    if (!isNaN(d.getTime())) {
        const formatted = d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
        return `<span class="report-date-badge d-inline-flex align-items-center"><i data-lucide="calendar" class="text-muted me-1.5" style="width: 14px; height: 14px; flex-shrink: 0;"></i><span>${formatted}</span></span>`;
    }
    return `<span class="report-date-badge d-inline-flex align-items-center"><span>${val}</span></span>`;
};

window.formatReportDateText = function (val) {
    if (!val) return '-';
    const raw = String(val).trim();
    const parts = raw.split('T')[0].split('-');
    if (parts.length === 3) {
        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        if (!isNaN(d.getTime())) {
            return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
        }
    }
    const d = new Date(val);
    return isNaN(d.getTime()) ? val : d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
};

window.renderRowNumber = renderRowNumber;
window.setupDataTableExtensions = setupDataTableExtensions;
window.applySavedColumnVisibility = applySavedColumnVisibility;
window.userCanExport = userCanExport;
window.defaultExportFormats = defaultExportFormats;
window.BizLiteDataTable = {
    setupExtensions: setupDataTableExtensions,
    applySavedColumnVisibility: applySavedColumnVisibility,
    userCanExport: userCanExport,
    defaultExportFormats: defaultExportFormats,
    renderRowNumber: renderRowNumber
};

jQuery(document).on('preInit.dt', function (e, settings) {
    if (!settings) return;
    const tableNode = settings.nTable || settings.table;
    if (!tableNode) return;
    const api = new DataTable.Api(settings);
    applySavedColumnVisibility(tableNode, api);
});

jQuery(document).on('init.dt', function (e, settings) {
    if (!settings) return;
    const tableNode = settings.nTable || settings.table;
    if (!tableNode) return;
    const api = new DataTable.Api(settings);
    applySavedColumnVisibility(tableNode, api);
    setupDataTableExtensions(tableNode, api);
});

jQuery(document).on('change', '.dt-colvis-checkbox', function () {
    const $cb = jQuery(this);
    const tableId = $cb.data('table-id');
    const colIdx = parseInt($cb.data('col-idx'), 10);
    const isChecked = $cb.is(':checked');
    const table = jQuery('#' + tableId).DataTable();
    table.column(colIdx).visible(isChecked);
    localStorage.setItem(`dt_colvis_${tableId}_${colIdx}`, isChecked ? '1' : '0');
    createIcons({ icons });
});

jQuery(document).on('click', '.dt-colvis-reset', function (e) {
    e.preventDefault();
    const tableId = jQuery(this).data('table-id');
    const table = jQuery('#' + tableId).DataTable();
    const $dropdown = jQuery(this).closest('.dt-colvis-menu');
    $dropdown.find('.dt-colvis-checkbox').each(function () {
        const colIdx = parseInt(jQuery(this).data('col-idx'), 10);
        $(this).prop('checked', true);
        table.column(colIdx).visible(true, false);
        localStorage.removeItem(`dt_colvis_${tableId}_${colIdx}`);
    });
    table.columns.adjust().draw(false);
    createIcons({ icons });
});

jQuery(document).on('click', '.dt-export-action', function (e) {
    e.preventDefault();
    const tableId = jQuery(this).data('table-id');
    const format = jQuery(this).data('format');
    const table = jQuery('#' + tableId).DataTable();
    const settings = table.settings()[0];

    if (!userCanExport(format, tableId, settings)) {
        const errorMsg = `You do not have permission to export as ${String(format).toUpperCase()}.`;
        if (window.Swal) {
            window.Swal.fire({
                icon: 'error',
                title: 'Export Not Permitted',
                text: errorMsg
            });
        } else {
            alert(errorMsg);
        }
        return;
    }

    const exportColumns = getExportableColumns(table).filter(c => table.column(c.index).visible());
    const headers = exportColumns.map(c => c.title);
    const isServerSide = table.init().serverSide;

    if (isServerSide && (format === 'excel' || format === 'csv')) {
        if (window.Swal) {
            window.Swal.fire({
                title: 'Preparing Export',
                text: 'Fetching data from server...',
                allowOutsideClick: false,
                didOpen: () => window.Swal.showLoading()
            });
        }
        const ajaxParams = Object.assign({}, table.ajax.params() || {}, {
            start: 0,
            length: -1,
            is_export: 1,
            export_format: format
        });
        const ajaxUrl = typeof table.ajax.url === 'function' ? table.ajax.url() : (table.init().ajax?.url || table.init().ajax || window.location.href);
        const csrfToken = jQuery('meta[name="csrf-token"]').attr('content');

        jQuery.ajax({
            url: ajaxUrl,
            type: 'GET',
            data: ajaxParams,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (res) {
                if (window.Swal) window.Swal.close();
                const rawItems = res.data || [];
                const rows = rawItems.map((item, rIdx) => {
                    return exportColumns.map(col => {
                        if (col.name === 'row_number' || col.title === '#' || !col.dataProp) {
                            return String(rIdx + 1);
                        }
                        const val = item[col.dataProp];
                        if (col.dataProp === 'name' && item.email) {
                            return `${item.name} (${item.email})`;
                        }
                        return formatCellValue(val);
                    });
                });
                executeExport(format, tableId, headers, rows);
            },
            error: function (xhr) {
                if (window.Swal) window.Swal.close();
                if (xhr && xhr.status === 403) {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'You do not have permission to export this data.';
                    if (window.Swal) {
                        window.Swal.fire({ icon: 'error', title: 'Export Forbidden', text: msg });
                    } else {
                        alert(msg);
                    }
                    return;
                }
                const rows = [];
                table.rows({ search: 'applied' }).every(function () {
                    rows.push(extractRowDataFromNode(table, this.index(), exportColumns));
                });
                executeExport(format, tableId, headers, rows);
            }
        });
    } else {
        const rows = [];
        table.rows({ search: 'applied' }).every(function () {
            rows.push(extractRowDataFromNode(table, this.index(), exportColumns));
        });
        executeExport(format, tableId, headers, rows);
    }
});

// =========================================================================
// Universal Filter Toggle Feature (All Modules)
// =========================================================================
function syncFilterButtonState($btn, isVisible) {
    if (isVisible) {
        $btn.addClass('active btn-primary text-white').removeClass('btn-outline-secondary');
        $btn.attr('aria-expanded', 'true');
    } else {
        $btn.removeClass('active btn-primary text-white').addClass('btn-outline-secondary');
        $btn.attr('aria-expanded', 'false');
    }
}

function initFilterToggle() {
    const isFilterVisible = localStorage.getItem('twa_show_filter') !== '0';

    jQuery('.btn-filter-toggle').each(function () {
        const $btn = jQuery(this);
        const targetSelector = $btn.data('target') || '.filter-section';
        const $target = jQuery(targetSelector);
        if (!$target.length) return;

        if (isFilterVisible) {
            $target.removeClass('d-none').show();
            syncFilterButtonState($btn, true);
        } else {
            $target.addClass('d-none').hide();
            syncFilterButtonState($btn, false);
        }
    });

    createIcons({ icons });
}

jQuery(document).on('click', '.btn-filter-toggle, .btn-filter-toggle *', function (e) {
    e.preventDefault();
    const $btn = jQuery(this).closest('.btn-filter-toggle');
    const targetSelector = $btn.data('target') || '.filter-section';
    const $target = jQuery(targetSelector);
    if (!$target.length) return;

    const isHidden = $target.is(':hidden') || $target.hasClass('d-none');
    if (isHidden) {
        $target.removeClass('d-none').hide().slideDown(180, function () {
            if (window.DataTable && window.DataTable.tables) {
                DataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
        syncFilterButtonState($btn, true);
        localStorage.setItem('twa_show_filter', '1');
    } else {
        $target.slideUp(180, function () {
            $target.addClass('d-none');
            if (window.DataTable && window.DataTable.tables) {
                DataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
        syncFilterButtonState($btn, false);
        localStorage.setItem('twa_show_filter', '0');
    }
});

// =========================================================================
// Universal Definition Toggle Feature (All Modules)
// =========================================================================
function syncDefinitionButtonState($btn, isVisible) {
    if (isVisible) {
        $btn.addClass('active btn-primary text-white').removeClass('btn-outline-secondary');
        $btn.attr('aria-expanded', 'true');
    } else {
        $btn.removeClass('active btn-primary text-white').addClass('btn-outline-secondary');
        $btn.attr('aria-expanded', 'false');
    }
}

function initDefinitionToggle() {
    const isDefVisible = localStorage.getItem('twa_show_definition') === '1';

    jQuery('.btn-definition-toggle').each(function () {
        const $btn = jQuery(this);
        const targetSelector = $btn.data('target') || '.definition-section';
        const $target = jQuery(targetSelector);
        if (!$target.length) return;

        if (isDefVisible) {
            $target.removeClass('d-none').show();
            syncDefinitionButtonState($btn, true);
        } else {
            $target.addClass('d-none').hide();
            syncDefinitionButtonState($btn, false);
        }
    });

    createIcons({ icons });
}

jQuery(document).on('click', '.btn-definition-toggle, .btn-definition-toggle *', function (e) {
    e.preventDefault();
    const $btn = jQuery(this).closest('.btn-definition-toggle');
    const targetSelector = $btn.data('target') || '.definition-section';
    const $target = jQuery(targetSelector);
    if (!$target.length) return;

    const isHidden = $target.is(':hidden') || $target.hasClass('d-none');
    if (isHidden) {
        $target.removeClass('d-none').hide().slideDown(180, function () {
            if (window.DataTable && window.DataTable.tables) {
                DataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
        syncDefinitionButtonState($btn, true);
        localStorage.setItem('twa_show_definition', '1');
    } else {
        $target.slideUp(180, function () {
            $target.addClass('d-none');
            if (window.DataTable && window.DataTable.tables) {
                DataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
        syncDefinitionButtonState($btn, false);
        localStorage.setItem('twa_show_definition', '0');
    }
});

jQuery(document).on('click', '.btn-close-definition', function (e) {
    e.preventDefault();
    const $target = jQuery(this).closest('.definition-section, .report-purpose-card');
    if (!$target.length) return;

    $target.slideUp(180, function () {
        $target.addClass('d-none');
        if (window.DataTable && window.DataTable.tables) {
            DataTable.tables({ visible: true, api: true }).columns.adjust();
        }
    });

    const targetId = $target.attr('id');
    const $toggleBtn = targetId
        ? jQuery(`.btn-definition-toggle[data-target="#${targetId}"]`)
        : jQuery('.btn-definition-toggle');
    syncDefinitionButtonState($toggleBtn, false);
    localStorage.setItem('twa_show_definition', '0');
});

jQuery(function () {
    initFilterToggle();
    initDefinitionToggle();
});

const palettes = { blue: '#2563EB', sky: '#0EA5E9', purple: '#7C3AED', emerald: '#059669', royal: '#1D4ED8', orange: '#EA580C', cyan: '#06B6D4', mint: '#10B981', coral: '#F43F5E' };
const fontSizes = { small: '12px', normal: '14px', large: '16px' };
const themeDefaults = { mode: 'light', layout: 'vertical', container: 'fluid', sidebar: '0', primary: '#1b4fe0', bgPage: '#eeefff', font: 'Inter', radius: '8px', fontSize: 'normal' };
const availableFonts = ['Inter', 'Roboto', 'Poppins', 'Public Sans'];
const khmerFont = "'Nokora', 'Noto Sans Khmer', 'Noto Sans', sans-serif";
const isColor = (value) => /^#[0-9a-f]{6}$/i.test(value || '');
const darken = (hex) => `#${[0, 2, 4].map((offset) => Math.max(0, Math.round(parseInt(hex.slice(offset + 1, offset + 3), 16) * .7)).toString(16).padStart(2, '0')).join('')}`;

const applyThemePreferences = () => {
    const root = document.documentElement;
    const mode = localStorage.getItem('tc_mode') === 'dark' ? 'dark' : themeDefaults.mode;
    const savedPrimary = localStorage.getItem('tc_primary');
    const primary = savedPrimary || themeDefaults.primary;
    const font = localStorage.getItem('tc_font') || themeDefaults.font;
    const radius = localStorage.getItem('tc_rounding') || themeDefaults.radius;
    const fontSize = localStorage.getItem('tc_font_size') || themeDefaults.fontSize;
    const layout = localStorage.getItem('tc_layout') || themeDefaults.layout;
    const container = localStorage.getItem('tc_container') || themeDefaults.container;
    const lang = localStorage.getItem('twa_lang') || 'en';
    root.lang = lang;
    root.dataset.lang = lang;
    root.dataset.theme = mode;
    root.dataset.bsTheme = mode;
    root.setAttribute('data-theme', mode);
    root.setAttribute('data-bs-theme', mode);
    root.dataset.fontSize = fontSize;
    root.setAttribute('data-font-size', fontSize);
    const activeFontSize = fontSizes[fontSize] || '14px';
    root.style.setProperty('--fs-base', activeFontSize);

    const savedBg = localStorage.getItem('tc_bg_page');
    const bgPage = isColor(savedBg) ? savedBg : themeDefaults.bgPage;
    if (mode === 'light') {
        root.style.setProperty('--bg-page', bgPage);
        root.style.setProperty('--bs-body-bg', bgPage);
    } else {
        root.style.removeProperty('--bg-page');
        root.style.removeProperty('--bs-body-bg');
    }

    root.style.colorScheme = mode;
    root.dataset.layout = layout === 'horizontal' ? 'horizontal' : 'vertical';
    root.setAttribute('data-layout', root.dataset.layout);
    root.dataset.container = container === 'container' ? 'container' : 'fluid';
    root.setAttribute('data-container', root.dataset.container);
    root.dataset.sidebar = localStorage.getItem('ks_col') === '1' ? 'collapsed' : 'expanded';
    const activePrimary = isColor(savedPrimary) ? primary : themeDefaults.primary;
    root.style.setProperty('--pri', activePrimary);
    root.style.setProperty('--pri-dark', darken(activePrimary));
    root.style.setProperty('--pri-soft', `${activePrimary}1a`);
    root.style.setProperty('--bs-primary', activePrimary);
    root.style.setProperty('--bs-link-color', activePrimary);
    root.style.setProperty('--bs-link-hover-color', darken(activePrimary));
    root.style.setProperty('--bs-pagination-color', activePrimary);
    root.style.setProperty('--bs-pagination-hover-color', darken(activePrimary));
    root.style.setProperty('--bs-pagination-focus-color', darken(activePrimary));
    root.style.setProperty('--bs-pagination-active-bg', activePrimary);
    root.style.setProperty('--bs-pagination-active-border-color', activePrimary);
    const prR = parseInt(activePrimary.slice(1, 3), 16);
    const prG = parseInt(activePrimary.slice(3, 5), 16);
    const prB = parseInt(activePrimary.slice(5, 7), 16);
    root.style.setProperty('--bs-primary-rgb', `${prR}, ${prG}, ${prB}`);
    if (lang === 'km') {
        root.style.setProperty('--font', khmerFont);
        root.style.setProperty('--font-kh', khmerFont);
        root.style.setProperty('--bs-body-font-family', khmerFont);
    } else if (availableFonts.includes(font)) {
        root.style.setProperty('--font', `'${font}', sans-serif`);
        root.style.removeProperty('--bs-body-font-family');
    }
    const radNum = parseFloat(radius) || 0;
    root.dataset.radius = radius;
    root.setAttribute('data-radius', radius);
    if (/^\d+(\.\d+)?px$/.test(radius || '')) {
        const radSm = `${Math.round(radNum * 0.75)}px`;
        const radLg = `${Math.max(radNum, Math.round(radNum * 1.5 + 2))}px`;
        const radXl = `${Math.max(radNum, Math.round(radNum * 2 + 4))}px`;
        root.style.setProperty('--rad-s', radNum === 0 ? '0px' : radSm);
        root.style.setProperty('--rad', radNum === 0 ? '0px' : radLg);
        root.style.setProperty('--bs-border-radius', radius);
        root.style.setProperty('--bs-border-radius-sm', radNum === 0 ? '0px' : radSm);
        root.style.setProperty('--bs-border-radius-lg', radNum === 0 ? '0px' : radLg);
        root.style.setProperty('--bs-border-radius-xl', radNum === 0 ? '0px' : radXl);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    applyThemePreferences();
    const shell = document.querySelector('[data-app-shell]');
    const sidebar = document.querySelector('[data-app-sidebar]');
    const sidebarBackdrop = document.querySelector('[data-sidebar-backdrop]');
    const themeDrawer = document.querySelector('[data-theme-drawer]');
    const themeBackdrop = document.querySelector('[data-theme-drawer-backdrop]');
    const drawerTrigger = document.querySelector('[data-theme-drawer-toggle]');
    const isMobile = () => window.innerWidth <= 991;
    const closeSidebar = () => { sidebar?.classList.remove('is-open'); sidebarBackdrop?.classList.remove('is-visible'); };
    const closeThemeDrawer = () => {
        themeDrawer?.classList.remove('is-open');
        themeDrawer?.setAttribute('aria-hidden', 'true');
        themeBackdrop?.classList.remove('is-visible');
        document.querySelectorAll('[data-theme-drawer-toggle]').forEach((trigger) => trigger.setAttribute('aria-expanded', 'false'));
    };
    const openThemeDrawer = () => {
        themeDrawer?.classList.add('is-open');
        themeDrawer?.setAttribute('aria-hidden', 'false');
        themeBackdrop?.classList.add('is-visible');
        document.querySelectorAll('[data-theme-drawer-toggle]').forEach((trigger) => trigger.setAttribute('aria-expanded', 'true'));
        closePanels();
    };
    const closePanels = () => document.querySelectorAll('[data-panel-toggle][aria-expanded="true"]').forEach((button) => { button.setAttribute('aria-expanded', 'false'); document.getElementById(button.dataset.panelToggle)?.setAttribute('hidden', ''); });

    // Panel Toggle Handler (Organization, Notifications, Popovers)
    document.querySelectorAll('[data-panel-toggle]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const panelId = button.dataset.panelToggle;
            const panel = document.getElementById(panelId);
            if (!panel) return;
            const isExpanded = button.getAttribute('aria-expanded') === 'true';
            closePanels();
            if (!isExpanded) {
                button.setAttribute('aria-expanded', 'true');
                panel.removeAttribute('hidden');
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }
        });
    });

    if (localStorage.getItem('ks_col') === '1') shell?.classList.add('is-sidebar-collapsed');
    const syncThemeControls = () => {
        const mode = document.documentElement.dataset.theme;
        const layout = document.documentElement.dataset.layout;
        const storedPrimary = localStorage.getItem('tc_primary');
        const primary = isColor(storedPrimary) ? storedPrimary : themeDefaults.primary;
        const font = localStorage.getItem('tc_font') || themeDefaults.font;
        const radius = localStorage.getItem('tc_rounding') || themeDefaults.radius;
        const fontSize = document.documentElement.dataset.fontSize || localStorage.getItem('tc_font_size') || themeDefaults.fontSize;
        document.querySelectorAll('[data-theme-mode]').forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.themeMode === mode)));
        document.querySelectorAll('[data-layout]').forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.layout === layout)));
        document.querySelectorAll('[data-font-size]').forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.fontSize === fontSize)));
        document.querySelectorAll('[data-primary-color]').forEach((button) => button.setAttribute('aria-pressed', String(palettes[button.dataset.primaryColor] === primary.toUpperCase())));
        const colorInput = document.querySelector('[data-primary-color-input]'); if (colorInput) colorInput.value = primary;
        const storedBg = localStorage.getItem('tc_bg_page') || themeDefaults.bgPage;
        const currentBg = (isColor(storedBg) ? storedBg : themeDefaults.bgPage).toLowerCase();
        document.querySelectorAll('[data-bg-preset]').forEach((button) => {
            button.setAttribute('aria-pressed', String((button.dataset.bgPreset || '').toLowerCase() === currentBg));
        });
        const bgInput = document.querySelector('[data-bg-color-input]');
        if (bgInput) bgInput.value = currentBg;
        const bgHexInput = document.querySelector('[data-bg-color-hex]');
        if (bgHexInput) bgHexInput.value = currentBg;
        const fontInput = document.querySelector('[data-theme-font]');
        if (fontInput) {
            fontInput.value = font;
            if (window.jQuery && jQuery(fontInput).hasClass('select2-hidden-accessible')) {
                jQuery(fontInput).val(font).trigger('change.select2');
            }
        }
        const container = document.documentElement.dataset.container || localStorage.getItem('tc_container') || themeDefaults.container;
        document.querySelectorAll('[data-container]').forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.container === container)));

        document.querySelectorAll('[data-theme-radius]').forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.themeRadius === radius)));
        const radiusRange = document.querySelector('[data-theme-radius-range]');
        if (radiusRange) radiusRange.value = parseFloat(radius) || 0;
        const radiusBadge = document.querySelector('#themeRadiusBadge');
        if (radiusBadge) radiusBadge.textContent = radius;

        if (window.lucide) {
            window.lucide.createIcons({ icons });
        }
        const themeToggleSwitch = document.querySelector('#themeToggleSwitch');
        if (themeToggleSwitch) themeToggleSwitch.checked = (mode === 'dark');
        const themeToggleBtn = document.querySelector('#themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.setAttribute('aria-checked', String(mode === 'dark'));
            themeToggleBtn.setAttribute('title', mode === 'dark' ? 'Dark mode active (Click for Light mode)' : 'Light mode active (Click for Dark mode)');
            themeToggleBtn.setAttribute('aria-label', mode === 'dark' ? 'Dark mode active (Click for Light mode)' : 'Light mode active (Click for Dark mode)');
        }
    };
    syncThemeControls();

    // Appearance Toggle Switch (Not Dropdown)
    const toggleTheme = () => {
        const currentMode = (document.documentElement.dataset.theme === 'dark' || document.documentElement.getAttribute('data-theme') === 'dark') ? 'dark' : 'light';
        const nextMode = currentMode === 'dark' ? 'light' : 'dark';
        localStorage.setItem('tc_mode', nextMode);
        applyThemePreferences();
        syncThemeControls();
    };

    document.querySelector('#themeToggleBtn')?.addEventListener('click', (event) => {
        event.preventDefault();
        toggleTheme();
    });

    document.querySelector('#themeToggleSwitch')?.addEventListener('change', (event) => {
        const nextMode = event.target.checked ? 'dark' : 'light';
        localStorage.setItem('tc_mode', nextMode);
        applyThemePreferences();
        syncThemeControls();
    });

    // Organization Switching
    const savedOrg = localStorage.getItem('twa_org') || (document.cookie.match(/(?:^|; )twa_org=([^;]*)/)?.[1] ? decodeURIComponent(document.cookie.match(/(?:^|; )twa_org=([^;]*)/)[1]) : 'NonOil Report');
    const updateOrgDisplay = (orgName) => {
        const label = document.querySelector('.topbar-org-label');
        if (label) label.textContent = orgName;
        document.querySelectorAll('.org-switch-item').forEach((item) => {
            const check = item.querySelector('.org-check');
            const isMatch = item.dataset.org === orgName;
            if (check) check.style.display = isMatch ? 'inline-block' : 'none';
            item.classList.toggle('active', isMatch);
        });
    };
    updateOrgDisplay(savedOrg);

    document.querySelectorAll('.org-switch-item').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const orgName = button.dataset.org;
            const prevOrg = localStorage.getItem('twa_org') || (document.cookie.match(/(?:^|; )twa_org=([^;]*)/)?.[1] ? decodeURIComponent(document.cookie.match(/(?:^|; )twa_org=([^;]*)/)[1]) : 'NonOil Report');
            localStorage.setItem('twa_org', orgName);
            document.cookie = `twa_org=${encodeURIComponent(orgName)};path=/;max-age=31536000;SameSite=Lax`;
            updateOrgDisplay(orgName);
            closePanels();
            document.dispatchEvent(new CustomEvent('twa:org-changed', { detail: { organization: orgName } }));
            window.showToast?.(`Organization switched to ${orgName}`, 'info');
            if (prevOrg && prevOrg !== orgName) {
                setTimeout(() => {
                    window.location.reload();
                }, 350);
            }
        });
    });

    // Notifications Feed Integration
    const markAllReadBtn = document.querySelector('#markAllReadBtn');
    if (markAllReadBtn) {
        if (localStorage.getItem('twa_notifs_read') === 'true') {
            document.querySelector('#notifBadge')?.classList.add('d-none');
            const countBadge = document.querySelector('#notifCountBadge');
            if (countBadge) countBadge.textContent = '0 new';
            document.querySelectorAll('.notification-item.unread').forEach((item) => item.classList.remove('unread'));
        }

        markAllReadBtn.addEventListener('click', () => {
            document.querySelector('#notifBadge')?.classList.add('d-none');
            const countBadge = document.querySelector('#notifCountBadge');
            if (countBadge) countBadge.textContent = '0 new';
            document.querySelectorAll('.notification-item.unread').forEach((item) => item.classList.remove('unread'));
            localStorage.setItem('twa_notifs_read', 'true');
            window.showToast?.('All notifications marked as read', 'success');
        });
    }

    // Language Toggle (Concept Toggle)
    const savedLang = localStorage.getItem('twa_lang') || 'en';
    const updateLangDisplay = (lang) => {
        document.documentElement.lang = lang;
        document.documentElement.dataset.lang = lang;
        if (lang === 'km') {
            document.documentElement.style.setProperty('--font', khmerFont);
            document.documentElement.style.setProperty('--font-kh', khmerFont);
            document.documentElement.style.setProperty('--bs-body-font-family', khmerFont);
        } else {
            const font = localStorage.getItem('tc_font') || themeDefaults.font;
            if (availableFonts.includes(font)) {
                document.documentElement.style.setProperty('--font', `'${font}', sans-serif`);
                document.documentElement.style.removeProperty('--bs-body-font-family');
            }
        }
        document.querySelectorAll('.lang-option-btn').forEach((btn) => {
            const isActive = btn.dataset.lang === lang;
            btn.classList.toggle('active', isActive);
            btn.classList.toggle('text-secondary', !isActive);
        });
        document.querySelectorAll('.lang-stage-en').forEach((el) => {
            el.style.setProperty('display', lang === 'en' ? 'inline-flex' : 'none', 'important');
        });
        document.querySelectorAll('.lang-stage-km').forEach((el) => {
            el.style.setProperty('display', lang === 'km' ? 'inline-flex' : 'none', 'important');
        });
        const langToggle = document.querySelector('#languageToggle');
        if (langToggle) {
            langToggle.setAttribute('title', lang === 'en' ? 'Language: English (Click for ភាសាខ្មែរ)' : 'Language: ភាសាខ្មែរ (Click for English)');
            langToggle.setAttribute('aria-label', lang === 'en' ? 'Language: English (Click for ភាសាខ្មែរ)' : 'Language: ភាសាខ្មែរ (Click for English)');
        }
    };
    updateLangDisplay(savedLang);

    document.querySelectorAll('.lang-option-btn').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.stopPropagation();
            const lang = btn.dataset.lang;
            localStorage.setItem('twa_lang', lang);
            document.cookie = `twa_locale=${lang};path=/;max-age=31536000;SameSite=Lax`;
            window.location.href = `/locale/${lang}`;
        });
    });

    document.querySelector('#languageToggle')?.addEventListener('click', (event) => {
        event.preventDefault();
        const currentLang = localStorage.getItem('twa_lang') || 'en';
        const nextLang = currentLang === 'en' ? 'km' : 'en';
        localStorage.setItem('twa_lang', nextLang);
        document.cookie = `twa_locale=${nextLang};path=/;max-age=31536000;SameSite=Lax`;
        window.location.href = `/locale/${nextLang}`;
    });

    // Collapsed Sidebar Flyout Submenu Hover Intent
    let activeHoverItem = null;
    let hoverTimeout = null;

    const clearHoverActive = () => {
        if (hoverTimeout) {
            clearTimeout(hoverTimeout);
            hoverTimeout = null;
        }
        if (activeHoverItem) {
            activeHoverItem.classList.remove('is-hover-active');
            activeHoverItem = null;
        }
        document.querySelectorAll('.sidebar-nav [data-nav-item].is-hover-active').forEach((el) => {
            el.classList.remove('is-hover-active');
        });
    };

    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
        if (isMobile()) { sidebar?.classList.add('is-open'); sidebarBackdrop?.classList.add('is-visible'); return; }
        clearHoverActive();
        shell?.classList.toggle('is-sidebar-collapsed');
        localStorage.setItem('ks_col', shell?.classList.contains('is-sidebar-collapsed') ? '1' : '0');
        applyThemePreferences(); syncThemeControls();
    });
    document.querySelectorAll('[data-nav-toggle]').forEach((toggle) => toggle.addEventListener('click', () => {
        const item = toggle.closest('[data-nav-item]');
        const submenu = document.getElementById(toggle.getAttribute('aria-controls'));
        const shouldOpen = !item?.classList.contains('is-expanded');
        item?.parentElement.querySelectorAll(':scope > [data-nav-item].is-expanded').forEach((openItem) => { openItem.classList.remove('is-expanded'); openItem.querySelector('[data-nav-toggle]')?.setAttribute('aria-expanded', 'false'); openItem.querySelector('.nav-submenu')?.classList.remove('is-open'); });
        item?.classList.toggle('is-expanded', shouldOpen);
        toggle.setAttribute('aria-expanded', String(shouldOpen));
        submenu?.classList.toggle('is-open', shouldOpen);
    }));

    document.querySelectorAll('.sidebar-nav [data-nav-item].has-children').forEach((item) => {
        const submenu = item.querySelector('.nav-submenu');

        const enterHandler = () => {
            const isCollapsed = document.documentElement.dataset.sidebar === 'collapsed'
                || shell?.classList.contains('is-sidebar-collapsed');
            if (!isCollapsed || isMobile()) return;

            if (hoverTimeout) {
                clearTimeout(hoverTimeout);
                hoverTimeout = null;
            }

            if (activeHoverItem && activeHoverItem !== item) {
                activeHoverItem.classList.remove('is-hover-active');
            }

            activeHoverItem = item;
            item.classList.add('is-hover-active');
        };

        const leaveHandler = () => {
            const isCollapsed = document.documentElement.dataset.sidebar === 'collapsed'
                || shell?.classList.contains('is-sidebar-collapsed');
            if (!isCollapsed || isMobile()) return;

            if (hoverTimeout) {
                clearTimeout(hoverTimeout);
            }

            hoverTimeout = setTimeout(() => {
                item.classList.remove('is-hover-active');
                if (activeHoverItem === item) activeHoverItem = null;
            }, 250);
        };

        item.addEventListener('mouseenter', enterHandler);
        item.addEventListener('mouseleave', leaveHandler);
        if (submenu) {
            submenu.addEventListener('mouseenter', enterHandler);
            submenu.addEventListener('mouseleave', leaveHandler);
        }
    });
    document.querySelectorAll('[data-theme-mode]').forEach((control) => control.addEventListener('click', () => { localStorage.setItem('tc_mode', control.dataset.themeMode); applyThemePreferences(); syncThemeControls(); closePanels(); }));
    document.querySelectorAll('[data-layout]').forEach((control) => control.addEventListener('click', () => { localStorage.setItem('tc_layout', control.dataset.layout); applyThemePreferences(); syncThemeControls(); closeSidebar(); }));
    document.querySelectorAll('[data-container]').forEach((control) => control.addEventListener('click', () => { localStorage.setItem('tc_container', control.dataset.container); applyThemePreferences(); syncThemeControls(); }));
    document.querySelectorAll('[data-primary-color]').forEach((control) => control.addEventListener('click', () => { const color = palettes[control.dataset.primaryColor] || control.dataset.primaryColor; if (isColor(color)) { localStorage.setItem('tc_primary', color); applyThemePreferences(); syncThemeControls(); } }));
    document.querySelector('[data-primary-color-input]')?.addEventListener('input', (event) => { localStorage.setItem('tc_primary', event.target.value); applyThemePreferences(); syncThemeControls(); });
    document.querySelector('[data-primary-color-hex]')?.addEventListener('change', (event) => { const color = event.target.value.trim(); if (isColor(color)) { localStorage.setItem('tc_primary', color); applyThemePreferences(); syncThemeControls(); } else syncThemeControls(); });
    document.querySelectorAll('[data-bg-preset]').forEach((control) => control.addEventListener('click', () => {
        const color = control.dataset.bgPreset;
        if (isColor(color)) {
            localStorage.setItem('tc_bg_page', color);
            applyThemePreferences();
            syncThemeControls();
        }
    }));
    document.querySelector('[data-bg-color-input]')?.addEventListener('input', (event) => {
        localStorage.setItem('tc_bg_page', event.target.value);
        applyThemePreferences();
        syncThemeControls();
    });
    document.querySelector('[data-bg-color-hex]')?.addEventListener('change', (event) => {
        const color = event.target.value.trim();
        if (isColor(color)) {
            localStorage.setItem('tc_bg_page', color);
            applyThemePreferences();
            syncThemeControls();
        } else {
            syncThemeControls();
        }
    });
    document.querySelector('[data-theme-reset]')?.addEventListener('click', () => {
        localStorage.removeItem('tc_mode');
        localStorage.removeItem('tc_layout');
        localStorage.removeItem('tc_container');
        localStorage.removeItem('tc_primary');
        localStorage.removeItem('tc_bg_page');
        localStorage.removeItem('tc_font');
        localStorage.removeItem('tc_rounding');
        localStorage.removeItem('tc_font_size');
        localStorage.removeItem('ks_col');
        applyThemePreferences();
        syncThemeControls();
    });
    document.querySelector('[data-theme-font]')?.addEventListener('change', (event) => { localStorage.setItem('tc_font', event.target.value); applyThemePreferences(); syncThemeControls(); });
    document.querySelectorAll('[data-font-size]').forEach((control) => control.addEventListener('click', () => { localStorage.setItem('tc_font_size', control.dataset.fontSize); applyThemePreferences(); syncThemeControls(); }));
    document.querySelectorAll('[data-theme-radius]').forEach((control) => control.addEventListener('click', () => { localStorage.setItem('tc_rounding', control.dataset.themeRadius); applyThemePreferences(); syncThemeControls(); }));
    document.querySelector('[data-theme-radius-range]')?.addEventListener('input', (event) => { localStorage.setItem('tc_rounding', `${event.target.value}px`); applyThemePreferences(); syncThemeControls(); });
    document.querySelectorAll('[data-theme-drawer-toggle]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            openThemeDrawer();
        });
    });
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-theme-drawer-toggle]');
        if (trigger) {
            event.preventDefault();
            openThemeDrawer();
        }
    });
    document.querySelector('[data-theme-drawer-close]')?.addEventListener('click', closeThemeDrawer);
    sidebarBackdrop?.addEventListener('click', closeSidebar);
    themeBackdrop?.addEventListener('click', closeThemeDrawer);
    document.addEventListener('click', (event) => { if (!event.target.closest('.topbar-popover-wrap')) closePanels(); });

    // Spotlight Quick Search Modal
    const spotlightModalEl = document.getElementById('spotlightModal');
    let spotlightModalInstance = null;
    if (spotlightModalEl && window.bootstrap) {
        spotlightModalInstance = new window.bootstrap.Modal(spotlightModalEl);
    }

    const openSpotlight = () => {
        if (!spotlightModalEl) return;
        closePanels();
        closeSidebar();
        closeThemeDrawer();
        spotlightModalInstance?.show();
    };

    document.querySelectorAll('[data-spotlight-open]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openSpotlight();
        });
    });

    if (spotlightModalEl) {
        const searchInput = document.getElementById('spotlightSearchInput');
        const clearBtn = document.getElementById('spotlightClearBtn');
        const itemsList = document.querySelector('.spotlight-items-list');
        const items = document.querySelectorAll('.spotlight-item');
        const emptyState = document.getElementById('spotlightEmpty');
        let selectedIndex = -1;

        spotlightModalEl.addEventListener('shown.bs.modal', () => {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            if (clearBtn) clearBtn.classList.add('d-none');
            filterItems('');
            if (window.lucide) window.lucide.createIcons();
        });

        const filterItems = (query) => {
            const q = query.trim().toLowerCase();
            let visibleCount = 0;
            selectedIndex = -1;

            items.forEach((item) => {
                const title = item.dataset.title || '';
                const cat = item.dataset.category || '';
                const desc = item.dataset.description || '';
                const matches = !q || title.includes(q) || cat.includes(q) || desc.includes(q);

                item.classList.toggle('d-none', !matches);
                item.classList.remove('active');
                if (matches) visibleCount++;
            });

            if (emptyState) emptyState.classList.toggle('d-none', visibleCount > 0);
            if (itemsList) itemsList.classList.toggle('d-none', visibleCount === 0);

            const firstVisible = document.querySelector('.spotlight-item:not(.d-none)');
            if (firstVisible) {
                firstVisible.classList.add('active');
                selectedIndex = 0;
            }
        };

        searchInput?.addEventListener('input', (e) => {
            const val = e.target.value;
            clearBtn?.classList.toggle('d-none', !val);
            filterItems(val);
        });

        clearBtn?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            clearBtn.classList.add('d-none');
            filterItems('');
        });

        searchInput?.addEventListener('keydown', (e) => {
            const visibleItems = Array.from(document.querySelectorAll('.spotlight-item:not(.d-none)'));
            if (!visibleItems.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % visibleItems.length;
                visibleItems.forEach((el, i) => el.classList.toggle('active', i === selectedIndex));
                visibleItems[selectedIndex]?.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + visibleItems.length) % visibleItems.length;
                visibleItems.forEach((el, i) => el.classList.toggle('active', i === selectedIndex));
                visibleItems[selectedIndex]?.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
                    visibleItems[selectedIndex].click();
                } else if (visibleItems[0]) {
                    visibleItems[0].click();
                }
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
            closeThemeDrawer();
            closePanels();
        }
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            openSpotlight();
        }
    });
    document.querySelector('[data-fullscreen-toggle]')?.addEventListener('click', async () => { if (!document.fullscreenElement) await document.documentElement.requestFullscreen(); else await document.exitFullscreen(); });
    window.addEventListener('resize', () => { if (!isMobile()) closeSidebar(); });

    // Select2 format with Icon function (Theme Customization & UI)
    const formatSelect2WithIcon = (item) => {
        if (!item.id) {
            return item.text;
        }
        const element = item.element;
        const iconName = element?.dataset?.icon || (typeof jQuery !== 'undefined' ? jQuery(element).data('icon') : null);
        if (!iconName) {
            return item.text;
        }
        let iconSvg = '';
        if (window.lucide?.icons?.[iconName]?.toSvg) {
            iconSvg = window.lucide.icons[iconName].toSvg({
                width: 16,
                height: 16,
                class: 'select2-opt-icon me-2 flex-shrink-0'
            });
        } else {
            iconSvg = `<i data-lucide="${iconName}" class="select2-opt-icon me-2 flex-shrink-0" style="width: 16px; height: 16px;"></i>`;
        }
        return jQuery ? jQuery(`<span class="d-inline-flex align-items-center w-100 select2-icon-item">${iconSvg}<span>${item.text}</span></span>`) : item.text;
    };

    // Select2 Initialization for all selections
    const initSelect2 = (scope = document) => {
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            scope.querySelectorAll('select.js-select2, select.form-select').forEach((element) => {
                if (element.classList.contains('dt-input') || element.closest('.dataTables_length')) {
                    return;
                }
                if (!jQuery(element).hasClass('select2-hidden-accessible')) {
                    const placeholder = element.dataset.placeholder || element.getAttribute('placeholder') || '';
                    const modal = jQuery(element).closest('.modal');
                    const drawer = jQuery(element).closest('.theme-drawer');
                    const hasIconOptions = Array.from(element.options || []).some((opt) => Boolean(opt.dataset.icon));
                    const dropdownParent = modal.length ? modal : (drawer.length ? drawer : undefined);

                    jQuery(element).select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: placeholder || undefined,
                        allowClear: Boolean(placeholder),
                        dropdownParent: dropdownParent,
                        templateResult: hasIconOptions ? formatSelect2WithIcon : undefined,
                        templateSelection: hasIconOptions ? formatSelect2WithIcon : undefined,
                        escapeMarkup: function (markup) {
                            return markup;
                        }
                    });

                    if (hasIconOptions) {
                        jQuery(element).on('select2:open select2:select', () => {
                            if (window.lucide) {
                                window.lucide.createIcons({ icons });
                            }
                        });
                    }
                }
            });
        }
    };
    initSelect2();
    document.addEventListener('shown.bs.modal', (e) => initSelect2(e.target));

    // Global Date Filter Preset Listeners
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('click', '.js-date-preset-btn', function (e) {
            e.preventDefault();
            const preset = jQuery(this).data('preset');
            const targetSelector = jQuery(this).data('target') || '#filter-created-date';
            const input = document.querySelector(targetSelector);
            if (input && input._flatpickr) {
                applyPresetToFlatpickr(input._flatpickr, preset);
            } else if (input) {
                if (preset === 'clear') {
                    jQuery(input).val('').trigger('change');
                } else {
                    const range = getPresetDateRange(preset);
                    if (range) {
                        jQuery(input).val(`${range[0]} to ${range[1]}`).trigger('change');
                    }
                }
            }
        });

        jQuery(document).on('change', '.js-date-preset-select', function () {
            const preset = jQuery(this).val();
            const targetSelector = jQuery(this).data('target') || '#filter-created-date';
            const input = document.querySelector(targetSelector);
            if (preset === 'all' || !preset) {
                if (input && input._flatpickr) {
                    input._flatpickr.clear(true);
                } else if (input) {
                    jQuery(input).val('').trigger('change');
                }
                return;
            }
            if (preset === 'custom') {
                if (input && input._flatpickr) {
                    input._flatpickr.open();
                }
                return;
            }
            if (input && input._flatpickr) {
                applyPresetToFlatpickr(input._flatpickr, preset);
            } else if (input) {
                const range = getPresetDateRange(preset);
                if (range) {
                    jQuery(input).val(`${range[0]} to ${range[1]}`).trigger('change');
                }
            }
        });
    }

    // Datepicker with Today and Clear Action Options
    const initDatepickers = (scope = document) => {
        scope.querySelectorAll('.js-datepicker').forEach((element) => {
            if (element._flatpickr) return;
            window.flatpickr(element, {
                allowInput: true,
                dateFormat: 'Y-m-d'
            });
        });
    };
    initDatepickers();
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => new bootstrap.Tooltip(element));
    document.querySelectorAll('[data-filter-panel-toggle]').forEach((button) => button.addEventListener('click', () => {
        const panel = button.closest('.t-hdr')?.parentElement.querySelector('[data-filter-panel]');
        if (panel) panel.hidden = !panel.hidden;
    }));
    document.querySelectorAll('[data-list-search]').forEach((input) => {
        let timer;
        input.addEventListener('input', () => { window.clearTimeout(timer); timer = window.setTimeout(() => input.dispatchEvent(new CustomEvent('bizlite:search', { bubbles: true, detail: { value: input.value } })), 350); });
    });
    document.querySelectorAll('[data-filter-chip-remove]').forEach((button) => button.addEventListener('click', () => {
        const chip = button.closest('.af-chip');
        chip?.dispatchEvent(new CustomEvent('bizlite:filter-removed', { bubbles: true }));
        chip?.remove();
    }));
    document.querySelectorAll('[data-demo-listing]').forEach((listing) => {
        const rows = [...listing.querySelectorAll('[data-demo-row]')];
        const search = listing.querySelector('[data-list-search]');
        const status = listing.querySelector('[data-demo-status-filter]');
        const empty = listing.querySelector('[data-demo-empty]');
        const filterRows = () => {
            const query = search?.value.toLowerCase().trim() || '';
            const selectedStatus = status?.value || 'all';
            let visible = 0;
            rows.forEach((row) => { const matches = row.dataset.search.includes(query) && (selectedStatus === 'all' || row.dataset.status === selectedStatus); row.hidden = !matches; if (matches) visible += 1; });
            if (empty) empty.hidden = visible !== 0;
        };
        search?.addEventListener('bizlite:search', filterRows);
        status?.addEventListener('change', filterRows);
    });
    document.querySelectorAll('[data-statistics-id]').forEach((element) => {
        const key = `tc_statistics_${element.dataset.statisticsId}`;
        if (localStorage.getItem(key) === 'open') new bootstrap.Collapse(element, { toggle: true });
        element.addEventListener('shown.bs.collapse', () => localStorage.setItem(key, 'open'));
        element.addEventListener('hidden.bs.collapse', () => localStorage.setItem(key, 'closed'));
    });
    document.querySelectorAll('[data-delete-confirm]').forEach((button) => button.addEventListener('click', async () => {
        const result = await Swal.fire({ title: button.dataset.deleteTitle, text: button.dataset.deleteMessage, icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete', cancelButtonText: 'Cancel', confirmButtonColor: 'var(--red)' });
        if (result.isConfirmed) {
            const form = button.closest('form');
            if (form) form.requestSubmit();
            else await Swal.fire({ title: 'No delete action configured', text: 'Connect this control to a delete form or endpoint.', icon: 'info' });
        }
    }));
    document.addEventListener('draw.dt', () => createIcons({ icons }));
    createIcons({ icons });
});

window.showToast = (message, type = 'success') => {
    const stack = document.getElementById('ksToastStack');
    if (!stack) return;
    const toastEl = document.createElement('div');
    const bgClass = type === 'error' || type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'primary');
    toastEl.className = `toast align-items-center text-bg-${bgClass} border-0 shadow-sm`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${escapeHtml(message)}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    stack.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
};

// Global Session Timeout Handling: Redirect immediately to /login when session expires
jQuery(document).ajaxError((event, jqXHR) => {
    if (jqXHR.status === 401 || jqXHR.status === 419) {
        window.location.href = '/login';
    }
});

// Proactive session heartbeat check when page tab regains focus or periodically
if (window.AppUser && window.AppUser.id) {
    const checkSessionStatus = () => {
        fetch('/auth/session-status', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((res) => {
                if (res.status === 401 || res.status === 419) {
                    window.location.href = '/login';
                    return null;
                }
                return res.json();
            })
            .then((data) => {
                if (data && data.authenticated === false) {
                    window.location.href = '/login';
                }
            })
            .catch(() => {});
    };

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            checkSessionStatus();
        }
    });

    // Check periodically every 2 minutes
    setInterval(checkSessionStatus, 120000);
}

window.BizLiteTheme = { applyThemePreferences, showToast: window.showToast };

