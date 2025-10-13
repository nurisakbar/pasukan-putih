# Implementasi Validasi Frontend untuk Form Create Pasien

## Overview
Menambahkan validasi frontend yang komprehensif untuk mengurangi error sebelum data dikirim ke server, memberikan feedback real-time kepada user, dan meningkatkan user experience.

## Fitur Validasi yang Diimplementasikan

### 1. **Real-time Field Validation**
Validasi langsung saat user mengetik atau mengubah input:

#### **NIK Validation**
```javascript
// Validasi: 16 digit angka
$('#nik').on('input', function() {
    const rules = [
        { test: (val) => val.length === 0 || val.length === 16, message: 'NIK harus 16 digit' },
        { test: (val) => val.length === 0 || /^\d+$/.test(val), message: 'NIK harus berupa angka' }
    ];
    validateField('nik', value, 'NIK', rules);
});
```

#### **Name Validation**
```javascript
// Validasi: minimal 2 karakter
$('#name').on('input', function() {
    const rules = [
        { test: (val) => val.length === 0 || val.length >= 2, message: 'Nama minimal 2 karakter' }
    ];
    validateField('name', value, 'Nama', rules);
});
```

#### **RT/RW Validation**
```javascript
// Validasi: harus berupa angka
$('#rt, #rw').on('input', function() {
    const rules = [
        { test: (val) => val.length === 0 || /^\d+$/.test(val), message: 'RT/RW harus berupa angka' }
    ];
    validateField(fieldId, value, fieldName, rules);
});
```

#### **WhatsApp Validation**
```javascript
// Validasi: 10-13 digit angka (opsional)
$('#nomor_whatsapp').on('input', function() {
    const rules = [
        { test: (val) => val.length === 0 || /^[0-9]{10,13}$/.test(val), message: 'Nomor WhatsApp harus 10-13 digit angka' }
    ];
    validateField('nomor_whatsapp', value, 'Nomor WhatsApp', rules);
});
```

#### **Tanggal Lahir Validation**
```javascript
// Validasi: usia 0-120 tahun
$('#tanggal_lahir').on('change', function() {
    const birthDate = new Date(value);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    
    if (age < 0 || age > 120) {
        field.addClass('is-invalid');
        feedback.text('Tanggal Lahir tidak valid').show();
    }
});
```

### 2. **Form Status Indicator**
Indikator visual yang menunjukkan status form secara real-time:

#### **Status Types:**
- 🔴 **Error State**: Ada error pada form
- 🟡 **Incomplete State**: Form belum lengkap (X/9 lengkap)
- 🟢 **Ready State**: Form lengkap dan siap disimpan

#### **Visual Feedback:**
```javascript
if (hasErrors) {
    // Red alert: Ada error pada form
    formStatus.addClass('alert-danger');
    formStatusText.html('Ada error pada form, perbaiki terlebih dahulu');
    submitBtn.prop('disabled', true);
} else if (filledFields === totalFields) {
    // Green alert: Form siap disimpan
    formStatus.addClass('alert-success');
    formStatusText.html('Form sudah lengkap dan siap disimpan');
    submitBtn.prop('disabled', false);
} else {
    // Blue alert: Form belum lengkap
    formStatus.addClass('alert-info');
    formStatusText.html(`Form ${filledFields}/${totalFields} lengkap`);
    submitBtn.prop('disabled', true);
}
```

### 3. **Comprehensive Form Validation**
Validasi lengkap sebelum submit:

```javascript
function validateForm() {
    let isValid = true;
    let errorMessages = [];
    
    // Validasi semua field required
    const validations = [
        { field: 'nik', rules: ['required', 'length:16', 'numeric'] },
        { field: 'name', rules: ['required', 'minLength:2'] },
        { field: 'jenis_ktp', rules: ['required'] },
        { field: 'tanggal_lahir', rules: ['required', 'validDate'] },
        { field: 'jenis_kelamin', rules: ['required'] },
        { field: 'alamat', rules: ['required'] },
        { field: 'rt', rules: ['required', 'numeric'] },
        { field: 'rw', rules: ['required', 'numeric'] },
        { field: 'village_id', rules: ['required'] }
    ];
    
    // Validasi optional fields
    const nomorWhatsapp = $('#nomor_whatsapp').val().trim();
    if (nomorWhatsapp && !/^[0-9]{10,13}$/.test(nomorWhatsapp)) {
        errorMessages.push('• Nomor WhatsApp harus 10-13 digit angka');
        isValid = false;
    }
    
    return { isValid, errorMessages };
}
```

## User Experience Improvements

### ✅ **Real-time Feedback**
- Error message muncul langsung saat user mengetik
- Visual indicator (red border) pada field yang error
- Form status indicator yang update real-time

### ✅ **Progressive Validation**
- Field-by-field validation saat user mengetik
- Overall form status checking
- Submit button disabled/enabled berdasarkan status form

### ✅ **Clear Error Messages**
- Pesan error yang spesifik dan actionable
- Format bullet point untuk multiple errors
- SweetAlert untuk error summary

### ✅ **Visual Indicators**
- Bootstrap validation classes (`is-invalid`)
- Color-coded form status (red/yellow/green)
- Progress indicator (X/9 fields complete)

## Technical Implementation

### **Validation Rules Engine**
```javascript
function validateField(fieldId, value, fieldName, rules) {
    const field = $(`#${fieldId}`);
    const feedback = field.siblings('.invalid-feedback');
    
    for (const rule of rules) {
        if (!rule.test(value)) {
            field.addClass('is-invalid');
            feedback.text(rule.message).show();
            return false;
        }
    }
    
    field.removeClass('is-invalid');
    feedback.hide();
    return true;
}
```

### **Form Status Monitoring**
```javascript
function checkFormStatus() {
    const requiredFields = [
        { id: 'nik', name: 'NIK' },
        { id: 'name', name: 'Nama' },
        // ... other fields
    ];

    let filledFields = 0;
    let hasErrors = false;

    requiredFields.forEach(field => {
        const value = $(`#${field.id}`).val();
        const isInvalid = $(`#${field.id}`).hasClass('is-invalid');
        
        if (value && value.trim() !== '') filledFields++;
        if (isInvalid) hasErrors = true;
    });

    // Update UI based on status
    updateFormStatus(filledFields, hasErrors);
}
```

## Benefits

1. **Reduced Server Load**: Validasi frontend mengurangi request ke server
2. **Better UX**: User mendapat feedback langsung tanpa reload
3. **Error Prevention**: Mencegah submit form yang tidak valid
4. **Clear Guidance**: User tahu persis apa yang perlu diperbaiki
5. **Progressive Enhancement**: Form tetap berfungsi tanpa JavaScript

## Testing Scenarios

### 1. **Real-time Validation**
- Ketik NIK kurang dari 16 digit → Error message muncul
- Ketik nama kurang dari 2 karakter → Error message muncul
- Pilih tanggal lahir yang tidak valid → Error message muncul

### 2. **Form Status Indicator**
- Isi sebagian field → Status "Form X/9 lengkap"
- Ada error pada field → Status "Ada error pada form"
- Isi semua field dengan benar → Status "Form sudah lengkap"

### 3. **Submit Prevention**
- Form tidak lengkap → Submit button disabled
- Ada error → Submit button disabled
- Form lengkap dan valid → Submit button enabled
