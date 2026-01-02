-- =============================================
-- QUERY MANUAL UNTUK CHECK DATA BY DISTRICT
-- =============================================

-- 1. CEK SEMUA DISTRICT YANG ADA
SELECT 
    d.id as district_id,
    d.name as district_name,
    r.name as regency_name,
    p.name as province_name
FROM districts d
LEFT JOIN regencies r ON d.regency_id = r.id
LEFT JOIN provinces p ON r.province_id = p.id
WHERE p.id = 31  -- Jakarta
ORDER BY d.name;

-- 2. CEK DATA PASIEN PER DISTRICT
SELECT 
    d.id as district_id,
    d.name as district_name,
    COUNT(p.id) as total_pasien,
    COUNT(CASE WHEN p.flag_sicarik = 1 THEN 1 END) as pasien_carik,
    COUNT(CASE WHEN p.flag_sicarik = 0 OR p.flag_sicarik IS NULL THEN 1 END) as pasien_manual,
    COUNT(CASE WHEN p.user_id != '-' THEN 1 END) as pasien_dengan_user
FROM districts d
LEFT JOIN villages v ON d.id = v.district_id
LEFT JOIN pasiens p ON v.id = p.village_id AND p.deleted_at IS NULL
WHERE d.id IN (
    SELECT DISTINCT d2.id 
    FROM districts d2
    LEFT JOIN regencies r2 ON d2.regency_id = r2.id
    WHERE r2.province_id = 31
)
GROUP BY d.id, d.name
ORDER BY total_pasien DESC;

-- 3. CEK DATA KUNJUNGAN PER DISTRICT
SELECT 
    d.id as district_id,
    d.name as district_name,
    COUNT(v.id) as total_kunjungan,
    COUNT(CASE WHEN v.status = 'Kunjungan Awal' THEN 1 END) as kunjungan_awal,
    COUNT(CASE WHEN v.status = 'Kunjungan Lanjutan' THEN 1 END) as kunjungan_lanjutan
FROM districts d
LEFT JOIN villages vl ON d.id = vl.district_id
LEFT JOIN pasiens p ON vl.id = p.village_id AND p.deleted_at IS NULL
LEFT JOIN visitings v ON p.id = v.pasien_id
WHERE d.id IN (
    SELECT DISTINCT d2.id 
    FROM districts d2
    LEFT JOIN regencies r2 ON d2.regency_id = r2.id
    WHERE r2.province_id = 31
)
GROUP BY d.id, d.name
ORDER BY total_kunjungan DESC;

-- 4. CEK USER PER DISTRICT (UNTUK DEBUGGING ROLE)
SELECT 
    u.id as user_id,
    u.name as user_name,
    u.role,
    u.pustu_id,
    p.nama_pustu,
    d.id as district_id,
    d.name as district_name
FROM users u
LEFT JOIN pustus p ON u.pustu_id = p.id
LEFT JOIN villages v ON p.village_id = v.id
LEFT JOIN districts d ON v.district_id = d.id
WHERE u.role IN ('perawat', 'operator', 'sudinkes')
ORDER BY u.role, d.name, u.name;

-- 5. CEK DATA PASIEN DENGAN DETAIL USER CREATOR
SELECT 
    p.id as pasien_id,
    p.name as pasien_name,
    p.nik,
    p.user_id as creator_user_id,
    u.name as creator_name,
    u.role as creator_role,
    v.name as village_name,
    d.name as district_name,
    r.name as regency_name,
    p.created_at
FROM pasiens p
LEFT JOIN users u ON p.user_id = u.id
LEFT JOIN villages v ON p.village_id = v.id
LEFT JOIN districts d ON v.district_id = d.id
LEFT JOIN regencies r ON d.regency_id = r.id
WHERE p.deleted_at IS NULL
ORDER BY d.name, p.created_at DESC;

-- 6. CEK DATA PASIEN PER DISTRICT DENGAN BREAKDOWN BY ROLE CREATOR
SELECT 
    d.id as district_id,
    d.name as district_name,
    u.role as creator_role,
    COUNT(p.id) as total_pasien
FROM districts d
LEFT JOIN villages v ON d.id = v.district_id
LEFT JOIN pasiens p ON v.id = p.village_id AND p.deleted_at IS NULL
LEFT JOIN users u ON p.user_id = u.id
WHERE d.id IN (
    SELECT DISTINCT d2.id 
    FROM districts d2
    LEFT JOIN regencies r2 ON d2.regency_id = r2.id
    WHERE r2.province_id = 31
)
GROUP BY d.id, d.name, u.role
ORDER BY d.name, u.role;

-- 7. CEK PUSTU PER DISTRICT
SELECT 
    d.id as district_id,
    d.name as district_name,
    COUNT(pu.id) as total_pustu,
    COUNT(CASE WHEN pu.jenis_faskes = 'puskesmas' THEN 1 END) as puskesmas,
    COUNT(CASE WHEN pu.jenis_faskes = 'pustu' THEN 1 END) as pustu
FROM districts d
LEFT JOIN villages v ON d.id = v.district_id
LEFT JOIN pustus pu ON v.id = pu.village_id
WHERE d.id IN (
    SELECT DISTINCT d2.id 
    FROM districts d2
    LEFT JOIN regencies r2 ON d2.regency_id = r2.id
    WHERE r2.province_id = 31
)
GROUP BY d.id, d.name
ORDER BY d.name;

-- 8. QUERY UNTUK TEST FILTERING BERDASARKAN DISTRICT (SIMULASI DASHBOARD)
-- Ganti DISTRICT_ID dengan ID district yang ingin dicek
SET @DISTRICT_ID = 1; -- Ganti dengan district ID yang ingin dicek

SELECT 
    'PASIEN' as data_type,
    COUNT(p.id) as total_count
FROM pasiens p
LEFT JOIN villages v ON p.village_id = v.id
WHERE v.district_id = @DISTRICT_ID
AND p.deleted_at IS NULL

UNION ALL

SELECT 
    'KUNJUNGAN' as data_type,
    COUNT(v.id) as total_count
FROM visitings v
LEFT JOIN pasiens p ON v.pasien_id = p.id
LEFT JOIN villages vl ON p.village_id = vl.id
WHERE vl.district_id = @DISTRICT_ID;

-- 9. CEK DATA PASIEN YANG TIDAK MEMILIKI VILLAGE_ID (POTENSI MASALAH)
SELECT 
    p.id,
    p.name,
    p.nik,
    p.village_id,
    p.user_id,
    u.name as creator_name,
    u.role as creator_role,
    p.created_at
FROM pasiens p
LEFT JOIN users u ON p.user_id = u.id
WHERE p.village_id IS NULL
AND p.deleted_at IS NULL
ORDER BY p.created_at DESC;

-- 10. CEK KONSISTENSI DATA VILLAGE-DISTRICT
SELECT 
    v.id as village_id,
    v.name as village_name,
    v.district_id,
    d.name as district_name,
    COUNT(p.id) as total_pasien
FROM villages v
LEFT JOIN districts d ON v.district_id = d.id
LEFT JOIN pasiens p ON v.id = p.village_id AND p.deleted_at IS NULL
WHERE v.district_id IS NULL OR d.id IS NULL
GROUP BY v.id, v.name, v.district_id, d.name
ORDER BY total_pasien DESC;
