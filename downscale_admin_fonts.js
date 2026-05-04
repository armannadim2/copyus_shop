const fs = require('fs');
const path = require('path');

const map = {
    'text-h1': '__TMP_H4__',
    'text-h2': '__TMP_H5__',
    'text-h3': '__TMP_H6__',
    'text-h4': '__TMP_SM_HEADER__',
    'text-h5': '__TMP_LG__',
    'text-h6': '__TMP_BASE__',
    'text-sm-header': '__TMP_SM__',
    'text-body-lg': '__TMP_TEXT_SM__'
};

const finalMap = {
    '__TMP_H4__': 'text-h4',
    '__TMP_H5__': 'text-h5',
    '__TMP_H6__': 'text-h6',
    '__TMP_SM_HEADER__': 'text-sm-header',
    '__TMP_LG__': 'text-lg',
    '__TMP_BASE__': 'text-base',
    '__TMP_SM__': 'text-sm',
    '__TMP_TEXT_SM__': 'text-sm'
};

const targets = [
    'resources/views/admin',
    'resources/views/layouts/admin.blade.php'
];

function processFile(fullPath) {
    let content = fs.readFileSync(fullPath, 'utf-8');
    let modified = false;
    
    for (const [oldClass, newClass] of Object.entries(map)) {
        const regex = new RegExp(`\\b${oldClass}\\b`, 'g');
        if (regex.test(content)) {
            content = content.replace(regex, newClass);
            modified = true;
        }
    }
    
    if (modified) {
        for (const [tempClass, newClass] of Object.entries(finalMap)) {
            const regex = new RegExp(`\\b${tempClass}\\b`, 'g');
            content = content.replace(regex, newClass);
        }
        fs.writeFileSync(fullPath, content);
        console.log(`Updated: ${fullPath}`);
    }
}

function walk(dir) {
    if (!fs.existsSync(dir)) return;
    
    if (fs.statSync(dir).isFile()) {
        if (dir.endsWith('.blade.php')) processFile(dir);
        return;
    }

    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            walk(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            processFile(fullPath);
        }
    }
}

targets.forEach(walk);
